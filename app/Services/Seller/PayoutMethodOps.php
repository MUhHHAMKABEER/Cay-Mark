<?php

namespace App\Services\Seller;

class PayoutMethodOps
{
    public static function store($request)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $request->validated();

        $hasActiveListings = \App\Models\Listing::sellerHasActiveListings($user->id);
        $existingMethod = \App\Models\SellerPayoutMethod::where('user_id', $user->id)->first();

        if ($existingMethod && $existingMethod->is_locked && $hasActiveListings) {
            return back()->with('error', 'Cannot edit payout method while you have active listings. Please wait until all listings are completed.');
        }

        $payoutMethod = \App\Models\SellerPayoutMethod::updateOrCreate(
            ['user_id' => $user->id],
            [
                'bank_name'           => $request->bank_name,
                'account_holder_name' => $request->account_holder_name,
                'account_number'      => $request->account_number,
                'routing_number'      => $request->routing_number ?: null,
                'is_active'           => true,
                'is_verified'         => false,
            ]
        );

        if ($hasActiveListings) {
            $payoutMethod->lock();
        }

        try {
            (new \App\Services\NotificationService())->payoutDetailsUpdated($user);
        } catch (\Throwable $e) {
        }

        return redirect()->route('seller.account')
            ->with('success', 'Payout method saved successfully.');
    }
}

