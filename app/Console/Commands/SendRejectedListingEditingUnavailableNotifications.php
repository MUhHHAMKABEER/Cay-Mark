<?php

namespace App\Console\Commands;

use App\Models\Listing;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendRejectedListingEditingUnavailableNotifications extends Command
{
    protected $signature = 'caymark:send-rejected-editing-unavailable-notifications';

    protected $description = 'Send "editing unavailable" in-app notification to sellers whose listing '
        . 'has been rejected for 72+ hours and has not been resubmitted.';

    public function handle(): int
    {
        $cutoff = Carbon::now()->subHours(72);

        // Find listings that:
        //   - are still in "rejected" status (seller never resubmitted via edit,
        //     because updateFromSellerInput() resets status → "pending" on resubmit)
        //   - were rejected at least 72 hours ago
        $listings = Listing::query()
            ->where('status', 'rejected')
            ->whereNotNull('rejected_at')
            ->where('rejected_at', '<=', $cutoff)
            ->with('seller')
            ->get();

        if ($listings->isEmpty()) {
            $this->info('No listings qualify for the editing-unavailable notification.');
            return self::SUCCESS;
        }

        $ns = new NotificationService();

        foreach ($listings as $listing) {
            $seller = $listing->seller;

            if (! $seller) {
                continue;
            }

            // Deduplicate: skip if we already sent this notification for this listing.
            // GenericNotification stores data as JSON; the column contains
            // {"type":"editing_unavailable_listing_rejected","listing_id":N,...}.
            $alreadySent = $seller->notifications()
                ->whereJsonContains('data->type', 'editing_unavailable_listing_rejected')
                ->whereJsonContains('data->listing_id', $listing->id)
                ->exists();

            if ($alreadySent) {
                continue;
            }

            try {
                $ns->editingUnavailableListingRejected($seller, $listing);

                Log::info('Sent editing-unavailable notification', [
                    'listing_id' => $listing->id,
                    'seller_id'  => $seller->id,
                    'rejected_at' => $listing->rejected_at,
                ]);
            } catch (\Throwable $e) {
                Log::error('Failed to send editing-unavailable notification', [
                    'listing_id' => $listing->id,
                    'seller_id'  => $seller->id,
                    'error'      => $e->getMessage(),
                ]);
            }
        }

        $this->info('Editing-unavailable notifications processed.');

        return self::SUCCESS;
    }
}
