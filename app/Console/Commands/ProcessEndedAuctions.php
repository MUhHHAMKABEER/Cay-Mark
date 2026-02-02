<?php

namespace App\Console\Commands;

use App\Services\InvoiceService;
use Illuminate\Console\Command;

class ProcessEndedAuctions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auctions:process-ended';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process ended auctions and generate invoices for winners';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Processing ended auctions...');
        $this->info('Detailed logs are written to storage/logs/laravel.log (search for [processEndedAuctions]).');

        $invoiceService = new InvoiceService();
        $count = $invoiceService->processEndedAuctions();

        $this->info("Generated {$count} invoice(s) for ended auctions.");
        if ($count === 0) {
            $this->warn('No invoices were generated. Check storage/logs/laravel.log for:');
            $this->warn('  - "Candidates" = approved auctions with no invoice yet');
            $this->warn('  - "Listing end check" = whether each listing has passed its end date');
            $this->warn('  - "Ended auctions" = listings that passed the end-date filter');
            $this->warn('  - "No winning bid" / "Reserve price not met" / "Failed to generate invoice" = why a listing was skipped');
        }

        return Command::SUCCESS;
    }
}
