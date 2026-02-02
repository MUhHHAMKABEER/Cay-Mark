<?php

namespace App\Services\Seller;

class PickupPinOps
{
    public static function confirm($request, $listingId)
    {
        $request->validated();

        $user = \Illuminate\Support\Facades\Auth::user();
        $listing = \App\Models\Listing::where('id', $listingId)
            ->where('seller_id', $user->id)
            ->firstOrFail();

        $invoice = $listing->invoices()->where('payment_status', 'paid')->firstOrFail();

        return \Illuminate\Support\Facades\DB::transaction(function () use ($listing, $invoice, $user, $request) {
            if (!$listing->confirmPickup($request->pickup_pin, $user->id)) {
                return back()->with('error', 'Invalid pickup PIN. Please check and try again.');
            }

            $payoutService = new \App\Services\PayoutService();
            $payout = $payoutService->createPayoutAfterPickup($invoice, $listing);

            try {
                \Mail::send('emails.payout-processing-started', [
                    'payout' => $payout,
                    'seller' => $user,
                    'listing' => $listing,
                ], function ($message) use ($user) {
                    $message->to($user->email, $user->name)
                        ->subject('Payout Processing Started');
                });

                $notificationService = new \App\Services\NotificationService();
                $notificationService->transactionCompletedPayoutPending($user, $listing);
                $notificationService->pickupCompleted($invoice->buyer, $listing);
            } catch (\Exception $e) {
                \Log::error('Failed to send payout processing email: ' . $e->getMessage());
            }

            return redirect()->route('seller.payouts')
                ->with('success', 'Pickup confirmed! Payout record created and sent to finance for processing.');
        });
    }
}

