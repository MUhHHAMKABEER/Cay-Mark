<?php

namespace App\Console\Commands;

use App\Models\Listing;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SendAuctionEndingReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'caymark:send-auction-ending-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send auction ending soon reminder emails to sellers (24h, 1h)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();

        // 24-hour reminder: auctions ending in 24 hours
        $twentyFourHourReminders = Listing::where('listing_method', 'auction')
            ->where('status', 'approved')
            ->whereNotNull('auction_end_time')
            ->whereBetween('auction_end_time', [
                $now->copy()->addHours(24)->subMinutes(5),
                $now->copy()->addHours(24)->addMinutes(5)
            ])
            ->whereDoesntHave('endingReminders', function ($query) {
                $query->where('type', '24_hour');
            })
            ->with(['seller'])
            ->get();

        foreach ($twentyFourHourReminders as $listing) {
            $this->sendReminder($listing, '24_hour', 'auction-ending-soon-24h', 'Auction Ending Soon – 24 Hours Left – ' . ($listing->year ?? '') . ' ' . ($listing->make ?? '') . ' ' . ($listing->model ?? '[VEHICLE_NAME]'));
        }

        // 1-hour reminder: auctions ending in 1 hour
        $oneHourReminders = Listing::where('listing_method', 'auction')
            ->where('status', 'approved')
            ->whereNotNull('auction_end_time')
            ->whereBetween('auction_end_time', [
                $now->copy()->addHour()->subMinutes(5),
                $now->copy()->addHour()->addMinutes(5)
            ])
            ->whereDoesntHave('endingReminders', function ($query) {
                $query->where('type', '1_hour');
            })
            ->with(['seller'])
            ->get();

        $notificationService = new \App\Services\NotificationService();
        
        foreach ($oneHourReminders as $listing) {
            $this->sendReminder($listing, '1_hour', 'auction-ending-soon-1h', 'Auction Ending Soon – 1 Hour Left – ' . ($listing->year ?? '') . ' ' . ($listing->make ?? '') . ' ' . ($listing->model ?? '[VEHICLE_NAME]'));
            $notificationService->auctionEndingSoon($listing->seller, $listing);
        }

        $this->info('Auction ending reminders sent successfully.');
    }

    protected function sendReminder(Listing $listing, string $type, string $template, string $subject)
    {
        try {
            Mail::send('emails.' . $template, [
                'listing' => $listing,
                'seller' => $listing->seller,
            ], function ($message) use ($listing, $subject) {
                $message->to($listing->seller->email, $listing->seller->name)
                    ->subject($subject);
            });

            // Mark reminder as sent (you may want to create a listing_reminders table)
            Log::info('Auction ending reminder sent', [
                'listing_id' => $listing->id,
                'type' => $type,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send auction ending reminder: ' . $e->getMessage(), [
                'listing_id' => $listing->id,
                'type' => $type,
            ]);
        }
    }
}

