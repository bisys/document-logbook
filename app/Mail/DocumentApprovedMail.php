<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class DocumentApprovedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $document;
    public $documentType;
    public $approver;
    public $approverRoleName;
    public $remark;

    public $url;
    public $recipientRole;

    public function __construct(Model $document, string $documentType, User $approver, string $approverRoleName, ?string $remark = null, string $url, string $recipientRole = 'user')
    {
        $this->document = $document;
        $this->documentType = $documentType;
        $this->approver = $approver;
        $this->approverRoleName = $approverRoleName;
        $this->remark = $remark;
        $this->url = $url;
        $this->recipientRole = $recipientRole;
    }

    public function build()
    {
        return $this->subject("[Document Logbook] Document Approved: {$this->documentType} - {$this->document->number}")
            ->view('emails.document-approved');
    }
}
