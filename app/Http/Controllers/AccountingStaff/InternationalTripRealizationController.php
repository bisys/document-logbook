<?php

namespace App\Http\Controllers\AccountingStaff;

use App\Http\Controllers\Controller;
use App\Models\InternationalTripRealization;
use App\Models\Revision;
use App\Models\Approval;
use App\Models\DocumentStatus;
use App\Models\ApprovalRole;
use App\Services\ApprovalService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InternationalTripRealizationController extends Controller
{
    protected $approvalService;
    protected $notificationService;
    public function __construct(ApprovalService $approvalService, NotificationService $notificationService) { $this->approvalService = $approvalService; $this->notificationService = $notificationService; }

    public function index(Request $request)
    {
        $allDocuments = InternationalTripRealization::with(['trip.user.department', 'trip.user.position', 'trip.costCenter', 'status', 'revisions.user.department', 'revisions.status', 'approvals.role', 'approvals.user'])
            ->orderBy('created_at', 'desc')->get();
        $statusFilter = $request->query('status', 'all');
        $internationalTripRealizations = $allDocuments->filter(function ($doc) use ($statusFilter) {
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
        return view('accounting_staff.international_trip_realization.index', compact('internationalTripRealizations', 'statusFilter', 'counts'));
    }

    public function show(InternationalTripRealization $internationalTripRealization)
    {
        $internationalTripRealization->load(['trip.user.department', 'trip.user.position', 'trip.costCenter', 'status', 'revisions.user.department', 'revisions.status', 'approvals.user.department', 'approvals.role', 'approvals.status']);
        $approvalChain = $this->approvalService->getApprovalChain($internationalTripRealization);
        $pendingRevisions = $internationalTripRealization->revisions()->where('revision_status_id', '!=', 2)->count();
        $canApprove = $pendingRevisions === 0;
        $totalRevisions = $internationalTripRealization->revisions()->count();
        $maxRevisions = 3;
        return view('accounting_staff.international_trip_realization.show', compact('internationalTripRealization', 'canApprove', 'totalRevisions', 'maxRevisions', 'approvalChain'));
    }

    public function addRevision(Request $request, InternationalTripRealization $internationalTripRealization)
    {
        $validated = $request->validate(['remark' => 'required|string|max:1000']);
        if ($internationalTripRealization->approvals()->exists()) {
            return redirect()->route('accounting-staff.international-trip-realization.show', $internationalTripRealization)->with('error', 'Cannot add revision: Document is on approval process.');
        }
        try {
            DB::transaction(function () use ($internationalTripRealization, $validated) {
                $currentRevisions = $internationalTripRealization->revisions()->count();
                if ($currentRevisions >= 3) throw new \Exception('Maximum revisions (3) reached.');
                $revision = new Revision(['revision_times' => $currentRevisions + 1, 'user_id' => Auth::user()->id, 'revision_status_id' => 1, 'remark' => $validated['remark'], 'revision_at' => now()]);
                $internationalTripRealization->revisions()->save($revision);
                $status = DocumentStatus::where('slug', 'waiting-revision')->first();
                if ($status) $internationalTripRealization->update(['document_status_id' => $status->id]);
                $this->notificationService->notifyRevisionRequested($internationalTripRealization, $revision);
            });
            return redirect()->route('accounting-staff.international-trip-realization.show', $internationalTripRealization)->with('success', 'Revision request added successfully.');
        } catch (\Exception $e) {
            return redirect()->route('accounting-staff.international-trip-realization.show', $internationalTripRealization)->with('error', $e->getMessage());
        }
    }

    public function approve(Request $request, InternationalTripRealization $internationalTripRealization)
    {
        $validated = $request->validate(['remark' => 'nullable|string|max:1000']);
        try {
            DB::transaction(function () use ($internationalTripRealization, $validated) {
                if ($internationalTripRealization->revisions()->where('revision_status_id', '!=', 2)->count() > 0) throw new \Exception('Cannot approve while there are pending revisions.');
                $userRole = ApprovalRole::where('sequence', 1)->first();
                if (!$userRole) throw new \Exception('Approval role not found.');
                if ($this->approvalService->hasRejected($internationalTripRealization)) throw new \Exception('Approval process halted: document already rejected.');
                if (!$this->approvalService->isValidApprovalSequence($internationalTripRealization, $userRole->id)) throw new \Exception('Not ready for your approval.');
                if ($internationalTripRealization->approvals()->where('approval_role_id', $userRole->id)->where('approval_status_id', 1)->exists()) throw new \Exception('Already approved by your role.');
                $approval = new Approval(['user_id' => Auth::user()->id, 'approval_role_id' => $userRole->id, 'approval_status_id' => 1, 'remark' => $validated['remark'] ?? null, 'approval_at' => now()]);
                $nextStatus = DocumentStatus::where('slug', 'waiting-approval-manager')->first();
                if ($nextStatus) $internationalTripRealization->update(['document_status_id' => $nextStatus->id]);
                $internationalTripRealization->approvals()->save($approval);
            });
            $this->notificationService->notifyDocumentApproved($internationalTripRealization, Auth::user(), 'Accounting Staff', $validated['remark'] ?? null, 1);
            return redirect()->route('accounting-staff.international-trip-realization.show', $internationalTripRealization)->with('success', 'International Trip Realization approved successfully.');
        } catch (\Exception $e) {
            return redirect()->route('accounting-staff.international-trip-realization.show', $internationalTripRealization)->with('error', $e->getMessage());
        }
    }

    public function reject(Request $request, InternationalTripRealization $internationalTripRealization)
    {
        $validated = $request->validate(['remark' => 'required|string|max:1000']);
        try {
            DB::transaction(function () use ($internationalTripRealization, $validated) {
                $userRole = ApprovalRole::where('sequence', 1)->first();
                if (!$userRole) throw new \Exception('Approval role not found.');
                if ($internationalTripRealization->revisions()->where('revision_status_id', '!=', 2)->count() > 0) throw new \Exception('Cannot reject while there are pending revisions.');
                if ($this->approvalService->hasRejected($internationalTripRealization)) throw new \Exception('Already rejected.');
                if (!$this->approvalService->isValidApprovalSequence($internationalTripRealization, $userRole->id)) throw new \Exception('Not ready for rejection.');
                $approval = new Approval(['user_id' => Auth::user()->id, 'approval_role_id' => $userRole->id, 'approval_status_id' => 2, 'remark' => $validated['remark'], 'approval_at' => now()]);
                $internationalTripRealization->approvals()->save($approval);
                $rejectedStatus = DocumentStatus::where('slug', 'rejected')->first();
                if ($rejectedStatus) $internationalTripRealization->update(['document_status_id' => $rejectedStatus->id]);
            });
            $this->notificationService->notifyDocumentRejected($internationalTripRealization, Auth::user(), 'Accounting Staff', $validated['remark'], 1);
            return redirect()->route('accounting-staff.international-trip-realization.show', $internationalTripRealization)->with('success', 'International Trip Realization rejected successfully.');
        } catch (\Exception $e) {
            return redirect()->route('accounting-staff.international-trip-realization.show', $internationalTripRealization)->with('error', $e->getMessage());
        }
    }

    public function receiveHardfile(Request $request, InternationalTripRealization $internationalTripRealization)
    {
        try {
            if ($internationalTripRealization->hardfile_received_at) {
                throw new \Exception('Hardfile has already been received for this document.');
            }
            $staffRole = ApprovalRole::where('sequence', 1)->first();
            if (!$staffRole) throw new \Exception('Approval role not found.');
            if (!$internationalTripRealization->approvals()->where('approval_role_id', $staffRole->id)->where('approval_status_id', 1)->exists()) {
                throw new \Exception('Cannot receive hardfile: document has not been approved by Accounting Staff yet.');
            }
            $internationalTripRealization->update(['hardfile_received_at' => now(), 'hardfile_received_by' => Auth::id()]);
            $this->notificationService->notifyHardfileReceived($internationalTripRealization, Auth::user());
            return redirect()->route('accounting-staff.international-trip-realization.show', $internationalTripRealization)->with('success', 'Hardfile received successfully.');
        } catch (\Exception $e) {
            return redirect()->route('accounting-staff.international-trip-realization.show', $internationalTripRealization)->with('error', $e->getMessage());
        }
    }

    public function bulkReceiveHardfile(Request $request)
    {
        $validated = $request->validate([
            'document_ids' => 'required|array',
            'document_ids.*' => 'exists:international_trip_realization,id',
        ]);
        $successCount = 0;
        $errors = [];
        foreach ($validated['document_ids'] as $docId) {
            try {
                $doc = InternationalTripRealization::findOrFail($docId);
                if ($doc->hardfile_received_at) throw new \Exception('Hardfile has already been received.');
                $staffRole = ApprovalRole::where('sequence', 1)->first();
                if (!$staffRole) throw new \Exception('Approval role not found.');
                if (!$doc->approvals()->where('approval_role_id', $staffRole->id)->where('approval_status_id', 1)->exists()) throw new \Exception('Document has not been approved by Accounting Staff yet.');
                $doc->update(['hardfile_received_at' => now(), 'hardfile_received_by' => Auth::id()]);
                $this->notificationService->notifyHardfileReceived($doc, Auth::user());
                $successCount++;
            } catch (\Exception $e) {
                $errors[] = "ID {$docId}: " . $e->getMessage();
            }
        }
        if (count($errors) > 0) {
            return redirect()->back()->with('error', "Received hardfile for {$successCount} documents. Errors on " . count($errors) . " documents: " . implode(', ', $errors));
        }
        return redirect()->back()->with('success', "Successfully received hardfile for {$successCount} documents.");
    }
}
