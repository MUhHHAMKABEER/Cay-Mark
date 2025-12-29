<?php

namespace App\Services;

use App\Models\BuyerDefault;
use App\Models\Invoice;
use App\Models\User;
use App\Models\Deposit;
use App\Models\UserWallet;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DefaultService
{
    protected $commissionService;
    protected $depositService;

    public function __construct()
    {
        $this->commissionService = new CommissionService();
        $this->depositService = new DepositService();
    }

    /**
     * Process buyer default when payment deadline passes (48 hours).
     * 
     * @param Invoice $invoice
     * @return BuyerDefault
     */
    public function processBuyerDefault(Invoice $invoice): BuyerDefault
    {
        return DB::transaction(function () use ($invoice) {
            $buyer = $invoice->buyer;
            
            // Calculate deposit penalty (configurable percentage)
            $penaltyPercentage = config('caymark.deposit_penalty_percentage', 10); // Default 10%
            $wallet = UserWallet::getOrCreateForUser($buyer);
            $penaltyAmount = ($wallet->locked_balance * $penaltyPercentage) / 100;

            // Apply deposit penalty
            if ($penaltyAmount > 0) {
                $wallet->locked_balance = max(0, $wallet->locked_balance - $penaltyAmount);
                $wallet->updateTotalBalance();

                // Log penalty deposit
                Deposit::create([
                    'user_id' => $buyer->id,
                    'amount' => -$penaltyAmount,
                    'type' => 'penalty',
                    'status' => 'completed',
                    'notes' => 'Deposit penalty for non-payment default',
                    'metadata' => [
                        'invoice_id' => $invoice->id,
                        'penalty_percentage' => $penaltyPercentage,
                    ],
                ]);
            }

            // Restrict buyer account for 14 days
            $restrictionEndsAt = now()->addDays(14);
            $buyer->update([
                'is_restricted' => true,
                'restriction_ends_at' => $restrictionEndsAt,
                'restriction_reason' => 'Non-payment default - Invoice #' . $invoice->invoice_number,
            ]);

            // Create default record
            $default = BuyerDefault::create([
                'user_id' => $buyer->id,
                'invoice_id' => $invoice->id,
                'listing_id' => $invoice->listing_id,
                'bid_id' => $invoice->bid_id,
                'invoice_amount' => $invoice->total_amount_due,
                'deposit_penalty_amount' => $penaltyAmount,
                'deposit_penalty_percentage' => $penaltyPercentage,
                'status' => 'restricted',
                'defaulted_at' => now(),
                'restriction_ends_at' => $restrictionEndsAt,
            ]);

            // Mark invoice as overdue
            $invoice->update([
                'is_overdue' => true,
                'overdue_at' => now(),
            ]);

            Log::info('Buyer default processed', [
                'default_id' => $default->id,
                'user_id' => $buyer->id,
                'invoice_id' => $invoice->id,
                'penalty_amount' => $penaltyAmount,
            ]);

            return $default;
        });
    }

    /**
     * Resolve default by relisting (Option A).
     * 
     * @param BuyerDefault $default
     * @param string $adminNotes
     * @return void
     */
    public function resolveByRelist(BuyerDefault $default, string $adminNotes = ''): void
    {
        DB::transaction(function () use ($default, $adminNotes) {
            $default->update([
                'status' => 'resolved',
                'resolution_type' => 'relist',
                'admin_notes' => $adminNotes,
            ]);

            // Mark original invoice as canceled
            $default->invoice->update([
                'payment_status' => 'canceled',
            ]);

            // Mark listing as available for relist
            $default->listing->update([
                'status' => 'default_relist',
            ]);

            Log::info('Default resolved by relist', [
                'default_id' => $default->id,
                'listing_id' => $default->listing_id,
            ]);
        });
    }

    /**
     * Get second-highest bidder for a listing.
     * 
     * @param Invoice $invoice
     * @return \App\Models\Bid|null
     */
    public function getSecondHighestBidder(Invoice $invoice): ?\App\Models\Bid
    {
        $winningBid = $invoice->bid;
        
        if (!$winningBid) {
            return null;
        }

        // Get second-highest bid (excluding the winning bid)
        $secondBid = \App\Models\Bid::where('listing_id', $invoice->listing_id)
            ->where('id', '!=', $winningBid->id)
            ->orderByDesc('amount')
            ->first();

        return $secondBid;
    }

    /**
     * Check and auto-remove restrictions that have expired.
     * 
     * @return int Number of restrictions removed
     */
    public function removeExpiredRestrictions(): int
    {
        $expiredUsers = User::where('is_restricted', true)
            ->where('restriction_ends_at', '<=', now())
            ->get();

        $count = 0;
        foreach ($expiredUsers as $user) {
            $user->update([
                'is_restricted' => false,
                'restriction_ends_at' => null,
                'restriction_reason' => null,
            ]);
            $count++;
        }

        return $count;
    }
}

