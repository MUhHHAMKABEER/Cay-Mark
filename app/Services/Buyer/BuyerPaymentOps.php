<?php

namespace App\Services\Buyer;

use App\Services\DemoPaymentGateway;

class BuyerPaymentOps
{
    public static function checkoutMultiple($request)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $request->validated();

        $invoices = \App\Models\Invoice::whereIn('id', $request->invoice_ids)
            ->where('buyer_id', $user->id)
            ->where('payment_status', 'pending')
            ->with(['listing.images', 'seller'])
            ->get();

        if ($invoices->isEmpty()) {
            return redirect()->route('buyer.auctions-won')
                ->with('error', 'No valid invoices selected for payment.');
        }

        $grandTotal = $invoices->sum('total_amount_due');

        return view('Buyer.payment-checkout-multiple', compact('invoices', 'grandTotal'));
    }

    public static function processPayment($request, $commissionService)
    {
        $user = \Illuminate\Support\Facades\Auth::user();

        if ($user->is_restricted) {
            if ($user->restriction_ends_at && now()->greaterThan($user->restriction_ends_at)) {
                $user->update([
                    'is_restricted' => false,
                    'restriction_ends_at' => null,
                    'restriction_reason' => null,
                ]);
            } else {
                return back()->withErrors([
                    'restricted' => 'Your account is currently restricted from making purchases due to a non-payment default. This restriction will be lifted on ' . $user->restriction_ends_at->format('F d, Y') . '. You can still browse listings, view your account, and contact support.'
                ]);
            }
        }

        $request->validated();

        $invoices = \App\Models\Invoice::whereIn('id', $request->invoice_ids)
            ->where('buyer_id', $user->id)
            ->where('payment_status', 'pending')
            ->with(['listing', 'seller'])
            ->get();

        if ($invoices->isEmpty()) {
            return back()->with('error', 'No valid invoices found.');
        }

        return \Illuminate\Support\Facades\DB::transaction(function () use ($user, $invoices, $request, $commissionService) {
            $grandTotal = $invoices->sum('total_amount_due');
            $amountCents = (int) round($grandTotal * 100);

            $gateway = new DemoPaymentGateway();
            $result = $gateway->charge(
                $amountCents,
                $request->card_number ?? '',
                $request->card_expiry ?? '',
                $request->card_cvv ?? '',
                $request->cardholder_name ?? ''
            );

            if (!$result['success']) {
                return back()->withErrors(['card_number' => $result['message']]);
            }

            $gatewayTransactionId = $result['transaction_id'] ?? ('TXN-' . time() . '-' . uniqid());
            $paymentStatus = 'completed';

            foreach ($invoices as $invoice) {
                $sellerPayout = $commissionService->calculateSellerPayout($invoice->winning_bid_amount);

                $payment = \App\Models\Payment::create([
                    'user_id' => $user->id,
                    'invoice_id' => $invoice->id,
                    'listing_id' => $invoice->listing_id,
                    'seller_id' => $invoice->seller_id,
                    'amount' => $invoice->total_amount_due,
                    'method' => 'credit_card',
                    'status' => $paymentStatus,
                    'gateway_transaction_id' => $gatewayTransactionId,
                    'payment_reference' => $gatewayTransactionId,
                    'item_title' => $invoice->item_name,
                    'item_id' => $invoice->item_id,
                    'platform_fee_retained' => $invoice->buyer_commission + $sellerPayout['seller_commission'],
                    'seller_payout_amount' => $sellerPayout['net_payout'],
                    'metadata' => [
                        'card_last4' => substr($request->card_number, -4),
                        'cardholder_name' => $request->cardholder_name,
                    ],
                ]);

                $invoice->payment_status = 'paid';
                $invoice->paid_at = now();
                $invoice->save();

                $invoice->listing->generatePickupPin();
                static::unlockPostAuctionThread($invoice);
                static::sendPaymentNotifications($invoice, $user, $payment);

                $notificationService = new \App\Services\NotificationService();
                $notificationService->paymentSuccessful($user, $invoice);
                $notificationService->sendPickupInfo($invoice->seller, $invoice->listing);
            }

            return redirect()->route('buyer.auctions-won')
                ->with('success', 'Payment processed successfully! Sellers have been notified.');
        });
    }

    protected static function sendPaymentNotifications($invoice, $buyer, $payment)
    {
        try {
            \Mail::send('emails.payment-successful', [
                'invoice' => $invoice,
                'buyer' => $buyer,
                'payment' => $payment,
            ], function ($message) use ($buyer, $invoice) {
                $message->to($buyer->email, $buyer->name)
                    ->subject('Payment Successful – ' . ($invoice->item_name ?? '[VEHICLE_NAME]'));
            });
        } catch (\Exception $e) {
            \Log::error('Failed to send payment success email: ' . $e->getMessage());
        }

        try {
            \Mail::send('emails.seller-payment-received', [
                'invoice' => $invoice,
                'seller' => $invoice->seller,
                'payment' => $payment,
            ], function ($message) use ($invoice) {
                $message->to($invoice->seller->email, $invoice->seller->name)
                    ->subject('Buyer Payment Received - CayMark');
            });
        } catch (\Exception $e) {
            \Log::error('Failed to send seller payment notification: ' . $e->getMessage());
        }
    }

    protected static function unlockPostAuctionThread($invoice): void
    {
        try {
            $thread = \App\Models\PostAuctionThread::firstOrCreate(
                ['invoice_id' => $invoice->id],
                [
                    'listing_id' => $invoice->listing_id,
                    'buyer_id' => $invoice->buyer_id,
                    'seller_id' => $invoice->seller_id,
                    'is_unlocked' => false,
                ]
            );

            $thread->unlock();
        } catch (\Exception $e) {
            \Log::error('Failed to unlock post-auction thread: ' . $e->getMessage());
        }
    }
}

