<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Department;
use App\Models\CostCenter;
use App\Models\SupplierPayment;
use App\Models\PettyCash;
use App\Models\InternationalTrip;
use App\Models\CashAdvanceDraw;
use App\Models\CashAdvanceRealization;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $totalDepartments = Department::count();
        $totalCostCenters = CostCenter::count();

        // Count all documents
        $spQuery = SupplierPayment::query();
        $pcQuery = PettyCash::query();
        $itQuery = InternationalTrip::query();
        $cadQuery = CashAdvanceDraw::query();
        $carQuery = CashAdvanceRealization::query();

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

        // Breakdown by type
        $breakdown = [
            'Supplier Payment' => $spQuery->count(),
            'Petty Cash' => $pcQuery->count(),
            'International Trip' => $itQuery->count(),
            'Cash Advance Draw' => $cadQuery->count(),
            'Cash Advance Realization' => $carQuery->count(),
        ];

        return view('admin.dashboard', compact(
            'totalUsers', 'totalDepartments', 'totalCostCenters', 'totalDocuments', 'totalPending', 'totalApproved', 'breakdown'
        ));
    }
}
