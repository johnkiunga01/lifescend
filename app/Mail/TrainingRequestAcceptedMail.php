<?php

namespace App\Mail;

use App\Models\TrainingRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TrainingRequestAcceptedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public TrainingRequest $trainingRequest,
        public string $customMessage,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Training Request Approved - ' . $this->trainingRequest->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            html: 'mail.training-request-accepted',
        );
    }
}
