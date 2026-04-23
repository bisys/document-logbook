<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCashAdvanceRealizationRequest;
use App\Http\Requests\UpdateCashAdvanceRealizationRequest;
use App\Models\CashAdvanceRealization;
use App\Models\CashAdvanceDraw;
use App\Models\Revision;
use App\Models\DocumentStatus;
use App\Models\CostCenter;
use App\Services\ApprovalService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CashAdvanceRealizationController extends Controller
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
        $allDocuments = CashAdvanceRealization::with(['revisions', 'approvals', 'draw.user', 'draw.costCenter', 'status'])
            ->whereHas('draw', function ($q) {
                $q->where('user_id', Auth::user()->id);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $statusFilter = $request->query('status', 'all');

        $cashAdvanceRealizations = $allDocuments->filter(function ($doc) use ($statusFilter) {
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

        return view('user.cash_advance_realization.index', compact('cashAdvanceRealizations', 'statusFilter', 'counts'));
    }

    public function create()
    {
        // Only show fully-approved Cash Advance Draws that don't have a realization yet
        $fullyApprovedStatus = DocumentStatus::where('slug', 'fully-approved')->first();
        $availableDraws = CashAdvanceDraw::where('user_id', Auth::user()->id)
            ->where('document_status_id', $fullyApprovedStatus->id ?? 0)
            ->doesntHave('realization')
            ->with('costCenter')
            ->get();
        $costCenters = CostCenter::select('id', 'number', 'name')->get();

        return view('user.cash_advance_realization.create', compact('availableDraws', 'costCenters'));
    }

    public function store(StoreCashAdvanceRealizationRequest $request)
    {
        // Validate that the selected draw is fully approved and belongs to user
        $draw = CashAdvanceDraw::findOrFail($request->input('cash_advance_draw_id'));
        $fullyApprovedStatus = DocumentStatus::where('slug', 'fully-approved')->first();

        if ($draw->user_id !== Auth::user()->id) {
            abort(403);
        }

        if ($draw->document_status_id !== ($fullyApprovedStatus->id ?? 0)) {
            return redirect()->back()->with('error', 'Cash Advance Draw must be fully approved before creating realization.');
        }

        if ($draw->realization()->exists()) {
            return redirect()->back()->with('error', 'This Cash Advance Draw already has a realization.');
        }

        $document = null;
        DB::transaction(function () use ($request, $draw, &$document) {
            $data = $request->validated();
            $data['number'] = CashAdvanceRealization::generateNumber();
            $data['cash_advance_draw_id'] = $draw->id;
            $data['user_id'] = Auth::user()->id;
            $data['cost_center_id'] = $draw->cost_center_id;
            $data['document_status_id'] = DocumentStatus::where('slug', 'waiting-approval-staff')->first()->id;

            foreach (['car_form', 'original_invoice', 'copy_invoice', 'internal_memo_entertain', 'entertain_realization_form', 'minutes_of_meeting', 'nominative_summary', 'cic_form', 'transfer_evidence', 'other_document'] as $fileField) {
                if ($request->hasFile($fileField)) {
                    $file = $request->file($fileField);
                    $extension = $file->getClientOriginalExtension();
                    $filename = $fileField . '_' . $data['number'] . '.' . $extension;
                    $data[$fileField] = $file->storeAs('cash_advance_realization', $filename);
                }
            }

            $document = CashAdvanceRealization::create($data);
        });

        if ($document) {
            $this->notificationService->notifyDocumentSubmitted($document);
        }

        return redirect()->route('user.cash-advance-realization.index')->with('success', 'Cash Advance Realization created successfully.');
    }

    public function show(CashAdvanceRealization $cashAdvanceRealization)
    {
        // Check ownership via draw
        if ($cashAdvanceRealization->draw->user_id !== Auth::user()->id) {
            abort(403);
        }

        $cashAdvanceRealization->load(['draw.user.department', 'draw.user.position', 'draw.costCenter', 'status', 'revisions.user.department', 'revisions.status', 'approvals.user.department', 'approvals.role', 'approvals.status']);

        $canEdit = !in_array($cashAdvanceRealization->document_status_id, [2]) && is_null($cashAdvanceRealization->revisions()->first());
        $pendingRevisions = $cashAdvanceRealization->revisions()->where('revision_status_id', 1)->get();
        $approvalChain = $this->approvalService->getApprovalChain($cashAdvanceRealization);

        return view('user.cash_advance_realization.show', compact('cashAdvanceRealization', 'canEdit', 'pendingRevisions', 'approvalChain'));
    }

    public function edit(CashAdvanceRealization $cashAdvanceRealization)
    {
        if ($cashAdvanceRealization->draw->user_id !== Auth::user()->id) {
            abort(403);
        }

        if ($cashAdvanceRealization->revisions()->exists() || $cashAdvanceRealization->document_status_id == 2) {
            return redirect()->route('user.cash-advance-realization.show', $cashAdvanceRealization)->with('error', 'Cannot edit document while in revision status.');
        }

        if ($cashAdvanceRealization->approvals()->exists()) {
            return redirect()->route('user.cash-advance-realization.show', $cashAdvanceRealization)->with('error', 'Cannot edit document while on approval process.');
        }

        return view('user.cash_advance_realization.edit', compact('cashAdvanceRealization'));
    }

    public function update(UpdateCashAdvanceRealizationRequest $request, CashAdvanceRealization $cashAdvanceRealization)
    {
        if ($cashAdvanceRealization->draw->user_id !== Auth::user()->id) {
            abort(403);
        }

        if ($cashAdvanceRealization->revisions()->exists() || $cashAdvanceRealization->document_status_id == 2) {
            return redirect()->route('user.cash-advance-realization.show', $cashAdvanceRealization)->with('error', 'Cannot edit document while in revision status.');
        }

        if ($cashAdvanceRealization->approvals()->exists()) {
            return redirect()->route('user.cash-advance-realization.show', $cashAdvanceRealization)->with('error', 'Cannot edit document while on approval process.');
        }

        DB::transaction(function () use ($request, $cashAdvanceRealization) {
            $data = $request->validated();
            $data['number'] = $cashAdvanceRealization->number;
            $data['document_status_id'] = DocumentStatus::where('slug', 'waiting-approval-staff')->first()->id;

            $currentEditCount = $cashAdvanceRealization->edit_count ?? 0;
            $newEditCount = $currentEditCount + 1;

            foreach (['car_form', 'original_invoice', 'copy_invoice', 'internal_memo_entertain', 'entertain_realization_form', 'minutes_of_meeting', 'nominative_summary', 'cic_form', 'transfer_evidence', 'other_document'] as $fileField) {
                if ($request->hasFile($fileField)) {
                    $file = $request->file($fileField);
                    $extension = $file->getClientOriginalExtension();
                    $filename = $fileField . '_' . $cashAdvanceRealization->number . '_edited(' . $newEditCount . ').' . $extension;
                    $data[$fileField] = $file->storeAs('cash_advance_realization', $filename);
                }
            }

            $data['edit_count'] = $newEditCount;
            $cashAdvanceRealization->update($data);
        });

        return redirect()->route('user.cash-advance-realization.index')->with('success', 'Cash Advance Realization updated successfully.');
    }

    public function submitRevision(Request $request, CashAdvanceRealization $cashAdvanceRealization, Revision $revision)
    {
        if ($cashAdvanceRealization->draw->user_id !== Auth::user()->id) {
            abort(403);
        }

        if ($revision->revisable_id !== $cashAdvanceRealization->id || $revision->revisable_type !== CashAdvanceRealization::class) {
            abort(403);
        }

        $validated = $request->validate([
            'car_form' => 'nullable|file|mimes:pdf,xls,xlsx,jpg,jpeg,png|max:500',
            'original_invoice' => 'nullable|file|mimes:pdf,xls,xlsx,jpg,jpeg,png|max:500',
            'copy_invoice' => 'nullable|file|mimes:pdf,xls,xlsx,jpg,jpeg,png|max:500',
            'internal_memo_entertain' => 'nullable|file|mimes:pdf,xls,xlsx,jpg,jpeg,png|max:500',
            'entertain_realization_form' => 'nullable|file|mimes:pdf,xls,xlsx,jpg,jpeg,png|max:500',
            'minutes_of_meeting' => 'nullable|file|mimes:pdf,xls,xlsx,jpg,jpeg,png|max:500',
            'nominative_summary' => 'nullable|file|mimes:pdf,xls,xlsx,jpg,jpeg,png|max:500',
            'cic_form' => 'nullable|file|mimes:pdf,xls,xlsx,jpg,jpeg,png|max:500',
            'transfer_evidence' => 'nullable|file|mimes:pdf,xls,xlsx,jpg,jpeg,png|max:500',
            'other_document' => 'nullable|file|mimes:pdf,xls,xlsx,jpg,jpeg,png|max:500',
        ]);

        DB::transaction(function () use ($cashAdvanceRealization, $revision, $validated) {
            foreach (['car_form', 'original_invoice', 'copy_invoice', 'internal_memo_entertain', 'entertain_realization_form', 'minutes_of_meeting', 'nominative_summary', 'cic_form', 'transfer_evidence', 'other_document'] as $fileField) {
                if (isset($validated[$fileField])) {
                    $file = $validated[$fileField];
                    $extension = $file->getClientOriginalExtension();
                    $filename = $fileField . '_' . $cashAdvanceRealization->number . '_revised(' . $revision->revision_times . ').' . $extension;
                    $cashAdvanceRealization->update([$fileField => $file->storeAs('cash_advance_realization', $filename)]);
                }
            }

            $revision->update(['revision_status_id' => 2, 'revision_at' => now()]);

            $pendingRevisions = $cashAdvanceRealization->revisions()->where('revision_status_id', '!=', 2)->count();
            if ($pendingRevisions === 0) {
                $waitingApprovalStaffStatus = DocumentStatus::where('slug', 'waiting-approval-staff')->first();
                if ($waitingApprovalStaffStatus) {
                    $cashAdvanceRealization->update(['document_status_id' => $waitingApprovalStaffStatus->id]);
                }
            }
        });

        $this->notificationService->notifyRevisionSubmitted($cashAdvanceRealization);

        return redirect()->route('user.cash-advance-realization.show', $cashAdvanceRealization)->with('success', 'Revision submitted successfully.');
    }

    public function destroy(string $id)
    {
        //
    }
}
