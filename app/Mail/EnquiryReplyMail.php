<?php

namespace App\Mail;

use App\Models\EventEnquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EnquiryReplyMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  \App\Models\EventEnquiryAttachment[]  $attachments
     */
    public function __construct(
        public EventEnquiry $enquiry,
        public string $replyMessage,
        public array $attachments = [],
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Re: Your Training Enquiry - ' . ($this->enquiry->event->name ?? 'Lifescend'),
        );
    }

    public function content(): Content
    {
        return new Content(
            html: 'mail.enquiry-reply',
        );
    }

    public function attachments(): array
    {
        return array_map(
            fn ($attachment) => Attachment::fromStorageDisk('local', $attachment->path)
                ->as($attachment->original_name)
                ->withMime($attachment->mime ?: 'application/octet-stream'),
            $this->attachments
        );
    }
}
