<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCashAdvanceDrawRequest;
use App\Http\Requests\UpdateCashAdvanceDrawRequest;
use App\Models\CashAdvanceDraw;
use App\Models\CostCenter;
use App\Models\Revision;
use App\Models\DocumentStatus;
use App\Services\ApprovalService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CashAdvanceDrawController extends Controller
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
        $allDocuments = CashAdvanceDraw::with(['revisions', 'approvals', 'user', 'status', 'costCenter', 'realization'])
            ->where('user_id', Auth::user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $statusFilter = $request->query('status', 'all');

        $cashAdvanceDraws = $allDocuments->filter(function ($doc) use ($statusFilter) {
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

        return view('user.cash_advance_draw.index', compact('cashAdvanceDraws', 'statusFilter', 'counts'));
    }

    public function create()
    {
        $costCenters = CostCenter::select('id', 'number', 'name')->get();
        return view('user.cash_advance_draw.create', compact('costCenters'));
    }

    public function store(StoreCashAdvanceDrawRequest $request)
    {
        $document = null;
        DB::transaction(function () use ($request, &$document) {
            $data = $request->validated();
            $data['number'] = CashAdvanceDraw::generateNumber();
            $data['user_id'] = Auth::user()->id;
            $data['cost_center_id'] = $request->input('cost_center_id');
            $data['document_number'] = $request->input('document_number');
            $data['document_status_id'] = DocumentStatus::where('slug', 'waiting-approval-staff')->first()->id;

            foreach (['car_form', 'proposal_or_monitor_budget', 'budget_plan'] as $fileField) {
                if ($request->hasFile($fileField)) {
                    $file = $request->file($fileField);
                    $extension = $file->getClientOriginalExtension();
                    $filename = $fileField . '_' . $data['number'] . '.' . $extension;
                    $data[$fileField] = $file->storeAs('cash_advance_draw', $filename);
                }
            }

            $document = CashAdvanceDraw::create($data);
        });

        if ($document) {
            $this->notificationService->notifyDocumentSubmitted($document);
        }

        return redirect()->route('user.cash-advance-draw.index')->with('success', 'Cash Advance Draw created successfully.');
    }

    public function show(CashAdvanceDraw $cashAdvanceDraw)
    {
        if ($cashAdvanceDraw->user_id !== Auth::user()->id) {
            abort(403);
        }

        $cashAdvanceDraw->load(['user.department', 'user.position', 'costCenter', 'status', 'realization', 'revisions.user.department', 'revisions.status', 'approvals.user.department', 'approvals.role', 'approvals.status']);

        $canEdit = !in_array($cashAdvanceDraw->document_status_id, [2]) && is_null($cashAdvanceDraw->revisions()->first());
        $pendingRevisions = $cashAdvanceDraw->revisions()->where('revision_status_id', 1)->get();
        $approvalChain = $this->approvalService->getApprovalChain($cashAdvanceDraw);

        return view('user.cash_advance_draw.show', compact('cashAdvanceDraw', 'canEdit', 'pendingRevisions', 'approvalChain'));
    }

    public function edit(CashAdvanceDraw $cashAdvanceDraw)
    {
        if ($cashAdvanceDraw->user_id !== Auth::user()->id) {
            abort(403);
        }

        if ($cashAdvanceDraw->revisions()->exists() || $cashAdvanceDraw->document_status_id == 2) {
            return redirect()->route('user.cash-advance-draw.show', $cashAdvanceDraw)->with('error', 'Cannot edit document while in revision status.');
        }

        if ($cashAdvanceDraw->approvals()->exists()) {
            return redirect()->route('user.cash-advance-draw.show', $cashAdvanceDraw)->with('error', 'Cannot edit document while on approval process.');
        }

        $costCenters = CostCenter::select('id', 'number', 'name')->get();
        return view('user.cash_advance_draw.edit', compact('cashAdvanceDraw', 'costCenters'));
    }

    public function update(UpdateCashAdvanceDrawRequest $request, CashAdvanceDraw $cashAdvanceDraw)
    {
        if ($cashAdvanceDraw->user_id !== Auth::user()->id) {
            abort(403);
        }

        if ($cashAdvanceDraw->revisions()->exists() || $cashAdvanceDraw->document_status_id == 2) {
            return redirect()->route('user.cash-advance-draw.show', $cashAdvanceDraw)->with('error', 'Cannot edit document while in revision status.');
        }

        if ($cashAdvanceDraw->approvals()->exists()) {
            return redirect()->route('user.cash-advance-draw.show', $cashAdvanceDraw)->with('error', 'Cannot edit document while on approval process.');
        }

        DB::transaction(function () use ($request, $cashAdvanceDraw) {
            $data = $request->validated();
            $data['number'] = $cashAdvanceDraw->number;
            $data['user_id'] = Auth::user()->id;
            $data['cost_center_id'] = $request->input('cost_center_id');
            $data['document_number'] = $request->input('document_number');
            $data['document_status_id'] = DocumentStatus::where('slug', 'waiting-approval-staff')->first()->id;

            $currentEditCount = $cashAdvanceDraw->edit_count ?? 0;
            $newEditCount = $currentEditCount + 1;

            foreach (['car_form', 'proposal_or_monitor_budget', 'budget_plan'] as $fileField) {
                if ($request->hasFile($fileField)) {
                    $file = $request->file($fileField);
                    $extension = $file->getClientOriginalExtension();
                    $filename = $fileField . '_' . $cashAdvanceDraw->number . '_edited(' . $newEditCount . ').' . $extension;
                    $data[$fileField] = $file->storeAs('cash_advance_draw', $filename);
                }
            }

            $data['edit_count'] = $newEditCount;
            $cashAdvanceDraw->update($data);
        });

        return redirect()->route('user.cash-advance-draw.index')->with('success', 'Cash Advance Draw updated successfully.');
    }

    public function submitRevision(Request $request, CashAdvanceDraw $cashAdvanceDraw, Revision $revision)
    {
        if ($cashAdvanceDraw->user_id !== Auth::user()->id) {
            abort(403);
        }

        if ($revision->revisable_id !== $cashAdvanceDraw->id || $revision->revisable_type !== CashAdvanceDraw::class) {
            abort(403);
        }

        $validated = $request->validate([
            'car_form' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx',
            'document_number' => 'nullable|string',
            'proposal_or_monitor_budget' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx',
            'budget_plan' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx',
        ]);

        DB::transaction(function () use ($cashAdvanceDraw, $revision, $validated) {
            foreach (['car_form', 'proposal_or_monitor_budget', 'budget_plan'] as $fileField) {
                if (isset($validated[$fileField])) {
                    $file = $validated[$fileField];
                    $extension = $file->getClientOriginalExtension();
                    $filename = $fileField . '_' . $cashAdvanceDraw->number . '_revised(' . $revision->revision_times . ').' . $extension;
                    $cashAdvanceDraw->update([$fileField => $file->storeAs('cash_advance_draw', $filename)]);
                }
            }

            if (isset($validated['document_number'])) {
                $cashAdvanceDraw->update(['document_number' => $validated['document_number']]);
            }

            $revision->update(['revision_status_id' => 2, 'revision_at' => now()]);

            $pendingRevisions = $cashAdvanceDraw->revisions()->where('revision_status_id', '!=', 2)->count();
            if ($pendingRevisions === 0) {
                $waitingApprovalStaffStatus = DocumentStatus::where('slug', 'waiting-approval-staff')->first();
                if ($waitingApprovalStaffStatus) {
                    $cashAdvanceDraw->update(['document_status_id' => $waitingApprovalStaffStatus->id]);
                }
            }
        });

        $this->notificationService->notifyRevisionSubmitted($cashAdvanceDraw);

        return redirect()->route('user.cash-advance-draw.show', $cashAdvanceDraw)->with('success', 'Revision submitted successfully.');
    }

    public function destroy(string $id)
    {
        //
    }
}
