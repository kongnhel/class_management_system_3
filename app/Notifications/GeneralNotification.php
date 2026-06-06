<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class GeneralNotification extends Notification
{
    use Queueable;

    protected $data;

    /**
     * Create a new notification instance.
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // ប្រើ database (អាចបន្ថែម 'mail', 'broadcast' ប្រសិនបើត្រូវការ)
        return ['database'];
    }

    /**
     * Store notification in database.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'from_user_id' => $this->data['from_user_id'] ?? null,
            'from_user_name' => $this->data['from_user_name'] ?? null,
            'title' => $this->data['title'] ?? null,
            'message' => $this->data['message'] ?? null,
            'batch_uuid' => $this->data['batch_uuid'] ?? null,
            'recipient_ids' => $this->data['recipient_ids'] ?? [],
        ];
    }
}
