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
use Illuminate\Http\Request;
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
    public function sendPickupDetails(Request $request, $threadId)
    {
        $user = Auth::user();
        $thread = PostAuctionThread::findOrFail($threadId);

        // Verify user is seller
        if ($user->id !== $thread->seller_id) {
            abort(403, 'Only seller can send pickup details.');
        }

        // Validate and filter content
        $validated = $request->validate([
            'pickup_date' => 'required|date|after_or_equal:today',
            'pickup_time' => 'required|date_format:H:i',
            'street_address' => 'required|string|max:255',
            'directions_notes' => 'nullable|string|max:1000',
        ]);

        // Filter address for contact data
        $addressFilter = $this->contentFilter->validateAddress($validated['street_address']);
        if (!$addressFilter['is_valid']) {
            return back()->withErrors([
                'street_address' => 'Address contains prohibited content (phone numbers, emails, or links).',
            ])->withInput();
        }

        // Filter directions/notes
        if (!empty($validated['directions_notes'])) {
            $notesFilter = $this->contentFilter->filterContent($validated['directions_notes']);
            if (!$notesFilter['is_valid']) {
                return back()->withErrors([
                    'directions_notes' => 'Notes contain prohibited content (phone numbers, emails, or links).',
                ])->withInput();
            }
            $validated['directions_notes'] = $notesFilter['filtered_content'];
        }

        // Create pickup detail
        $pickupDetail = PickupDetail::create([
            'thread_id' => $thread->id,
            'seller_id' => $user->id,
            'pickup_date' => $validated['pickup_date'],
            'pickup_time' => $validated['pickup_time'],
            'street_address' => $addressFilter['filtered_content'],
            'directions_notes' => $validated['directions_notes'],
            'status' => 'pending',
            'submitted_at' => now(),
        ]);

        // Send notification to buyer
        $notificationService = new \App\Services\NotificationService();
        $notificationService->pickupInstructionsAvailable($thread->buyer, $thread->listing);

        Log::info('Pickup details sent', [
            'thread_id' => $thread->id,
            'pickup_detail_id' => $pickupDetail->id,
        ]);

        return back()->with('success', 'Pickup details sent successfully.');
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
    public function requestPickupChange(Request $request, $threadId)
    {
        $user = Auth::user();
        $thread = PostAuctionThread::findOrFail($threadId);

        // Verify user is buyer
        if ($user->id !== $thread->buyer_id) {
            abort(403, 'Only buyer can request pickup changes.');
        }

        $validated = $request->validate([
            'pickup_detail_id' => 'required|exists:pickup_details,id',
            'requested_pickup_date' => 'nullable|date|after_or_equal:today',
            'requested_pickup_time' => 'nullable|date_format:H:i',
        ]);

        // At least one must be provided
        if (empty($validated['requested_pickup_date']) && empty($validated['requested_pickup_time'])) {
            return back()->withErrors(['error' => 'Please provide either a new date or time.']);
        }

        $pickupDetail = PickupDetail::findOrFail($validated['pickup_detail_id']);
        if ($pickupDetail->thread_id !== $thread->id) {
            abort(403, 'Invalid pickup detail for this thread.');
        }

        // Create change request
        $changeRequest = PickupChangeRequest::create([
            'thread_id' => $thread->id,
            'pickup_detail_id' => $pickupDetail->id,
            'buyer_id' => $user->id,
            'requested_pickup_date' => $validated['requested_pickup_date'] ?? null,
            'requested_pickup_time' => $validated['requested_pickup_time'] ?? null,
            'status' => 'pending',
            'requested_at' => now(),
        ]);

        // Update pickup detail status
        $pickupDetail->update(['status' => 'change_requested']);

        Log::info('Pickup change requested', [
            'thread_id' => $thread->id,
            'change_request_id' => $changeRequest->id,
        ]);

        return back()->with('success', 'Change request sent to seller.');
    }

    /**
     * Seller: Approve or counter change request.
     */
    public function respondToChangeRequest(Request $request, $changeRequestId)
    {
        $user = Auth::user();
        $changeRequest = PickupChangeRequest::with('thread')->findOrFail($changeRequestId);

        // Verify user is seller
        if ($user->id !== $changeRequest->thread->seller_id) {
            abort(403, 'Only seller can respond to change requests.');
        }

        $validated = $request->validate([
            'action' => 'required|in:approve,counter',
            'countered_pickup_date' => 'required_if:action,counter|date|after_or_equal:today',
            'countered_pickup_time' => 'required_if:action,counter|date_format:H:i',
        ]);

        if ($validated['action'] === 'approve') {
            $changeRequest->approve();
            
            // Send notification to buyer
            $notificationService = new \App\Services\NotificationService();
            $notificationService->pickupRescheduleApproved($changeRequest->thread->buyer, $changeRequest->thread->listing);
            
            return back()->with('success', 'Change request approved.');
        } else {
            $changeRequest->counter(
                $validated['countered_pickup_date'],
                $validated['countered_pickup_time']
            );
            
            // Send notification to buyer
            $notificationService = new \App\Services\NotificationService();
            $notificationService->pickupRescheduleRejected($changeRequest->thread->buyer, $changeRequest->thread->listing);
            
            return back()->with('success', 'Counter offer sent to buyer.');
        }
    }

    /**
     * Buyer: Authorize third-party pickup.
     */
    public function authorizeThirdPartyPickup(Request $request, $threadId)
    {
        $user = Auth::user();
        $thread = PostAuctionThread::findOrFail($threadId);

        // Verify user is buyer
        if ($user->id !== $thread->buyer_id) {
            abort(403, 'Only buyer can authorize third-party pickup.');
        }

        $validated = $request->validate([
            'authorized_name' => 'required|string|max:255',
            'pickup_type' => 'required|in:tow_company,individual,authorized_representative',
        ]);

        // Deactivate any existing third-party pickups
        ThirdPartyPickup::where('thread_id', $thread->id)
            ->update(['is_active' => false]);

        // Create new third-party pickup authorization
        $thirdPartyPickup = ThirdPartyPickup::create([
            'thread_id' => $thread->id,
            'buyer_id' => $user->id,
            'authorized_name' => $validated['authorized_name'],
            'pickup_type' => $validated['pickup_type'],
            'is_active' => true,
            'authorized_at' => now(),
        ]);

        Log::info('Third-party pickup authorized', [
            'thread_id' => $thread->id,
            'third_party_pickup_id' => $thirdPartyPickup->id,
        ]);

        return back()->with('success', 'Third-party pickup authorized successfully.');
    }

    /**
     * Seller: Confirm pickup with PIN.
     */
    public function confirmPickupWithPin(Request $request, $threadId)
    {
        $user = Auth::user();
        $thread = PostAuctionThread::with(['listing', 'invoice'])->findOrFail($threadId);

        // Verify user is seller
        if ($user->id !== $thread->seller_id) {
            abort(403, 'Only seller can confirm pickup.');
        }

        $validated = $request->validate([
            'pickup_pin' => 'required|string|size:4',
        ]);

        $listing = $thread->listing;
        $pin = $validated['pickup_pin'];

        // Verify PIN
        if (!$listing->verifyPickupPin($pin)) {
            Log::warning('Invalid pickup PIN attempted', [
                'thread_id' => $thread->id,
                'seller_id' => $user->id,
                'attempted_pin' => $pin,
            ]);

            return back()->withErrors([
                'pickup_pin' => 'Invalid PIN. Please verify the PIN with the buyer.',
            ]);
        }

        // Confirm pickup
        DB::transaction(function () use ($thread, $listing) {
            $thread->update([
                'pickup_confirmed' => true,
                'pickup_confirmed_at' => now(),
            ]);

            $listing->update([
                'pickup_confirmed' => true,
                'pickup_confirmed_at' => now(),
                'pickup_confirmed_by' => $thread->seller_id,
            ]);

            // Trigger payout creation (per PDF requirements)
            $payoutService = new PayoutService();
            $payoutService->createPayoutAfterPickup($thread->invoice, $listing);

            Log::info('Pickup confirmed with PIN', [
                'thread_id' => $thread->id,
                'listing_id' => $listing->id,
                'invoice_id' => $thread->invoice_id,
            ]);
        });

        return back()->with('success', 'Pickup confirmed! Payout process has been initiated.');
    }
}
