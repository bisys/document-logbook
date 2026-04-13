<?php

namespace App\Http\Controllers\AccountingStaff;

use App\Http\Controllers\Controller;
use App\Models\CashAdvanceRealization;
use App\Models\Revision;
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
        $allDocuments = CashAdvanceRealization::with(['draw.user.department', 'draw.user.position', 'draw.costCenter', 'status', 'revisions.user.department', 'revisions.status', 'approvals.role', 'approvals.user'])
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
        return view('accounting_staff.cash_advance_realization.index', compact('cashAdvanceRealizations', 'statusFilter', 'counts'));
    }

    public function show(CashAdvanceRealization $cashAdvanceRealization)
    {
        $cashAdvanceRealization->load(['draw.user.department', 'draw.user.position', 'draw.costCenter', 'status', 'revisions.user.department', 'revisions.status', 'approvals.user.department', 'approvals.role', 'approvals.status']);
        $approvalChain = $this->approvalService->getApprovalChain($cashAdvanceRealization);
        $pendingRevisions = $cashAdvanceRealization->revisions()->where('revision_status_id', '!=', 2)->count();
        $canApprove = $pendingRevisions === 0;
        $totalRevisions = $cashAdvanceRealization->revisions()->count();
        $maxRevisions = 3;
        return view('accounting_staff.cash_advance_realization.show', compact('cashAdvanceRealization', 'canApprove', 'totalRevisions', 'maxRevisions', 'approvalChain'));
    }

    public function addRevision(Request $request, CashAdvanceRealization $cashAdvanceRealization)
    {
        $validated = $request->validate(['remark' => 'required|string|max:1000']);
        if ($cashAdvanceRealization->approvals()->exists()) {
            return redirect()->route('accounting-staff.cash-advance-realization.show', $cashAdvanceRealization)->with('error', 'Cannot add revision: Document is on approval process.');
        }
        try {
            DB::transaction(function () use ($cashAdvanceRealization, $validated) {
                $currentRevisions = $cashAdvanceRealization->revisions()->count();
                if ($currentRevisions >= 3) throw new \Exception('Maximum revisions (3) reached.');
                $revision = new Revision(['revision_times' => $currentRevisions + 1, 'user_id' => Auth::user()->id, 'revision_status_id' => 1, 'remark' => $validated['remark'], 'revision_at' => now()]);
                $cashAdvanceRealization->revisions()->save($revision);
                $status = DocumentStatus::where('slug', 'waiting-revision')->first();
                if ($status) $cashAdvanceRealization->update(['document_status_id' => $status->id]);

                $this->notificationService->notifyRevisionRequested($cashAdvanceRealization, $revision);
            });
            return redirect()->route('accounting-staff.cash-advance-realization.show', $cashAdvanceRealization)->with('success', 'Revision request added successfully.');
        } catch (\Exception $e) {
            return redirect()->route('accounting-staff.cash-advance-realization.show', $cashAdvanceRealization)->with('error', $e->getMessage());
        }
    }

    public function approve(Request $request, CashAdvanceRealization $cashAdvanceRealization)
    {
        $validated = $request->validate(['remark' => 'nullable|string|max:1000']);
        try {
            DB::transaction(function () use ($cashAdvanceRealization, $validated) {
                if ($cashAdvanceRealization->revisions()->where('revision_status_id', '!=', 2)->count() > 0) throw new \Exception('Cannot approve while there are pending revisions.');
                $userRole = ApprovalRole::where('sequence', 1)->first();
                if (!$userRole) throw new \Exception('Approval role not found.');
                if ($this->approvalService->hasRejected($cashAdvanceRealization)) throw new \Exception('Approval process halted: document already rejected.');
                if (!$this->approvalService->isValidApprovalSequence($cashAdvanceRealization, $userRole->id)) throw new \Exception('Not ready for your approval.');
                if ($cashAdvanceRealization->approvals()->where('approval_role_id', $userRole->id)->where('approval_status_id', 1)->exists()) throw new \Exception('Already approved by your role.');

                $approval = new Approval(['user_id' => Auth::user()->id, 'approval_role_id' => $userRole->id, 'approval_status_id' => 1, 'remark' => $validated['remark'] ?? null, 'approval_at' => now()]);
                $nextStatus = DocumentStatus::where('slug', 'waiting-approval-manager')->first();
                if ($nextStatus) $cashAdvanceRealization->update(['document_status_id' => $nextStatus->id]);
                $cashAdvanceRealization->approvals()->save($approval);
            });
            $this->notificationService->notifyDocumentApproved($cashAdvanceRealization, Auth::user(), 'Accounting Staff', $validated['remark'] ?? null, 1);
            return redirect()->route('accounting-staff.cash-advance-realization.show', $cashAdvanceRealization)->with('success', 'Cash Advance Realization approved successfully.');
        } catch (\Exception $e) {
            return redirect()->route('accounting-staff.cash-advance-realization.show', $cashAdvanceRealization)->with('error', $e->getMessage());
        }
    }

    public function reject(Request $request, CashAdvanceRealization $cashAdvanceRealization)
    {
        $validated = $request->validate(['remark' => 'required|string|max:1000']);
        try {
            DB::transaction(function () use ($cashAdvanceRealization, $validated) {
                $userRole = ApprovalRole::where('sequence', 1)->first();
                if (!$userRole) throw new \Exception('Approval role not found.');
                if ($cashAdvanceRealization->revisions()->where('revision_status_id', '!=', 2)->count() > 0) throw new \Exception('Cannot reject while there are pending revisions.');
                if ($this->approvalService->hasRejected($cashAdvanceRealization)) throw new \Exception('Already rejected.');
                if (!$this->approvalService->isValidApprovalSequence($cashAdvanceRealization, $userRole->id)) throw new \Exception('Not ready for rejection.');

                $approval = new Approval(['user_id' => Auth::user()->id, 'approval_role_id' => $userRole->id, 'approval_status_id' => 2, 'remark' => $validated['remark'], 'approval_at' => now()]);
                $cashAdvanceRealization->approvals()->save($approval);
                $rejectedStatus = DocumentStatus::where('slug', 'rejected')->first();
                if ($rejectedStatus) $cashAdvanceRealization->update(['document_status_id' => $rejectedStatus->id]);
            });
            $this->notificationService->notifyDocumentRejected($cashAdvanceRealization, Auth::user(), 'Accounting Staff', $validated['remark'], 1);
            return redirect()->route('accounting-staff.cash-advance-realization.show', $cashAdvanceRealization)->with('success', 'Cash Advance Realization rejected successfully.');
        } catch (\Exception $e) {
            return redirect()->route('accounting-staff.cash-advance-realization.show', $cashAdvanceRealization)->with('error', $e->getMessage());
        }
    }

    public function receiveHardfile(Request $request, CashAdvanceRealization $cashAdvanceRealization)
    {
        try {
            if ($cashAdvanceRealization->hardfile_received_at) {
                throw new \Exception('Hardfile has already been received for this document.');
            }
            $staffRole = ApprovalRole::where('sequence', 1)->first();
            if (!$staffRole) throw new \Exception('Approval role not found.');
            if (!$cashAdvanceRealization->approvals()->where('approval_role_id', $staffRole->id)->where('approval_status_id', 1)->exists()) {
                throw new \Exception('Cannot receive hardfile: document has not been approved by Accounting Staff yet.');
            }
            $cashAdvanceRealization->update(['hardfile_received_at' => now(), 'hardfile_received_by' => Auth::id()]);
            $this->notificationService->notifyHardfileReceived($cashAdvanceRealization, Auth::user());
            return redirect()->route('accounting-staff.cash-advance-realization.show', $cashAdvanceRealization)->with('success', 'Hardfile received successfully.');
        } catch (\Exception $e) {
            return redirect()->route('accounting-staff.cash-advance-realization.show', $cashAdvanceRealization)->with('error', $e->getMessage());
        }
    }
}
