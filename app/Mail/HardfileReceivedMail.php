<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class HardfileReceivedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $document;
    public $documentType;
    public $receiver;
    public $url;

    public function __construct(Model $document, string $documentType, User $receiver, string $url)
    {
        $this->document = $document;
        $this->documentType = $documentType;
        $this->receiver = $receiver;
        $this->url = $url;
    }

    public function build()
    {
        return $this->subject("[Document Logbook] Hardfile Received: {$this->documentType} - {$this->document->number}")
            ->view('emails.hardfile-received');
    }
}
