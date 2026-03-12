<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CashAdvanceRealization;
use App\Models\DocumentStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CashAdvanceRealizationController extends Controller
{
    public function index(Request $request)
    {
        $query = CashAdvanceRealization::with(['draw.user.department', 'draw.user.position', 'draw.costCenter', 'status', 'revisions.user.department', 'revisions.status', 'approvals.user.department', 'approvals.role', 'approvals.status']);

        if ($request->has('status_id') && $request->status_id) {
            $query->where('document_status_id', $request->status_id);
        }

        $cashAdvanceRealizations = $query->orderBy('created_at', 'desc')->get();
        $statuses = DocumentStatus::all();

        return view('admin.cash_advance_realization.index', compact('cashAdvanceRealizations', 'statuses'));
    }

    public function show(CashAdvanceRealization $cashAdvanceRealization)
    {
        $cashAdvanceRealization->load(['draw.user.department', 'draw.user.position', 'draw.costCenter', 'status', 'revisions.user.department', 'revisions.status', 'approvals.user.department', 'approvals.role', 'approvals.status']);
        return view('admin.cash_advance_realization.show', compact('cashAdvanceRealization'));
    }

    public function updateStatus(Request $request, CashAdvanceRealization $cashAdvanceRealization)
    {
        $validated = $request->validate([
            'document_status_id' => 'required|exists:document_statuses,id',
            'remark' => 'nullable|string|max:1000',
        ]);

        DB::transaction(function () use ($cashAdvanceRealization, $validated) {
            $cashAdvanceRealization->update(['document_status_id' => $validated['document_status_id']]);
            if ($validated['remark']) {
                Log::info('Admin updated cash advance realization status', [
                    'cash_advance_realization_id' => $cashAdvanceRealization->id,
                    'new_status_id' => $validated['document_status_id'],
                    'remark' => $validated['remark']
                ]);
            }
        });

        return redirect()->route('admin.cash-advance-realization.show', $cashAdvanceRealization)->with('success', 'Document status updated successfully.');
    }
}
