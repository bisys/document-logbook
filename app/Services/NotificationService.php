<?php

namespace App\Services;

use App\Mail\DocumentSubmittedMail;
use App\Mail\RevisionRequestedMail;
use App\Mail\RevisionSubmittedMail;
use App\Mail\DocumentApprovedMail;
use App\Mail\DocumentRejectedMail;
use App\Models\User;
use App\Models\Revision;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class NotificationService
{
    /**
     * Document type labels for email display
     */
    protected $documentTypeLabels = [
        'App\Models\SupplierPayment' => 'Supplier Payment',
        'App\Models\PettyCash' => 'Petty Cash',
        'App\Models\InternationalTrip' => 'International Trip',
        'App\Models\CashAdvanceDraw' => 'Cash Advance Draw',
        'App\Models\CashAdvanceRealization' => 'Cash Advance Realization',
    ];

    /**
     * Get document type label from model
     */
    protected function getDocumentType(Model $document): string
    {
        return $this->documentTypeLabels[get_class($document)] ?? class_basename($document);
    }

    /**
     * Get users by role slug
     */
    protected function getUsersByRole(string $roleSlug)
    {
        return User::whereHas('role', function ($query) use ($roleSlug) {
            $query->where('slug', $roleSlug);
        })->get();
    }

    /**
     * Notify accounting staff when a user submits a new document
     */
    public function notifyDocumentSubmitted(Model $document): void
    {
        try {
            $documentType = $this->getDocumentType($document);
            $document->load('user.department');

            $staffUsers = $this->getUsersByRole('accounting-staff');
            $slug = Str::kebab(class_basename($document));
            $url = route('accounting-staff.' . $slug . '.show', $document->id);

            foreach ($staffUsers as $staff) {
                if ($staff->email) {
                    Mail::to($staff->email)->queue(new DocumentSubmittedMail($document, $documentType, $url));
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to send document submitted notification: ' . $e->getMessage());
        }
    }

    /**
     * Notify the document owner when accounting staff requests a revision
     */
    public function notifyRevisionRequested(Model $document, Revision $revision): void
    {
        try {
            $documentType = $this->getDocumentType($document);
            $document->load('user');
            $revision->load('user');

            $owner = $document->user;

            if ($owner && $owner->email) {
                $slug = Str::kebab(class_basename($document));
                $url = route('user.' . $slug . '.show', $document->id);
                Mail::to($owner->email)->queue(new RevisionRequestedMail($document, $documentType, $revision, $url));
            }
        } catch (\Exception $e) {
            Log::error('Failed to send revision requested notification: ' . $e->getMessage());
        }
    }

    /**
     * Notify accounting staff when a user submits a revision
     */
    public function notifyRevisionSubmitted(Model $document): void
    {
        try {
            $documentType = $this->getDocumentType($document);
            $document->load('user.department');

            $staffUsers = $this->getUsersByRole('accounting-staff');
            $slug = Str::kebab(class_basename($document));
            $url = route('accounting-staff.' . $slug . '.show', $document->id);

            foreach ($staffUsers as $staff) {
                if ($staff->email) {
                    Mail::to($staff->email)->queue(new RevisionSubmittedMail($document, $documentType, $url));
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to send revision submitted notification: ' . $e->getMessage());
        }
    }

    /**
     * Notify relevant stakeholders when a document is approved
     */
    public function notifyDocumentApproved(Model $document, User $approver, string $approverRoleName, ?string $remark = null, int $approvalSequence = 1): void
    {
        try {
            $documentType = $this->getDocumentType($document);
            $document->load('user');
            $owner = $document->user;

            $recipients = collect();

            // Always notify the document owner
            if ($owner && $owner->email) {
                $recipients->push(['email' => $owner->email, 'role' => 'user']);
            }

            // Determine additional recipients based on approval level
            switch ($approvalSequence) {
                case 1: // Staff approved → notify accounting managers
                    $managers = $this->getUsersByRole('accounting-manager');
                    foreach ($managers as $manager) {
                        if ($manager->email) {
                            $recipients->push(['email' => $manager->email, 'role' => 'accounting-manager']);
                        }
                    }
                    break;
                case 2: // Manager approved → notify accounting GMs
                    $gms = $this->getUsersByRole('accounting-gm');
                    foreach ($gms as $gm) {
                        if ($gm->email) {
                            $recipients->push(['email' => $gm->email, 'role' => 'accounting-gm']);
                        }
                    }
                    break;
                case 3: // GM approved → only notify user (already added above)
                    break;
            }

            // Send to all unique recipients
            $slug = Str::kebab(class_basename($document));
            $recipients->unique('email')->each(function ($recipient) use ($document, $documentType, $approver, $approverRoleName, $remark, $slug) {
                $url = route($recipient['role'] . '.' . $slug . '.show', $document->id);
                Mail::to($recipient['email'])->queue(new DocumentApprovedMail($document, $documentType, $approver, $approverRoleName, $remark, $url));
            });
        } catch (\Exception $e) {
            Log::error('Failed to send document approved notification: ' . $e->getMessage());
        }
    }

    /**
     * Notify relevant stakeholders when a document is rejected
     */
    public function notifyDocumentRejected(Model $document, User $rejector, string $rejectorRoleName, ?string $remark = null, int $approvalSequence = 1): void
    {
        try {
            $documentType = $this->getDocumentType($document);
            $document->load('user');
            $owner = $document->user;

            $recipients = collect();

            // Always notify the document owner
            if ($owner && $owner->email) {
                $recipients->push(['email' => $owner->email, 'role' => 'user']);
            }

            // Determine additional recipients based on rejection level
            switch ($approvalSequence) {
                case 1: // Staff rejected → notify accounting managers
                    $managers = $this->getUsersByRole('accounting-manager');
                    foreach ($managers as $manager) {
                        if ($manager->email) {
                            $recipients->push(['email' => $manager->email, 'role' => 'accounting-manager']);
                        }
                    }
                    break;
                case 2: // Manager rejected → notify accounting GMs
                    $gms = $this->getUsersByRole('accounting-gm');
                    foreach ($gms as $gm) {
                        if ($gm->email) {
                            $recipients->push(['email' => $gm->email, 'role' => 'accounting-gm']);
                        }
                    }
                    break;
                case 3: // GM rejected → only notify user (already added above)
                    break;
            }

            // Send to all unique recipients
            $slug = Str::kebab(class_basename($document));
            $recipients->unique('email')->each(function ($recipient) use ($document, $documentType, $rejector, $rejectorRoleName, $remark, $slug) {
                $url = route($recipient['role'] . '.' . $slug . '.show', $document->id);
                Mail::to($recipient['email'])->queue(new DocumentRejectedMail($document, $documentType, $rejector, $rejectorRoleName, $remark, $url));
            });
        } catch (\Exception $e) {
            Log::error('Failed to send document rejected notification: ' . $e->getMessage());
        }
    }
}
