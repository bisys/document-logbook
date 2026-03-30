<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class DocumentRejectedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $document;
    public $documentType;
    public $rejector;
    public $rejectorRoleName;
    public $remark;

        public $url;
public function __construct(Model $document, string $documentType, User $rejector, string $rejectorRoleName, ?string $remark = null, string $url)
    {
        $this->document = $document;
        $this->documentType = $documentType;
        $this->rejector = $rejector;
        $this->rejectorRoleName = $rejectorRoleName;
        $this->remark = $remark;
            $this->url = $url;
}

    public function build()
    {
        return $this->subject("[Document Logbook] Document Rejected: {$this->documentType} - {$this->document->number}")
            ->view('emails.document-rejected');
    }
}
