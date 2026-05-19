<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InternationalTripRealization;
use App\Models\DocumentStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InternationalTripRealizationController extends Controller
{
    public function index(Request $request)
    {
        $query = InternationalTripRealization::with(['trip.user.department', 'trip.user.position', 'trip.costCenter', 'status', 'revisions.user.department', 'revisions.status', 'approvals.user.department', 'approvals.role', 'approvals.status']);

        if ($request->has('status_id') && $request->status_id) {
            $query->where('document_status_id', $request->status_id);
        }

        $internationalTripRealizations = $query->orderBy('created_at', 'desc')->get();
        $statuses = DocumentStatus::all();

        return view('admin.international_trip_realization.index', compact('internationalTripRealizations', 'statuses'));
    }

    public function show(InternationalTripRealization $internationalTripRealization)
    {
        $internationalTripRealization->load(['trip.user.department', 'trip.user.position', 'trip.costCenter', 'status', 'revisions.user.department', 'revisions.status', 'approvals.user.department', 'approvals.role', 'approvals.status']);
        return view('admin.international_trip_realization.show', compact('internationalTripRealization'));
    }

    public function updateStatus(Request $request, InternationalTripRealization $internationalTripRealization)
    {
        $validated = $request->validate([
            'document_status_id' => 'required|exists:document_statuses,id',
            'remark' => 'nullable|string|max:1000',
        ]);

        DB::transaction(function () use ($internationalTripRealization, $validated) {
            $internationalTripRealization->update(['document_status_id' => $validated['document_status_id']]);
            if ($validated['remark']) {
                Log::info('Admin updated international trip realization status', [
                    'international_trip_realization_id' => $internationalTripRealization->id,
                    'new_status_id' => $validated['document_status_id'],
                    'remark' => $validated['remark']
                ]);
            }
        });

        return redirect()->route('admin.international-trip-realization.show', $internationalTripRealization)->with('success', 'Document status updated successfully.');
    }
}
