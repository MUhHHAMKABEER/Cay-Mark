<?php

namespace App\Services\Messaging;

use App\Models\PostAuctionThread;
use App\Models\User;
use App\Notifications\GenericNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MessagingNotifier
{
    /**
     * Notify CayMark admins that a Messaging Center thread has been flagged.
     */
    public function notifyAdmin(PostAuctionThread $thread): void
    {
        $thread->loadMissing(['listing', 'buyer', 'seller', 'invoice']);

        $listing = $thread->listing;
        $listingTitle = trim(implode(' ', array_filter([
            $listing?->year,
            $listing?->make,
            $listing?->model,
        ])));

        $payload = [
            'reason' => $thread->flag_reason,
            'reasonLabel' => $this->reasonLabel($thread->flag_reason),
            'listingTitle' => $listingTitle ?: 'Listing #'.$thread->listing_id,
            'invoiceNumber' => $thread->invoice?->invoice_number ?? $thread->invoice_id,
            'buyerName' => $thread->buyer?->name ?? '—',
            'buyerEmail' => $thread->buyer?->email ?? '—',
            'sellerName' => $thread->seller?->name ?? '—',
            'sellerEmail' => $thread->seller?->email ?? '—',
            'exchangesCount' => (int) $thread->exchanges_count,
            'maxExchanges' => PostAuctionThread::MAX_EXCHANGES,
            'firstExchangeAt' => optional($thread->first_exchange_at)?->format('M d, Y g:i A'),
            'lastExchangeAt' => optional($thread->last_exchange_at)?->format('M d, Y g:i A'),
            'reviewUrl' => route('admin.messaging.flags.show', $thread->id),
        ];

        $admins = User::where('role', 'admin')->get();

        foreach ($admins as $admin) {
            try {
                $admin->notify(new GenericNotification(
                    'messaging_thread_flagged',
                    sprintf('Messaging thread #%d flagged: %s', $thread->id, $payload['reasonLabel']),
                    [
                        'thread_id' => $thread->id,
                        'invoice_id' => $thread->invoice_id,
                        'reason' => $thread->flag_reason,
                        'link' => $payload['reviewUrl'],
                    ]
                ));
            } catch (\Throwable $e) {
                Log::error('MessagingNotifier in-app failed: '.$e->getMessage(), [
                    'admin_id' => $admin->id,
                    'thread_id' => $thread->id,
                ]);
            }
        }

        try {
            $recipients = $admins->pluck('email')->filter()->values()->all();
            if (empty($recipients)) {
                $recipients = [config('support.inbox')];
            }

            Mail::send(
                'emails.caymark.messaging-thread-flagged',
                $payload,
                function ($message) use ($recipients, $payload) {
                    $message->to($recipients)
                        ->subject('CayMark · Messaging thread flagged ('.$payload['reasonLabel'].')')
                        ->from(config('support.mail_from'), config('support.mail_from_name'));
                }
            );
        } catch (\Throwable $e) {
            Log::error('MessagingNotifier email failed: '.$e->getMessage(), [
                'thread_id' => $thread->id,
            ]);
        }
    }

    protected function reasonLabel(?string $reason): string
    {
        return match ($reason) {
            PostAuctionThread::FLAG_MAX_EXCHANGES => 'Maximum exchanges reached (3)',
            PostAuctionThread::FLAG_TIMEOUT_48H => 'No agreement after 48 hours',
            PostAuctionThread::FLAG_MANUAL => 'Buyer/seller requested CayMark assistance',
            default => 'Messaging Center review',
        };
    }
}
