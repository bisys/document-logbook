<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSupplierPaymentRequest;
use App\Http\Requests\UpdateSupplierPaymentRequest;
use Illuminate\Http\Request;
use App\Models\SupplierPayment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\CostCenter;

class SupplierPaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $supplierPayments = SupplierPayment::with(['revisions', 'approvals', 'user', 'status', 'costCenter'])->where('user_id', Auth::user()->id)->get();

        return view('user.supplier_payment.index', compact('supplierPayments'));
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

        return redirect()->route('supplier-payment.index')->with('success', 'Supplier Payment created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(SupplierPayment $supplierPayment)
    {
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

        return view('user.supplier_payment.show', compact('supplierPayment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SupplierPayment $supplierPayment)
    {
        $costCenters = CostCenter::select('id', 'number', 'name')->get();

        return view('user.supplier_payment.edit', compact('supplierPayment', 'costCenters'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSupplierPaymentRequest $request, SupplierPayment $supplierPayment)
    {
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

        return redirect()->route('supplier-payment.index')->with('success', 'Supplier Payment updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
