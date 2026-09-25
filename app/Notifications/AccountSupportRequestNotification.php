<?php

namespace App\Notifications;

use App\Models\AccountSupportRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountSupportRequestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public AccountSupportRequest $supportRequest;

    public function __construct(AccountSupportRequest $supportRequest)
    {
        $this->supportRequest = $supportRequest;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Account Support Request - Lifescend')
            ->view('mail.account-support-request', [
                'supportRequest' => $this->supportRequest,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'support_request_id' => $this->supportRequest->id,
            'email' => $this->supportRequest->email,
            'message' => $this->supportRequest->message,
        ];
    }
}
