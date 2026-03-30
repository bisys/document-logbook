<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePettyCashRequest;
use App\Http\Requests\UpdatePettyCashRequest;
use App\Models\PettyCash;
use App\Models\CostCenter;
use App\Models\Revision;
use App\Models\DocumentStatus;
use App\Services\ApprovalService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PettyCashController extends Controller
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
        $allDocuments = PettyCash::with(['revisions', 'approvals', 'user', 'status', 'costCenter'])
            ->where('user_id', Auth::user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $statusFilter = $request->query('status', 'all');

        $pettyCashes = $allDocuments->filter(function ($doc) use ($statusFilter) {
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

        return view('user.petty_cash.index', compact('pettyCashes', 'statusFilter', 'counts'));
    }

    public function create()
    {
        $costCenters = CostCenter::select('id', 'number', 'name')->get();
        return view('user.petty_cash.create', compact('costCenters'));
    }

    public function store(StorePettyCashRequest $request)
    {
        $document = null;
        DB::transaction(function () use ($request, &$document) {
            $data = $request->validated();
            $data['number'] = PettyCash::generateNumber();
            $data['user_id'] = Auth::user()->id;
            $data['cost_center_id'] = $request->input('cost_center_id');
            $data['document_number'] = $request->input('document_number');
            $data['document_status_id'] = DocumentStatus::where('slug', 'waiting-approval-staff')->first()->id;

            foreach (['pcr_form', 'original_invoice', 'copy_invoice', 'internal_memo_entertain', 'entertain_realization_form', 'minutes_of_meeting', 'nominative_summary', 'cic_form', 'budget_plan'] as $fileField) {
                if ($request->hasFile($fileField)) {
                    $file = $request->file($fileField);
                    $extension = $file->getClientOriginalExtension();
                    $filename = $fileField . '_' . $data['number'] . '.' . $extension;
                    $data[$fileField] = $file->storeAs('petty_cash', $filename);
                }
            }

            $document = PettyCash::create($data);
        });

        if ($document) {
            $this->notificationService->notifyDocumentSubmitted($document);
        }

        return redirect()->route('user.petty-cash.index')->with('success', 'Petty Cash created successfully.');
    }

    public function show(PettyCash $pettyCash)
    {
        if ($pettyCash->user_id !== Auth::user()->id) {
            abort(403);
        }

        $pettyCash->load(['user.department', 'user.position', 'costCenter', 'status', 'revisions.user.department', 'revisions.status', 'approvals.user.department', 'approvals.role', 'approvals.status']);

        $canEdit = !in_array($pettyCash->document_status_id, [2]) && is_null($pettyCash->revisions()->first());

        $pendingRevisions = $pettyCash->revisions()->where('revision_status_id', 1)->get();

        $approvalChain = $this->approvalService->getApprovalChain($pettyCash);

        return view('user.petty_cash.show', compact('pettyCash', 'canEdit', 'pendingRevisions', 'approvalChain'));
    }

    public function edit(PettyCash $pettyCash)
    {
        if ($pettyCash->user_id !== Auth::user()->id) {
            abort(403);
        }

        if ($pettyCash->revisions()->exists() || $pettyCash->document_status_id == 2) {
            return redirect()->route('user.petty-cash.show', $pettyCash)->with('error', 'Cannot edit document while in revision status.');
        }

        if ($pettyCash->approvals()->exists()) {
            return redirect()->route('user.petty-cash.show', $pettyCash)->with('error', 'Cannot edit document while on approval process.');
        }

        $costCenters = CostCenter::select('id', 'number', 'name')->get();
        return view('user.petty_cash.edit', compact('pettyCash', 'costCenters'));
    }

    public function update(UpdatePettyCashRequest $request, PettyCash $pettyCash)
    {
        if ($pettyCash->user_id !== Auth::user()->id) {
            abort(403);
        }

        if ($pettyCash->revisions()->exists() || $pettyCash->document_status_id == 2) {
            return redirect()->route('user.petty-cash.show', $pettyCash)->with('error', 'Cannot edit document while in revision status.');
        }

        if ($pettyCash->approvals()->exists()) {
            return redirect()->route('user.petty-cash.show', $pettyCash)->with('error', 'Cannot edit document while on approval process.');
        }

        DB::transaction(function () use ($request, $pettyCash) {
            $data = $request->validated();
            $data['number'] = $pettyCash->number;
            $data['user_id'] = Auth::user()->id;
            $data['cost_center_id'] = $request->input('cost_center_id');
            $data['document_number'] = $request->input('document_number');
            $data['document_status_id'] = DocumentStatus::where('slug', 'waiting-approval-staff')->first()->id;

            $currentEditCount = $pettyCash->edit_count ?? 0;
            $newEditCount = $currentEditCount + 1;

            foreach (['pcr_form', 'original_invoice', 'copy_invoice', 'internal_memo_entertain', 'entertain_realization_form', 'minutes_of_meeting', 'nominative_summary', 'cic_form', 'budget_plan'] as $fileField) {
                if ($request->hasFile($fileField)) {
                    $file = $request->file($fileField);
                    $extension = $file->getClientOriginalExtension();
                    $filename = $fileField . '_' . $pettyCash->number . '_edited(' . $newEditCount . ').' . $extension;
                    $data[$fileField] = $file->storeAs('petty_cash', $filename);
                }
            }

            $data['edit_count'] = $newEditCount;
            $pettyCash->update($data);
        });

        return redirect()->route('user.petty-cash.index')->with('success', 'Petty Cash updated successfully.');
    }

    public function submitRevision(Request $request, PettyCash $pettyCash, Revision $revision)
    {
        if ($pettyCash->user_id !== Auth::user()->id) {
            abort(403);
        }

        if ($revision->revisable_id !== $pettyCash->id || $revision->revisable_type !== PettyCash::class) {
            abort(403);
        }

        $validated = $request->validate([
            'pcr_form' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx',
            'document_number' => 'nullable|string',
            'original_invoice' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx',
            'copy_invoice' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx',
            'internal_memo_entertain' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx',
            'entertain_realization_form' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx',
            'minutes_of_meeting' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx',
            'nominative_summary' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx',
            'cic_form' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx',
            'budget_plan' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx',
        ]);

        DB::transaction(function () use ($pettyCash, $revision, $validated) {
            foreach (['pcr_form', 'original_invoice', 'copy_invoice', 'internal_memo_entertain', 'entertain_realization_form', 'minutes_of_meeting', 'nominative_summary', 'cic_form', 'budget_plan'] as $fileField) {
                if (isset($validated[$fileField])) {
                    $file = $validated[$fileField];
                    $extension = $file->getClientOriginalExtension();
                    $filename = $fileField . '_' . $pettyCash->number . '_revised(' . $revision->revision_times . ').' . $extension;
                    $pettyCash->update([$fileField => $file->storeAs('petty_cash', $filename)]);
                }
            }

            if (isset($validated['document_number'])) {
                $pettyCash->update(['document_number' => $validated['document_number']]);
            }

            $revision->update(['revision_status_id' => 2, 'revision_at' => now()]);

            $pendingRevisions = $pettyCash->revisions()->where('revision_status_id', '!=', 2)->count();
            if ($pendingRevisions === 0) {
                $waitingApprovalStaffStatus = DocumentStatus::where('slug', 'waiting-approval-staff')->first();
                if ($waitingApprovalStaffStatus) {
                    $pettyCash->update(['document_status_id' => $waitingApprovalStaffStatus->id]);
                }
            }
        });

        $this->notificationService->notifyRevisionSubmitted($pettyCash);

        return redirect()->route('user.petty-cash.show', $pettyCash)->with('success', 'Revision submitted successfully.');
    }

    public function destroy(string $id)
    {
        //
    }
}
