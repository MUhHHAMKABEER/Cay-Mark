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

        $invoiceService = new InvoiceService();
        $count = $invoiceService->processEndedAuctions();

        $this->info("Generated {$count} invoice(s) for ended auctions.");

        return Command::SUCCESS;
    }
}
