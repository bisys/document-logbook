<?php

namespace App\Http\Controllers\AccountingGM;

use App\Http\Controllers\Controller;
use App\Models\CashAdvanceDraw;
use App\Models\Approval;
use App\Models\DocumentStatus;
use App\Models\ApprovalRole;
use App\Services\ApprovalService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CashAdvanceDrawController extends Controller
{
    protected $approvalService;
    protected $notificationService;
    public function __construct(ApprovalService $approvalService, NotificationService $notificationService) { $this->approvalService = $approvalService; $this->notificationService = $notificationService; }

    public function index(Request $request)
    {
        $allDocuments = CashAdvanceDraw::with(['user.department', 'user.position', 'costCenter', 'status', 'realization', 'revisions', 'approvals.role', 'approvals.user'])
            ->orderBy('created_at', 'desc')->get();
        $statusFilter = $request->query('status', 'all');
        $cashAdvanceDraws = $allDocuments->filter(function ($doc) use ($statusFilter) {
            $slug = optional($doc->status)->slug ?? '';
            switch ($statusFilter) {
                case 'waiting-approval-staff': return $slug === 'waiting-approval-staff';
                case 'waiting-approval-manager': return $slug === 'waiting-approval-manager';
                case 'waiting-approval-gm': return $slug === 'waiting-approval-gm';
                case 'waiting-revision': return $slug === 'waiting-revision';
                case 'fully-approved': return $slug === 'fully-approved';
                default: return true;
            }
        });
        $counts = [
            'all' => $allDocuments->count(),
            'waiting-approval-staff' => $allDocuments->where('status.slug', 'waiting-approval-staff')->count(),
            'waiting-approval-manager' => $allDocuments->where('status.slug', 'waiting-approval-manager')->count(),
            'waiting-approval-gm' => $allDocuments->where('status.slug', 'waiting-approval-gm')->count(),
            'waiting-revision' => $allDocuments->where('status.slug', 'waiting-revision')->count(),
            'fully-approved' => $allDocuments->where('status.slug', 'fully-approved')->count(),
        ];
        return view('accounting_gm.cash_advance_draw.index', compact('cashAdvanceDraws', 'statusFilter', 'counts'));
    }

    public function show(CashAdvanceDraw $cashAdvanceDraw)
    {
        $cashAdvanceDraw->load(['user.department', 'user.position', 'costCenter', 'status', 'realization', 'revisions.user.department', 'revisions.status', 'approvals.user.department', 'approvals.role', 'approvals.status']);
        $approvalChain = $this->approvalService->getApprovalChain($cashAdvanceDraw);
        return view('accounting_gm.cash_advance_draw.show', compact('cashAdvanceDraw', 'approvalChain'));
    }

    public function approve(Request $request, CashAdvanceDraw $cashAdvanceDraw)
    {
        $validated = $request->validate(['remark' => 'nullable|string|max:1000']);
        try {
            DB::transaction(function () use ($cashAdvanceDraw, $validated) {
                $userRole = ApprovalRole::where('sequence', 3)->first();
                if (!$userRole) throw new \Exception('Approval role not found.');
                if ($this->approvalService->hasRejected($cashAdvanceDraw)) throw new \Exception('Document already rejected.');
                if (!$this->approvalService->isValidApprovalSequence($cashAdvanceDraw, $userRole->id)) throw new \Exception('Not ready for your approval.');

                if (empty($cashAdvanceDraw->hardfile_received_at)) {
                    throw new \Exception('Approval failed: Accounting staff must confirm hardfile receipt first.');
                }
                if ($cashAdvanceDraw->approvals()->where('approval_role_id', $userRole->id)->where('approval_status_id', 1)->exists()) throw new \Exception('Already approved.');

                $approval = new Approval(['user_id' => Auth::user()->id, 'approval_role_id' => $userRole->id, 'approval_status_id' => 1, 'remark' => $validated['remark'] ?? null, 'approval_at' => now()]);
                $fullyApprovedStatus = DocumentStatus::where('slug', 'fully-approved')->first();
                if ($fullyApprovedStatus) $cashAdvanceDraw->update(['document_status_id' => $fullyApprovedStatus->id]);
                $cashAdvanceDraw->approvals()->save($approval);
            });
            $this->notificationService->notifyDocumentApproved($cashAdvanceDraw, Auth::user(), 'Accounting GM', $validated['remark'] ?? null, 3);
            return redirect()->route('accounting-gm.cash-advance-draw.show', $cashAdvanceDraw)->with('success', 'Cash Advance Draw fully approved.');
        } catch (\Exception $e) {
            return redirect()->route('accounting-gm.cash-advance-draw.show', $cashAdvanceDraw)->with('error', $e->getMessage());
        }
    }

    public function reject(Request $request, CashAdvanceDraw $cashAdvanceDraw)
    {
        $validated = $request->validate(['remark' => 'required|string|max:1000']);
        try {
            DB::transaction(function () use ($cashAdvanceDraw, $validated) {
                $userRole = ApprovalRole::where('sequence', 3)->first();
                if (!$userRole) throw new \Exception('Approval role not found.');
                if ($this->approvalService->hasRejected($cashAdvanceDraw)) throw new \Exception('Already rejected.');
                if (!$this->approvalService->isValidApprovalSequence($cashAdvanceDraw, $userRole->id)) throw new \Exception('Not ready for rejection.');

                $approval = new Approval(['user_id' => Auth::user()->id, 'approval_role_id' => $userRole->id, 'approval_status_id' => 2, 'remark' => $validated['remark'], 'approval_at' => now()]);
                $cashAdvanceDraw->approvals()->save($approval);
                $rejectedStatus = DocumentStatus::where('slug', 'rejected')->first();
                if ($rejectedStatus) $cashAdvanceDraw->update(['document_status_id' => $rejectedStatus->id]);
            });
            $this->notificationService->notifyDocumentRejected($cashAdvanceDraw, Auth::user(), 'Accounting GM', $validated['remark'], 3);
            return redirect()->route('accounting-gm.cash-advance-draw.show', $cashAdvanceDraw)->with('success', 'Cash Advance Draw rejected.');
        } catch (\Exception $e) {
            return redirect()->route('accounting-gm.cash-advance-draw.show', $cashAdvanceDraw)->with('error', $e->getMessage());
        }
    }
}
