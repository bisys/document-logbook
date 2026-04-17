<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DocumentType;
use App\Models\Department;
use App\Models\DocumentStatus;
use App\Models\SupplierPayment;
use App\Models\PettyCash;
use App\Models\CashAdvanceDraw;
use App\Models\CashAdvanceRealization;
use App\Models\InternationalTrip;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportExport;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function index()
    {
        // Define document types manually since the table might be empty and these models represent types directly
        $documentTypes = [
            (object)['id' => 'supplier-payment', 'name' => 'Supplier Payment'],
            (object)['id' => 'petty-cash', 'name' => 'Petty Cash'],
            (object)['id' => 'international-trip', 'name' => 'International Trip'],
            (object)['id' => 'cash-advance-draw', 'name' => 'Cash Advance Draw'],
            (object)['id' => 'cash-advance-realization', 'name' => 'Cash Advance Realization'],
        ];
        $departments = Department::all();
        $documentStatuses = DocumentStatus::all();

        return view('report.index', compact('documentTypes', 'departments', 'documentStatuses'));
    }

    public function export(Request $request)
    {
        $request->validate([
            'export_type' => 'required|in:pdf,excel',
            'document_type_id' => 'nullable',
            'department_id' => 'nullable',
            'document_status_id' => 'nullable',
            'has_revision' => 'nullable|in:all,yes,no',
            'hardfile_status' => 'nullable|in:all,received,not_received',
            'payment_status' => 'nullable|in:all,paid,not_paid',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $data = $this->getReportData($request);

        if ($request->export_type === 'pdf') {
            $pdf = Pdf::loadView('report.pdf', ['data' => $data, 'filters' => $request->all()])
                      ->setPaper('a4', 'landscape');
            return $pdf->stream('report_'.Carbon::now()->format('Ymd_His').'.pdf');
        }

        return Excel::download(new ReportExport($data, $request->all()), 'report_'.Carbon::now()->format('Ymd_His').'.xlsx');
    }

    private function getReportData(Request $request)
    {
        $user = Auth::user();
        $role = $user->role->slug ?? 'user';
        
        $documentTypeFilterId = $request->document_type_id; 
        $targetSlug = null;
        if ($documentTypeFilterId && $documentTypeFilterId !== 'all') {
            $targetSlug = $documentTypeFilterId;
        }
        
        $models = [
            'supplier-payment' => SupplierPayment::class,
            'petty-cash' => PettyCash::class,
            'international-trip' => InternationalTrip::class,
            'cash-advance-draw' => CashAdvanceDraw::class,
            'cash-advance-realization' => CashAdvanceRealization::class,
        ];
        
        $results = [];

        foreach ($models as $slug => $modelClass) {
            // If filtering by document type and slug doesn't match, skip
            if ($targetSlug && $targetSlug !== $slug) {
                continue;
            }
            
            $query = $modelClass::with(['user.department', 'status', 'revisions']);
            
            // Scope by role: user can only see their own
            if ($role === 'user') {
                $query->where('user_id', $user->id);
            }

            // Apply Status filter (nullable if 'all' passed or not set)
            if ($request->document_status_id && $request->document_status_id !== 'all') {
                $query->where('document_status_id', $request->document_status_id);
            }

            // Apply Date range filter on created_at
            if ($request->start_date && $request->end_date) {
                $start = Carbon::parse($request->start_date)->startOfDay();
                $end = Carbon::parse($request->end_date)->endOfDay();
                $query->whereBetween('created_at', [$start, $end]);
            } elseif ($request->start_date) {
                $start = Carbon::parse($request->start_date)->startOfDay();
                $query->where('created_at', '>=', $start);
            } elseif ($request->end_date) {
                $end = Carbon::parse($request->end_date)->endOfDay();
                $query->where('created_at', '<=', $end);
            }

            // Fetch Items
            $items = $query->get();
            
            // Apply Has Revision filter
            if ($request->has_revision === 'yes') {
                $items = $items->filter(function($item) {
                    return $item->revisions->count() > 0;
                });
            } elseif ($request->has_revision === 'no') {
                $items = $items->filter(function($item) {
                    return $item->revisions->count() === 0;
                });
            }

            // Apply Hardfile Status filter
            if ($request->hardfile_status === 'received') {
                $items = $items->filter(function($item) {
                    return !is_null($item->hardfile_received_at);
                });
            } elseif ($request->hardfile_status === 'not_received') {
                $items = $items->filter(function($item) {
                    return is_null($item->hardfile_received_at);
                });
            }

            // Apply Payment Status filter
            if ($request->payment_status === 'paid') {
                $items = $items->filter(function($item) {
                    // We check if the attribute exists; if it doesn't, this doc doesn't have a payment state, so it's filtered out
                    return array_key_exists('is_paid', $item->getAttributes()) && $item->is_paid;
                });
            } elseif ($request->payment_status === 'not_paid') {
                $items = $items->filter(function($item) {
                    return array_key_exists('is_paid', $item->getAttributes()) && !$item->is_paid;
                });
            }
            
            // Apply Department filter
            if ($request->department_id && $request->department_id !== 'all') {
                $items = $items->filter(function($item) use ($request) {
                    return $item->user && $item->user->department_id == $request->department_id;
                });
            }

            foreach ($items as $item) {
                $typeName = ucwords(str_replace('-', ' ', $slug));
                
                $paymentReceipt = 'N/A';
                if (array_key_exists('is_paid', $item->getAttributes())) {
                    if ($item->is_paid) {
                        $paymentReceipt = 'Paid' . ($item->paid_at ? ' (' . $item->paid_at->format('Y-m-d H:i:s') . ')' : '');
                    } else {
                        $paymentReceipt = 'Not Paid';
                    }
                }

                $results[] = [
                    'document_type' => $typeName,
                    'number' => $item->number ?? '-',
                    'document_number' => $item->document_number ?? '-',
                    'user_name' => $item->user->name ?? '-',
                    'department' => $item->user->department->department ?? '-',
                    'status' => $item->status->status ?? '-',
                    'created_at' => $item->created_at->format('Y-m-d H:i:s'),
                    'has_revision' => $item->revisions->count() > 0 ? 'Yes' : 'No',
                    'hardfile_received_date' => $item->hardfile_received_at ? $item->hardfile_received_at->format('Y-m-d H:i:s') : '-',
                    'payment_receipt' => $paymentReceipt,
                ];
            }
        }
        
        // Sort by created_at desc
        usort($results, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });

        return collect($results);
    }
}
