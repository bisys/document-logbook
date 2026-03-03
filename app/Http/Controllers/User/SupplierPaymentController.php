<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSupplierPaymentRequest;
use App\Http\Requests\UpdateSupplierPaymentRequest;
use App\Models\SupplierPayment;
use App\Models\CostCenter;
use App\Models\Revision;
use App\Models\RevisionStatus;
use App\Models\DocumentStatus;
use App\Services\ApprovalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SupplierPaymentController extends Controller
{
    protected $approvalService;

    public function __construct(ApprovalService $approvalService)
    {
        $this->approvalService = $approvalService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // fetch all documents for current user first
        $allPayments = SupplierPayment::with([
            'revisions',
            'approvals',
            'user',
            'status',
            'costCenter'
        ])
            ->where('user_id', Auth::user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $statusFilter = $request->query('status', 'all');

        $supplierPayments = $allPayments->filter(function ($payment) use ($statusFilter) {
            $slug = optional($payment->status)->slug ?? '';

            switch ($statusFilter) {
                case 'waiting-approval':
                    return $slug === 'waiting-approval';
                case 'waiting-revision':
                    return $slug === 'waiting-revision';
                case 'approved':
                    return in_array($slug, ['approved', 'fully-approved']);
                default:
                    return true;
            }
        });

        // counts for tabs
        $counts = [
            'all' => $allPayments->count(),
            'waiting-approval' => $allPayments->where('status.slug', 'waiting-approval')->count(),
            'waiting-revision' => $allPayments->where('status.slug', 'waiting-revision')->count(),
            'approved' => $allPayments->filter(function ($p) {
                $s = optional($p->status)->slug ?? '';
                return in_array($s, ['approved', 'fully-approved']);
            })->count(),
        ];

        return view('user.supplier_payment.index', compact('supplierPayments', 'statusFilter', 'counts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $costCenters = CostCenter::select('id', 'number', 'name')->get();

        return view('user.supplier_payment.create', compact('costCenters'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSupplierPaymentRequest $request)
    {
        DB::transaction(function () use ($request) {
            $data = $request->validated();
            $data['number'] = SupplierPayment::generateNumber();
            $data['user_id'] = Auth::user()->id;
            $data['cost_center_id'] = $request->input('cost_center_id');
            $data['document_number'] = $request->input('document_number');
            $data['document_status_id'] = 1; // Set initial status to 'Waiting Approval'

            // Handle file uploads with custom naming format
            foreach (
                [
                    'spr_form',
                    'original_invoice',
                    'copy_invoice',
                    'tax_invoice',
                    'budget_plan',
                    'agreement',
                    'internal_memo_entertain',
                    'entertain_realization_form',
                    'minutes_of_meeting',
                    'nominative_summary',
                    'calculation_summary',
                ] as $fileField
            ) {
                if ($request->hasFile($fileField)) {
                    $file = $request->file($fileField);
                    $extension = $file->getClientOriginalExtension();
                    $filename = $fileField . '_' . $data['number'] . '.' . $extension;
                    $data[$fileField] = $file->storeAs('supplier_payments', $filename);
                }
            }

            SupplierPayment::create($data);
        });

        return redirect()->route('user.supplier-payment.index')->with('success', 'Supplier Payment created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(SupplierPayment $supplierPayment)
    {
        // Check if user owns this supplier payment
        if ($supplierPayment->user_id !== Auth::user()->id) {
            abort(403);
        }

        $supplierPayment->load([
            'user.department',
            'user.position',
            'costCenter',
            'status',
            'revisions.user.department',
            'revisions.status',
            'approvals.user.department',
            'approvals.role',
            'approvals.status'
        ]);

        // Check if user can edit (not in waiting revision status and no revisions yet)
        $canEdit = !in_array($supplierPayment->document_status_id, [2]) &&  // 2 = waiting revision
            is_null($supplierPayment->revisions()->first()); // No revisions yet

        // Get pending revisions for user to revise
        $pendingRevisions = $supplierPayment->revisions()
            ->where('revision_status_id', 1) // 'revision requested' status
            ->get();

        // Get approval chain with role information
        $approvalChain = $this->approvalService->getApprovalChain($supplierPayment);

        return view('user.supplier_payment.show', compact(
            'supplierPayment',
            'canEdit',
            'pendingRevisions',
            'approvalChain'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SupplierPayment $supplierPayment)
    {
        // Check if user owns this supplier payment and document has revisions
        if ($supplierPayment->user_id !== Auth::user()->id) {
            abort(403);
        }

        // Check if document has revisions or in waiting revision status       
        if ($supplierPayment->revisions()->exists() || $supplierPayment->document_status_id == 2) { // 2 = waiting revision
            return redirect()->route('user.supplier-payment.show', $supplierPayment)
                ->with('error', 'Cannot edit document while in revision status. Please use the revision button.');
        }

        // Check if document has approval
        if ($supplierPayment->approvals()->exists()) { // Any approval exists
            return redirect()->route('user.supplier-payment.show', $supplierPayment)
                ->with('error', 'Cannot edit document while on approval process or approval is already completed.');
        }

        $costCenters = CostCenter::select('id', 'number', 'name')->get();

        return view('user.supplier_payment.edit', compact('supplierPayment', 'costCenters'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSupplierPaymentRequest $request, SupplierPayment $supplierPayment)
    {
        // Check if user owns this supplier payment
        if ($supplierPayment->user_id !== Auth::user()->id) {
            abort(403);
        }

        // Check if document has revisions or in waiting revision status       
        if ($supplierPayment->revisions()->exists() || $supplierPayment->document_status_id == 2) { // 2 = waiting revision
            return redirect()->route('user.supplier-payment.show', $supplierPayment)
                ->with('error', 'Cannot edit document while in revision status. Please use the revision button.');
        }

        // Check if document has approval
        if ($supplierPayment->approvals()->exists()) { // Any approval exists
            return redirect()->route('user.supplier-payment.show', $supplierPayment)
                ->with('error', 'Cannot edit document while on approval process.');
        }

        DB::transaction(function () use ($request, $supplierPayment) {
            $data = $request->validated();
            $data['number'] = $supplierPayment->number; // Keep the same number
            $data['user_id'] = Auth::user()->id;
            $data['cost_center_id'] = $request->input('cost_center_id');
            $data['document_number'] = $request->input('document_number');
            $data['document_status_id'] = 1; // Set initial status to 'Waiting Approval'

            // Increment edit count
            $currentEditCount = $supplierPayment->edit_count ?? 0;
            $newEditCount = $currentEditCount + 1;

            // Handle file uploads with custom naming format
            foreach (
                [
                    'spr_form',
                    'original_invoice',
                    'copy_invoice',
                    'tax_invoice',
                    'agreement',
                    'internal_memo_entertain',
                    'entertain_realization_form',
                    'minutes_of_meeting',
                    'nominative_summary',
                    'calculation_summary',
                    'budget_plan'
                ] as $fileField
            ) {
                if ($request->hasFile($fileField)) {
                    $file = $request->file($fileField);
                    $extension = $file->getClientOriginalExtension();

                    // If file is being edited, use edited naming format
                    if ($newEditCount > 1) {
                        // Format: fieldName_documentNumber_edited(editCount).extension
                        $filename = $fileField . '_' . $supplierPayment->number . '_edited(' . $newEditCount . ').' . $extension;
                    } else {
                        // First edit, use regular format
                        $filename = $fileField . '_' . $supplierPayment->number . '_edited(1).' . $extension;
                    }

                    $data[$fileField] = $file->storeAs('supplier_payments', $filename);
                }
            }

            // Update with new edit count and data
            $data['edit_count'] = $newEditCount;

            $supplierPayment->update($data);
        });

        return redirect()->route('user.supplier-payment.index')->with('success', 'Supplier Payment updated successfully.');
    }

    /**
     * Submit revision for a supplier payment
     */
    public function submitRevision(Request $request, SupplierPayment $supplierPayment, Revision $revision)
    {
        // Check if user owns this supplier payment
        if ($supplierPayment->user_id !== Auth::user()->id) {
            abort(403);
        }

        // Check if revision belongs to this supplier payment
        if ($revision->revisable_id !== $supplierPayment->id || $revision->revisable_type !== SupplierPayment::class) {
            abort(403);
        }

        $validated = $request->validate([
            'spr_form' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx',
            'document_number' => 'nullable|string',
            'original_invoice' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx',
            'copy_invoice' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx',
            'tax_invoice' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx',
            'agreement' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx',
            'internal_memo_entertain' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx',
            'entertain_realization_form' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx',
            'minutes_of_meeting' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx',
            'nominative_summary' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx',
            'calculation_summary' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx',
            'budget_plan' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx',
        ]);

        DB::transaction(function () use ($supplierPayment, $revision, $validated) {
            // Update files if provided
            foreach (
                [
                    'spr_form',
                    'original_invoice',
                    'copy_invoice',
                    'tax_invoice',
                    'agreement',
                    'internal_memo_entertain',
                    'entertain_realization_form',
                    'minutes_of_meeting',
                    'nominative_summary',
                    'calculation_summary',
                    'budget_plan'
                ] as $fileField
            ) {
                if (isset($validated[$fileField])) {
                    $file = $validated[$fileField];
                    $extension = $file->getClientOriginalExtension();
                    $filename = $fileField . '_' . $supplierPayment->number . '_revised(' . $revision->revision_times . ').' . $extension;
                    $supplierPayment->update([
                        $fileField => $file->storeAs('supplier_payments', $filename)
                    ]);
                }
            }

            // Update document number if provided
            if (isset($validated['document_number'])) {
                $supplierPayment->update(['document_number' => $validated['document_number']]);
            }

            // Update revision status to 'revised'
            $revision->update([
                'revision_status_id' => 2, // 'revised' status
                'revision_at' => now(),
            ]);

            // Check if all revisions are revised
            $pendingRevisions = $supplierPayment->revisions()
                ->where('revision_status_id', '!=', 2) // Not 'revised' status
                ->count();

            // If all revisions are revised, update document status to 'waiting approval'
            if ($pendingRevisions === 0) {
                $waitingApprovalStatus = DocumentStatus::where('slug', 'waiting-approval')->first();
                if ($waitingApprovalStatus) {
                    $supplierPayment->update([
                        'document_status_id' => $waitingApprovalStatus->id
                    ]);
                }
            }
        });

        return redirect()->route('user.supplier-payment.show', $supplierPayment)
            ->with('success', 'Revision submitted successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
