<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Listing;
use App\Models\Bid;
use App\Models\User;
use App\Models\Deposit;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Services\PayoutService;

class InvoiceService
{
    protected $commissionService;
    protected $depositService;
    protected $payoutService;

    public function __construct()
    {
        $this->commissionService = new CommissionService();
        $this->depositService = new DepositService();
        $this->payoutService = new PayoutService();
    }

    /**
     * Generate invoice automatically when buyer wins an auction.
     * This should be called when an auction ends and has a winning bid.
     * 
     * @param Listing $listing
     * @param Bid $winningBid
     * @return Invoice
     */
    public function generateInvoiceForAuctionWin(Listing $listing, Bid $winningBid): Invoice
    {
        return DB::transaction(function () use ($listing, $winningBid) {
            // Check if invoice already exists
            $existingInvoice = Invoice::where('listing_id', $listing->id)
                ->where('bid_id', $winningBid->id)
                ->first();

            if ($existingInvoice) {
                return $existingInvoice;
            }

            $buyer = $winningBid->user;
            $seller = $listing->seller;
            $winningAmount = (float) $winningBid->amount;

            // Calculate buyer commission and total due
            $buyerInvoice = $this->commissionService->calculateBuyerInvoice($winningAmount);

            // Generate invoice number
            $invoiceNumber = Invoice::generateInvoiceNumber();

            // Create invoice with 48-hour payment deadline (per PDF requirements)
            $invoice = Invoice::create([
                'invoice_number' => $invoiceNumber,
                'listing_id' => $listing->id,
                'bid_id' => $winningBid->id,
                'buyer_id' => $buyer->id,
                'seller_id' => $seller->id,
                'item_name' => ($listing->year ?? '') . ' ' . ($listing->make ?? '') . ' ' . ($listing->model ?? ''),
                'item_id' => $listing->item_number ?? (string) $listing->id, // Use Item Number (CM000245) if available, NOT "Lot ID"
                'winning_bid_amount' => $winningAmount,
                'buyer_commission' => $buyerInvoice['buyer_commission'],
                'total_amount_due' => $buyerInvoice['total_due'],
                'sale_date' => now()->toDateString(),
                'invoice_generated_at' => now(),
                'payment_deadline' => now()->addHours(48), // 48-hour payment window
                'payment_status' => 'pending',
            ]);

            // Apply deposit to invoice if available
            $depositApplied = $this->depositService->applyDepositToInvoice($buyer, $winningBid, $buyerInvoice['total_due']);

            // Generate PDF invoice
            $pdfPath = $this->generateInvoicePDF($invoice);
            $invoice->pdf_path = $pdfPath;
            $invoice->save();

            // Send email notification to buyer
            $this->sendInvoiceEmail($invoice, $buyer);
            
            // Send in-app notifications (per PDF requirements)
            $this->sendInAppNotification($invoice, $buyer);
            
            // Send invoice available notification
            $notificationService = new \App\Services\NotificationService();
            $notificationService->invoiceAvailable($buyer, $invoice);

            // NOTE: Payout is NOT created here anymore
            // Payout is created AFTER seller confirms pickup with PIN (per PDF requirements)

            Log::info('Invoice generated for auction win', [
                'invoice_id' => $invoice->id,
                'listing_id' => $listing->id,
                'buyer_id' => $buyer->id,
            ]);

            return $invoice;
        });
    }

    /**
     * Generate PDF invoice file (per PDF requirements).
     * File naming: CAYMARK_INVOICE_[ITEMID]_[DATE].PDF
     * 
     * @param Invoice $invoice
     * @return string PDF file path
     */
    protected function generateInvoicePDF(Invoice $invoice): string
    {
        // File naming format: CAYMARK_INVOICE_[ITEMID]_[DATE].PDF
        $date = date('Ymd');
        $itemId = strtoupper(str_replace([' ', '-'], '_', $invoice->item_id));
        $filename = 'CAYMARK_INVOICE_' . $itemId . '_' . $date . '.PDF';
        
        $directory = public_path('uploads/invoices/' . $invoice->buyer_id);
        
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }
        
        $filepath = $directory . '/' . $filename;
        
        // Generate PDF using dompdf if available, otherwise create HTML file
        try {
            // Try to use dompdf if installed
            if (class_exists('\Barryvdh\DomPDF\Facade\Pdf')) {
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('invoices.auction-invoice', [
                    'invoice' => $invoice->load(['buyer', 'seller']),
                    'buyer' => $invoice->buyer,
                    'seller' => $invoice->seller,
                ]);
                $pdf->save($filepath);
            } else {
                // Fallback: Generate HTML and save (can be converted to PDF later)
                $html = view('invoices.auction-invoice', [
                    'invoice' => $invoice->load(['buyer', 'seller']),
                    'buyer' => $invoice->buyer,
                    'seller' => $invoice->seller,
                ])->render();
                
                // Save as HTML for now (install dompdf: composer require barryvdh/laravel-dompdf)
                file_put_contents(str_replace('.PDF', '.html', $filepath), $html);
                Log::warning('PDF library not installed. Invoice saved as HTML. Install: composer require barryvdh/laravel-dompdf');
            }
        } catch (\Exception $e) {
            Log::error('Failed to generate invoice PDF: ' . $e->getMessage());
            // Create placeholder file
            file_put_contents($filepath, 'Invoice PDF generation failed. Please contact support.');
        }
        
        return 'uploads/invoices/' . $invoice->buyer_id . '/' . $filename;
    }

    /**
     * Send invoice email to buyer (per PDF requirements).
     * Email must NOT attach invoice, must NOT include invoice details.
     * Only directs user to Dashboard → Auctions Won → Download Invoice.
     * 
     * @param Invoice $invoice
     * @param User $buyer
     * @return void
     */
    protected function sendInvoiceEmail(Invoice $invoice, User $buyer): void
    {
        try {
            Mail::send('emails.auction-won-invoice', [
                'invoice' => $invoice,
                'buyer' => $buyer,
            ], function ($message) use ($buyer, $invoice) {
                $message->to($buyer->email, $buyer->name)
                    ->subject('Congratulations — You Won ' . ($invoice->item_name ?? '[VEHICLE_NAME]'));
                // DO NOT attach invoice per PDF requirements
            });
        } catch (\Exception $e) {
            Log::error('Failed to send invoice email: ' . $e->getMessage());
        }
    }

    /**
     * Send in-app notification when buyer wins auction (per PDF requirements).
     * 
     * @param Invoice $invoice
     * @param User $buyer
     * @return void
     */
    protected function sendInAppNotification(Invoice $invoice, User $buyer): void
    {
        try {
            // Create notification record
            $buyer->notify(new \App\Notifications\AuctionWonNotification($invoice));
        } catch (\Exception $e) {
            Log::error('Failed to send in-app notification: ' . $e->getMessage());
        }
    }

    /**
     * Process ended auctions and generate invoices for winners.
     * This should be called by a scheduled job.
     * 
     * @return int Number of invoices generated
     */
    public function processEndedAuctions(): int
    {
        $count = 0;
        $now = now();

        Log::info('[processEndedAuctions] Started', ['at' => $now->toDateTimeString()]);

        // Find approved auction listings that have no invoice yet (candidates)
        $candidates = Listing::where('listing_method', 'auction')
            ->where('status', 'approved')
            ->whereDoesntHave('invoices')
            ->get();

        Log::info('[processEndedAuctions] Candidates (approved auction, no invoice)', [
            'count' => $candidates->count(),
            'listing_ids' => $candidates->pluck('id')->toArray(),
        ]);

        // Filter to only those that have actually ended (use listing's actual end time)
        $endedAuctions = $candidates->filter(function ($listing) use ($now) {
            $endDate = $listing->getAuctionEndDate();
            $hasEnded = $endDate && $now->greaterThanOrEqualTo($endDate);
            Log::info('[processEndedAuctions] Listing end check', [
                'listing_id' => $listing->id,
                'item_number' => $listing->item_number,
                'auction_end_time' => $listing->auction_end_time?->toDateTimeString(),
                'auction_start_time' => $listing->auction_start_time?->toDateTimeString(),
                'auction_duration_days' => $listing->auction_duration,
                'computed_end_date' => $endDate?->toDateTimeString(),
                'now' => $now->toDateTimeString(),
                'has_ended' => $hasEnded,
            ]);
            return $hasEnded;
        })->values();

        Log::info('[processEndedAuctions] Ended auctions (passed end-date filter)', [
            'count' => $endedAuctions->count(),
            'listing_ids' => $endedAuctions->pluck('id')->toArray(),
        ]);

        foreach ($endedAuctions as $listing) {
            $winningBid = $listing->bids()
                ->where('status', 'active')
                ->orderByDesc('amount')
                ->first();

            if (!$winningBid) {
                Log::warning('[processEndedAuctions] No winning bid (no active bids)', [
                    'listing_id' => $listing->id,
                    'item_number' => $listing->item_number,
                    'total_bids' => $listing->bids()->count(),
                ]);
                continue;
            }

            $reservePrice = $listing->reserve_price ? (float) $listing->reserve_price : null;
            $winningAmount = (float) $winningBid->amount;

            if ($reservePrice && $winningAmount < $reservePrice) {
                Log::warning('[processEndedAuctions] Reserve price not met – skipping invoice', [
                    'listing_id' => $listing->id,
                    'item_number' => $listing->item_number,
                    'winning_amount' => $winningAmount,
                    'reserve_price' => $reservePrice,
                    'buyer_id' => $winningBid->user_id,
                ]);
                try {
                    $notificationService = new \App\Services\NotificationService();
                    $notificationService->auctionEndedReserveNotMet($listing->seller, $listing, $winningAmount, $reservePrice);
                } catch (\Exception $e) {
                    Log::error('[processEndedAuctions] Failed to send reserve-not-met notification', [
                        'listing_id' => $listing->id,
                        'error' => $e->getMessage(),
                    ]);
                }
                continue;
            }

            try {
                $invoice = $this->generateInvoiceForAuctionWin($listing, $winningBid);

                $listing->status = 'sold';
                $listing->save();

                $this->sendAuctionEndedEmail($listing, $winningBid);

                $notificationService = new \App\Services\NotificationService();
                $notificationService->auctionSold($listing->seller, $listing, $winningAmount);

                $count++;
                Log::info('[processEndedAuctions] Invoice generated and listing marked sold', [
                    'listing_id' => $listing->id,
                    'invoice_id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'buyer_id' => $winningBid->user_id,
                    'winning_amount' => $winningAmount,
                ]);
            } catch (\Exception $e) {
                Log::error('[processEndedAuctions] Failed to generate invoice', [
                    'listing_id' => $listing->id,
                    'item_number' => $listing->item_number,
                    'winning_bid_id' => $winningBid->id,
                    'buyer_id' => $winningBid->user_id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }

        Log::info('[processEndedAuctions] Finished', ['invoices_generated' => $count]);
        return $count;
    }

    /**
     * Get invoices for a buyer.
     * 
     * @param User $buyer
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getBuyerInvoices(User $buyer)
    {
        return Invoice::where('buyer_id', $buyer->id)
            ->with(['listing.images', 'seller'])
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Mark invoice as paid.
     * 
     * @param Invoice $invoice
     * @return Invoice
     */
    public function markInvoiceAsPaid(Invoice $invoice): Invoice
    {
        $invoice->payment_status = 'paid';
        $invoice->paid_at = now();
        $invoice->save();

        return $invoice;
    }

    /**
     * Send auction ended email to seller.
     * 
     * @param Listing $listing
     * @param Bid $winningBid
     * @return void
     */
    protected function sendAuctionEndedEmail(Listing $listing, Bid $winningBid): void
    {
        try {
            Mail::send('emails.auction-ended-sold', [
                'listing' => $listing,
                'seller' => $listing->seller,
                'winningBidAmount' => $winningBid->amount,
            ], function ($message) use ($listing) {
                $message->to($listing->seller->email, $listing->seller->name)
                    ->subject('Your Auction Has Ended – ' . ($listing->year ?? '') . ' ' . ($listing->make ?? '') . ' ' . ($listing->model ?? '[VEHICLE_NAME]') . ' Sold');
            });
        } catch (\Exception $e) {
            Log::error('Failed to send auction ended email: ' . $e->getMessage());
        }
    }
}
