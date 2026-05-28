<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class HardfileSubmittedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $document;
    public $documentType;
    public $submitter;
    public $url;

    public function __construct(Model $document, string $documentType, User $submitter, string $url)
    {
        $this->document = $document;
        $this->documentType = $documentType;
        $this->submitter = $submitter;
        $this->url = $url;
    }

    public function build()
    {
        return $this->subject("[Document Logbook] Hardfile Submitted: {$this->documentType} - {$this->document->number}")
            ->view('emails.hardfile-submitted');
    }
}
