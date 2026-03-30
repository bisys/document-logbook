<?php

namespace App\Http\Controllers\AccountingStaff;

use App\Http\Controllers\Controller;
use App\Models\InternationalTrip;
use App\Models\Revision;
use App\Models\Approval;
use App\Models\DocumentStatus;
use App\Models\ApprovalRole;
use App\Services\ApprovalService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InternationalTripController extends Controller
{
    protected $approvalService;
    protected $notificationService;

    public function __construct(ApprovalService $approvalService, NotificationService $notificationService)
    {
        $this->approvalService = $approvalService;
        $this->notificationService = $notificationService;
    }

    public function index(Request $request)
    {
        $allDocuments = InternationalTrip::with(['user.department', 'user.position', 'costCenter', 'status', 'revisions.user.department', 'revisions.status', 'approvals.role', 'approvals.user'])
            ->orderBy('created_at', 'desc')->get();

        $statusFilter = $request->query('status', 'all');
        $internationalTrips = $allDocuments->filter(function ($doc) use ($statusFilter) {
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

        return view('accounting_staff.international_trip.index', compact('internationalTrips', 'statusFilter', 'counts'));
    }

    public function show(InternationalTrip $internationalTrip)
    {
        $internationalTrip->load(['user.department', 'user.position', 'costCenter', 'status', 'revisions.user.department', 'revisions.status', 'approvals.user.department', 'approvals.role', 'approvals.status']);
        $approvalChain = $this->approvalService->getApprovalChain($internationalTrip);
        $pendingRevisions = $internationalTrip->revisions()->where('revision_status_id', '!=', 2)->count();
        $canApprove = $pendingRevisions === 0;
        $totalRevisions = $internationalTrip->revisions()->count();
        $maxRevisions = 3;

        return view('accounting_staff.international_trip.show', compact('internationalTrip', 'canApprove', 'totalRevisions', 'maxRevisions', 'approvalChain'));
    }

    public function addRevision(Request $request, InternationalTrip $internationalTrip)
    {
        $validated = $request->validate(['remark' => 'required|string|max:1000']);
        if ($internationalTrip->approvals()->exists()) {
            return redirect()->route('accounting-staff.international-trip.show', $internationalTrip)->with('error', 'Cannot add revision: Document is on approval process.');
        }

        try {
            DB::transaction(function () use ($internationalTrip, $validated) {
                $currentRevisions = $internationalTrip->revisions()->count();
                if ($currentRevisions >= 3) throw new \Exception('Maximum revisions (3) reached.');

                $revision = new Revision(['revision_times' => $currentRevisions + 1, 'user_id' => Auth::user()->id, 'revision_status_id' => 1, 'remark' => $validated['remark'], 'revision_at' => now()]);
                $internationalTrip->revisions()->save($revision);

                $status = DocumentStatus::where('slug', 'waiting-revision')->first();
                if ($status) $internationalTrip->update(['document_status_id' => $status->id]);

                $this->notificationService->notifyRevisionRequested($internationalTrip, $revision);
            });
            return redirect()->route('accounting-staff.international-trip.show', $internationalTrip)->with('success', 'Revision request added successfully.');
        } catch (\Exception $e) {
            return redirect()->route('accounting-staff.international-trip.show', $internationalTrip)->with('error', $e->getMessage());
        }
    }

    public function approve(Request $request, InternationalTrip $internationalTrip)
    {
        $validated = $request->validate(['remark' => 'nullable|string|max:1000']);
        try {
            DB::transaction(function () use ($internationalTrip, $validated) {
                $pendingRevisions = $internationalTrip->revisions()->where('revision_status_id', '!=', 2)->count();
                if ($pendingRevisions > 0) throw new \Exception('Cannot approve while there are pending revisions.');

                $userRole = ApprovalRole::where('sequence', 1)->first();
                if (!$userRole) throw new \Exception('Approval role not found.');
                if ($this->approvalService->hasRejected($internationalTrip)) throw new \Exception('Approval process halted: document already rejected.');
                if (!$this->approvalService->isValidApprovalSequence($internationalTrip, $userRole->id)) throw new \Exception('This document is not ready for your approval or has already been processed.');
                if ($internationalTrip->approvals()->where('approval_role_id', $userRole->id)->where('approval_status_id', 1)->exists()) throw new \Exception('This document is already approved by your role.');

                $approval = new Approval(['user_id' => Auth::user()->id, 'approval_role_id' => $userRole->id, 'approval_status_id' => 1, 'remark' => $validated['remark'] ?? null, 'approval_at' => now()]);
                $nextStatus = DocumentStatus::where('slug', 'waiting-approval-manager')->first();
                if ($nextStatus) $internationalTrip->update(['document_status_id' => $nextStatus->id]);
                $internationalTrip->approvals()->save($approval);
            });
            $this->notificationService->notifyDocumentApproved($internationalTrip, Auth::user(), 'Accounting Staff', $validated['remark'] ?? null, 1);
            return redirect()->route('accounting-staff.international-trip.show', $internationalTrip)->with('success', 'International Trip approved successfully.');
        } catch (\Exception $e) {
            return redirect()->route('accounting-staff.international-trip.show', $internationalTrip)->with('error', $e->getMessage());
        }
    }

    public function reject(Request $request, InternationalTrip $internationalTrip)
    {
        $validated = $request->validate(['remark' => 'required|string|max:1000']);
        try {
            DB::transaction(function () use ($internationalTrip, $validated) {
                $userRole = ApprovalRole::where('sequence', 1)->first();
                if (!$userRole) throw new \Exception('Approval role not found.');
                $pendingRevisions = $internationalTrip->revisions()->where('revision_status_id', '!=', 2)->count();
                if ($pendingRevisions > 0) throw new \Exception('Cannot reject while there are pending revisions.');
                if ($this->approvalService->hasRejected($internationalTrip)) throw new \Exception('Cannot reject: document has already been rejected.');
                if (!$this->approvalService->isValidApprovalSequence($internationalTrip, $userRole->id)) throw new \Exception('This document is not ready for your rejection.');
                if ($internationalTrip->approvals()->where('approval_role_id', $userRole->id)->where('approval_status_id', 2)->exists()) throw new \Exception('This document is already rejected by your role.');

                $approval = new Approval(['user_id' => Auth::user()->id, 'approval_role_id' => $userRole->id, 'approval_status_id' => 2, 'remark' => $validated['remark'], 'approval_at' => now()]);
                $internationalTrip->approvals()->save($approval);
                $rejectedStatus = DocumentStatus::where('slug', 'rejected')->first();
                if ($rejectedStatus) $internationalTrip->update(['document_status_id' => $rejectedStatus->id]);
            });
            $this->notificationService->notifyDocumentRejected($internationalTrip, Auth::user(), 'Accounting Staff', $validated['remark'], 1);
            return redirect()->route('accounting-staff.international-trip.show', $internationalTrip)->with('success', 'International Trip rejected successfully.');
        } catch (\Exception $e) {
            return redirect()->route('accounting-staff.international-trip.show', $internationalTrip)->with('error', $e->getMessage());
        }
    }
}
