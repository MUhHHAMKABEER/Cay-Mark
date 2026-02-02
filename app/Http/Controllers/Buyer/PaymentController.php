<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PostAuctionThread;
use App\Services\CommissionService;
use App\Services\Buyer\BuyerPaymentOps;
use Illuminate\Http\Request;
use App\Http\Requests\BuyerPaymentInvoiceSelectionRequest;
use App\Http\Requests\BuyerPaymentCardRequest;
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
    public function checkoutMultiple(BuyerPaymentInvoiceSelectionRequest $request)
    {
        return BuyerPaymentOps::checkoutMultiple($request);
    }

    /**
     * Process payment (both single and multi-item).
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function processPayment(BuyerPaymentCardRequest $request)
    {
        return BuyerPaymentOps::processPayment($request, $this->commissionService);
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
