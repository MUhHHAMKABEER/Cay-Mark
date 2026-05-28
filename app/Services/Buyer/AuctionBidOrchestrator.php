<?php

namespace App\Services\Buyer;

class AuctionBidOrchestrator
{
    public static function placeBid($request, $listing)
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login')->withErrors(['amount' => 'Please login to place a bid.']);
        }

        if ($listing->listing_method !== 'auction') {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'amount' => 'This listing is not an auction.',
            ]);
        }

        if ($user->role === 'seller') {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'amount' => 'Sellers are not allowed to bid on auctions.',
            ]);
        }

        if ($user->role !== 'buyer') {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'amount' => 'Buyer membership required to place bids.',
            ]);
        }

        // Require all profile fields before bidding
        $missingRequirements = $user->getMissingBidRequirements();
        if (!empty($missingRequirements)) {
            $list = implode(' • ', $missingRequirements);
            throw \Illuminate\Validation\ValidationException::withMessages([
                'amount' => 'Please complete the following in your profile before placing a bid: • ' . $list . '.',
            ]);
        }

        if ($user->is_restricted) {
            if ($user->restriction_ends_at && now()->greaterThan($user->restriction_ends_at)) {
                $user->update([
                    'is_restricted' => false,
                    'restriction_ends_at' => null,
                    'restriction_reason' => null,
                ]);
            } else {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'amount' => 'Your account is currently restricted from placing bids due to a non-payment default. This restriction will be lifted on ' . $user->restriction_ends_at->format('F d, Y') . '. You can still browse listings, view your account, and contact support.',
                ]);
            }
        }

        $depositService = new \App\Services\DepositService();
        $incrementService = new \App\Services\BiddingIncrementService();

        $data = $request->validated();
        $amount = (float) $data['amount'];

        return \Illuminate\Support\Facades\DB::transaction(function () use ($listing, $user, $amount, $depositService, $incrementService) {
            $listing = \App\Models\Listing::lockForUpdate()
                ->where('id', $listing->id)
                ->where('listing_method', 'auction')
                ->where('status', 'approved')
                ->firstOrFail();

            $auctionEndDate = $listing->auction_end_time
                ? \Carbon\Carbon::parse($listing->auction_end_time)
                : \Carbon\Carbon::parse($listing->auction_start_time ?? $listing->created_at)->addDays($listing->auction_duration);

            if (now()->greaterThanOrEqualTo($auctionEndDate)) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'amount' => 'This auction has ended.',
                ]);
            }

            $highestBid = $listing->bids()->where('status', 'active')->orderByDesc('amount')->first();
            $previousHighestBidderId = $highestBid ? (int) $highestBid->user_id : null;
            $current = $highestBid ? (float) $highestBid->amount : (float) ($listing->current_bid ?? 0);
            $startingPrice = (float) ($listing->starting_price ?? $listing->price ?? 0);

            $bidBase = max($startingPrice, $current);
            $incrementValidation = $incrementService->validateBidIncrement($bidBase, $amount);
            if (!$incrementValidation['valid']) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'amount' => $incrementValidation['message'],
                ]);
            }

            if ($amount < $startingPrice) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'amount' => 'Your bid must be at least the starting price of $' . number_format($startingPrice, 2) . '.',
                ]);
            }

            // ── DEPOSIT CHECK ────────────────────────────────────────────────────
            // Runs on EVERY bid, server-side, BEFORE anything is saved.
            // calculateRequiredDeposit() returns 0 for bids below the $2,000
            // threshold, so sub-$2k bids pass through automatically.
            //
            // NOTE: This check is intentionally NOT gated behind any sandbox or
            // feature-flag config.  Payment sandbox settings only affect the
            // payment-gateway integration — deposit validation is a hard business
            // rule that must fire in every environment, including local dev.
            $depositCheck    = $depositService->checkDepositForBid($user, $amount);
            $requiredDeposit = $depositCheck['required'];   // 0.00 when bid < $2,000

            if ($requiredDeposit > 0 && !$depositCheck['has_deposit']) {
                \Illuminate\Support\Facades\Log::warning('[BidBlocked] Insufficient deposit — bid rejected before save', [
                    'user_id'    => $user->id,
                    'user_name'  => $user->name,
                    'listing_id' => $listing->id,
                    'bid_amount' => $amount,
                    'required'   => $depositCheck['required'],
                    'available'  => $depositCheck['available'],
                    'shortfall'  => $depositCheck['shortfall'],
                ]);

                return response()->json([
                    // Spec-required keys
                    'blocked'          => true,
                    'reason'           => 'insufficient_deposit',
                    // Frontend modal keys (AuctionDetail.blade.php line ~1052)
                    'success'          => false,
                    'deposit_required' => true,
                    'required'         => $depositCheck['required'],
                    'available'        => $depositCheck['available'],
                    'shortfall'        => $depositCheck['shortfall'],
                    'deposit_url'      => route('buyer.deposit-withdrawal'),
                    'message'          => 'A deposit of $' . number_format($depositCheck['required'], 2) . ' is required to place this bid.',
                ], 422);
            }
            // ─────────────────────────────────────────────────────────────────────

            // ── STEP 3: Unlock the outbid buyer's deposit BEFORE accepting the new bid ─
            // Runs inside the same DB transaction as the bid save so it is atomic.
            // If unlock throws a DB error we propagate it — the transaction rolls back,
            // the new bid is NOT saved, and wallet integrity is guaranteed.
            // If no lock record exists (bid was below the $2k threshold), unlockDepositForBid
            // returns null and we continue normally.
            $outbidUser = null;
            if ($previousHighestBidderId !== null
                && $previousHighestBidderId !== (int) $user->id
                && $highestBid) {
                $outbidUser = \App\Models\User::find($previousHighestBidderId);
                if ($outbidUser) {
                    try {
                        $unlockRecord = $depositService->unlockDepositForBid($outbidUser, $highestBid);
                        if ($unlockRecord !== null && (float) $unlockRecord->amount > 0) {
                            \Illuminate\Support\Facades\Log::info('[BidUnlock] Deposit unlocked for outbid buyer', [
                                'outbid_user_id'   => $outbidUser->id,
                                'outbid_user_name' => $outbidUser->name,
                                'listing_id'       => $listing->id,
                                'bid_id'           => $highestBid->id,
                                'amount_unlocked'  => $unlockRecord->amount,
                            ]);
                        }
                    } catch (\Throwable $e) {
                        // A DB-level failure here must abort the bid — it is safer to
                        // reject the new bid than to accept it and leave the previous
                        // buyer's deposit locked forever.
                        \Illuminate\Support\Facades\Log::error('[BidUnlock] CRITICAL: Deposit unlock failed — new bid rejected to preserve wallet integrity', [
                            'outbid_user_id' => $outbidUser->id,
                            'listing_id'     => $listing->id,
                            'bid_id'         => $highestBid->id,
                            'new_bidder_id'  => $user->id,
                            'bid_amount'     => $amount,
                            'error'          => $e->getMessage(),
                        ]);
                        throw \Illuminate\Validation\ValidationException::withMessages([
                            'amount' => 'Unable to process your bid due to a system error. Please try again in a moment.',
                        ]);
                    }
                }
            }
            // ─────────────────────────────────────────────────────────────────────

            // Timer extension (anti-sniping)
            $secondsRemaining = now()->diffInSeconds($auctionEndDate, false);
            $timerReset = false;
            if ($secondsRemaining > 0 && $secondsRemaining < 60) {
                $newEndTime = now()->addSeconds(60);
                $listing->auction_end_time = $newEndTime;
                $timerReset = true;
            }

            // ── STEP 4: Accept the new bid ────────────────────────────────────────
            $bid = \App\Models\Bid::create([
                'listing_id' => $listing->id,
                'user_id'    => $user->id,
                'amount'     => $amount,
                'status'     => 'active',
            ]);

            // ── STEP 5: Lock the new bidder's required deposit ────────────────────
            if ($requiredDeposit > 0) {
                $depositService->lockDepositForBid($user, $bid, $requiredDeposit);
            }

            $listing->current_bid = $amount;
            $listing->save();

            $notificationService = new \App\Services\NotificationService();
            $notificationService->bidPlaced($user, $listing);

            // Outbid notification — sent after bid commits, $outbidUser set in Step 3
            if ($outbidUser !== null) {
                $notificationService->outbid($outbidUser, $listing);
            }

            $listing->loadMissing('seller');
            if ($listing->seller && (int) $listing->seller_id !== (int) $user->id) {
                $notificationService->newBidOnListing($listing->seller, $listing, $amount);
            }

            $newEndDate = $timerReset ? $listing->auction_end_time : $auctionEndDate;

            return response()->json([
                'success' => true,
                'bid' => [
                    'id' => $bid->id,
                    'amount' => number_format($bid->amount, 2),
                    'created_at' => $bid->created_at->toDateTimeString(),
                ],
                'currentBid' => number_format($amount, 2),
                'timerReset' => $timerReset,
                'newEndTime' => $timerReset ? $newEndDate->toDateTimeString() : null,
                'message' => $timerReset ? 'Bid placed! Timer extended by 60 seconds.' : 'Bid placed successfully!',
            ]);
        });
    }
}

