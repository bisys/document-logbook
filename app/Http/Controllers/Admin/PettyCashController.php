<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PettyCash;
use App\Models\DocumentStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PettyCashController extends Controller
{
    public function index(Request $request)
    {
        $query = PettyCash::with(['user.department', 'user.position', 'costCenter', 'status', 'revisions.user.department', 'revisions.status', 'approvals.user.department', 'approvals.role', 'approvals.status']);

        if ($request->has('status_id') && $request->status_id) {
            $query->where('document_status_id', $request->status_id);
        }
        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        $pettyCashes = $query->orderBy('created_at', 'desc')->get();
        $statuses = DocumentStatus::all();

        return view('admin.petty_cash.index', compact('pettyCashes', 'statuses'));
    }

    public function show(PettyCash $pettyCash)
    {
        $pettyCash->load(['user.department', 'user.position', 'costCenter', 'status', 'revisions.user.department', 'revisions.status', 'approvals.user.department', 'approvals.role', 'approvals.status']);
        return view('admin.petty_cash.show', compact('pettyCash'));
    }

    public function updateStatus(Request $request, PettyCash $pettyCash)
    {
        $validated = $request->validate([
            'document_status_id' => 'required|exists:document_statuses,id',
            'remark' => 'nullable|string|max:1000',
        ]);

        DB::transaction(function () use ($pettyCash, $validated) {
            $pettyCash->update(['document_status_id' => $validated['document_status_id']]);
            if ($validated['remark']) {
                Log::info('Admin updated petty cash status', [
                    'petty_cash_id' => $pettyCash->id,
                    'new_status_id' => $validated['document_status_id'],
                    'remark' => $validated['remark']
                ]);
            }
        });

        return redirect()->route('admin.petty-cash.show', $pettyCash)->with('success', 'Document status updated successfully.');
    }
}
