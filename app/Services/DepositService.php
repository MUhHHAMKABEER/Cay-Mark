<?php

namespace App\Services;

use App\Models\Deposit;
use App\Models\User;
use App\Models\UserWallet;
use App\Models\Bid;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DepositService
{
    /**
     * Deposit threshold: Bids at or above this amount require a deposit
     */
    const DEPOSIT_THRESHOLD = 2000.00;

    /**
     * Deposit percentage: 10% of bid amount
     */
    const DEPOSIT_PERCENTAGE = 0.10;

    /**
     * Calculate required deposit for a bid amount.
     * 
     * @param float $bidAmount
     * @return float Required deposit amount (0 if below threshold)
     */
    public function calculateRequiredDeposit(float $bidAmount): float
    {
        if ($bidAmount < self::DEPOSIT_THRESHOLD) {
            return 0.00;
        }

        return round($bidAmount * self::DEPOSIT_PERCENTAGE, 2);
    }

    /**
     * Check if user has sufficient deposit for a bid.
     * 
     * @param User $user
     * @param float $bidAmount
     * @return array ['has_deposit' => bool, 'required' => float, 'available' => float, 'shortfall' => float]
     */
    public function checkDepositForBid(User $user, float $bidAmount): array
    {
        $required = $this->calculateRequiredDeposit($bidAmount);
        $wallet = UserWallet::getOrCreateForUser($user->id);
        $available = (float) $wallet->available_balance;

        return [
            'has_deposit' => $available >= $required,
            'required' => $required,
            'available' => $available,
            'shortfall' => max(0, $required - $available),
            'threshold' => self::DEPOSIT_THRESHOLD,
        ];
    }

    /**
     * Add deposit to user's wallet.
     * 
     * @param User $user
     * @param float $amount
     * @param string|null $notes
     * @return Deposit
     */
    public function addDeposit(User $user, float $amount, ?string $notes = null): Deposit
    {
        return DB::transaction(function () use ($user, $amount, $notes) {
            $wallet = UserWallet::getOrCreateForUser($user->id);

            // Create deposit record
            $deposit = Deposit::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'type' => 'deposit',
                'status' => 'completed',
                'notes' => $notes ?? 'Deposit added to wallet',
            ]);

            // Update wallet balance
            $wallet->available_balance += $amount;
            $wallet->updateTotalBalance();
            $wallet->save();

            try {
                (new NotificationService)->depositReceived($user, $amount);
            } catch (\Throwable $e) {
                Log::error('depositReceived notification failed: '.$e->getMessage());
            }

            return $deposit;
        });
    }

    /**
     * Lock deposit when user places a bid.
     *
     * Only runs for bids at or above DEPOSIT_THRESHOLD ($2,000).
     * Uses SELECT … FOR UPDATE on the wallet row to prevent concurrent
     * double-spend between simultaneous bid submissions.
     *
     * @param User  $user
     * @param Bid   $bid
     * @param float $requiredDeposit  Must be > 0 (caller is responsible for the threshold check)
     * @return Deposit
     */
    public function lockDepositForBid(User $user, Bid $bid, float $requiredDeposit): Deposit
    {
        if ($requiredDeposit <= 0) {
            // Bid is below the $2,000 threshold — record the fact but touch nothing.
            return Deposit::create([
                'user_id'    => $user->id,
                'amount'     => 0,
                'type'       => 'lock',
                'status'     => 'completed',
                'bid_id'     => $bid->id,
                'listing_id' => $bid->listing_id,
                'notes'      => 'No deposit required (bid below $' . number_format(self::DEPOSIT_THRESHOLD, 0) . ' threshold)',
            ]);
        }

        return DB::transaction(function () use ($user, $bid, $requiredDeposit) {
            // Ensure wallet exists, then lock the row for this transaction.
            UserWallet::getOrCreateForUser($user->id);
            $wallet = UserWallet::where('user_id', $user->id)->lockForUpdate()->firstOrFail();

            if ((float) $wallet->available_balance < $requiredDeposit) {
                throw new \Exception('Insufficient deposit balance. Required: $' . number_format($requiredDeposit, 2));
            }

            // Move from Available → Locked
            $wallet->available_balance = (float) $wallet->available_balance - $requiredDeposit;
            $wallet->locked_balance    = (float) $wallet->locked_balance    + $requiredDeposit;
            $wallet->total_balance     = $wallet->available_balance + $wallet->locked_balance;
            $wallet->save();

            return Deposit::create([
                'user_id'    => $user->id,
                'amount'     => $requiredDeposit,
                'type'       => 'lock',
                'status'     => 'completed',
                'bid_id'     => $bid->id,
                'listing_id' => $bid->listing_id,
                'notes'      => 'Deposit locked for bid #' . $bid->id . ' on listing #' . $bid->listing_id,
            ]);
        });
    }

    /**
     * Unlock deposit when a buyer is outbid or their bid is retracted.
     *
     * Looks up the specific lock record for this bid (by bid_id + user_id).
     * Returns null — without touching the wallet — when:
     *   • No lock record exists for the bid (bid was below the $2k threshold), OR
     *   • The lock record has amount = 0 (recorded for sub-threshold bids)
     *
     * Uses SELECT … FOR UPDATE on the wallet row so concurrent bid submissions
     * cannot read a stale balance during the unlock.
     *
     * @param  User $user  The buyer who was outbid.
     * @param  Bid  $bid   The bid that is no longer the highest (the outbid bid).
     * @return Deposit|null  The unlock record, or null if no deposit was locked.
     * @throws \Exception   On any DB failure — caller must let this propagate so
     *                      the enclosing transaction rolls back.
     */
    public function unlockDepositForBid(User $user, Bid $bid): ?Deposit
    {
        // Find the lock record that was created when this specific bid was placed.
        $lockDeposit = Deposit::where('user_id', $user->id)
            ->where('bid_id', $bid->id)
            ->where('type', 'lock')
            ->where('status', 'completed')
            ->first();

        if (!$lockDeposit) {
            // Log a warning only when the bid was above the threshold and we expected a lock.
            $expectedRequired = $this->calculateRequiredDeposit((float) $bid->amount);
            if ($expectedRequired > 0) {
                Log::warning('[DepositService] No lock record found for outbid buyer — wallet NOT modified', [
                    'user_id'           => $user->id,
                    'bid_id'            => $bid->id,
                    'bid_amount'        => $bid->amount,
                    'expected_required' => $expectedRequired,
                    'note'              => 'This may indicate a lock was never created (e.g. sandbox bypass was active when the bid was placed).',
                ]);
            }
            return null;
        }

        if ((float) $lockDeposit->amount <= 0) {
            // Lock record exists but amount is 0 — bid was below threshold when placed.
            return null;
        }

        return DB::transaction(function () use ($user, $bid, $lockDeposit) {
            // Lock the wallet row to prevent concurrent modifications.
            $wallet = UserWallet::where('user_id', $user->id)->lockForUpdate()->first();
            if (!$wallet) {
                throw new \Exception('Wallet not found for user #' . $user->id . ' during deposit unlock.');
            }

            // Move from Locked → Available.
            // Use max(0, …) as a safety guard against going negative due to any
            // prior data inconsistency.
            $unlockAmount              = (float) $lockDeposit->amount;
            $wallet->locked_balance    = max(0, (float) $wallet->locked_balance - $unlockAmount);
            $wallet->available_balance = (float) $wallet->available_balance + $unlockAmount;
            $wallet->total_balance     = $wallet->available_balance + $wallet->locked_balance;
            $wallet->save();

            // Record the unlock event
            return Deposit::create([
                'user_id'    => $user->id,
                'amount'     => $unlockAmount,
                'type'       => 'unlock',
                'status'     => 'completed',
                'bid_id'     => $bid->id,
                'listing_id' => $bid->listing_id,
                'notes'      => 'Deposit unlocked — buyer outbid on listing #' . $bid->listing_id . ' (bid #' . $bid->id . ')',
            ]);
        });
    }

    /**
     * Apply locked deposit to invoice when buyer wins auction.
     * 
     * @param User $user
     * @param Bid $winningBid
     * @param float $invoiceTotal
     * @return Deposit
     */
    public function applyDepositToInvoice(User $user, Bid $winningBid, float $invoiceTotal): Deposit
    {
        $lockDeposit = Deposit::where('user_id', $user->id)
            ->where('bid_id', $winningBid->id)
            ->where('type', 'lock')
            ->where('status', 'completed')
            ->first();

        $depositAmount = $lockDeposit ? $lockDeposit->amount : 0;

        return DB::transaction(function () use ($user, $winningBid, $invoiceTotal, $depositAmount) {
            $wallet = UserWallet::getOrCreateForUser($user->id);

            // Remove from locked balance
            if ($depositAmount > 0) {
                $wallet->locked_balance -= $depositAmount;
                $wallet->updateTotalBalance();
                $wallet->save();
            }

            // Create record of deposit applied to invoice
            return Deposit::create([
                'user_id' => $user->id,
                'amount' => $depositAmount,
                'type' => 'applied_to_invoice',
                'status' => 'completed',
                'bid_id' => $winningBid->id,
                'listing_id' => $winningBid->listing_id,
                'notes' => 'Deposit applied to invoice. Invoice total: $' . number_format($invoiceTotal, 2),
                'metadata' => [
                    'invoice_total' => $invoiceTotal,
                    'remaining_due' => max(0, $invoiceTotal - $depositAmount),
                ],
            ]);
        });
    }

    /**
     * Check whether the user is currently the highest bidder on any active auction.
     */
    public function isHighestBidderOnActiveAuction(User $user): bool
    {
        return \App\Models\Listing::where('status', 'approved')
            ->where(function ($q) {
                $q->where('auction_end_time', '>', now())
                  ->orWhere(function ($q2) {
                      $q2->whereNull('auction_end_time')
                         ->whereRaw('DATE_ADD(auction_start_time, INTERVAL auction_duration DAY) > NOW()');
                  });
            })
            ->whereHas('bids', function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->where('status', 'active')
                  ->whereIn('id', function ($sub) {
                      $sub->selectRaw('MAX(id)')
                          ->from('bids')
                          ->where('status', 'active')
                          ->groupBy('listing_id');
                  });
            })
            ->exists();
    }

    public function requestWithdrawal(User $user, float $amount, ?string $notes = null): Deposit
    {
        return DB::transaction(function () use ($user, $amount, $notes) {
            $wallet = UserWallet::getOrCreateForUser($user->id);

            if ($wallet->available_balance < $amount) {
                throw new \Exception('Insufficient available balance for withdrawal.');
            }

            if ($this->isHighestBidderOnActiveAuction($user)) {
                throw new \Exception('You cannot withdraw your deposit while you are the highest bidder on an active auction.');
            }

            // Create withdrawal request (pending until admin approves)
            $withdrawal = Deposit::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'type' => 'withdrawal',
                'status' => 'pending',
                'notes' => $notes ?? 'Withdrawal request',
            ]);

            // Reserve the amount (move to locked until approved)
            $wallet->available_balance -= $amount;
            $wallet->locked_balance += $amount;
            $wallet->updateTotalBalance();
            $wallet->save();

            return $withdrawal;
        });
    }

    /**
     * Get user's wallet summary.
     * 
     * @param User $user
     * @return array
     */
    public function getWalletSummary(User $user): array
    {
        $wallet = UserWallet::getOrCreateForUser($user->id);

        return [
            'available_balance' => (float) $wallet->available_balance,
            'locked_balance' => (float) $wallet->locked_balance,
            'total_balance' => (float) $wallet->total_balance,
            'deposit_threshold' => self::DEPOSIT_THRESHOLD,
            'deposit_percentage' => self::DEPOSIT_PERCENTAGE * 100,
        ];
    }
}
