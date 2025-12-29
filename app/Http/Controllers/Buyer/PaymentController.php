<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PostAuctionThread;
use App\Services\CommissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $commissionService;

    public function __construct()
    {
        $this->commissionService = new CommissionService();
    }

    /**
     * Payment Path A: Single item checkout from email/notification.
     * 
     * @param int $invoiceId
     * @return \Illuminate\View\View
     */
    public function checkoutSingle($invoiceId)
    {
        $user = Auth::user();
        $invoice = Invoice::where('id', $invoiceId)
            ->where('buyer_id', $user->id)
            ->where('payment_status', 'pending')
            ->with(['listing.images', 'seller'])
            ->firstOrFail();

        return view('Buyer.payment-checkout-single', compact('invoice'));
    }

    /**
     * Payment Path B: Multi-item checkout from buyer dashboard.
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function checkoutMultiple(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'invoice_ids' => 'required|array',
            'invoice_ids.*' => 'exists:invoices,id',
        ]);

        $invoices = Invoice::whereIn('id', $request->invoice_ids)
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

    /**
     * Process payment (both single and multi-item).
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function processPayment(Request $request)
    {
        $user = Auth::user();
        
        // ACCOUNT RESTRICTION: Check if user is restricted from making purchases (per PDF requirements)
        if ($user->is_restricted) {
            // Check if restriction has expired
            if ($user->restriction_ends_at && now()->greaterThan($user->restriction_ends_at)) {
                // Auto-remove expired restriction
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
        
        $request->validate([
            'invoice_ids' => 'required|array',
            'invoice_ids.*' => 'exists:invoices,id',
            'card_number' => 'required|string',
            'card_expiry' => 'required|string',
            'card_cvv' => 'required|string',
            'cardholder_name' => 'required|string',
        ]);

        $invoices = Invoice::whereIn('id', $request->invoice_ids)
            ->where('buyer_id', $user->id)
            ->where('payment_status', 'pending')
            ->with(['listing', 'seller'])
            ->get();

        if ($invoices->isEmpty()) {
            return back()->with('error', 'No valid invoices found.');
        }

        return DB::transaction(function () use ($user, $invoices, $request) {
            $grandTotal = $invoices->sum('total_amount_due');
            
            // TODO: Integrate with payment gateway (Stripe, PayPal, etc.)
            // For now, simulate successful payment
            $gatewayTransactionId = 'TXN-' . time() . '-' . uniqid();
            $paymentStatus = 'completed'; // Will be updated based on gateway response

            // Create payment record(s)
            foreach ($invoices as $invoice) {
                // Calculate seller payout amount
                $sellerPayout = $this->commissionService->calculateSellerPayout($invoice->winning_bid_amount);
                
                $payment = Payment::create([
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

                // Update invoice status
                $invoice->payment_status = 'paid';
                $invoice->paid_at = now();
                $invoice->save();

                // Generate pickup PIN (4 digits, visible to buyer only)
                $invoice->listing->generatePickupPin();

                // Unlock post-auction messaging thread (per PDF requirements)
                $this->unlockPostAuctionThread($invoice);

                // Send notifications
                $this->sendPaymentNotifications($invoice, $user, $payment);
                
                // Send in-app notification
                $notificationService = new \App\Services\NotificationService();
                $notificationService->paymentSuccessful($user, $invoice);
                
                // Notify seller to send pickup info
                $notificationService->sendPickupInfo($invoice->seller, $invoice->listing);
            }

            return redirect()->route('buyer.auctions-won')
                ->with('success', 'Payment processed successfully! Sellers have been notified.');
        });
    }

    /**
     * Send payment notifications to buyer and seller.
     */
    protected function sendPaymentNotifications(Invoice $invoice, $buyer, Payment $payment)
    {
        // Buyer notification
        try {
            Mail::send('emails.payment-successful', [
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

        // Seller notification
        try {
            Mail::send('emails.seller-payment-received', [
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

    /**
     * Unlock post-auction messaging thread when payment clears (per PDF requirements).
     * Creates thread if it doesn't exist and unlocks it.
     */
    protected function unlockPostAuctionThread(Invoice $invoice): void
    {
        try {
            $thread = PostAuctionThread::firstOrCreate(
                ['invoice_id' => $invoice->id],
                [
                    'listing_id' => $invoice->listing_id,
                    'buyer_id' => $invoice->buyer_id,
                    'seller_id' => $invoice->seller_id,
                    'is_unlocked' => false,
                ]
            );

            // Unlock the thread
            $thread->unlock();

            Log::info('Post-auction thread unlocked after payment', [
                'thread_id' => $thread->id,
                'invoice_id' => $invoice->id,
            ]);

            // TODO: Send dashboard notifications to both buyer and seller
            // that messaging thread is now available
        } catch (\Exception $e) {
            Log::error('Failed to unlock post-auction thread: ' . $e->getMessage());
        }
    }
}
