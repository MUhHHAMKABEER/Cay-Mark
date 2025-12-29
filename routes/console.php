<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

use Illuminate\Support\Facades\Schedule;


Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule the ExpireListings command to run daily
Schedule::command('listings:expire')->daily();

// Schedule the ProcessEndedAuctions command to run every 5 minutes
// This ensures invoices are generated quickly when auctions end
Schedule::command('auctions:process-ended')->everyFiveMinutes();

// Schedule the CheckOverduePayments command to run hourly
// This checks for invoices with overdue payments (48 hours) and processes buyer defaults
Schedule::command('caymark:check-overdue-payments')->hourly();

// Schedule payment reminder emails (6h, 24h, 48h)
Schedule::command('caymark:send-payment-reminders')->everyFiveMinutes();

// Schedule auction ending soon reminder emails (24h, 1h)
Schedule::command('caymark:send-auction-ending-reminders')->everyFiveMinutes();
