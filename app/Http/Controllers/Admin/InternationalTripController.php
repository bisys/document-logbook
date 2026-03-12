<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InternationalTrip;
use App\Models\DocumentStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InternationalTripController extends Controller
{
    public function index(Request $request)
    {
        $query = InternationalTrip::with(['user.department', 'user.position', 'costCenter', 'status', 'revisions.user.department', 'revisions.status', 'approvals.user.department', 'approvals.role', 'approvals.status']);

        if ($request->has('status_id') && $request->status_id) {
            $query->where('document_status_id', $request->status_id);
        }
        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        $internationalTrips = $query->orderBy('created_at', 'desc')->get();
        $statuses = DocumentStatus::all();

        return view('admin.international_trip.index', compact('internationalTrips', 'statuses'));
    }

    public function show(InternationalTrip $internationalTrip)
    {
        $internationalTrip->load(['user.department', 'user.position', 'costCenter', 'status', 'revisions.user.department', 'revisions.status', 'approvals.user.department', 'approvals.role', 'approvals.status']);
        return view('admin.international_trip.show', compact('internationalTrip'));
    }

    public function updateStatus(Request $request, InternationalTrip $internationalTrip)
    {
        $validated = $request->validate([
            'document_status_id' => 'required|exists:document_statuses,id',
            'remark' => 'nullable|string|max:1000',
        ]);

        DB::transaction(function () use ($internationalTrip, $validated) {
            $internationalTrip->update(['document_status_id' => $validated['document_status_id']]);
            if ($validated['remark']) {
                Log::info('Admin updated international trip status', [
                    'international_trip_id' => $internationalTrip->id,
                    'new_status_id' => $validated['document_status_id'],
                    'remark' => $validated['remark']
                ]);
            }
        });

        return redirect()->route('admin.international-trip.show', $internationalTrip)->with('success', 'Document status updated successfully.');
    }
}
