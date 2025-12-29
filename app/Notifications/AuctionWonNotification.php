<?php

namespace App\Notifications;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AuctionWonNotification extends Notification
{
    use Queueable;

    protected $invoice;

    /**
     * Create a new notification instance.
     */
    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $vehicleName = $this->invoice->item_name ?? '[VEHICLE_NAME]';
        return [
            'type' => 'auction_won',
            'message' => "Congratulations! You won {$vehicleName}. Payment is now required.",
            'invoice_id' => $this->invoice->id,
            'item_name' => $vehicleName,
            'item_id' => $this->invoice->item_id,
            'link' => route('buyer.payment.checkout-single', $this->invoice->id),
        ];
    }
}
