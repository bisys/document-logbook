<?php

namespace App\Http\Controllers\AccountingGM;

use App\Http\Controllers\Controller;
use App\Models\PettyCash;
use App\Models\Approval;
use App\Models\DocumentStatus;
use App\Models\ApprovalRole;
use App\Services\ApprovalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PettyCashController extends Controller
{
    protected $approvalService;
    public function __construct(ApprovalService $approvalService) { $this->approvalService = $approvalService; }

    public function index(Request $request)
    {
        $allDocuments = PettyCash::with(['user.department', 'user.position', 'costCenter', 'status', 'revisions', 'approvals.role', 'approvals.user'])
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
        return view('accounting_gm.petty_cash.index', compact('pettyCashes', 'statusFilter', 'counts'));
    }

    public function show(PettyCash $pettyCash)
    {
        $pettyCash->load(['user.department', 'user.position', 'costCenter', 'status', 'revisions.user.department', 'revisions.status', 'approvals.user.department', 'approvals.role', 'approvals.status']);
        $approvalChain = $this->approvalService->getApprovalChain($pettyCash);
        return view('accounting_gm.petty_cash.show', compact('pettyCash', 'approvalChain'));
    }

    public function approve(Request $request, PettyCash $pettyCash)
    {
        $validated = $request->validate(['remark' => 'nullable|string|max:1000']);
        try {
            DB::transaction(function () use ($pettyCash, $validated) {
                $userRole = ApprovalRole::where('sequence', 3)->first();
                if (!$userRole) throw new \Exception('Approval role not found.');
                if ($this->approvalService->hasRejected($pettyCash)) throw new \Exception('Document already rejected.');
                if (!$this->approvalService->isValidApprovalSequence($pettyCash, $userRole->id)) throw new \Exception('Not ready for your approval.');
                if ($pettyCash->approvals()->where('approval_role_id', $userRole->id)->where('approval_status_id', 1)->exists()) throw new \Exception('Already approved by your role.');

                $approval = new Approval(['user_id' => Auth::user()->id, 'approval_role_id' => $userRole->id, 'approval_status_id' => 1, 'remark' => $validated['remark'] ?? null, 'approval_at' => now()]);
                $fullyApprovedStatus = DocumentStatus::where('slug', 'fully-approved')->first();
                if ($fullyApprovedStatus) $pettyCash->update(['document_status_id' => $fullyApprovedStatus->id]);
                $pettyCash->approvals()->save($approval);
            });
            return redirect()->route('accounting-gm.petty-cash.show', $pettyCash)->with('success', 'Petty Cash fully approved.');
        } catch (\Exception $e) {
            return redirect()->route('accounting-gm.petty-cash.show', $pettyCash)->with('error', $e->getMessage());
        }
    }

    public function reject(Request $request, PettyCash $pettyCash)
    {
        $validated = $request->validate(['remark' => 'required|string|max:1000']);
        try {
            DB::transaction(function () use ($pettyCash, $validated) {
                $userRole = ApprovalRole::where('sequence', 3)->first();
                if (!$userRole) throw new \Exception('Approval role not found.');
                if ($this->approvalService->hasRejected($pettyCash)) throw new \Exception('Already rejected.');
                if (!$this->approvalService->isValidApprovalSequence($pettyCash, $userRole->id)) throw new \Exception('Not ready for rejection.');

                $approval = new Approval(['user_id' => Auth::user()->id, 'approval_role_id' => $userRole->id, 'approval_status_id' => 2, 'remark' => $validated['remark'], 'approval_at' => now()]);
                $pettyCash->approvals()->save($approval);
                $rejectedStatus = DocumentStatus::where('slug', 'rejected')->first();
                if ($rejectedStatus) $pettyCash->update(['document_status_id' => $rejectedStatus->id]);
            });
            return redirect()->route('accounting-gm.petty-cash.show', $pettyCash)->with('success', 'Petty Cash rejected.');
        } catch (\Exception $e) {
            return redirect()->route('accounting-gm.petty-cash.show', $pettyCash)->with('error', $e->getMessage());
        }
    }
}
