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

            $requiresDeposit = !config('services.payment.sandbox', true);
            $requiredDeposit = $requiresDeposit
                ? $depositService->calculateRequiredDeposit($amount)
                : 0.00;

            if ($requiresDeposit) {
                $depositCheck = $depositService->checkDepositForBid($user, $amount);
                if (!$depositCheck['has_deposit']) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'amount' => 'Insufficient deposit. Required: $' . number_format($depositCheck['required'], 2) . '. Available: $' . number_format($depositCheck['available'], 2) . '. Please add funds to your deposit wallet.',
                    ]);
                }
            }

            $secondsRemaining = now()->diffInSeconds($auctionEndDate, false);
            $timerReset = false;
            if ($secondsRemaining > 0 && $secondsRemaining < 60) {
                $newEndTime = now()->addSeconds(60);
                $listing->auction_end_time = $newEndTime;
                $timerReset = true;
            }

            $bid = \App\Models\Bid::create([
                'listing_id' => $listing->id,
                'user_id' => $user->id,
                'amount' => $amount,
                'status' => 'active',
            ]);

            if ($requiredDeposit > 0) {
                $depositService->lockDepositForBid($user, $bid, $requiredDeposit);
            }

            $listing->current_bid = $amount;
            $listing->save();

            $notificationService = new \App\Services\NotificationService();
            $notificationService->bidPlaced($user, $listing);

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

