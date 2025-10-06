<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class UpdateExpiredListings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-expired-listings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
{
    Listing::where('expiry_status', '!=', 'expired')
        ->where('expires_at', '<', now())
        ->update(['expiry_status' => 'expired']);
}

}
