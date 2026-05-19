<?php

namespace App\Http\Controllers\AccountingGM;

use App\Http\Controllers\Controller;
use App\Models\InternationalTripRealization;
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
        $allDocuments = InternationalTripRealization::with(['trip.user.department', 'trip.user.position', 'trip.costCenter', 'status', 'revisions', 'approvals.role', 'approvals.user'])
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
        return view('accounting_gm.international_trip_realization.index', compact('internationalTripRealizations', 'statusFilter', 'counts'));
    }

    public function show(InternationalTripRealization $internationalTripRealization)
    {
        $internationalTripRealization->load(['trip.user.department', 'trip.user.position', 'trip.costCenter', 'status', 'revisions.user.department', 'revisions.status', 'approvals.user.department', 'approvals.role', 'approvals.status']);
        $approvalChain = $this->approvalService->getApprovalChain($internationalTripRealization);
        return view('accounting_gm.international_trip_realization.show', compact('internationalTripRealization', 'approvalChain'));
    }

    public function approve(Request $request, InternationalTripRealization $internationalTripRealization)
    {
        $validated = $request->validate(['remark' => 'nullable|string|max:1000']);
        try {
            DB::transaction(function () use ($internationalTripRealization, $validated) {
                $userRole = ApprovalRole::where('sequence', 3)->first();
                if (!$userRole) throw new \Exception('Approval role not found.');
                if ($this->approvalService->hasRejected($internationalTripRealization)) throw new \Exception('Document already rejected.');
                if (!$this->approvalService->isValidApprovalSequence($internationalTripRealization, $userRole->id)) throw new \Exception('Not ready for your approval.');
                if (empty($internationalTripRealization->hardfile_received_at)) {
                    throw new \Exception('Approval failed: Accounting staff must confirm hardfile receipt first.');
                }
                if ($internationalTripRealization->approvals()->where('approval_role_id', $userRole->id)->where('approval_status_id', 1)->exists()) throw new \Exception('Already approved.');
                $approval = new Approval(['user_id' => Auth::user()->id, 'approval_role_id' => $userRole->id, 'approval_status_id' => 1, 'remark' => $validated['remark'] ?? null, 'approval_at' => now()]);
                $fullyApprovedStatus = DocumentStatus::where('slug', 'fully-approved')->first();
                if ($fullyApprovedStatus) $internationalTripRealization->update(['document_status_id' => $fullyApprovedStatus->id]);
                $internationalTripRealization->approvals()->save($approval);
            });
            $this->notificationService->notifyDocumentApproved($internationalTripRealization, Auth::user(), 'Accounting GM', $validated['remark'] ?? null, 3);
            return redirect()->route('accounting-gm.international-trip-realization.show', $internationalTripRealization)->with('success', 'International Trip Realization fully approved.');
        } catch (\Exception $e) {
            return redirect()->route('accounting-gm.international-trip-realization.show', $internationalTripRealization)->with('error', $e->getMessage());
        }
    }

    public function reject(Request $request, InternationalTripRealization $internationalTripRealization)
    {
        $validated = $request->validate(['remark' => 'required|string|max:1000']);
        try {
            DB::transaction(function () use ($internationalTripRealization, $validated) {
                $userRole = ApprovalRole::where('sequence', 3)->first();
                if (!$userRole) throw new \Exception('Approval role not found.');
                if ($this->approvalService->hasRejected($internationalTripRealization)) throw new \Exception('Already rejected.');
                if (!$this->approvalService->isValidApprovalSequence($internationalTripRealization, $userRole->id)) throw new \Exception('Not ready for rejection.');
                $approval = new Approval(['user_id' => Auth::user()->id, 'approval_role_id' => $userRole->id, 'approval_status_id' => 2, 'remark' => $validated['remark'], 'approval_at' => now()]);
                $internationalTripRealization->approvals()->save($approval);
                $rejectedStatus = DocumentStatus::where('slug', 'rejected')->first();
                if ($rejectedStatus) $internationalTripRealization->update(['document_status_id' => $rejectedStatus->id]);
            });
            $this->notificationService->notifyDocumentRejected($internationalTripRealization, Auth::user(), 'Accounting GM', $validated['remark'], 3);
            return redirect()->route('accounting-gm.international-trip-realization.show', $internationalTripRealization)->with('success', 'International Trip Realization rejected.');
        } catch (\Exception $e) {
            return redirect()->route('accounting-gm.international-trip-realization.show', $internationalTripRealization)->with('error', $e->getMessage());
        }
    }

    public function bulkApprove(Request $request)
    {
        $validated = $request->validate([
            'document_ids' => 'required|array',
            'document_ids.*' => 'exists:international_trip_realization,id',
            'remark' => 'nullable|string|max:1000'
        ]);
        $successCount = 0;
        $errors = [];
        foreach ($validated['document_ids'] as $docId) {
            try {
                $doc = InternationalTripRealization::findOrFail($docId);
                DB::transaction(function () use ($doc, $validated) {
                    $userRole = ApprovalRole::where('sequence', 3)->first();
                    if (!$userRole) throw new \Exception('Approval role not found.');
                    if ($this->approvalService->hasRejected($doc)) throw new \Exception('Document already rejected.');
                    if (!$this->approvalService->isValidApprovalSequence($doc, $userRole->id)) throw new \Exception('Not ready for your approval.');
                    if (empty($doc->hardfile_received_at)) throw new \Exception('Approval failed: Accounting staff must confirm hardfile receipt first.');
                    if ($doc->approvals()->where('approval_role_id', $userRole->id)->where('approval_status_id', 1)->exists()) throw new \Exception('Already approved.');
                    $approval = new Approval(['user_id' => Auth::user()->id, 'approval_role_id' => $userRole->id, 'approval_status_id' => 1, 'remark' => $validated['remark'] ?? null, 'approval_at' => now()]);
                    $fullyApprovedStatus = DocumentStatus::where('slug', 'fully-approved')->first();
                    if ($fullyApprovedStatus) $doc->update(['document_status_id' => $fullyApprovedStatus->id]);
                    $doc->approvals()->save($approval);
                });
                $this->notificationService->notifyDocumentApproved($doc, Auth::user(), 'Accounting GM', $validated['remark'] ?? null, 3);
                $successCount++;
            } catch (\Exception $e) {
                $errors[] = "ID {$docId}: " . $e->getMessage();
            }
        }
        if (count($errors) > 0) {
            return redirect()->back()->with('error', "Approved {$successCount} documents. Errors on " . count($errors) . " documents: " . implode(', ', $errors));
        }
        return redirect()->back()->with('success', "Successfully approved {$successCount} documents.");
    }
}
