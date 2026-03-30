<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInternationalTripRequest;
use App\Http\Requests\UpdateInternationalTripRequest;
use App\Models\InternationalTrip;
use App\Models\CostCenter;
use App\Models\Revision;
use App\Models\DocumentStatus;
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
        $allDocuments = InternationalTrip::with(['revisions', 'approvals', 'user', 'status', 'costCenter'])
            ->where('user_id', Auth::user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $statusFilter = $request->query('status', 'all');

        $internationalTrips = $allDocuments->filter(function ($doc) use ($statusFilter) {
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

        return view('user.international_trip.index', compact('internationalTrips', 'statusFilter', 'counts'));
    }

    public function create()
    {
        $costCenters = CostCenter::select('id', 'number', 'name')->get();
        return view('user.international_trip.create', compact('costCenters'));
    }

    public function store(StoreInternationalTripRequest $request)
    {
        $document = null;
        DB::transaction(function () use ($request, &$document) {
            $data = $request->validated();
            $data['number'] = InternationalTrip::generateNumber();
            $data['user_id'] = Auth::user()->id;
            $data['cost_center_id'] = $request->input('cost_center_id');
            $data['document_number'] = $request->input('document_number');
            $data['document_status_id'] = DocumentStatus::where('slug', 'waiting-approval-staff')->first()->id;

            foreach (['itar_form', 'internal_memo', 'summary_bussiness_trip', 'overseas_allowance_form', 'bussiness_trip_allowance', 'rate', 'budget_plan'] as $fileField) {
                if ($request->hasFile($fileField)) {
                    $file = $request->file($fileField);
                    $extension = $file->getClientOriginalExtension();
                    $filename = $fileField . '_' . $data['number'] . '.' . $extension;
                    $data[$fileField] = $file->storeAs('international_trip', $filename);
                }
            }

            $document = InternationalTrip::create($data);
        });

        if ($document) {
            $this->notificationService->notifyDocumentSubmitted($document);
        }

        return redirect()->route('user.international-trip.index')->with('success', 'International Trip created successfully.');
    }

    public function show(InternationalTrip $internationalTrip)
    {
        if ($internationalTrip->user_id !== Auth::user()->id) {
            abort(403);
        }

        $internationalTrip->load(['user.department', 'user.position', 'costCenter', 'status', 'revisions.user.department', 'revisions.status', 'approvals.user.department', 'approvals.role', 'approvals.status']);

        $canEdit = !in_array($internationalTrip->document_status_id, [2]) && is_null($internationalTrip->revisions()->first());
        $pendingRevisions = $internationalTrip->revisions()->where('revision_status_id', 1)->get();
        $approvalChain = $this->approvalService->getApprovalChain($internationalTrip);

        return view('user.international_trip.show', compact('internationalTrip', 'canEdit', 'pendingRevisions', 'approvalChain'));
    }

    public function edit(InternationalTrip $internationalTrip)
    {
        if ($internationalTrip->user_id !== Auth::user()->id) {
            abort(403);
        }

        if ($internationalTrip->revisions()->exists() || $internationalTrip->document_status_id == 2) {
            return redirect()->route('user.international-trip.show', $internationalTrip)->with('error', 'Cannot edit document while in revision status.');
        }

        if ($internationalTrip->approvals()->exists()) {
            return redirect()->route('user.international-trip.show', $internationalTrip)->with('error', 'Cannot edit document while on approval process.');
        }

        $costCenters = CostCenter::select('id', 'number', 'name')->get();
        return view('user.international_trip.edit', compact('internationalTrip', 'costCenters'));
    }

    public function update(UpdateInternationalTripRequest $request, InternationalTrip $internationalTrip)
    {
        if ($internationalTrip->user_id !== Auth::user()->id) {
            abort(403);
        }

        if ($internationalTrip->revisions()->exists() || $internationalTrip->document_status_id == 2) {
            return redirect()->route('user.international-trip.show', $internationalTrip)->with('error', 'Cannot edit document while in revision status.');
        }

        if ($internationalTrip->approvals()->exists()) {
            return redirect()->route('user.international-trip.show', $internationalTrip)->with('error', 'Cannot edit document while on approval process.');
        }

        DB::transaction(function () use ($request, $internationalTrip) {
            $data = $request->validated();
            $data['number'] = $internationalTrip->number;
            $data['user_id'] = Auth::user()->id;
            $data['cost_center_id'] = $request->input('cost_center_id');
            $data['document_number'] = $request->input('document_number');
            $data['document_status_id'] = DocumentStatus::where('slug', 'waiting-approval-staff')->first()->id;

            $currentEditCount = $internationalTrip->edit_count ?? 0;
            $newEditCount = $currentEditCount + 1;

            foreach (['itar_form', 'internal_memo', 'summary_bussiness_trip', 'overseas_allowance_form', 'bussiness_trip_allowance', 'rate', 'budget_plan'] as $fileField) {
                if ($request->hasFile($fileField)) {
                    $file = $request->file($fileField);
                    $extension = $file->getClientOriginalExtension();
                    $filename = $fileField . '_' . $internationalTrip->number . '_edited(' . $newEditCount . ').' . $extension;
                    $data[$fileField] = $file->storeAs('international_trip', $filename);
                }
            }

            $data['edit_count'] = $newEditCount;
            $internationalTrip->update($data);
        });

        return redirect()->route('user.international-trip.index')->with('success', 'International Trip updated successfully.');
    }

    public function submitRevision(Request $request, InternationalTrip $internationalTrip, Revision $revision)
    {
        if ($internationalTrip->user_id !== Auth::user()->id) {
            abort(403);
        }

        if ($revision->revisable_id !== $internationalTrip->id || $revision->revisable_type !== InternationalTrip::class) {
            abort(403);
        }

        $validated = $request->validate([
            'itar_form' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx',
            'document_number' => 'nullable|string',
            'internal_memo' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx',
            'summary_bussiness_trip' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx',
            'overseas_allowance_form' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx',
            'bussiness_trip_allowance' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx',
            'rate' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx',
            'budget_plan' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx',
        ]);

        DB::transaction(function () use ($internationalTrip, $revision, $validated) {
            foreach (['itar_form', 'internal_memo', 'summary_bussiness_trip', 'overseas_allowance_form', 'bussiness_trip_allowance', 'rate', 'budget_plan'] as $fileField) {
                if (isset($validated[$fileField])) {
                    $file = $validated[$fileField];
                    $extension = $file->getClientOriginalExtension();
                    $filename = $fileField . '_' . $internationalTrip->number . '_revised(' . $revision->revision_times . ').' . $extension;
                    $internationalTrip->update([$fileField => $file->storeAs('international_trip', $filename)]);
                }
            }

            if (isset($validated['document_number'])) {
                $internationalTrip->update(['document_number' => $validated['document_number']]);
            }

            $revision->update(['revision_status_id' => 2, 'revision_at' => now()]);

            $pendingRevisions = $internationalTrip->revisions()->where('revision_status_id', '!=', 2)->count();
            if ($pendingRevisions === 0) {
                $waitingApprovalStaffStatus = DocumentStatus::where('slug', 'waiting-approval-staff')->first();
                if ($waitingApprovalStaffStatus) {
                    $internationalTrip->update(['document_status_id' => $waitingApprovalStaffStatus->id]);
                }
            }
        });

        $this->notificationService->notifyRevisionSubmitted($internationalTrip);

        return redirect()->route('user.international-trip.show', $internationalTrip)->with('success', 'Revision submitted successfully.');
    }

    public function destroy(string $id)
    {
        //
    }
}
