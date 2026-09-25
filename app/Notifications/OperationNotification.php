<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OperationNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $action,
        public string $label,
        public string $description,
        public ?string $url = null,
        public array $metadata = [],
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return array_merge([
            'action' => $this->action,
            'label' => $this->label,
            'description' => $this->description,
            'url' => $this->url,
        ], $this->metadata);
    }
}
