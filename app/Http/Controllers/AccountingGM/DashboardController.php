<?php

namespace App\Http\Controllers\AccountingGM;

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

        // Pending GM Approval
        $pendingSP = (clone $spQuery)->whereHas('status', function($q){ $q->where('slug', 'waiting-approval-gm'); })->count();
        $pendingPC = (clone $pcQuery)->whereHas('status', function($q){ $q->where('slug', 'waiting-approval-gm'); })->count();
        $pendingIT = (clone $itQuery)->whereHas('status', function($q){ $q->where('slug', 'waiting-approval-gm'); })->count();
        $pendingCAD = (clone $cadQuery)->whereHas('status', function($q){ $q->where('slug', 'waiting-approval-gm'); })->count();
        $pendingCAR = (clone $carQuery)->whereHas('status', function($q){ $q->where('slug', 'waiting-approval-gm'); })->count();
        $needYourAction = $pendingSP + $pendingPC + $pendingIT + $pendingCAD + $pendingCAR;

        // Fully Approved
        $approvedSP = (clone $spQuery)->whereHas('status', function($q){ $q->whereIn('slug', ['approved', 'fully-approved']); })->count();
        $approvedPC = (clone $pcQuery)->whereHas('status', function($q){ $q->whereIn('slug', ['approved', 'fully-approved']); })->count();
        $approvedIT = (clone $itQuery)->whereHas('status', function($q){ $q->whereIn('slug', ['approved', 'fully-approved']); })->count();
        $approvedCAD = (clone $cadQuery)->whereHas('status', function($q){ $q->whereIn('slug', ['approved', 'fully-approved']); })->count();
        $approvedCAR = (clone $carQuery)->whereHas('status', function($q){ $q->whereIn('slug', ['approved', 'fully-approved']); })->count();
        $totalApproved = $approvedSP + $approvedPC + $approvedIT + $approvedCAD + $approvedCAR;

        // Breakdown by type
        $breakdown = [
            'Supplier Payment' => $spQuery->count(),
            'Petty Cash' => $pcQuery->count(),
            'International Trip' => $itQuery->count(),
            'Cash Advance Draw' => $cadQuery->count(),
            'Cash Advance Realization' => $carQuery->count(),
        ];

        return view('accounting_gm.dashboard', compact(
            'totalDocuments', 'needYourAction', 'totalApproved', 'breakdown'
        ));
    }
}
