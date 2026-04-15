<?php

namespace App\Http\Controllers\AccountingStaff;

use App\Http\Controllers\Controller;
use App\Models\PettyCash;
use App\Models\Revision;
use App\Models\Approval;
use App\Models\RevisionStatus;
use App\Models\DocumentStatus;
use App\Models\ApprovalRole;
use App\Services\ApprovalService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PettyCashController extends Controller
{
    protected $approvalService;
    protected $notificationService;

    public function __construct(ApprovalService $approvalService, NotificationService $notificationService)
    {
        $this->approvalService = $approvalService;
        $this->notificationService = $notificationService;
    }

    public function index(Request $request)
    {
        $allDocuments = PettyCash::with(['user.department', 'user.position', 'costCenter', 'status', 'revisions.user.department', 'revisions.status', 'approvals.role', 'approvals.user'])
            ->orderBy('created_at', 'desc')->get();

        $statusFilter = $request->query('status', 'all');

        $pettyCashes = $allDocuments->filter(function ($doc) use ($statusFilter) {
            $slug = optional($doc->status)->slug ?? '';
            switch ($statusFilter) {
                case 'waiting-approval-staff': return $slug === 'waiting-approval-staff';
                case 'waiting-approval-manager': return $slug === 'waiting-approval-manager';
                case 'waiting-approval-gm': return $slug === 'waiting-approval-gm';
                case 'waiting-revision': return $slug === 'waiting-revision';
                case 'fully-approved': return $slug === 'fully-approved';
                default: return true;
            }
        });

        $counts = [
            'all' => $allDocuments->count(),
            'waiting-approval-staff' => $allDocuments->where('status.slug', 'waiting-approval-staff')->count(),
            'waiting-approval-manager' => $allDocuments->where('status.slug', 'waiting-approval-manager')->count(),
            'waiting-approval-gm' => $allDocuments->where('status.slug', 'waiting-approval-gm')->count(),
            'waiting-revision' => $allDocuments->where('status.slug', 'waiting-revision')->count(),
            'fully-approved' => $allDocuments->where('status.slug', 'fully-approved')->count(),
        ];

        return view('accounting_staff.petty_cash.index', compact('pettyCashes', 'statusFilter', 'counts'));
    }

    public function show(PettyCash $pettyCash)
    {
        $pettyCash->load(['user.department', 'user.position', 'costCenter', 'status', 'revisions.user.department', 'revisions.status', 'approvals.user.department', 'approvals.role', 'approvals.status']);

        $approvalChain = $this->approvalService->getApprovalChain($pettyCash);
        $pendingRevisions = $pettyCash->revisions()->where('revision_status_id', '!=', 2)->count();
        $canApprove = $pendingRevisions === 0;
        $totalRevisions = $pettyCash->revisions()->count();
        $maxRevisions = 3;

        return view('accounting_staff.petty_cash.show', compact('pettyCash', 'canApprove', 'totalRevisions', 'maxRevisions', 'approvalChain'));
    }

    public function addRevision(Request $request, PettyCash $pettyCash)
    {
        $validated = $request->validate(['remark' => 'required|string|max:1000']);

        $existingApproval = $pettyCash->approvals()->exists();
        if ($existingApproval) {
            return redirect()->route('accounting-staff.petty-cash.show', $pettyCash)->with('error', 'Cannot add revision: Document is on approval process.');
        }

        try {
            DB::transaction(function () use ($pettyCash, $validated) {
                $currentRevisions = $pettyCash->revisions()->count();
                if ($currentRevisions >= 3) {
                    throw new \Exception('Maximum revisions (3) reached.');
                }

                $revision = new Revision([
                    'revision_times' => $currentRevisions + 1,
                    'user_id' => Auth::user()->id,
                    'revision_status_id' => 1,
                    'remark' => $validated['remark'],
                    'revision_at' => now(),
                ]);
                $pettyCash->revisions()->save($revision);

                $waittingRevisionStatus = DocumentStatus::where('slug', 'waiting-revision')->first();
                if ($waittingRevisionStatus) {
                    $pettyCash->update(['document_status_id' => $waittingRevisionStatus->id]);
                }

                $this->notificationService->notifyRevisionRequested($pettyCash, $revision);
            });

            return redirect()->route('accounting-staff.petty-cash.show', $pettyCash)->with('success', 'Revision request added successfully.');
        } catch (\Exception $e) {
            return redirect()->route('accounting-staff.petty-cash.show', $pettyCash)->with('error', $e->getMessage());
        }
    }

    public function approve(Request $request, PettyCash $pettyCash)
    {
        $validated = $request->validate(['remark' => 'nullable|string|max:1000']);

        try {
            DB::transaction(function () use ($pettyCash, $validated) {
                $pendingRevisions = $pettyCash->revisions()->where('revision_status_id', '!=', 2)->count();
                if ($pendingRevisions > 0) {
                    throw new \Exception('Cannot approve while there are pending revisions.');
                }

                $userRole = ApprovalRole::where('sequence', 1)->first();
                if (!$userRole) throw new \Exception('Approval role not found.');
                if ($this->approvalService->hasRejected($pettyCash)) throw new \Exception('Approval process halted: document already rejected.');
                if (!$this->approvalService->isValidApprovalSequence($pettyCash, $userRole->id)) throw new \Exception('This document is not ready for your approval or has already been processed.');

                $alreadyApproved = $pettyCash->approvals()->where('approval_role_id', $userRole->id)->where('approval_status_id', 1)->exists();
                if ($alreadyApproved) throw new \Exception('This document is already approved by your role.');

                $approval = new Approval([
                    'user_id' => Auth::user()->id,
                    'approval_role_id' => $userRole->id,
                    'approval_status_id' => 1,
                    'remark' => $validated['remark'] ?? null,
                    'approval_at' => now(),
                ]);

                $nextStatus = DocumentStatus::where('slug', 'waiting-approval-manager')->first();
                if ($nextStatus) {
                    $pettyCash->update(['document_status_id' => $nextStatus->id]);
                }
                $pettyCash->approvals()->save($approval);
            });

            $this->notificationService->notifyDocumentApproved($pettyCash, Auth::user(), 'Accounting Staff', $validated['remark'] ?? null, 1);

            return redirect()->route('accounting-staff.petty-cash.show', $pettyCash)->with('success', 'Petty Cash approved successfully.');
        } catch (\Exception $e) {
            return redirect()->route('accounting-staff.petty-cash.show', $pettyCash)->with('error', $e->getMessage());
        }
    }

    public function reject(Request $request, PettyCash $pettyCash)
    {
        $validated = $request->validate(['remark' => 'required|string|max:1000']);

        try {
            DB::transaction(function () use ($pettyCash, $validated) {
                $userRole = ApprovalRole::where('sequence', 1)->first();
                if (!$userRole) throw new \Exception('Approval role not found.');

                $pendingRevisions = $pettyCash->revisions()->where('revision_status_id', '!=', 2)->count();
                if ($pendingRevisions > 0) throw new \Exception('Cannot reject while there are pending revisions.');
                if ($this->approvalService->hasRejected($pettyCash)) throw new \Exception('Cannot reject: document has already been rejected.');
                if (!$this->approvalService->isValidApprovalSequence($pettyCash, $userRole->id)) throw new \Exception('This document is not ready for your rejection or has already been processed.');

                $alreadyRejected = $pettyCash->approvals()->where('approval_role_id', $userRole->id)->where('approval_status_id', 2)->exists();
                if ($alreadyRejected) throw new \Exception('This document is already rejected by your role.');

                $approval = new Approval([
                    'user_id' => Auth::user()->id,
                    'approval_role_id' => $userRole->id,
                    'approval_status_id' => 2,
                    'remark' => $validated['remark'],
                    'approval_at' => now(),
                ]);
                $pettyCash->approvals()->save($approval);

                $rejectedStatus = DocumentStatus::where('slug', 'rejected')->first();
                if ($rejectedStatus) {
                    $pettyCash->update(['document_status_id' => $rejectedStatus->id]);
                }
            });

            $this->notificationService->notifyDocumentRejected($pettyCash, Auth::user(), 'Accounting Staff', $validated['remark'], 1);

            return redirect()->route('accounting-staff.petty-cash.show', $pettyCash)->with('success', 'Petty Cash rejected successfully.');
        } catch (\Exception $e) {
            return redirect()->route('accounting-staff.petty-cash.show', $pettyCash)->with('error', $e->getMessage());
        }
    }

    /**
     * Record hardfile receipt for petty cash
     */
    public function receiveHardfile(Request $request, PettyCash $pettyCash)
    {
        try {
            if ($pettyCash->hardfile_received_at) {
                throw new \Exception('Hardfile has already been received for this document.');
            }

            $staffRole = ApprovalRole::where('sequence', 1)->first();
            if (!$staffRole) {
                throw new \Exception('Approval role not found.');
            }

            $staffApproval = $pettyCash->approvals()
                ->where('approval_role_id', $staffRole->id)
                ->where('approval_status_id', 1)
                ->exists();

            if (!$staffApproval) {
                throw new \Exception('Cannot receive hardfile: document has not been approved by Accounting Staff yet.');
            }

            $pettyCash->update([
                'hardfile_received_at' => now(),
                'hardfile_received_by' => Auth::id(),
            ]);

            $this->notificationService->notifyHardfileReceived($pettyCash, Auth::user());

            return redirect()->route('accounting-staff.petty-cash.show', $pettyCash)
                ->with('success', 'Hardfile received successfully.');
        } catch (\Exception $e) {
            return redirect()->route('accounting-staff.petty-cash.show', $pettyCash)
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Process payment and upload receipt
     */
    public function processPayment(Request $request, PettyCash $pettyCash)
    {
        $request->validate([
            'payment_receipt' => 'required|file|mimes:png,jpg,jpeg,pdf|max:500',
        ]);

        try {
            if ($pettyCash->is_paid) {
                throw new \Exception('Payment has already been processed for this document.');
            }

            if (!$pettyCash->hardfile_received_at) {
                throw new \Exception('Cannot process payment: hardfile receipt has not been recorded yet.');
            }

            $fullyApprovedStatus = DocumentStatus::where('slug', 'fully-approved')->first();
            if (!$fullyApprovedStatus || $pettyCash->document_status_id !== $fullyApprovedStatus->id) {
                throw new \Exception('Cannot process payment: document is not fully approved yet.');
            }

            if ($request->hasFile('payment_receipt')) {
                $path = $request->file('payment_receipt')->store('payments/petty_cash', 'public');
                
                $pettyCash->update([
                    'is_paid' => true,
                    'paid_at' => now(),
                    'paid_by' => Auth::id(),
                    'payment_receipt_path' => $path,
                ]);

                if (method_exists($this->notificationService, 'notifyDocumentPaid')) {
                    $this->notificationService->notifyDocumentPaid($pettyCash, Auth::user());
                }
            }

            return redirect()->route('accounting-staff.petty-cash.show', $pettyCash)
                ->with('success', 'Payment processed successfully.');
        } catch (\Exception $e) {
            return redirect()->route('accounting-staff.petty-cash.show', $pettyCash)
                ->with('error', $e->getMessage());
        }
    }
}
