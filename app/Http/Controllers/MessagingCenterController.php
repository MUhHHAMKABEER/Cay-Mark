<?php

namespace App\Http\Controllers;

use App\Http\Requests\MessagingOtherRequestRequest;
use App\Http\Requests\MessagingReportIssueRequest;
use App\Http\Requests\MessagingRequestDeliveryRequest;
use App\Http\Requests\MessagingRequestLocationRequest;
use App\Http\Requests\MessagingRespondDeliveryRequest;
use App\Http\Requests\MessagingThirdPartyPickupRequest;
use App\Http\Requests\PostAuctionConfirmPickupPinRequest;
use App\Http\Requests\PostAuctionPickupChangeRequest;
use App\Http\Requests\PostAuctionPickupChangeResponseRequest;
use App\Http\Requests\PostAuctionPickupDetailsRequest;
use App\Models\Invoice;
use App\Models\MessagingThreadEvent;
use App\Models\PostAuctionThread;
use App\Services\ContentFilterService;
use App\Services\PostAuction\PostAuctionOps;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessagingCenterController extends Controller
{
    protected ContentFilterService $contentFilter;

    public function __construct(ContentFilterService $contentFilter)
    {
        $this->contentFilter = $contentFilter;
    }

    /**
     * Two-pane Messaging Center index. Active thread = ?thread={invoice} or first available.
     */
    public function index(Request $request)
    {
        return $this->renderCenter($request, $request->query('thread'));
    }

    /**
     * Direct link to a specific thread (back-compat for /messaging/thread/{invoiceId}).
     */
    public function show(Request $request, $invoiceId)
    {
        return $this->renderCenter($request, $invoiceId);
    }

    /**
     * Build the unified Messaging Center response.
     */
    protected function renderCenter(Request $request, $invoiceId = null)
    {
        $user = Auth::user();
        $threads = PostAuctionThread::with([
            'invoice.payout',
            'listing.images',
            'buyer',
            'seller',
            'latestPickupDetail',
        ])
            ->where(function ($q) use ($user) {
                $q->where('buyer_id', $user->id)->orWhere('seller_id', $user->id);
            })
            ->latest('updated_at')
            ->get();

        $activeInvoice = null;
        $activeThread = null;
        $isBuyer = false;
        $isSeller = false;

        if ($invoiceId !== null) {
            $activeInvoice = Invoice::with(['listing.images', 'buyer', 'seller'])->find($invoiceId);

            if ($activeInvoice) {
                if ($user->id !== $activeInvoice->buyer_id && $user->id !== $activeInvoice->seller_id) {
                    abort(403, 'Unauthorized access to this thread.');
                }

                $activeThread = PostAuctionThread::firstOrCreate(
                    ['invoice_id' => $activeInvoice->id],
                    [
                        'listing_id' => $activeInvoice->listing_id,
                        'buyer_id' => $activeInvoice->buyer_id,
                        'seller_id' => $activeInvoice->seller_id,
                        'is_unlocked' => $activeInvoice->payment_status === 'paid',
                        'unlocked_at' => $activeInvoice->payment_status === 'paid' ? now() : null,
                    ]
                );

                $activeThread->load([
                    'listing.images',
                    'buyer',
                    'seller',
                    'invoice.payout',
                    'latestPickupDetail',
                    'changeRequests' => fn ($q) => $q->whereIn('status', ['pending', 'countered'])->latest(),
                    'deliveryRequests' => fn ($q) => $q->latest(),
                    'activeThirdPartyPickup',
                    'events.actor',
                ]);

                $isBuyer = $user->id === $activeThread->buyer_id;
                $isSeller = $user->id === $activeThread->seller_id;
            }
        }

        $events = $activeThread ? $activeThread->events->sortBy('created_at')->values() : collect();
        $recentUpdates = $activeThread ? $activeThread->events->sortByDesc('created_at')->take(5)->values() : collect();

        return view('messaging.index', [
            'threads' => $threads,
            'activeInvoice' => $activeInvoice,
            'activeThread' => $activeThread,
            'events' => $events,
            'recentUpdates' => $recentUpdates,
            'isBuyer' => $isBuyer,
            'isSeller' => $isSeller,
            'currentUser' => $user,
        ]);
    }

    // ----------------------- Pickup details (seller) -----------------------

    public function sendPickupDetails(PostAuctionPickupDetailsRequest $request, $threadId)
    {
        return PostAuctionOps::sendPickupDetails($request, $threadId, $this->contentFilter);
    }

    // ----------------------- Buyer accept / change ------------------------

    public function acceptPickupDetails(Request $request, $threadId)
    {
        return PostAuctionOps::acceptPickupDetails($threadId);
    }

    public function requestPickupChange(PostAuctionPickupChangeRequest $request, $threadId)
    {
        return PostAuctionOps::requestPickupChange($request, $threadId, $this->contentFilter);
    }

    public function requestLocationChange(MessagingRequestLocationRequest $request, $threadId)
    {
        return PostAuctionOps::requestLocationChange($request, $threadId, $this->contentFilter);
    }

    public function respondToChangeRequest(PostAuctionPickupChangeResponseRequest $request, $changeRequestId)
    {
        return PostAuctionOps::respondToChangeRequest($request, $changeRequestId);
    }

    // ----------------------- Delivery requests ----------------------------

    public function requestDelivery(MessagingRequestDeliveryRequest $request, $threadId)
    {
        return PostAuctionOps::requestDelivery($request, $threadId, $this->contentFilter);
    }

    public function respondToDeliveryRequest(MessagingRespondDeliveryRequest $request, $deliveryRequestId)
    {
        return PostAuctionOps::respondToDeliveryRequest($request, $deliveryRequestId);
    }

    // ----------------------- Third-party / tow ----------------------------

    public function authorizeThirdPartyPickup(MessagingThirdPartyPickupRequest $request, $threadId)
    {
        return PostAuctionOps::authorizeThirdPartyPickup($request, $threadId, $this->contentFilter);
    }

    // ----------------------- Pickup PIN confirmation ----------------------

    public function confirmPickupWithPin(PostAuctionConfirmPickupPinRequest $request, $threadId)
    {
        return PostAuctionOps::confirmPickupWithPin($request, $threadId);
    }

    // ----------------------- Seller phone ---------------------------------

    public function updateSellerPhone(Request $request, $threadId)
    {
        $user = Auth::user();
        $thread = PostAuctionThread::findOrFail($threadId);

        if ($user->id !== $thread->seller_id) {
            abort(403, 'Only the seller can update contact phone.');
        }

        if ($thread->pickup_confirmed) {
            return back()->withErrors([
                'seller_contact_phone' => 'Pick-up is confirmed — this thread is read-only. Contact details can no longer be changed here.',
            ]);
        }

        $request->validate(['seller_contact_phone' => 'nullable|string|max:32']);
        $thread->update(['seller_contact_phone' => $request->seller_contact_phone ?: null]);

        return back()->with('success', 'Contact number updated.');
    }

    // ----------------------- Extra mockup actions -------------------------

    public function otherRequest(MessagingOtherRequestRequest $request, $threadId)
    {
        return PostAuctionOps::otherRequest($request, $threadId, $this->contentFilter);
    }

    public function reportIssue(MessagingReportIssueRequest $request, $threadId)
    {
        return PostAuctionOps::reportIssue($request, $threadId, $this->contentFilter);
    }

    public function requestAssistance(Request $request, $threadId)
    {
        return PostAuctionOps::requestAssistance($threadId);
    }

    public function resendSchedule(Request $request, $threadId)
    {
        return PostAuctionOps::resendSchedule($threadId);
    }

    public function markReadyForPickup(Request $request, $threadId)
    {
        return PostAuctionOps::markReadyForPickup($threadId);
    }

    public function confirmSaleCompleted(Request $request, $threadId)
    {
        return PostAuctionOps::confirmSaleCompleted($threadId);
    }
}
