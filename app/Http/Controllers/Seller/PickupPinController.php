<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Listing;
use App\Models\Invoice;
use App\Services\PayoutService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class PickupPinController extends Controller
{
    /**
     * Show pickup PIN confirmation form.
     */
    public function show($listingId)
    {
        $user = Auth::user();
        $listing = Listing::where('id', $listingId)
            ->where('seller_id', $user->id)
            ->with(['invoices.buyer'])
            ->firstOrFail();

        // Get the invoice for this listing
        $invoice = $listing->invoices()->where('payment_status', 'paid')->first();

        if (!$invoice) {
            return back()->with('error', 'No paid invoice found for this listing.');
        }

        return view('Seller.pickup-pin-confirm', compact('listing', 'invoice'));
    }

    /**
     * Confirm pickup with PIN (creates payout record).
     */
    public function confirm(Request $request, $listingId)
    {
        $request->validate([
            'pickup_pin' => 'required|string|size:6',
        ]);

        $user = Auth::user();
        $listing = Listing::where('id', $listingId)
            ->where('seller_id', $user->id)
            ->firstOrFail();

        // Get the invoice
        $invoice = $listing->invoices()->where('payment_status', 'paid')->firstOrFail();

        return DB::transaction(function () use ($listing, $invoice, $user, $request) {
            // Confirm pickup with PIN
            if (!$listing->confirmPickup($request->pickup_pin, $user->id)) {
                return back()->with('error', 'Invalid pickup PIN. Please check and try again.');
            }

            // Create payout record AFTER PIN confirmation (per PDF requirements)
            $payoutService = new PayoutService();
            $payout = $payoutService->createPayoutAfterPickup($invoice, $listing);

            // Send seller notification
            try {
                Mail::send('emails.payout-processing-started', [
                    'payout' => $payout,
                    'seller' => $user,
                    'listing' => $listing,
                ], function ($message) use ($user) {
                    $message->to($user->email, $user->name)
                        ->subject('Payout Processing Started');
                });
                
                // Send in-app notifications
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
