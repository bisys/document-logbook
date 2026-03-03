<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupplierPayment;
use App\Models\DocumentStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SupplierPaymentController extends Controller
{
    /**
     * Display a listing of all supplier payments
     */
    public function index(Request $request)
    {
        $query = SupplierPayment::with([
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

        // Filter by status if provided
        if ($request->has('status_id') && $request->status_id) {
            $query->where('document_status_id', $request->status_id);
        }

        // Filter by user if provided
        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        $supplierPayments = $query->orderBy('created_at', 'desc')->get();
        $statuses = DocumentStatus::all();

        return view('admin.supplier_payment.index', compact('supplierPayments', 'statuses'));
    }

    /**
     * Show the detailed view of a supplier payment
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

        return view('admin.supplier_payment.show', compact('supplierPayment'));
    }

    /**
     * Force update document status (admin only)
     */
    public function updateStatus(Request $request, SupplierPayment $supplierPayment)
    {
        $validated = $request->validate([
            'document_status_id' => 'required|exists:document_statuses,id',
            'remark' => 'nullable|string|max:1000',
        ]);

        DB::transaction(function () use ($supplierPayment, $validated) {
            $supplierPayment->update([
                'document_status_id' => $validated['document_status_id']
            ]);

            // Log the status change
            if ($validated['remark']) {
                Log::info('Admin updated supplier payment status', [
                    'supplier_payment_id' => $supplierPayment->id,
                    'new_status_id' => $validated['document_status_id'],
                    'remark' => $validated['remark']
                ]);
            }
        });

        return redirect()->route('admin.supplier-payment.show', $supplierPayment)
            ->with('success', 'Document status updated successfully.');
    }
}
