<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SupplierPayment;
use App\Models\PettyCash;
use App\Models\InternationalTrip;
use App\Models\CashAdvanceDraw;
use App\Models\CashAdvanceRealization;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Count all documents
        $spQuery = SupplierPayment::where('user_id', $user->id);
        $pcQuery = PettyCash::where('user_id', $user->id);
        $itQuery = InternationalTrip::where('user_id', $user->id);
        $cadQuery = CashAdvanceDraw::where('user_id', $user->id);
        $carQuery = CashAdvanceRealization::where('user_id', $user->id);

        $totalDocuments = $spQuery->count() + $pcQuery->count() + $itQuery->count() + $cadQuery->count() + $carQuery->count();

        // Pending
        $pendingSP = (clone $spQuery)->whereHas('status', function($q){ $q->where('slug', 'pending'); })->count();
        $pendingPC = (clone $pcQuery)->whereHas('status', function($q){ $q->where('slug', 'pending'); })->count();
        $pendingIT = (clone $itQuery)->whereHas('status', function($q){ $q->where('slug', 'pending'); })->count();
        $pendingCAD = (clone $cadQuery)->whereHas('status', function($q){ $q->where('slug', 'pending'); })->count();
        $pendingCAR = (clone $carQuery)->whereHas('status', function($q){ $q->where('slug', 'pending'); })->count();
        $totalPending = $pendingSP + $pendingPC + $pendingIT + $pendingCAD + $pendingCAR;

        // Approved/Finished
        $approvedSP = (clone $spQuery)->whereHas('status', function($q){ $q->where('slug', 'finished'); })->count();
        $approvedPC = (clone $pcQuery)->whereHas('status', function($q){ $q->where('slug', 'finished'); })->count();
        $approvedIT = (clone $itQuery)->whereHas('status', function($q){ $q->where('slug', 'finished'); })->count();
        $approvedCAD = (clone $cadQuery)->whereHas('status', function($q){ $q->where('slug', 'finished'); })->count();
        $approvedCAR = (clone $carQuery)->whereHas('status', function($q){ $q->where('slug', 'finished'); })->count();
        $totalApproved = $approvedSP + $approvedPC + $approvedIT + $approvedCAD + $approvedCAR;

        // Rejected/Revision
        $rejectedSP = (clone $spQuery)->whereHas('status', function($q){ $q->whereIn('slug', ['rejected', 'revision']); })->count();
        $rejectedPC = (clone $pcQuery)->whereHas('status', function($q){ $q->whereIn('slug', ['rejected', 'revision']); })->count();
        $rejectedIT = (clone $itQuery)->whereHas('status', function($q){ $q->whereIn('slug', ['rejected', 'revision']); })->count();
        $rejectedCAD = (clone $cadQuery)->whereHas('status', function($q){ $q->whereIn('slug', ['rejected', 'revision']); })->count();
        $rejectedCAR = (clone $carQuery)->whereHas('status', function($q){ $q->whereIn('slug', ['rejected', 'revision']); })->count();
        $totalRejected = $rejectedSP + $rejectedPC + $rejectedIT + $rejectedCAD + $rejectedCAR;

        // Breakdown by type
        $breakdown = [
            'Supplier Payment' => $spQuery->count(),
            'Petty Cash' => $pcQuery->count(),
            'International Trip' => $itQuery->count(),
            'Cash Advance Draw' => $cadQuery->count(),
            'Cash Advance Realization' => $carQuery->count(),
        ];

        return view('user.dashboard', compact(
            'totalDocuments', 'totalPending', 'totalApproved', 'totalRejected', 'breakdown'
        ));
    }
}
