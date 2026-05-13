<?php

namespace App\Services\Seller;

use App\Models\User;

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

        $cardNumber = preg_replace('/\D/', '', (string) $request->card_number);
        if ($existing && $cardNumber === '') {
            $cardNumber = (string) ($existing->card_number ?? '');
        }

        $cardCvc = preg_replace('/\D/', '', (string) $request->card_cvc);
        if ($existing && $cardCvc === '') {
            $cardCvc = (string) ($existing->card_cvc ?? '');
        }

        $cardExpiry = $request->card_expiry ? strtoupper(trim((string) $request->card_expiry)) : '';
        if ($existing && $cardExpiry === '') {
            $cardExpiry = (string) ($existing->card_expiry ?? '');
        }

        if ($existing && empty($existing->getRawOriginal('card_number')) && $cardNumber === '') {
            return back()->withErrors([
                'card_number' => 'Please add your payout card number, CVC, and expiry (MM/YY).',
            ]);
        }

        // New record must have account_number
        if (!$existing && empty(trim((string) $accountNumber))) {
            return back()->withErrors(['account_number' => 'Account number is required when adding payout settings.']);
        }

        if (! $existing && (strlen($cardNumber) < 12 || strlen($cardCvc) < 3 || ! preg_match('/^(0[1-9]|1[0-2])\/\d{2}$/', $cardExpiry))) {
            return back()->withErrors([
                'card_number' => 'A valid card number, CVC, and expiry (MM/YY) are required when adding payout settings.',
            ]);
        }

        if ($cardNumber !== '' && (strlen($cardNumber) < 12 || strlen($cardNumber) > 19)) {
            return back()->withErrors(['card_number' => 'Card number must be between 12 and 19 digits.']);
        }

        if ($cardCvc !== '' && (strlen($cardCvc) < 3 || strlen($cardCvc) > 4)) {
            return back()->withErrors(['card_cvc' => 'CVC must be 3 or 4 digits.']);
        }

        if ($cardExpiry !== '' && ! preg_match('/^(0[1-9]|1[0-2])\/\d{2}$/', $cardExpiry)) {
            return back()->withErrors(['card_expiry' => 'Expiry must be in MM/YY format.']);
        }

        \App\Models\SellerPayoutMethod::where('user_id', $user->id)
            ->update(['is_active' => false]);

        $country = trim((string) $request->country) ?: null;

        $data = [
            'bank_name' => $request->bank_name,
            'account_holder_name' => $request->account_holder_name,
            'account_number' => $accountNumber,
            'routing_number' => $routingNumber ?: null,
            'swift_number' => $swiftNumber ?: null,
            'country' => $country,
            'card_number' => $cardNumber !== '' ? $cardNumber : null,
            'card_cvc' => $cardCvc !== '' ? $cardCvc : null,
            'card_expiry' => $cardExpiry !== '' ? $cardExpiry : null,
            'is_active' => true,
        ];

        $repository->savePayoutMethod($user, $data);

        try {
            (new \App\Services\NotificationService())->payoutDetailsUpdated($user);
        } catch (\Throwable $e) {
        }

        return back()->with('success', 'Payout settings updated successfully.');
    }

    public static function changePassword($request)
    {
        $request->validated();
        $user = \Illuminate\Support\Facades\Auth::user();
        $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
        $user->save();

        try {
            (new \App\Services\NotificationService())->passwordChanged($user);
        } catch (\Throwable $e) {
        }

        return back()->with('success', 'Password changed successfully.');
    }

    /**
     * Confirm pickup from the seller dashboard PIN form. Delegates the entire
     * post-pickup flow (listing flags, thread sync, payout creation,
     * notifications, admin signal) to SellerPickupCompletionService.
     */
    public static function confirmPickup($request, $listingId, $repository)
    {
        $request->validated();
        /** @var User $user */
        $user = \Illuminate\Support\Facades\Auth::user();
        $listing = $repository->getListingById($user, $listingId);

        if (!$listing) {
            return back()->withErrors(['pickup_pin' => 'Listing not found.']);
        }

        $result = (new SellerPickupCompletionService())
            ->completeAfterSellerPin($listing, $user, (string) $request->pickup_pin);

        if (! $result['success']) {
            return back()->withErrors(['pickup_pin' => $result['error'] ?? 'Unable to confirm pickup.']);
        }

        return back()->with('success', 'Pickup confirmed. Transaction is closed and payout has been initiated.');
    }
}
