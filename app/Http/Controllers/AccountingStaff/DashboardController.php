<?php

namespace App\Http\Controllers\AccountingStaff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SupplierPayment;
use App\Models\PettyCash;
use App\Models\InternationalTrip;
use App\Models\CashAdvanceDraw;
use App\Models\CashAdvanceRealization;

class DashboardController extends Controller
{
    public function index()
    {
        // Count all documents
        $spQuery = SupplierPayment::query();
        $pcQuery = PettyCash::query();
        $itQuery = InternationalTrip::query();
        $cadQuery = CashAdvanceDraw::query();
        $carQuery = CashAdvanceRealization::query();

        $totalDocuments = $spQuery->count() + $pcQuery->count() + $itQuery->count() + $cadQuery->count() + $carQuery->count();

        // Pending Staff Approval
        $pendingSP = (clone $spQuery)->whereHas('status', function($q){ $q->where('slug', 'waiting-approval-staff'); })->count();
        $pendingPC = (clone $pcQuery)->whereHas('status', function($q){ $q->where('slug', 'waiting-approval-staff'); })->count();
        $pendingIT = (clone $itQuery)->whereHas('status', function($q){ $q->where('slug', 'waiting-approval-staff'); })->count();
        $pendingCAD = (clone $cadQuery)->whereHas('status', function($q){ $q->where('slug', 'waiting-approval-staff'); })->count();
        $pendingCAR = (clone $carQuery)->whereHas('status', function($q){ $q->where('slug', 'waiting-approval-staff'); })->count();
        $totalPendingAction = $pendingSP + $pendingPC + $pendingIT + $pendingCAD + $pendingCAR;

        // Waiting Revision
        $revSP = (clone $spQuery)->whereHas('status', function($q){ $q->where('slug', 'waiting-revision'); })->count();
        $revPC = (clone $pcQuery)->whereHas('status', function($q){ $q->where('slug', 'waiting-revision'); })->count();
        $revIT = (clone $itQuery)->whereHas('status', function($q){ $q->where('slug', 'waiting-revision'); })->count();
        $revCAD = (clone $cadQuery)->whereHas('status', function($q){ $q->where('slug', 'waiting-revision'); })->count();
        $revCAR = (clone $carQuery)->whereHas('status', function($q){ $q->where('slug', 'waiting-revision'); })->count();
        $totalWaitingRevision = $revSP + $revPC + $revIT + $revCAD + $revCAR;

        $needYourAction = $totalPendingAction + $totalWaitingRevision;

        // Breakdown by type
        $breakdown = [
            'Supplier Payment' => $spQuery->count(),
            'Petty Cash' => $pcQuery->count(),
            'International Trip' => $itQuery->count(),
            'Cash Advance Draw' => $cadQuery->count(),
            'Cash Advance Realization' => $carQuery->count(),
        ];

        return view('accounting_staff.dashboard', compact(
            'totalDocuments', 'needYourAction', 'totalWaitingRevision', 'breakdown'
        ));
    }
}
