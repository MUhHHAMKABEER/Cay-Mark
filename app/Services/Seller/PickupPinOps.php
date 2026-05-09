<?php

namespace App\Services\Seller;

class PickupPinOps
{
    /**
     * Standalone pickup-PIN confirm page (legacy /seller/pickup-pin/{id}).
     * Delegates to SellerPickupCompletionService and redirects to payouts so
     * the seller lands on the payout dashboard immediately after success.
     */
    public static function confirm($request, $listingId)
    {
        $request->validated();

        $user = \Illuminate\Support\Facades\Auth::user();
        $listing = \App\Models\Listing::where('id', $listingId)
            ->where('seller_id', $user->id)
            ->firstOrFail();

        $result = (new SellerPickupCompletionService())
            ->completeAfterSellerPin($listing, $user, (string) $request->pickup_pin);

        if (! $result['success']) {
            return back()->with('error', $result['error'] ?? 'Unable to confirm pickup.');
        }

        return redirect()->route('seller.payouts')
            ->with('success', 'Pickup confirmed! Payout record created and sent to finance for processing.');
    }
}
