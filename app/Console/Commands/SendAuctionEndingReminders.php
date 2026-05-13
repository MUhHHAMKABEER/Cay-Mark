<?php

namespace App\Console\Commands;

use App\Models\Listing;
use App\Models\ListingAuctionReminderDispatch;
use App\Models\User;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendAuctionEndingReminders extends Command
{
    protected $signature = 'caymark:send-auction-ending-reminders';

    protected $description = 'Send auction ending reminders (24h / 1h): seller email + in-app; buyer bidders + watchlist in-app; deduped per listing/window/user.';

    public function handle(): int
    {
        $now = Carbon::now();
        $notificationService = new NotificationService();

        $this->processWindow(
            $now,
            '24_hour',
            [
                $now->copy()->addHours(24)->subMinutes(5),
                $now->copy()->addHours(24)->addMinutes(5),
            ],
            'auction-ending-soon-24h',
            'Auction Ending Soon – 24 Hours Left – ',
            '24 hours',
            $notificationService
        );

        $this->processWindow(
            $now,
            '1_hour',
            [
                $now->copy()->addHour()->subMinutes(5),
                $now->copy()->addHour()->addMinutes(5),
            ],
            'auction-ending-soon-1h',
            'Auction Ending Soon – 1 Hour Left – ',
            '1 hour',
            $notificationService
        );

        $this->info('Auction ending reminders processed.');

        return self::SUCCESS;
    }

    /**
     * @param  array{0: Carbon, 1: Carbon}  $between
     */
    protected function processWindow(
        Carbon $now,
        string $window,
        array $between,
        string $emailTemplate,
        string $emailSubjectPrefix,
        string $endWindowLabel,
        NotificationService $notificationService
    ): void {
        $listings = Listing::query()
            ->where('listing_method', 'auction')
            ->where('status', 'approved')
            ->whereNotNull('auction_end_time')
            ->whereBetween('auction_end_time', $between)
            ->with(['seller', 'bids.user', 'watchlistedBy'])
            ->get();

        foreach ($listings as $listing) {
            $seller = $listing->seller;
            if (! $seller) {
                continue;
            }

            $subjectVehicle = trim(($listing->year ?? '').' '.($listing->make ?? '').' '.($listing->model ?? '')) ?: '[VEHICLE_NAME]';

            // --- Seller email (once per listing/window) ---
            if (! ListingAuctionReminderDispatch::wasDispatched($listing->id, $window, 'seller_email', $seller->id)) {
                try {
                    Mail::send('emails.caymark.'.$emailTemplate, [
                        'listing' => $listing,
                        'seller' => $seller,
                    ], function ($message) use ($seller, $emailSubjectPrefix, $subjectVehicle) {
                        $message->to($seller->email, $seller->name)
                            ->subject($emailSubjectPrefix.$subjectVehicle);
                    });
                    ListingAuctionReminderDispatch::record($listing->id, $window, 'seller_email', $seller->id);
                    Log::info('Auction ending seller email sent', ['listing_id' => $listing->id, 'window' => $window]);
                } catch (\Throwable $e) {
                    Log::error('Failed to send auction ending seller email: '.$e->getMessage(), [
                        'listing_id' => $listing->id,
                        'window' => $window,
                    ]);
                }
            }

            // --- Seller in-app ---
            if ($window === '24_hour') {
                if (! ListingAuctionReminderDispatch::wasDispatched($listing->id, $window, 'seller_in_app', $seller->id)) {
                    $notificationService->auctionEndingSoon24h($seller, $listing);
                    ListingAuctionReminderDispatch::record($listing->id, $window, 'seller_in_app', $seller->id);
                }
            } else {
                if (! ListingAuctionReminderDispatch::wasDispatched($listing->id, $window, 'seller_in_app', $seller->id)) {
                    $notificationService->auctionEndingSoon($seller, $listing);
                    ListingAuctionReminderDispatch::record($listing->id, $window, 'seller_in_app', $seller->id);
                }
            }

            $bidderUserIds = $listing->bids()
                ->where('status', 'active')
                ->pluck('user_id')
                ->unique()
                ->filter()
                ->values()
                ->all();

            foreach ($bidderUserIds as $bidderId) {
                if ((int) $bidderId === (int) $seller->id) {
                    continue;
                }
                if (ListingAuctionReminderDispatch::wasDispatched($listing->id, $window, 'buyer_bidder_in_app', (int) $bidderId)) {
                    continue;
                }
                $buyer = User::find($bidderId);
                if (! $buyer) {
                    continue;
                }
                $notificationService->auctionEndingSoonBidder($buyer, $listing, $endWindowLabel);
                ListingAuctionReminderDispatch::record($listing->id, $window, 'buyer_bidder_in_app', (int) $bidderId);
            }

            $bidderSet = array_fill_keys(array_map('intval', $bidderUserIds), true);
            foreach ($listing->watchlistedBy as $watcher) {
                if (isset($bidderSet[(int) $watcher->id])) {
                    continue;
                }
                if ((int) $watcher->id === (int) $seller->id) {
                    continue;
                }
                if (ListingAuctionReminderDispatch::wasDispatched($listing->id, $window, 'buyer_watchlist_in_app', (int) $watcher->id)) {
                    continue;
                }
                $notificationService->auctionEndingSoonWatchlist($watcher, $listing, $endWindowLabel);
                ListingAuctionReminderDispatch::record($listing->id, $window, 'buyer_watchlist_in_app', (int) $watcher->id);
            }
        }
    }
}
