<?php

namespace App\Http\Controllers\AccountingStaff;

use App\Http\Controllers\Controller;
use App\Models\SupplierPayment;
use App\Models\Revision;
use App\Models\Approval;
use App\Models\RevisionStatus;
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
     * Display a listing of supplier payments waiting for approval
     */
    public function index(Request $request)
    {
        // retrieve all documents first
        $allPayments = SupplierPayment::with([
            'user.department',
            'user.position',
            'costCenter',
            'status',
            'revisions.user.department',
            'revisions.status',
            'approvals.role',
            'approvals.user'
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
                    return in_array($slug, ['approved', 'fully-approved']);
                default:
                    return true; // all
            }
        });

        // pass counts for UI
        $counts = [
            'all' => $allPayments->count(),
            'waiting-approval-staff' => $allPayments->where('status.slug', 'waiting-approval-staff')->count(),
            'waiting-approval-manager' => $allPayments->where('status.slug', 'waiting-approval-manager')->count(),
            'waiting-approval-gm' => $allPayments->where('status.slug', 'waiting-approval-gm')->count(),
            'waiting-revision' => $allPayments->where('status.slug', 'waiting-revision')->count(),
            'fully-approved' => $allPayments->where('status.slug', 'fully-approved')->count(),
        ];

        return view('accounting_staff.supplier_payment.index', compact('supplierPayments', 'statusFilter', 'counts'));
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

        // Check revision status - if all revisions are revised, document can be approved
        $pendingRevisions = $supplierPayment->revisions()
            ->where('revision_status_id', '!=', 2) // Not 'revised' status
            ->count();

        $canApprove = $pendingRevisions === 0;
        $totalRevisions = $supplierPayment->revisions()->count();
        $maxRevisions = 3;

        return view('accounting_staff.supplier_payment.show', compact(
            'supplierPayment',
            'canApprove',
            'totalRevisions',
            'maxRevisions',
            'approvalChain'
        ));
    }

    /**
     * Add revision request to supplier payment
     */
    public function addRevision(Request $request, SupplierPayment $supplierPayment)
    {
        $validated = $request->validate([
            'remark' => 'required|string|max:1000',
        ]);

        // Ensure that the document cannot go back to revision process if approval already exists
        $existingApproval = $supplierPayment->approvals()->exists(); // Any approval exists

        if ($existingApproval) {
            return redirect()->route('accounting-staff.supplier-payment.show', $supplierPayment)
                ->with('error', 'Cannot add revision: Document is on approval process.');
        }

        try {
            DB::transaction(function () use ($supplierPayment, $validated) {
                // Check current revision count
                $currentRevisions = $supplierPayment->revisions()->count();

                if ($currentRevisions >= 3) {
                    throw new \Exception('Maximum revisions (3) reached.');
                }

                // Create new revision
                $revision = new Revision([
                    'revision_times' => $currentRevisions + 1,
                    'user_id' => Auth::user()->id,
                    'revision_status_id' => 1, // 'revision requested' status
                    'remark' => $validated['remark'],
                    'revision_at' => now(),
                ]);

                $supplierPayment->revisions()->save($revision);

                // Update document status to 'waiting revision'
                $waittingRevisionStatus = DocumentStatus::where('slug', 'waiting-revision')->first();
                if ($waittingRevisionStatus) {
                    $supplierPayment->update([
                        'document_status_id' => $waittingRevisionStatus->id
                    ]);
                }
            });

            // Show success message if revision request is successfully created
            return redirect()->route('accounting-staff.supplier-payment.show', $supplierPayment)
                ->with('success', 'Revision request added successfully.');
        } catch (\Exception $e) {
            return redirect()->route('accounting-staff.supplier-payment.show', $supplierPayment)
                ->with('error', $e->getMessage());
        }
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
                // Check if there are pending revisions
                $pendingRevisions = $supplierPayment->revisions()
                    ->where('revision_status_id', '!=', 2) // Not 'revised' status
                    ->count();

                if ($pendingRevisions > 0) {
                    throw new \Exception('Cannot approve while there are pending revisions.');
                }

                // Get current user's approval role
                $userRole = ApprovalRole::where('sequence', 1)->first(); // Accounting staff = sequence 1

                if (!$userRole) {
                    throw new \Exception('Approval role not found.');
                }

                // Make sure no rejection has already occurred elsewhere
                if ($this->approvalService->hasRejected($supplierPayment)) {
                    throw new \Exception('Approval process halted: document already rejected.');
                }

                // Validate that this is the correct sequence for approval
                if (!$this->approvalService->isValidApprovalSequence($supplierPayment, $userRole->id)) {
                    throw new \Exception('This document is not ready for your approval or has already been processed.');
                }

                // check if this document is already approved by this role
                $alreadyApproved = $supplierPayment->approvals()
                    ->where('approval_role_id', $userRole->id)
                    ->where('approval_status_id', 1) // approved
                    ->exists();

                if ($alreadyApproved) {
                    throw new \Exception('This document is already approved by your role.');
                }

                // Create approval record
                $approval = new Approval([
                    'user_id' => Auth::user()->id,
                    'approval_role_id' => $userRole->id,
                    'approval_status_id' => 1, // 'approved' status
                    'remark' => $validated['remark'] ?? null,
                    'approval_at' => now(),
                ]);

                // Update document status to next approval stage
                $nextStatusSlug = 'waiting-approval-manager'; // Next status after staff approval
                $nextStatus = DocumentStatus::where('slug', $nextStatusSlug)->first();
                if ($nextStatus) {
                    $supplierPayment->update([
                        'document_status_id' => $nextStatus->id
                    ]);
                }

                $supplierPayment->approvals()->save($approval);
            });

            // Show success message if approval is successful created
            return redirect()->route('accounting-staff.supplier-payment.show', $supplierPayment)
                ->with('success', 'Supplier payment approved successfully.');
        } catch (\Exception $e) {
            return redirect()->route('accounting-staff.supplier-payment.show', $supplierPayment)
                ->with('error', $e->getMessage());
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
                // Get current user's approval role
                $userRole = ApprovalRole::where('sequence', 1)->first(); // Accounting staff = sequence 1

                if (!$userRole) {
                    throw new \Exception('Approval role not found.');
                }

                // Prevent rejection if document on revision process
                $pendingRevisions = $supplierPayment->revisions()
                    ->where('revision_status_id', '!=', 2) // Not 'revised' status
                    ->count();

                if ($pendingRevisions > 0) {
                    throw new \Exception('Cannot reject while there are pending revisions.');
                }

                // Prevent multiple rejections if any role has already rejected
                if ($this->approvalService->hasRejected($supplierPayment)) {
                    throw new \Exception('Cannot reject: document has already been rejected.');
                }

                // Validate that this is the correct sequence for approval
                if (!$this->approvalService->isValidApprovalSequence($supplierPayment, $userRole->id)) {
                    throw new \Exception('This document is not ready for your rejection or has already been processed.');
                }

                // check if this document is already rejected by this role
                $alreadyRejected = $supplierPayment->approvals()
                    ->where('approval_role_id', $userRole->id)
                    ->where('approval_status_id', 2) // rejected
                    ->exists();

                if ($alreadyRejected) {
                    throw new \Exception('This document is already rejected by your role.');
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

            // Show success message if rejection is successful created
            return redirect()->route('accounting-staff.supplier-payment.show', $supplierPayment)
                ->with('success', 'Supplier payment rejected successfully.');
        } catch (\Exception $e) {
            return redirect()->route('accounting-staff.supplier-payment.show', $supplierPayment)
                ->with('error', $e->getMessage());
        }
    }
}
