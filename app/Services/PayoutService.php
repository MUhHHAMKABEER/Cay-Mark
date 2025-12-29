<?php

namespace App\Services;

use App\Models\Payout;
use App\Models\Invoice;
use App\Models\Listing;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PayoutService
{
    protected $commissionService;

    public function __construct()
    {
        $this->commissionService = new CommissionService();
    }

    /**
     * Create payout AFTER pickup PIN confirmation (per PDF requirements).
     * This is called when seller confirms pickup with PIN.
     * 
     * @param Invoice $invoice
     * @param Listing $listing
     * @return Payout
     */
    public function createPayoutAfterPickup(Invoice $invoice, Listing $listing): Payout
    {
        return DB::transaction(function () use ($invoice, $listing) {
            // Check if payout already exists
            $existingPayout = Payout::where('invoice_id', $invoice->id)->first();

            if ($existingPayout) {
                return $existingPayout;
            }

            $seller = $invoice->seller;
            $buyer = $invoice->buyer;
            $salePrice = (float) $invoice->winning_bid_amount;

            // Calculate seller commission and net payout
            $sellerPayout = $this->commissionService->calculateSellerPayout($salePrice);

            // Generate payout number
            $payoutNumber = Payout::generatePayoutNumber();

            // Create payout record (per PDF requirements)
            $payout = Payout::create([
                'payout_number' => $payoutNumber,
                'invoice_id' => $invoice->id,
                'listing_id' => $listing->id,
                'seller_id' => $seller->id,
                'buyer_name' => $buyer->name ?? null,
                'item_title' => $invoice->item_name,
                'sale_price' => $salePrice,
                'seller_commission' => $sellerPayout['seller_commission'],
                'net_payout' => $sellerPayout['net_payout'],
                'sale_date' => $invoice->sale_date,
                'payout_generated_at' => now(),
                'status' => 'pending', // Finance will update status
                'payment_method' => 'bank_transfer', // Bank wire only per PDF
            ]);

            Log::info('Payout created after pickup confirmation', [
                'payout_id' => $payout->id,
                'invoice_id' => $invoice->id,
                'seller_id' => $seller->id,
                'net_payout' => $payout->net_payout,
            ]);

            return $payout;
        });
    }

    /**
     * Generate payout automatically when invoice is created (DEPRECATED - now done after PIN).
     * Kept for backward compatibility but should not be used.
     * 
     * @param Invoice $invoice
     * @return Payout|null
     */
    public function generatePayoutForInvoice(Invoice $invoice): ?Payout
    {
        // Payouts are now created AFTER pickup PIN confirmation
        // This method is kept for backward compatibility but returns null
        return null;
    }

    /**
     * Process payout when buyer payment is confirmed.
     * 
     * @param Payout $payout
     * @param string $paymentMethod
     * @param string|null $paymentReference
     * @return Payout
     */
    public function processPayout(Payout $payout, string $paymentMethod, ?string $paymentReference = null): Payout
    {
        return DB::transaction(function () use ($payout, $paymentMethod, $paymentReference) {
            $payout->status = 'processing';
            $payout->payment_method = $paymentMethod;
            $payout->payment_reference = $paymentReference;
            $payout->payout_processed_at = now();
            $payout->save();

            // TODO: Integrate with payment processor (Stripe, PayPal, etc.)
            // For now, we'll mark it as completed after a delay
            // In production, this would be handled by a webhook or queue job

            Log::info('Payout processed', [
                'payout_id' => $payout->id,
                'seller_id' => $payout->seller_id,
                'amount' => $payout->net_payout,
            ]);

            return $payout;
        });
    }

    /**
     * Mark payout as completed (after payment processor confirms).
     * 
     * @param Payout $payout
     * @return Payout
     */
    public function markPayoutAsCompleted(Payout $payout): Payout
    {
        $payout->status = 'completed';
        $payout->save();

        // Send email notification to seller
        $this->sendPayoutEmail($payout, $payout->seller);

        return $payout;
    }

    /**
     * Send payout email to seller.
     * 
     * @param Payout $payout
     * @param User $seller
     * @return void
     */
    protected function sendPayoutEmail(Payout $payout, User $seller): void
    {
        try {
            Mail::send('emails.seller-payout', [
                'payout' => $payout,
                'seller' => $seller,
            ], function ($message) use ($seller, $payout) {
                $message->to($seller->email, $seller->name)
                    ->subject('Your Payout Has Been Processed - CayMark');
            });
        } catch (\Exception $e) {
            Log::error('Failed to send payout email: ' . $e->getMessage());
        }
    }

    /**
     * Get payouts for a seller.
     * 
     * @param User $seller
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getSellerPayouts(User $seller)
    {
        return Payout::where('seller_id', $seller->id)
            ->with(['listing.images', 'invoice'])
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Get pending payouts for a seller.
     * 
     * @param User $seller
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getPendingPayouts(User $seller)
    {
        return Payout::where('seller_id', $seller->id)
            ->whereIn('status', ['pending', 'processing'])
            ->with(['listing.images', 'invoice'])
            ->orderByDesc('created_at')
            ->get();
    }
}
