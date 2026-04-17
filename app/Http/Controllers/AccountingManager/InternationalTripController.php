<?php

namespace App\Http\Controllers\AccountingManager;

use App\Http\Controllers\Controller;
use App\Models\InternationalTrip;
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
    public function __construct(ApprovalService $approvalService, NotificationService $notificationService) { $this->approvalService = $approvalService; $this->notificationService = $notificationService; }

    public function index(Request $request)
    {
        $allDocuments = InternationalTrip::with(['user.department', 'user.position', 'costCenter', 'status', 'revisions', 'approvals.role', 'approvals.user'])
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
        return view('accounting_manager.international_trip.index', compact('internationalTrips', 'statusFilter', 'counts'));
    }

    public function show(InternationalTrip $internationalTrip)
    {
        $internationalTrip->load(['user.department', 'user.position', 'costCenter', 'status', 'revisions.user.department', 'revisions.status', 'approvals.user.department', 'approvals.role', 'approvals.status']);
        $approvalChain = $this->approvalService->getApprovalChain($internationalTrip);
        return view('accounting_manager.international_trip.show', compact('internationalTrip', 'approvalChain'));
    }

    public function approve(Request $request, InternationalTrip $internationalTrip)
    {
        $validated = $request->validate(['remark' => 'nullable|string|max:1000']);
        try {
            DB::transaction(function () use ($internationalTrip, $validated) {
                $userRole = ApprovalRole::where('sequence', 2)->first();
                if (!$userRole) throw new \Exception('Approval role not found.');
                if ($this->approvalService->hasRejected($internationalTrip)) throw new \Exception('Document already rejected.');
                if (!$this->approvalService->isValidApprovalSequence($internationalTrip, $userRole->id)) throw new \Exception('Not ready for your approval.');

                if (empty($internationalTrip->hardfile_received_at)) {
                    throw new \Exception('Approval failed: Accounting staff must confirm hardfile receipt first.');
                }
                if ($internationalTrip->approvals()->where('approval_role_id', $userRole->id)->where('approval_status_id', 1)->exists()) throw new \Exception('Already approved by your role.');

                $approval = new Approval(['user_id' => Auth::user()->id, 'approval_role_id' => $userRole->id, 'approval_status_id' => 1, 'remark' => $validated['remark'] ?? null, 'approval_at' => now()]);
                $nextStatus = DocumentStatus::where('slug', 'waiting-approval-gm')->first();
                if ($nextStatus) $internationalTrip->update(['document_status_id' => $nextStatus->id]);
                $internationalTrip->approvals()->save($approval);
            });
            $this->notificationService->notifyDocumentApproved($internationalTrip, Auth::user(), 'Accounting Manager', $validated['remark'] ?? null, 2);
            return redirect()->route('accounting-manager.international-trip.show', $internationalTrip)->with('success', 'International Trip approved successfully.');
        } catch (\Exception $e) {
            return redirect()->route('accounting-manager.international-trip.show', $internationalTrip)->with('error', $e->getMessage());
        }
    }

    public function reject(Request $request, InternationalTrip $internationalTrip)
    {
        $validated = $request->validate(['remark' => 'required|string|max:1000']);
        try {
            DB::transaction(function () use ($internationalTrip, $validated) {
                $userRole = ApprovalRole::where('sequence', 2)->first();
                if (!$userRole) throw new \Exception('Approval role not found.');
                if ($this->approvalService->hasRejected($internationalTrip)) throw new \Exception('Already rejected.');
                if (!$this->approvalService->isValidApprovalSequence($internationalTrip, $userRole->id)) throw new \Exception('Not ready for rejection.');

                $approval = new Approval(['user_id' => Auth::user()->id, 'approval_role_id' => $userRole->id, 'approval_status_id' => 2, 'remark' => $validated['remark'], 'approval_at' => now()]);
                $internationalTrip->approvals()->save($approval);
                $rejectedStatus = DocumentStatus::where('slug', 'rejected')->first();
                if ($rejectedStatus) $internationalTrip->update(['document_status_id' => $rejectedStatus->id]);
            });
            $this->notificationService->notifyDocumentRejected($internationalTrip, Auth::user(), 'Accounting Manager', $validated['remark'], 2);
            return redirect()->route('accounting-manager.international-trip.show', $internationalTrip)->with('success', 'International Trip rejected successfully.');
        } catch (\Exception $e) {
            return redirect()->route('accounting-manager.international-trip.show', $internationalTrip)->with('error', $e->getMessage());
        }
    }

    public function bulkApprove(Request $request)
    {
        $validated = $request->validate([
            'document_ids' => 'required|array',
            'document_ids.*' => 'exists:international_trip,id',
            'remark' => 'nullable|string|max:1000'
        ]);

        $successCount = 0;
        $errors = [];

        foreach ($validated['document_ids'] as $docId) {
            try {
                $internationalTrip = InternationalTrip::findOrFail($docId);
                                DB::transaction(function () use ($internationalTrip, $validated) {
                $userRole = ApprovalRole::where('sequence', 2)->first();
                if (!$userRole) throw new \Exception('Approval role not found.');
                if ($this->approvalService->hasRejected($internationalTrip)) throw new \Exception('Document already rejected.');
                if (!$this->approvalService->isValidApprovalSequence($internationalTrip, $userRole->id)) throw new \Exception('Not ready for your approval.');

                if (empty($internationalTrip->hardfile_received_at)) {
                throw new \Exception('Approval failed: Accounting staff must confirm hardfile receipt first.');
                }
                if ($internationalTrip->approvals()->where('approval_role_id', $userRole->id)->where('approval_status_id', 1)->exists()) throw new \Exception('Already approved by your role.');

                $approval = new Approval(['user_id' => Auth::user()->id, 'approval_role_id' => $userRole->id, 'approval_status_id' => 1, 'remark' => $validated['remark'] ?? null, 'approval_at' => now()]);
                $nextStatus = DocumentStatus::where('slug', 'waiting-approval-gm')->first();
                if ($nextStatus) $internationalTrip->update(['document_status_id' => $nextStatus->id]);
                $internationalTrip->approvals()->save($approval);
                });
                $this->notificationService->notifyDocumentApproved($internationalTrip, Auth::user(), 'Accounting Manager', $validated['remark'] ?? null, 2);
                $successCount++;
            } catch (\Exception $e) {
                $errors[] = "InternationalTrip ID {$docId}: " . $e->getMessage();
            }
        }

        if (count($errors) > 0) {
            $errorMessage = "Approved {$successCount} documents. Errors on " . count($errors) . " documents: " . implode(', ', $errors);
            return redirect()->back()->with('error', $errorMessage);
        }

        return redirect()->back()->with('success', "Successfully approved {$successCount} documents.");
    }
}
