<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Database\Eloquent\Model;

class RevisionSubmittedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $document;
    public $documentType;
    public $submitter;

        public $url;
public function __construct(Model $document, string $documentType, string $url)
    {
        $this->document = $document;
        $this->documentType = $documentType;
                $this->url = $url;
$this->submitter = $document->user;
    }

    public function build()
    {
        return $this->subject("[Document Logbook] Revision Submitted: {$this->documentType} - {$this->document->number}")
            ->view('emails.revision-submitted');
    }
}
