<?php

namespace App\Http\Controllers\AccountingGM;

use App\Http\Controllers\Controller;
use App\Models\CashAdvanceRealization;
use App\Models\Approval;
use App\Models\DocumentStatus;
use App\Models\ApprovalRole;
use App\Services\ApprovalService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CashAdvanceRealizationController extends Controller
{
    protected $approvalService;
    protected $notificationService;
    public function __construct(ApprovalService $approvalService, NotificationService $notificationService) { $this->approvalService = $approvalService; $this->notificationService = $notificationService; }

    public function index(Request $request)
    {
        $allDocuments = CashAdvanceRealization::with(['draw.user.department', 'draw.user.position', 'draw.costCenter', 'status', 'revisions', 'approvals.role', 'approvals.user'])
            ->orderBy('created_at', 'desc')->get();
        $statusFilter = $request->query('status', 'all');
        $cashAdvanceRealizations = $allDocuments->filter(function ($doc) use ($statusFilter) {
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
        return view('accounting_gm.cash_advance_realization.index', compact('cashAdvanceRealizations', 'statusFilter', 'counts'));
    }

    public function show(CashAdvanceRealization $cashAdvanceRealization)
    {
        $cashAdvanceRealization->load(['draw.user.department', 'draw.user.position', 'draw.costCenter', 'status', 'revisions.user.department', 'revisions.status', 'approvals.user.department', 'approvals.role', 'approvals.status']);
        $approvalChain = $this->approvalService->getApprovalChain($cashAdvanceRealization);
        return view('accounting_gm.cash_advance_realization.show', compact('cashAdvanceRealization', 'approvalChain'));
    }

    public function approve(Request $request, CashAdvanceRealization $cashAdvanceRealization)
    {
        $validated = $request->validate(['remark' => 'nullable|string|max:1000']);
        try {
            DB::transaction(function () use ($cashAdvanceRealization, $validated) {
                $userRole = ApprovalRole::where('sequence', 3)->first();
                if (!$userRole) throw new \Exception('Approval role not found.');
                if ($this->approvalService->hasRejected($cashAdvanceRealization)) throw new \Exception('Document already rejected.');
                if (!$this->approvalService->isValidApprovalSequence($cashAdvanceRealization, $userRole->id)) throw new \Exception('Not ready for your approval.');

                if (empty($cashAdvanceRealization->hardfile_received_at)) {
                    throw new \Exception('Approval failed: Accounting staff must confirm hardfile receipt first.');
                }
                if ($cashAdvanceRealization->approvals()->where('approval_role_id', $userRole->id)->where('approval_status_id', 1)->exists()) throw new \Exception('Already approved.');

                $approval = new Approval(['user_id' => Auth::user()->id, 'approval_role_id' => $userRole->id, 'approval_status_id' => 1, 'remark' => $validated['remark'] ?? null, 'approval_at' => now()]);
                $fullyApprovedStatus = DocumentStatus::where('slug', 'fully-approved')->first();
                if ($fullyApprovedStatus) $cashAdvanceRealization->update(['document_status_id' => $fullyApprovedStatus->id]);
                $cashAdvanceRealization->approvals()->save($approval);
            });
            $this->notificationService->notifyDocumentApproved($cashAdvanceRealization, Auth::user(), 'Accounting GM', $validated['remark'] ?? null, 3);
            return redirect()->route('accounting-gm.cash-advance-realization.show', $cashAdvanceRealization)->with('success', 'Cash Advance Realization fully approved.');
        } catch (\Exception $e) {
            return redirect()->route('accounting-gm.cash-advance-realization.show', $cashAdvanceRealization)->with('error', $e->getMessage());
        }
    }

    public function reject(Request $request, CashAdvanceRealization $cashAdvanceRealization)
    {
        $validated = $request->validate(['remark' => 'required|string|max:1000']);
        try {
            DB::transaction(function () use ($cashAdvanceRealization, $validated) {
                $userRole = ApprovalRole::where('sequence', 3)->first();
                if (!$userRole) throw new \Exception('Approval role not found.');
                if ($this->approvalService->hasRejected($cashAdvanceRealization)) throw new \Exception('Already rejected.');
                if (!$this->approvalService->isValidApprovalSequence($cashAdvanceRealization, $userRole->id)) throw new \Exception('Not ready for rejection.');

                $approval = new Approval(['user_id' => Auth::user()->id, 'approval_role_id' => $userRole->id, 'approval_status_id' => 2, 'remark' => $validated['remark'], 'approval_at' => now()]);
                $cashAdvanceRealization->approvals()->save($approval);
                $rejectedStatus = DocumentStatus::where('slug', 'rejected')->first();
                if ($rejectedStatus) $cashAdvanceRealization->update(['document_status_id' => $rejectedStatus->id]);
            });
            $this->notificationService->notifyDocumentRejected($cashAdvanceRealization, Auth::user(), 'Accounting GM', $validated['remark'], 3);
            return redirect()->route('accounting-gm.cash-advance-realization.show', $cashAdvanceRealization)->with('success', 'Cash Advance Realization rejected.');
        } catch (\Exception $e) {
            return redirect()->route('accounting-gm.cash-advance-realization.show', $cashAdvanceRealization)->with('error', $e->getMessage());
        }
    }

    public function bulkApprove(Request $request)
    {
        $validated = $request->validate([
            'document_ids' => 'required|array',
            'document_ids.*' => 'exists:cash_advance_realization,id',
            'remark' => 'nullable|string|max:1000'
        ]);

        $successCount = 0;
        $errors = [];

        foreach ($validated['document_ids'] as $docId) {
            try {
                $cashAdvanceRealization = CashAdvanceRealization::findOrFail($docId);
                                DB::transaction(function () use ($cashAdvanceRealization, $validated) {
                $userRole = ApprovalRole::where('sequence', 3)->first();
                if (!$userRole) throw new \Exception('Approval role not found.');
                if ($this->approvalService->hasRejected($cashAdvanceRealization)) throw new \Exception('Document already rejected.');
                if (!$this->approvalService->isValidApprovalSequence($cashAdvanceRealization, $userRole->id)) throw new \Exception('Not ready for your approval.');

                if (empty($cashAdvanceRealization->hardfile_received_at)) {
                throw new \Exception('Approval failed: Accounting staff must confirm hardfile receipt first.');
                }
                if ($cashAdvanceRealization->approvals()->where('approval_role_id', $userRole->id)->where('approval_status_id', 1)->exists()) throw new \Exception('Already approved.');

                $approval = new Approval(['user_id' => Auth::user()->id, 'approval_role_id' => $userRole->id, 'approval_status_id' => 1, 'remark' => $validated['remark'] ?? null, 'approval_at' => now()]);
                $fullyApprovedStatus = DocumentStatus::where('slug', 'fully-approved')->first();
                if ($fullyApprovedStatus) $cashAdvanceRealization->update(['document_status_id' => $fullyApprovedStatus->id]);
                $cashAdvanceRealization->approvals()->save($approval);
                });
                $this->notificationService->notifyDocumentApproved($cashAdvanceRealization, Auth::user(), 'Accounting GM', $validated['remark'] ?? null, 3);
                $successCount++;
            } catch (\Exception $e) {
                $errors[] = "CashAdvanceRealization ID {$docId}: " . $e->getMessage();
            }
        }

        if (count($errors) > 0) {
            $errorMessage = "Approved {$successCount} documents. Errors on " . count($errors) . " documents: " . implode(', ', $errors);
            return redirect()->back()->with('error', $errorMessage);
        }

        return redirect()->back()->with('success', "Successfully approved {$successCount} documents.");
    }
}
