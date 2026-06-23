<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInternationalTripRealizationRequest;
use App\Http\Requests\UpdateInternationalTripRealizationRequest;
use App\Models\InternationalTripRealization;
use App\Models\InternationalTrip;
use App\Models\Revision;
use App\Models\DocumentStatus;
use App\Models\CostCenter;
use App\Services\ApprovalService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InternationalTripRealizationController extends Controller
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
        $allDocuments = InternationalTripRealization::with(['revisions', 'approvals', 'trip.user', 'trip.costCenter', 'status'])
            ->whereHas('trip', function ($q) {
                $q->where('user_id', Auth::user()->id);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $statusFilter = $request->query('status', 'all');

        $internationalTripRealizations = $allDocuments->filter(function ($doc) use ($statusFilter) {
            $slug = optional($doc->status)->slug ?? '';
            switch ($statusFilter) {
                case 'waiting-approval-staff': return $slug === 'waiting-approval-staff';
                case 'waiting-approval-manager': return $slug === 'waiting-approval-manager';
                case 'waiting-approval-gm': return $slug === 'waiting-approval-gm';
                case 'waiting-revision': return $slug === 'waiting-revision';
                case 'fully-approved': return in_array($slug, ['approved', 'fully-approved']);
                default: return true;
            }
        });

        $counts = [
            'all' => $allDocuments->count(),
            'waiting-approval-staff' => $allDocuments->where('status.slug', 'waiting-approval-staff')->count(),
            'waiting-approval-manager' => $allDocuments->where('status.slug', 'waiting-approval-manager')->count(),
            'waiting-approval-gm' => $allDocuments->where('status.slug', 'waiting-approval-gm')->count(),
            'waiting-revision' => $allDocuments->where('status.slug', 'waiting-revision')->count(),
            'fully-approved' => $allDocuments->filter(fn($p) => in_array(optional($p->status)->slug ?? '', ['approved', 'fully-approved']))->count(),
        ];

        return view('user.international_trip_realization.index', compact('internationalTripRealizations', 'statusFilter', 'counts'));
    }

    public function create()
    {
        // Only show fully-approved International Trips that don't have a realization yet and already paid
        $fullyApprovedStatus = DocumentStatus::where('slug', 'fully-approved')->first();
        $availableTrips = InternationalTrip::where('user_id', Auth::user()->id)
            ->where('document_status_id', $fullyApprovedStatus->id ?? 0)
            ->where('is_paid', true)
            ->doesntHave('realization')
            ->with('costCenter')
            ->get();
        $costCenters = CostCenter::select('id', 'number', 'name')->get();

        return view('user.international_trip_realization.create', compact('availableTrips', 'costCenters'));
    }

    public function store(StoreInternationalTripRealizationRequest $request)
    {
        // Validate that the selected trip is fully approved and belongs to user
        $trip = InternationalTrip::findOrFail($request->input('international_trip_id'));
        $fullyApprovedStatus = DocumentStatus::where('slug', 'fully-approved')->first();

        if ($trip->user_id != Auth::user()->id) {
            abort(403);
        }

        if ($trip->document_status_id != ($fullyApprovedStatus->id ?? 0)) {
            return redirect()->back()->with('error', 'International Trip must be fully approved before creating realization.');
        }

        if ($trip->realization()->exists()) {
            return redirect()->back()->with('error', 'This International Trip already has a realization.');
        }

        $document = null;
        DB::transaction(function () use ($request, $trip, &$document) {
            $data = $request->validated();
            $data['number'] = InternationalTripRealization::generateNumber();
            $data['international_trip_id'] = $trip->id;
            $data['user_id'] = Auth::user()->id;
            $data['cost_center_id'] = $trip->cost_center_id;
            $data['document_status_id'] = DocumentStatus::where('slug', 'waiting-approval-staff')->first()->id;

            foreach (['transfer_evidence', 'other_document'] as $fileField) {
                if ($request->hasFile($fileField)) {
                    $file = $request->file($fileField);
                    $extension = $file->getClientOriginalExtension();
                    $filename = $fileField . '_' . $data['number'] . '.' . $extension;
                    $data[$fileField] = $file->storeAs('international_trip_realization', $filename);
                }
            }

            $document = InternationalTripRealization::create($data);
        });

        if ($document) {
            $this->notificationService->notifyDocumentSubmitted($document);
        }

        return redirect()->route('user.international-trip-realization.index')->with('success', 'International Trip Realization created successfully.');
    }

    public function show(InternationalTripRealization $internationalTripRealization)
    {
        // Check ownership via trip
        if ($internationalTripRealization->trip->user_id != Auth::user()->id) {
            abort(403);
        }

        $internationalTripRealization->load(['trip.user.department', 'trip.user.position', 'trip.costCenter', 'status', 'revisions.user.department', 'revisions.status', 'approvals.user.department', 'approvals.role', 'approvals.status']);

        $canEdit = !in_array($internationalTripRealization->document_status_id, [2]) && is_null($internationalTripRealization->revisions()->first());
        $pendingRevisions = $internationalTripRealization->revisions()->where('revision_status_id', 1)->get();
        $approvalChain = $this->approvalService->getApprovalChain($internationalTripRealization);

        return view('user.international_trip_realization.show', compact('internationalTripRealization', 'canEdit', 'pendingRevisions', 'approvalChain'));
    }

    public function edit(InternationalTripRealization $internationalTripRealization)
    {
        if ($internationalTripRealization->trip->user_id != Auth::user()->id) {
            abort(403);
        }

        if ($internationalTripRealization->revisions()->exists() || $internationalTripRealization->document_status_id == 2) {
            return redirect()->route('user.international-trip-realization.show', $internationalTripRealization)->with('error', 'Cannot edit document while in revision status.');
        }

        if ($internationalTripRealization->approvals()->exists()) {
            return redirect()->route('user.international-trip-realization.show', $internationalTripRealization)->with('error', 'Cannot edit document while on approval process.');
        }

        return view('user.international_trip_realization.edit', compact('internationalTripRealization'));
    }

    public function update(UpdateInternationalTripRealizationRequest $request, InternationalTripRealization $internationalTripRealization)
    {
        if ($internationalTripRealization->trip->user_id != Auth::user()->id) {
            abort(403);
        }

        if ($internationalTripRealization->revisions()->exists() || $internationalTripRealization->document_status_id == 2) {
            return redirect()->route('user.international-trip-realization.show', $internationalTripRealization)->with('error', 'Cannot edit document while in revision status.');
        }

        if ($internationalTripRealization->approvals()->exists()) {
            return redirect()->route('user.international-trip-realization.show', $internationalTripRealization)->with('error', 'Cannot edit document while on approval process.');
        }

        DB::transaction(function () use ($request, $internationalTripRealization) {
            $data = $request->validated();
            $data['number'] = $internationalTripRealization->number;
            $data['document_status_id'] = DocumentStatus::where('slug', 'waiting-approval-staff')->first()->id;

            $currentEditCount = $internationalTripRealization->edit_count ?? 0;
            $newEditCount = $currentEditCount + 1;

            foreach (['transfer_evidence', 'other_document'] as $fileField) {
                if ($request->hasFile($fileField)) {
                    $file = $request->file($fileField);
                    $extension = $file->getClientOriginalExtension();
                    $filename = $fileField . '_' . $internationalTripRealization->number . '_edited(' . $newEditCount . ').' . $extension;
                    $data[$fileField] = $file->storeAs('international_trip_realization', $filename);
                }
            }

            $data['edit_count'] = $newEditCount;
            $internationalTripRealization->update($data);
        });

        return redirect()->route('user.international-trip-realization.index')->with('success', 'International Trip Realization updated successfully.');
    }

    public function submitRevision(Request $request, InternationalTripRealization $internationalTripRealization, Revision $revision)
    {
        if ($internationalTripRealization->trip->user_id != Auth::user()->id) {
            abort(403);
        }

        if ($revision->revisable_id != $internationalTripRealization->id || $revision->revisable_type != InternationalTripRealization::class) {
            abort(403);
        }

        $validated = $request->validate([
            'transfer_evidence' => 'nullable|file|mimes:pdf,xls,xlsx,jpg,jpeg,png|max:5120',
            'other_document' => 'nullable|file|mimes:pdf,xls,xlsx,jpg,jpeg,png|max:15360',
        ]);

        DB::transaction(function () use ($internationalTripRealization, $revision, $validated) {
            foreach (['transfer_evidence', 'other_document'] as $fileField) {
                if (isset($validated[$fileField])) {
                    $file = $validated[$fileField];
                    $extension = $file->getClientOriginalExtension();
                    $filename = $fileField . '_' . $internationalTripRealization->number . '_revised(' . $revision->revision_times . ').' . $extension;
                    $internationalTripRealization->update([$fileField => $file->storeAs('international_trip_realization', $filename)]);
                }
            }

            $revision->update(['revision_status_id' => 2, 'revision_at' => now()]);

            $pendingRevisions = $internationalTripRealization->revisions()->where('revision_status_id', '!=', 2)->count();
            if ($pendingRevisions === 0) {
                $waitingApprovalStaffStatus = DocumentStatus::where('slug', 'waiting-approval-staff')->first();
                if ($waitingApprovalStaffStatus) {
                    $internationalTripRealization->update(['document_status_id' => $waitingApprovalStaffStatus->id]);
                }
            }
        });

        $this->notificationService->notifyRevisionSubmitted($internationalTripRealization);

        return redirect()->route('user.international-trip-realization.show', $internationalTripRealization)->with('success', 'Revision submitted successfully.');
    }

    /**
     * Bulk submit hardfiles by user
     */
    public function bulkSubmitHardfile(Request $request)
    {
        $validated = $request->validate([
            'document_ids' => 'required|array',
            'document_ids.*' => 'exists:international_trip_realization,id',
        ]);

        $successCount = 0;
        $errors = [];

        foreach ($validated['document_ids'] as $docId) {
            try {
                $internationalTripRealization = InternationalTripRealization::findOrFail($docId);

                if ($internationalTripRealization->user_id != Auth::user()->id) {
                    throw new \Exception('Unauthorized action.');
                }

                if ($internationalTripRealization->is_hardfile_submitted) {
                    throw new \Exception('Hardfile has already been submitted for this document.');
                }

                $staffRole = \App\Models\ApprovalRole::where('sequence', 1)->first();
                if (!$staffRole) {
                    throw new \Exception('Approval role not found.');
                }

                $staffApproval = $internationalTripRealization->approvals()
                    ->where('approval_role_id', $staffRole->id)
                    ->where('approval_status_id', 1)
                    ->exists();

                if (!$staffApproval) {
                    throw new \Exception('Cannot submit hardfile: document has not been approved by Accounting Staff yet.');
                }

                $internationalTripRealization->update([
                    'is_hardfile_submitted' => true,
                    'hardfile_submitted_at' => now(),
                ]);

                $this->notificationService->notifyHardfileSubmitted($internationalTripRealization, Auth::user());
                $successCount++;
            } catch (\Exception $e) {
                $errors[] = "ID {$docId}: " . $e->getMessage();
            }
        }

        if (count($errors) > 0) {
            $errorMessage = "Submitted hardfile for {$successCount} documents. Errors on " . count($errors) . " documents: " . implode(', ', $errors);
            return redirect()->back()->with('error', $errorMessage);
        }

        return redirect()->back()->with('success', "Successfully submitted hardfile for {$successCount} documents.");
    }

    /**
     * Submit hardfile by user
     */
    public function submitHardfile(Request $request, InternationalTripRealization $internationalTripRealization)
    {
        // Check if user owns this document
        if ($internationalTripRealization->trip->user_id != Auth::user()->id) {
            abort(403);
        }

        try {
            if ($internationalTripRealization->is_hardfile_submitted) {
                throw new \Exception('Hardfile has already been submitted for this document.');
            }

            // Check if document has been approved by accounting staff (sequence 1)
            $staffRole = \App\Models\ApprovalRole::where('sequence', 1)->first();
            if (!$staffRole) {
                throw new \Exception('Approval role not found.');
            }

            $staffApproval = $internationalTripRealization->approvals()
                ->where('approval_role_id', $staffRole->id)
                ->where('approval_status_id', 1) // 1 = approved
                ->exists();

            if (!$staffApproval) {
                throw new \Exception('Cannot submit hardfile: document has not been approved by Accounting Staff yet.');
            }

            $internationalTripRealization->update([
                'is_hardfile_submitted' => true,
                'hardfile_submitted_at' => now(),
            ]);

            // Notify accounting staff
            $this->notificationService->notifyHardfileSubmitted($internationalTripRealization, Auth::user());

            return redirect()->route('user.international-trip-realization.show', $internationalTripRealization)
                ->with('success', 'Hardfile submitted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('user.international-trip-realization.show', $internationalTripRealization)
                ->with('error', $e->getMessage());
        }
    }

    public function destroy(string $id)
    {
        //
    }
}
