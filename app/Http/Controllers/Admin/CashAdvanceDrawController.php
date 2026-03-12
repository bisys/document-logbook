<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CashAdvanceDraw;
use App\Models\DocumentStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CashAdvanceDrawController extends Controller
{
    public function index(Request $request)
    {
        $query = CashAdvanceDraw::with(['user.department', 'user.position', 'costCenter', 'status', 'realization', 'revisions.user.department', 'revisions.status', 'approvals.user.department', 'approvals.role', 'approvals.status']);

        if ($request->has('status_id') && $request->status_id) {
            $query->where('document_status_id', $request->status_id);
        }
        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        $cashAdvanceDraws = $query->orderBy('created_at', 'desc')->get();
        $statuses = DocumentStatus::all();

        return view('admin.cash_advance_draw.index', compact('cashAdvanceDraws', 'statuses'));
    }

    public function show(CashAdvanceDraw $cashAdvanceDraw)
    {
        $cashAdvanceDraw->load(['user.department', 'user.position', 'costCenter', 'status', 'realization', 'revisions.user.department', 'revisions.status', 'approvals.user.department', 'approvals.role', 'approvals.status']);
        return view('admin.cash_advance_draw.show', compact('cashAdvanceDraw'));
    }

    public function updateStatus(Request $request, CashAdvanceDraw $cashAdvanceDraw)
    {
        $validated = $request->validate([
            'document_status_id' => 'required|exists:document_statuses,id',
            'remark' => 'nullable|string|max:1000',
        ]);

        DB::transaction(function () use ($cashAdvanceDraw, $validated) {
            $cashAdvanceDraw->update(['document_status_id' => $validated['document_status_id']]);
            if ($validated['remark']) {
                Log::info('Admin updated cash advance draw status', [
                    'cash_advance_draw_id' => $cashAdvanceDraw->id,
                    'new_status_id' => $validated['document_status_id'],
                    'remark' => $validated['remark']
                ]);
            }
        });

        return redirect()->route('admin.cash-advance-draw.show', $cashAdvanceDraw)->with('success', 'Document status updated successfully.');
    }
}
