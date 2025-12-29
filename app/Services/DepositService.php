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

            return $deposit;
        });
    }

    /**
     * Lock deposit when user places a bid.
     * Once ANY bid is placed, deposit becomes locked.
     * 
     * @param User $user
     * @param Bid $bid
     * @param float $requiredDeposit
     * @return Deposit
     */
    public function lockDepositForBid(User $user, Bid $bid, float $requiredDeposit): Deposit
    {
        if ($requiredDeposit <= 0) {
            // No deposit required for bids < $2,000
            return Deposit::create([
                'user_id' => $user->id,
                'amount' => 0,
                'type' => 'lock',
                'status' => 'completed',
                'bid_id' => $bid->id,
                'listing_id' => $bid->listing_id,
                'notes' => 'No deposit required (bid below $2,000 threshold)',
            ]);
        }

        return DB::transaction(function () use ($user, $bid, $requiredDeposit) {
            $wallet = UserWallet::getOrCreateForUser($user->id);

            // Check if user has available balance
            if ($wallet->available_balance < $requiredDeposit) {
                throw new \Exception('Insufficient deposit balance. Required: $' . number_format($requiredDeposit, 2));
            }

            // Lock the deposit
            $wallet->available_balance -= $requiredDeposit;
            $wallet->locked_balance += $requiredDeposit;
            $wallet->updateTotalBalance();
            $wallet->save();

            // Create lock record
            return Deposit::create([
                'user_id' => $user->id,
                'amount' => $requiredDeposit,
                'type' => 'lock',
                'status' => 'completed',
                'bid_id' => $bid->id,
                'listing_id' => $bid->listing_id,
                'notes' => 'Deposit locked for bid #' . $bid->id,
            ]);
        });
    }

    /**
     * Unlock deposit (if bid is retracted or user loses auction).
     * 
     * @param User $user
     * @param Bid $bid
     * @return Deposit|null
     */
    public function unlockDepositForBid(User $user, Bid $bid): ?Deposit
    {
        $lockDeposit = Deposit::where('user_id', $user->id)
            ->where('bid_id', $bid->id)
            ->where('type', 'lock')
            ->where('status', 'completed')
            ->first();

        if (!$lockDeposit || $lockDeposit->amount <= 0) {
            return null; // No deposit was locked
        }

        return DB::transaction(function () use ($user, $bid, $lockDeposit) {
            $wallet = UserWallet::getOrCreateForUser($user->id);

            // Unlock the deposit
            $wallet->locked_balance -= $lockDeposit->amount;
            $wallet->available_balance += $lockDeposit->amount;
            $wallet->updateTotalBalance();
            $wallet->save();

            // Create unlock record
            return Deposit::create([
                'user_id' => $user->id,
                'amount' => $lockDeposit->amount,
                'type' => 'unlock',
                'status' => 'completed',
                'bid_id' => $bid->id,
                'listing_id' => $bid->listing_id,
                'notes' => 'Deposit unlocked for bid #' . $bid->id,
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
     * Process withdrawal request.
     * 
     * @param User $user
     * @param float $amount
     * @param string|null $notes
     * @return Deposit
     */
    public function requestWithdrawal(User $user, float $amount, ?string $notes = null): Deposit
    {
        return DB::transaction(function () use ($user, $amount, $notes) {
            $wallet = UserWallet::getOrCreateForUser($user->id);

            if ($wallet->available_balance < $amount) {
                throw new \Exception('Insufficient available balance for withdrawal.');
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
