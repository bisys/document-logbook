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
use Illuminate\Support\Facades\Notification;
use App\Notifications\DocumentNotification;
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

            Notification::send($staffUsers, new DocumentNotification([
                'title' => 'New Document Submitted',
                'message' => 'A new ' . $documentType . ' has been submitted for your review by ' . ($document->user->name ?? 'User') . '.',
                'url' => $url,
                'icon' => 'fas fa-file-alt',
                'icon_bg' => 'bg-primary'
            ]));
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

            if ($owner) {
                $slug = Str::kebab(class_basename($document));
                $url = route('user.' . $slug . '.show', $document->id);
                
                if ($owner->email) {
                    Mail::to($owner->email)->queue(new RevisionRequestedMail($document, $documentType, $revision, $url));
                }

                Notification::send($owner, new DocumentNotification([
                    'title' => 'Revision Requested',
                    'message' => 'A revision has been requested for your ' . $documentType . '.',
                    'url' => $url,
                    'icon' => 'fas fa-edit',
                    'icon_bg' => 'bg-warning'
                ]));
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

            Notification::send($staffUsers, new DocumentNotification([
                'title' => 'Revision Submitted',
                'message' => 'A revision for ' . $documentType . ' has been submitted by ' . ($document->user->name ?? 'User') . '.',
                'url' => $url,
                'icon' => 'fas fa-reply',
                'icon_bg' => 'bg-info'
            ]));
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
            if ($owner) {
                $recipients->push(['user' => $owner, 'role' => 'user']);
            }

            // Determine additional recipients based on approval level
            switch ($approvalSequence) {
                case 1: // Staff approved → notify accounting managers
                    $managers = $this->getUsersByRole('accounting-manager');
                    foreach ($managers as $manager) {
                        $recipients->push(['user' => $manager, 'role' => 'accounting-manager']);
                    }
                    break;
                case 2: // Manager approved → notify accounting GMs
                    $gms = $this->getUsersByRole('accounting-gm');
                    foreach ($gms as $gm) {
                        $recipients->push(['user' => $gm, 'role' => 'accounting-gm']);
                    }
                    break;
                case 3: // GM approved → only notify user (already added above)
                    break;
            }

            // Send to all unique recipients
            $slug = Str::kebab(class_basename($document));
            $recipients->unique(function ($item) {
                return $item['user']->id;
            })->each(function ($recipient) use ($document, $documentType, $approver, $approverRoleName, $remark, $slug) {
                $url = route($recipient['role'] . '.' . $slug . '.show', $document->id);
                $user = $recipient['user'];

                if ($user->email) {
                    Mail::to($user->email)->queue(new DocumentApprovedMail($document, $documentType, $approver, $approverRoleName, $remark, $url));
                }

                Notification::send($user, new DocumentNotification([
                    'title' => 'Document Approved',
                    'message' => 'The ' . $documentType . ' has been approved by ' . $approverRoleName . '.',
                    'url' => $url,
                    'icon' => 'fas fa-check-circle',
                    'icon_bg' => 'bg-success'
                ]));
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
            if ($owner) {
                $recipients->push(['user' => $owner, 'role' => 'user']);
            }

            // Determine additional recipients based on rejection level
            switch ($approvalSequence) {
                case 1: // Staff rejected → notify accounting managers
                    $managers = $this->getUsersByRole('accounting-manager');
                    foreach ($managers as $manager) {
                        $recipients->push(['user' => $manager, 'role' => 'accounting-manager']);
                    }
                    break;
                case 2: // Manager rejected → notify accounting GMs
                    $gms = $this->getUsersByRole('accounting-gm');
                    foreach ($gms as $gm) {
                        $recipients->push(['user' => $gm, 'role' => 'accounting-gm']);
                    }
                    break;
                case 3: // GM rejected → only notify user (already added above)
                    break;
            }

            // Send to all unique recipients
            $slug = Str::kebab(class_basename($document));
            $recipients->unique(function ($item) {
                return $item['user']->id;
            })->each(function ($recipient) use ($document, $documentType, $rejector, $rejectorRoleName, $remark, $slug) {
                $url = route($recipient['role'] . '.' . $slug . '.show', $document->id);
                $user = $recipient['user'];

                if ($user->email) {
                    Mail::to($user->email)->queue(new DocumentRejectedMail($document, $documentType, $rejector, $rejectorRoleName, $remark, $url));
                }

                Notification::send($user, new DocumentNotification([
                    'title' => 'Document Rejected',
                    'message' => 'The ' . $documentType . ' has been rejected by ' . $rejectorRoleName . '.',
                    'url' => $url,
                    'icon' => 'fas fa-times-circle',
                    'icon_bg' => 'bg-danger'
                ]));
            });
        } catch (\Exception $e) {
            Log::error('Failed to send document rejected notification: ' . $e->getMessage());
        }
    }
}
