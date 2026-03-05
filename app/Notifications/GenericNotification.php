<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GenericNotification extends Notification
{
    use Queueable;

    protected $type;
    protected $message;
    protected $data;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $type, string $message, array $data = [])
    {
        $this->type = $type;
        $this->message = $message;
        $this->data = $data;
    }

    /**
     * Get the notification's delivery channels.
     * Database = notification center; Mail = email sync per requirement.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Build the mail representation (email sync with notification center).
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('CayMark: ' . \Illuminate\Support\Str::limit($this->message, 50))
            ->line($this->message);

        if (!empty($this->data['link'])) {
            $mail->action('View details', $this->data['link']);
        }

        $mail->line('You received this notification because you have an account on CayMark.');

        return $mail;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return array_merge([
            'type' => $this->type,
            'message' => $this->message,
        ], $this->data);
    }
}

