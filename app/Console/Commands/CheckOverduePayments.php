<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Services\DefaultService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckOverduePayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'caymark:check-overdue-payments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for invoices with overdue payments (48 hours) and process buyer defaults';

    protected $defaultService;

    public function __construct(DefaultService $defaultService)
    {
        parent::__construct();
        $this->defaultService = $defaultService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for overdue payments...');

        // Find invoices with payment deadline passed and status still pending
        $overdueInvoices = Invoice::where('payment_status', 'pending')
            ->whereNotNull('payment_deadline')
            ->where('payment_deadline', '<=', now())
            ->where('is_overdue', false) // Only process once
            ->with(['buyer', 'listing', 'bid'])
            ->get();

        $this->info("Found {$overdueInvoices->count()} overdue invoice(s)");

        $processed = 0;
        foreach ($overdueInvoices as $invoice) {
            try {
                // Process buyer default
                $default = $this->defaultService->processBuyerDefault($invoice);
                
                $this->info("Processed default for Invoice #{$invoice->invoice_number} (User: {$invoice->buyer->name})");
                $processed++;
            } catch (\Exception $e) {
                Log::error('Failed to process buyer default', [
                    'invoice_id' => $invoice->id,
                    'error' => $e->getMessage(),
                ]);
                $this->error("Failed to process Invoice #{$invoice->invoice_number}: {$e->getMessage()}");
            }
        }

        // Remove expired restrictions
        $removed = $this->defaultService->removeExpiredRestrictions();
        if ($removed > 0) {
            $this->info("Removed {$removed} expired restriction(s)");
        }

        $this->info("Completed. Processed {$processed} default(s).");
        
        return Command::SUCCESS;
    }
}
