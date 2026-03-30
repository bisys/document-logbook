<?php

namespace App\Http\Controllers\AccountingStaff;

use App\Http\Controllers\Controller;
use App\Models\CashAdvanceDraw;
use App\Models\Revision;
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
        $allDocuments = CashAdvanceDraw::with(['user.department', 'user.position', 'costCenter', 'status', 'realization', 'revisions.user.department', 'revisions.status', 'approvals.role', 'approvals.user'])
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
        return view('accounting_staff.cash_advance_draw.index', compact('cashAdvanceDraws', 'statusFilter', 'counts'));
    }

    public function show(CashAdvanceDraw $cashAdvanceDraw)
    {
        $cashAdvanceDraw->load(['user.department', 'user.position', 'costCenter', 'status', 'realization', 'revisions.user.department', 'revisions.status', 'approvals.user.department', 'approvals.role', 'approvals.status']);
        $approvalChain = $this->approvalService->getApprovalChain($cashAdvanceDraw);
        $pendingRevisions = $cashAdvanceDraw->revisions()->where('revision_status_id', '!=', 2)->count();
        $canApprove = $pendingRevisions === 0;
        $totalRevisions = $cashAdvanceDraw->revisions()->count();
        $maxRevisions = 3;
        return view('accounting_staff.cash_advance_draw.show', compact('cashAdvanceDraw', 'canApprove', 'totalRevisions', 'maxRevisions', 'approvalChain'));
    }

    public function addRevision(Request $request, CashAdvanceDraw $cashAdvanceDraw)
    {
        $validated = $request->validate(['remark' => 'required|string|max:1000']);
        if ($cashAdvanceDraw->approvals()->exists()) {
            return redirect()->route('accounting-staff.cash-advance-draw.show', $cashAdvanceDraw)->with('error', 'Cannot add revision: Document is on approval process.');
        }
        try {
            DB::transaction(function () use ($cashAdvanceDraw, $validated) {
                $currentRevisions = $cashAdvanceDraw->revisions()->count();
                if ($currentRevisions >= 3) throw new \Exception('Maximum revisions (3) reached.');
                $revision = new Revision(['revision_times' => $currentRevisions + 1, 'user_id' => Auth::user()->id, 'revision_status_id' => 1, 'remark' => $validated['remark'], 'revision_at' => now()]);
                $cashAdvanceDraw->revisions()->save($revision);
                $status = DocumentStatus::where('slug', 'waiting-revision')->first();
                if ($status) $cashAdvanceDraw->update(['document_status_id' => $status->id]);

                $this->notificationService->notifyRevisionRequested($cashAdvanceDraw, $revision);
            });
            return redirect()->route('accounting-staff.cash-advance-draw.show', $cashAdvanceDraw)->with('success', 'Revision request added successfully.');
        } catch (\Exception $e) {
            return redirect()->route('accounting-staff.cash-advance-draw.show', $cashAdvanceDraw)->with('error', $e->getMessage());
        }
    }

    public function approve(Request $request, CashAdvanceDraw $cashAdvanceDraw)
    {
        $validated = $request->validate(['remark' => 'nullable|string|max:1000']);
        try {
            DB::transaction(function () use ($cashAdvanceDraw, $validated) {
                if ($cashAdvanceDraw->revisions()->where('revision_status_id', '!=', 2)->count() > 0) throw new \Exception('Cannot approve while there are pending revisions.');
                $userRole = ApprovalRole::where('sequence', 1)->first();
                if (!$userRole) throw new \Exception('Approval role not found.');
                if ($this->approvalService->hasRejected($cashAdvanceDraw)) throw new \Exception('Approval process halted: document already rejected.');
                if (!$this->approvalService->isValidApprovalSequence($cashAdvanceDraw, $userRole->id)) throw new \Exception('This document is not ready for your approval.');
                if ($cashAdvanceDraw->approvals()->where('approval_role_id', $userRole->id)->where('approval_status_id', 1)->exists()) throw new \Exception('Already approved by your role.');

                $approval = new Approval(['user_id' => Auth::user()->id, 'approval_role_id' => $userRole->id, 'approval_status_id' => 1, 'remark' => $validated['remark'] ?? null, 'approval_at' => now()]);
                $nextStatus = DocumentStatus::where('slug', 'waiting-approval-manager')->first();
                if ($nextStatus) $cashAdvanceDraw->update(['document_status_id' => $nextStatus->id]);
                $cashAdvanceDraw->approvals()->save($approval);
            });
            $this->notificationService->notifyDocumentApproved($cashAdvanceDraw, Auth::user(), 'Accounting Staff', $validated['remark'] ?? null, 1);
            return redirect()->route('accounting-staff.cash-advance-draw.show', $cashAdvanceDraw)->with('success', 'Cash Advance Draw approved successfully.');
        } catch (\Exception $e) {
            return redirect()->route('accounting-staff.cash-advance-draw.show', $cashAdvanceDraw)->with('error', $e->getMessage());
        }
    }

    public function reject(Request $request, CashAdvanceDraw $cashAdvanceDraw)
    {
        $validated = $request->validate(['remark' => 'required|string|max:1000']);
        try {
            DB::transaction(function () use ($cashAdvanceDraw, $validated) {
                $userRole = ApprovalRole::where('sequence', 1)->first();
                if (!$userRole) throw new \Exception('Approval role not found.');
                if ($cashAdvanceDraw->revisions()->where('revision_status_id', '!=', 2)->count() > 0) throw new \Exception('Cannot reject while there are pending revisions.');
                if ($this->approvalService->hasRejected($cashAdvanceDraw)) throw new \Exception('Cannot reject: document has already been rejected.');
                if (!$this->approvalService->isValidApprovalSequence($cashAdvanceDraw, $userRole->id)) throw new \Exception('Not ready for rejection.');

                $approval = new Approval(['user_id' => Auth::user()->id, 'approval_role_id' => $userRole->id, 'approval_status_id' => 2, 'remark' => $validated['remark'], 'approval_at' => now()]);
                $cashAdvanceDraw->approvals()->save($approval);
                $rejectedStatus = DocumentStatus::where('slug', 'rejected')->first();
                if ($rejectedStatus) $cashAdvanceDraw->update(['document_status_id' => $rejectedStatus->id]);
            });
            $this->notificationService->notifyDocumentRejected($cashAdvanceDraw, Auth::user(), 'Accounting Staff', $validated['remark'], 1);
            return redirect()->route('accounting-staff.cash-advance-draw.show', $cashAdvanceDraw)->with('success', 'Cash Advance Draw rejected successfully.');
        } catch (\Exception $e) {
            return redirect()->route('accounting-staff.cash-advance-draw.show', $cashAdvanceDraw)->with('error', $e->getMessage());
        }
    }
}
