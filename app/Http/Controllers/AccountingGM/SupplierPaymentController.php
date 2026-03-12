<?php

namespace App\Http\Controllers\AccountingGM;

use App\Http\Controllers\Controller;
use App\Models\SupplierPayment;
use App\Models\Approval;
use App\Models\DocumentStatus;
use App\Models\ApprovalRole;
use App\Services\ApprovalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SupplierPaymentController extends Controller
{
    protected $approvalService;

    public function __construct(ApprovalService $approvalService)
    {
        $this->approvalService = $approvalService;
    }

    /**
     * Display a listing of supplier payments waiting for GM approval
     */
    public function index(Request $request)
    {
        $allPayments = SupplierPayment::with([
            'user.department',
            'user.position',
            'costCenter',
            'status',
            'revisions.user.department',
            'revisions.status',
            'approvals.user.department',
            'approvals.role',
            'approvals.status'
        ])->orderBy('created_at', 'desc')->get();

        $statusFilter = $request->query('status', 'all');

        $supplierPayments = $allPayments->filter(function ($payment) use ($statusFilter) {
            $slug = optional($payment->status)->slug ?? '';
            switch ($statusFilter) {
                case 'waiting-approval-staff':
                    return $slug === 'waiting-approval-staff';
                case 'waiting-approval-manager':
                    return $slug === 'waiting-approval-manager';
                case 'waiting-approval-gm':
                    return $slug === 'waiting-approval-gm';
                case 'waiting-revision':
                    return $slug === 'waiting-revision';
                case 'fully-approved':
                    return $slug === 'fully-approved';
                default:
                    return true;
            }
        });

        $counts = [
            'all' => $allPayments->count(),
            'waiting-approval-staff' => $allPayments->where('status.slug', 'waiting-approval-staff')->count(),
            'waiting-approval-manager' => $allPayments->where('status.slug', 'waiting-approval-manager')->count(),
            'waiting-approval-gm' => $allPayments->where('status.slug', 'waiting-approval-gm')->count(),
            'waiting-revision' => $allPayments->where('status.slug', 'waiting-revision')->count(),
            'fully-approved' => $allPayments->where('status.slug', 'fully-approved')->count(),
        ];

        return view('accounting_gm.supplier_payment.index', compact('supplierPayments', 'statusFilter', 'counts'));
    }

    /**
     * Show the detailed view of a supplier payment
     */
    public function show(SupplierPayment $supplierPayment)
    {
        $supplierPayment->load([
            'user.department',
            'user.position',
            'costCenter',
            'status',
            'revisions.user.department',
            'revisions.status',
            'approvals.user.department',
            'approvals.role',
            'approvals.status'
        ]);

        // Get the approval chain
        $approvalChain = $this->approvalService->getApprovalChain($supplierPayment);

        return view('accounting_gm.supplier_payment.show', compact('supplierPayment', 'approvalChain'));
    }

    /**
     * Approve supplier payment
     */
    public function approve(Request $request, SupplierPayment $supplierPayment)
    {
        $validated = $request->validate([
            'remark' => 'nullable|string|max:1000',
        ]);

        try {
            DB::transaction(function () use ($supplierPayment, $validated) {
                // Get current user's approval role (sequence 3 - GM)
                $userRole = ApprovalRole::where('sequence', 3)->first();

                if (!$userRole) {
                    throw new \Exception('Approval role not found.');
                }

                // disallow if any rejection has occurred
                if ($this->approvalService->hasRejected($supplierPayment)) {
                    throw new \Exception('Approval process halted: document already rejected.');
                }

                // Validate that all previous approvals are complete
                if (!$this->approvalService->allPreviousApprovalsComplete($supplierPayment, $userRole->id)) {
                    throw new \Exception('Cannot approve. Previous approval steps must be completed first.');
                }

                // Validate that this is the correct sequence for approval
                if (!$this->approvalService->isValidApprovalSequence($supplierPayment, $userRole->id)) {
                    throw new \Exception('This document is not ready for your approval.');
                }

                // Create approval record
                $approval = new Approval([
                    'user_id' => Auth::user()->id,
                    'approval_role_id' => $userRole->id,
                    'approval_status_id' => 1, // 'approved' status
                    'remark' => $validated['remark'] ?? null,
                    'approval_at' => now(),
                ]);

                $supplierPayment->approvals()->save($approval);

                // Update document status to 'fully-approved' after GM approval (final approval)
                $fullyApprovedStatus = DocumentStatus::where('slug', 'fully-approved')->first();
                if ($fullyApprovedStatus) {
                    $supplierPayment->update([
                        'document_status_id' => $fullyApprovedStatus->id
                    ]);
                }
            });

            return redirect()->route('accounting-gm.supplier-payment.show', $supplierPayment)
                ->with('success', 'Supplier payment approved by GM.');
        } catch (\Exception $e) {
            return redirect()->route('accounting-gm.supplier-payment.show', $supplierPayment)
                ->with('error', 'Error during approval: ' . $e->getMessage());
        }
    }

    /**
     * Reject supplier payment
     */
    public function reject(Request $request, SupplierPayment $supplierPayment)
    {
        $validated = $request->validate([
            'remark' => 'required|string|max:1000',
        ]);

        try {
            DB::transaction(function () use ($supplierPayment, $validated) {
                // Get current user's approval role (sequence 3 - GM)
                $userRole = ApprovalRole::where('sequence', 3)->first();

                if (!$userRole) {
                    throw new \Exception('Approval role not found.');
                }

                // prevent multiple rejects after one has occurred
                if ($this->approvalService->hasRejected($supplierPayment)) {
                    throw new \Exception('Cannot reject: document has already been rejected.');
                }

                // Validate that this is the correct sequence for approval
                if (!$this->approvalService->isValidApprovalSequence($supplierPayment, $userRole->id)) {
                    throw new \Exception('This document is not ready for your rejection or has already been processed.');
                }

                // Create rejection record
                $approval = new Approval([
                    'user_id' => Auth::user()->id,
                    'approval_role_id' => $userRole->id,
                    'approval_status_id' => 2, // 'rejected' status
                    'remark' => $validated['remark'],
                    'approval_at' => now(),
                ]);

                $supplierPayment->approvals()->save($approval);

                // Update document status to 'rejected'
                $rejectedStatus = DocumentStatus::where('slug', 'rejected')->first();
                if ($rejectedStatus) {
                    $supplierPayment->update([
                        'document_status_id' => $rejectedStatus->id
                    ]);
                }
            });

            return redirect()->route('accounting-gm.supplier-payment.show', $supplierPayment)
                ->with('success', 'Supplier payment rejected by GM.');
        } catch (\Exception $e) {
            return redirect()->route('accounting-gm.supplier-payment.show', $supplierPayment)
                ->with('error', 'Error during rejection: ' . $e->getMessage());
        }
    }
}
