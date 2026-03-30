<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Database\Eloquent\Model;
use App\Models\Revision;

class RevisionRequestedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $document;
    public $documentType;
    public $revision;
    public $requester;

        public $url;
public function __construct(Model $document, string $documentType, Revision $revision, string $url)
    {
        $this->document = $document;
        $this->documentType = $documentType;
        $this->revision = $revision;
                $this->url = $url;
$this->requester = $revision->user;
    }

    public function build()
    {
        return $this->subject("[Document Logbook] Revision Requested: {$this->documentType} - {$this->document->number}")
            ->view('emails.revision-requested');
    }
}
