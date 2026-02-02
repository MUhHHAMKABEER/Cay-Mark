<?php

namespace App\Http\Controllers;

use App\Models\PostAuctionThread;
use App\Models\PickupDetail;
use App\Models\PickupChangeRequest;
use App\Models\ThirdPartyPickup;
use App\Models\Invoice;
use App\Models\Listing;
use App\Services\ContentFilterService;
use App\Services\PayoutService;
use App\Services\PostAuction\PostAuctionOps;
use Illuminate\Http\Request;
use App\Http\Requests\PostAuctionPickupDetailsRequest;
use App\Http\Requests\PostAuctionPickupChangeRequest;
use App\Http\Requests\PostAuctionPickupChangeResponseRequest;
use App\Http\Requests\PostAuctionThirdPartyPickupRequest;
use App\Http\Requests\PostAuctionConfirmPickupPinRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PostAuctionMessageController extends Controller
{
    protected $contentFilter;

    public function __construct(ContentFilterService $contentFilter)
    {
        $this->contentFilter = $contentFilter;
    }

    /**
     * Show the post-auction messaging thread (restricted messaging portal).
     * Only accessible when payment is cleared.
     */
    public function showThread($invoiceId)
    {
        $user = Auth::user();
        $invoice = Invoice::with(['listing', 'buyer', 'seller'])->findOrFail($invoiceId);

        // Verify user is buyer or seller
        if ($user->id !== $invoice->buyer_id && $user->id !== $invoice->seller_id) {
            abort(403, 'Unauthorized access to this thread.');
        }

        // Get or create thread
        $thread = PostAuctionThread::firstOrCreate(
            ['invoice_id' => $invoice->id],
            [
                'listing_id' => $invoice->listing_id,
                'buyer_id' => $invoice->buyer_id,
                'seller_id' => $invoice->seller_id,
                'is_unlocked' => $invoice->payment_status === 'paid',
                'unlocked_at' => $invoice->payment_status === 'paid' ? now() : null,
            ]
        );

        // Check if thread is unlocked (payment must be cleared)
        if (!$thread->isUnlocked()) {
            return view('post-auction.thread-locked', [
                'invoice' => $invoice,
                'thread' => $thread,
            ]);
        }

        // Load relationships
        $thread->load([
            'latestPickupDetail',
            'changeRequests' => function ($q) {
                $q->where('status', 'pending')->orWhere('status', 'countered')->latest();
            },
            'activeThirdPartyPickup',
        ]);

        // Generate PIN if not exists and payment is paid
        if ($invoice->payment_status === 'paid' && !$invoice->listing->pickup_pin) {
            $invoice->listing->generatePickupPin();
            $invoice->listing->save();
        }

        return view('post-auction.thread', [
            'thread' => $thread,
            'invoice' => $invoice,
            'listing' => $invoice->listing,
            'buyer' => $invoice->buyer,
            'seller' => $invoice->seller,
            'isBuyer' => $user->id === $invoice->buyer_id,
            'isSeller' => $user->id === $invoice->seller_id,
        ]);
    }

    /**
     * Seller: Send pickup details (structured form only).
     */
    public function sendPickupDetails(PostAuctionPickupDetailsRequest $request, $threadId)
    {
        return PostAuctionOps::sendPickupDetails($request, $threadId, $this->contentFilter);
    }

    /**
     * Buyer: Accept pickup details.
     */
    public function acceptPickupDetails(Request $request, $threadId)
    {
        $user = Auth::user();
        $thread = PostAuctionThread::findOrFail($threadId);

        // Verify user is buyer
        if ($user->id !== $thread->buyer_id) {
            abort(403, 'Only buyer can accept pickup details.');
        }

        $pickupDetail = $thread->latestPickupDetail;
        if (!$pickupDetail || $pickupDetail->status !== 'pending') {
            return back()->withErrors(['error' => 'No pending pickup details to accept.']);
        }

        $pickupDetail->accept();
        $pickupDetail->confirm();

        // Generate and send PIN to buyer
        $listing = $thread->listing;
        if (!$listing->pickup_pin) {
            $pin = $listing->generatePickupPin();
            $notificationService = new \App\Services\NotificationService();
            $notificationService->pickupPinIssued($thread->buyer, $listing, $pin);
        }

        Log::info('Pickup details accepted', [
            'thread_id' => $thread->id,
            'pickup_detail_id' => $pickupDetail->id,
        ]);

        return back()->with('success', 'Pickup details accepted. Appointment confirmed.');
    }

    /**
     * Buyer: Request date/time change.
     */
    public function requestPickupChange(PostAuctionPickupChangeRequest $request, $threadId)
    {
        return PostAuctionOps::requestPickupChange($request, $threadId);
    }

    /**
     * Seller: Approve or counter change request.
     */
    public function respondToChangeRequest(PostAuctionPickupChangeResponseRequest $request, $changeRequestId)
    {
        return PostAuctionOps::respondToChangeRequest($request, $changeRequestId);
    }

    /**
     * Buyer: Authorize third-party pickup.
     */
    public function authorizeThirdPartyPickup(PostAuctionThirdPartyPickupRequest $request, $threadId)
    {
        return PostAuctionOps::authorizeThirdPartyPickup($request, $threadId);
    }

    /**
     * Seller: Confirm pickup with PIN.
     */
    public function confirmPickupWithPin(PostAuctionConfirmPickupPinRequest $request, $threadId)
    {
        return PostAuctionOps::confirmPickupWithPin($request, $threadId);
    }
}
