<?php

namespace App\Services;

use App\Models\ApprovalRole;
use App\Models\Approval;
use Illuminate\Database\Eloquent\Model;

class ApprovalService
{
    /**
     * Get all approval roles ordered by sequence
     */
    public function getAllApprovalRoles()
    {
        return ApprovalRole::orderBy('sequence')->get();
    }

    /**
     * Get the next required approval role for a document
     * Returns the approval role that still needs to be done
     */
    public function getNextApprovalRole(Model $document)
    {
        // if any rejection exists the workflow should stop
        if ($this->hasRejected($document)) {
            return null;
        }

        $allRoles = $this->getAllApprovalRoles();

        foreach ($allRoles as $role) {
            $approval = $this->getApprovalByRole($document, $role->id);

            if (!$approval) {
                // This role hasn't approved yet
                return $role;
            }

            // if there is an approval record but it's not approved (eg rejected) we stop
            if ($approval && !$this->isApproved($approval)) {
                return null;
            }
        }

        // All roles have approved
        return null;
    }

    /**
     * Get the next approval role sequence number for a document
     */
    public function getNextApprovalSequence(Model $document)
    {
        $nextRole = $this->getNextApprovalRole($document);
        return $nextRole ? $nextRole->sequence : null;
    }

    /**
     * Get approval by specific role for a document
     */
    public function getApprovalByRole(Model $document, $approvalRoleId)
    {
        return $document->approvals()
            ->where('approval_role_id', $approvalRoleId)
            ->first();
    }

    /**
     * Check if all previous approval roles have approved
     * Used to validate if current role can approve
     */
    public function allPreviousApprovalsComplete(Model $document, $approvalRoleId)
    {
        $allRoles = $this->getAllApprovalRoles();
        $currentRoleSequence = $allRoles->where('id', $approvalRoleId)->first()?->sequence;

        if (!$currentRoleSequence) {
            return false;
        }

        // Check if all roles with lower sequence have approved
        foreach ($allRoles as $role) {
            if ($role->sequence >= $currentRoleSequence) {
                break;
            }

            $approval = $this->getApprovalByRole($document, $role->id);

            // If any previous role hasn't approved, return false
            if (!$approval || !$this->isApproved($approval)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if an approval record is approved status (not rejected, etc)
     */
    public function isApproved(Approval $approval)
    {
        // Check if approval status is 'approved'
        // Assuming approval_status_id 1 = approved, 2 = rejected, etc
        // You may need to adjust based on your ApprovalStatus table values
        return $approval->approval_status_id === 1;
    }

    /**
     * Check if the approval sequence is valid for this document
     * Returns true if this role is the next role that should approve
     */
    public function isValidApprovalSequence(Model $document, $approvalRoleId)
    {
        // if there has been any rejection already, no further approvals/rejections allowed
        if ($this->hasRejected($document)) {
            return false;
        }

        $nextApprovalRole = $this->getNextApprovalRole($document);

        // If all roles have approved or workflow stopped by rejection, no more approvals needed
        if (!$nextApprovalRole) {
            return false;
        }

        // This role can only approve if it's the next role in sequence
        return $nextApprovalRole->id === $approvalRoleId;
    }

    /**
     * Get all completed approvals for a document in sequence order
     */
    public function getCompletedApprovals(Model $document)
    {
        return $document->approvals()
            ->with(['role', 'status', 'user'])
            ->where('approval_status_id', 1) // Only approved
            ->join('approval_roles', 'approvals.approval_role_id', '=', 'approval_roles.id')
            ->orderBy('approval_roles.sequence', 'asc')
            ->select('approvals.*')
            ->get();
    }

    /**
     * Get approval data with role information for a document
     */
    public function getApprovalChain(Model $document)
    {
        $allRoles = $this->getAllApprovalRoles();
        $approvals = $document->approvals()->with(['role', 'status', 'user'])->get();

        $chain = [];

        foreach ($allRoles as $role) {
            $approval = $approvals->where('approval_role_id', $role->id)->first();

            $chain[] = [
                'role' => $role,
                'approval' => $approval,
                'status' => $approval ? ($approval->approval_status_id === 1 ? 'approved' : 'rejected') : 'pending',
                'sequence' => $role->sequence
            ];
        }

        return $chain;
    }

    /**
     * helper to detect if any approval record has rejected the document
     */
    public function hasRejected(Model $document)
    {
        // approval_status_id 2 represents rejected in the standard setup
        return $document->approvals()
            ->where('approval_status_id', 2)
            ->exists();
    }

    /**
     * Get documents waiting for specific role approval
     */
    public function getDocumentsWaitingForRole($approvalRoleId)
    {
        // This is a generic method that would need to be called differently for different document types
        // For SupplierPayment, this would be called in the controller
    }
}
