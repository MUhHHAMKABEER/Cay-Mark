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
            ->whereIn('payment_status', ['pending', 'overdue', 'partial'])
            ->with(['listing.images', 'seller'])
            ->firstOrFail();

        // deposit_applied and total_amount_due are set correctly in the DB at invoice
        // creation time (InvoiceService). No re-computation needed here.
        // - deposit_applied : amount credited from the buyer's security deposit wallet
        // - total_amount_due: original_amount − deposit_applied (what the buyer owes)
        // - original_amount : full bid + commission, unaffected by deposit
        $depositApplied    = (float) ($invoice->deposit_applied ?? 0);
        $totalAfterDeposit = (float) $invoice->total_amount_due;

        return view('Buyer.payment-checkout-single', compact('invoice', 'depositApplied', 'totalAfterDeposit'));
    }

    /**
     * Immediate post-payment confirmation (pickup code + next steps).
     */
    public function paymentSuccess(Invoice $invoice)
    {
        $user = Auth::user();
        if ((int) $invoice->buyer_id !== (int) $user->id) {
            abort(403);
        }
        if ($invoice->payment_status !== 'paid') {
            return redirect()->route('buyer.payment.checkout-single', $invoice->id);
        }

        $invoice->load(['listing.images', 'seller']);

        return view('Buyer.payment-success', compact('invoice'));
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
        $invoice->loadMissing('listing');
        $pickupCode = $invoice->listing?->pickupCodeDisplay();
        $messagingCenterUrl = route('messaging.thread.show', $invoice->id);

        // Buyer notification
        try {
            Mail::send('emails.caymark.payment-successful', [
                'invoice' => $invoice,
                'buyer' => $buyer,
                'payment' => $payment,
                'pickup_code' => $pickupCode,
                'messaging_center_url' => $messagingCenterUrl,
            ], function ($message) use ($buyer, $invoice) {
                $message->to($buyer->email, $buyer->name)
                    ->subject('Payment Successful – ' . ($invoice->item_name ?? '[VEHICLE_NAME]'));
            });
        } catch (\Exception $e) {
            \Log::error('Failed to send payment success email: ' . $e->getMessage());
        }

        // Seller notification
        try {
            Mail::send('emails.caymark.seller-payment-received', [
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

}
