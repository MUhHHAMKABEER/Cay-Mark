<?php

namespace App\Services\Seller;

class SellerDashboardOps
{
    public static function updatePayout($request, $repository)
    {
        $request->validated();
        $user = \Illuminate\Support\Facades\Auth::user();

        \App\Models\SellerPayoutMethod::where('user_id', $user->id)
            ->update(['is_active' => false]);

        $repository->savePayoutMethod($user, [
            'bank_name' => $request->bank_name,
            'account_holder_name' => $request->account_holder_name,
            'account_number' => $request->account_number,
            'routing_number' => $request->routing_number,
            'swift_number' => $request->swift_number,
            'is_active' => true,
        ]);

        return back()->with('success', 'Payout settings updated successfully.');
    }

    public static function changePassword($request)
    {
        $request->validated();
        $user = \Illuminate\Support\Facades\Auth::user();
        $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
        $user->save();

        return back()->with('success', 'Password changed successfully.');
    }

    public static function confirmPickup($request, $listingId, $repository)
    {
        $request->validated();
        $user = \Illuminate\Support\Facades\Auth::user();
        $listing = $repository->getListingById($user, $listingId);

        if (!$listing) {
            return back()->withErrors(['pickup_pin' => 'Listing not found.']);
        }

        if ($listing->pickup_pin !== $request->pickup_pin) {
            return back()->withErrors(['pickup_pin' => 'Invalid pickup PIN.']);
        }

        $listing->pickup_confirmed = true;
        $listing->pickup_confirmed_at = now();
        $listing->pickup_confirmed_by = $user->id;
        $listing->save();

        return back()->with('success', 'Pickup confirmed successfully. Payment processing has begun.');
    }
}

