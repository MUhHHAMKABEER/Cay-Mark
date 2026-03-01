<?php

namespace App\Services\Seller;

class SellerDashboardOps
{
    public static function updatePayout($request, $repository)
    {
        $request->validated();
        $user = \Illuminate\Support\Facades\Auth::user();
        $existing = $repository->getPayoutMethod($user);

        // Do not overwrite with masked or empty values when updating
        $accountNumber = $request->account_number;
        if ($existing && (empty(trim((string) $accountNumber)) || str_starts_with(trim((string) $accountNumber), '****'))) {
            $accountNumber = $existing->account_number; // keep existing (decrypted)
        }
        $routingNumber = $request->routing_number;
        if ($existing && (empty(trim((string) $routingNumber)) || str_starts_with(trim((string) $routingNumber), '****'))) {
            $routingNumber = $existing->routing_number;
        }
        $swiftNumber = $request->swift_number ?? $request->swift_code;
        if ($existing && (empty(trim((string) $swiftNumber)) || str_starts_with(trim((string) $swiftNumber), '****'))) {
            $swiftNumber = $existing->swift_number;
        }

        // New record must have account_number
        if (!$existing && empty(trim((string) $accountNumber))) {
            return back()->withErrors(['account_number' => 'Account number is required when adding payout settings.']);
        }

        \App\Models\SellerPayoutMethod::where('user_id', $user->id)
            ->update(['is_active' => false]);

        $data = [
            'bank_name' => $request->bank_name,
            'account_holder_name' => $request->account_holder_name,
            'account_number' => $accountNumber,
            'routing_number' => $routingNumber ?: null,
            'swift_number' => $swiftNumber ?: null,
            'is_active' => true,
        ];

        $repository->savePayoutMethod($user, $data);

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

