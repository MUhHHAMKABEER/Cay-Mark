<?php

namespace App\Services\PostAuction;

use App\Models\MessagingThreadEvent;
use App\Models\PickupChangeRequest;
use App\Models\PickupDeliveryRequest;
use App\Models\PickupDetail;
use App\Models\PostAuctionThread;
use App\Models\SupportTicket;
use App\Models\ThirdPartyPickup;
use App\Models\User;
use App\Services\ContentFilterService;
use App\Services\Messaging\MessagingNotifier;
use App\Services\NotificationService;
use App\Services\PayoutService;
use App\Services\Seller\SellerPickupCompletionService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PostAuctionOps
{
    // ====================================================================
    //  Seller: send / resend pickup details
    // ====================================================================

    public static function sendPickupDetails($request, $threadId, ContentFilterService $contentFilter)
    {
        $user = Auth::user();
        $thread = PostAuctionThread::findOrFail($threadId);

        if ($user->id !== $thread->seller_id) {
            abort(403, 'Only seller can send pickup details.');
        }

        if ($r = self::guardClosed($thread)) { return $r; }

        $validated = $request->validated();

        $addressFilter = $contentFilter->validateAddress($validated['street_address']);
        if (! $addressFilter['is_valid']) {
            return back()->withErrors([
                'street_address' => $contentFilter->addressValidationUserMessage($addressFilter, 'Pickup address'),
            ])->withInput();
        }

        if (! empty($validated['directions_notes'])) {
            $notesFilter = $contentFilter->filterContent($validated['directions_notes']);
            if (! $notesFilter['is_valid']) {
                return back()->withErrors([
                    'directions_notes' => 'Notes contain prohibited content (phone numbers, emails, or links).',
                ])->withInput();
            }
            $validated['directions_notes'] = $notesFilter['filtered_content'];
        }

        $pickupDetail = PickupDetail::create([
            'thread_id' => $thread->id,
            'seller_id' => $user->id,
            'pickup_date' => $validated['pickup_date'],
            'pickup_time' => $validated['pickup_time'],
            'street_address' => $addressFilter['filtered_content'],
            'directions_notes' => $validated['directions_notes'] ?? null,
            'status' => 'pending',
            'submitted_at' => now(),
        ]);

        MessagingThreadEvent::record(
            $thread, $user,
            MessagingThreadEvent::TYPE_SCHEDULE_PROPOSED,
            [
                'pickup_detail_id' => $pickupDetail->id,
                'pickup_date' => $pickupDetail->pickup_date?->toDateString(),
                'pickup_time' => optional($pickupDetail->pickup_time)?->format('H:i'),
                'street_address' => $pickupDetail->street_address,
                'directions_notes' => $pickupDetail->directions_notes,
            ],
            countsAsExchange: true,
        );

        self::recordExchange($thread);

        (new NotificationService())->pickupInstructionsAvailable($thread->buyer, $thread->listing);

        Log::info('Pickup details sent', [
            'thread_id' => $thread->id,
            'pickup_detail_id' => $pickupDetail->id,
        ]);

        return back()->with('success', 'Pickup details sent successfully.');
    }

    public static function resendSchedule($threadId)
    {
        $user = Auth::user();
        $thread = PostAuctionThread::with('latestPickupDetail')->findOrFail($threadId);

        if ($user->id !== $thread->seller_id) {
            abort(403, 'Only seller can resend the pickup schedule.');
        }

        if ($r = self::guardClosed($thread)) { return $r; }

        $detail = $thread->latestPickupDetail;
        if (! $detail) {
            return back()->withErrors(['error' => 'There is no pickup schedule to resend yet.']);
        }

        $detail->update([
            'status' => 'pending',
            'submitted_at' => now(),
        ]);

        MessagingThreadEvent::record(
            $thread, $user,
            MessagingThreadEvent::TYPE_SCHEDULE_RESENT,
            [
                'pickup_detail_id' => $detail->id,
                'pickup_date' => $detail->pickup_date?->toDateString(),
                'pickup_time' => optional($detail->pickup_time)?->format('H:i'),
                'street_address' => $detail->street_address,
            ],
            countsAsExchange: true,
        );

        self::recordExchange($thread);

        (new NotificationService())->pickupInstructionsAvailable($thread->buyer, $thread->listing);

        return back()->with('success', 'Pickup schedule resent to buyer.');
    }

    // ====================================================================
    //  Buyer: accept / propose change / request location
    // ====================================================================

    public static function acceptPickupDetails($threadId)
    {
        $user = Auth::user();
        $thread = PostAuctionThread::with('latestPickupDetail', 'listing')->findOrFail($threadId);

        if ($user->id !== $thread->buyer_id) {
            abort(403, 'Only buyer can accept pickup details.');
        }

        if ($r = self::guardClosed($thread)) { return $r; }

        $pickupDetail = $thread->latestPickupDetail;
        if (! $pickupDetail || ! in_array($pickupDetail->status, ['pending', 'change_requested'])) {
            return back()->withErrors(['error' => 'No pending pickup details to accept.']);
        }

        $pickupDetail->accept();
        $pickupDetail->confirm();

        $listing = $thread->listing;
        if (! $listing->pickup_pin) {
            $pin = $listing->generatePickupPin();
            $listing->save();
            (new NotificationService())->pickupPinIssued($thread->buyer, $listing, $pin);
        }

        MessagingThreadEvent::record(
            $thread, $user,
            MessagingThreadEvent::TYPE_PICKUP_CONFIRMED,
            [
                'pickup_detail_id' => $pickupDetail->id,
                'pickup_date' => $pickupDetail->pickup_date?->toDateString(),
                'pickup_time' => optional($pickupDetail->pickup_time)?->format('H:i'),
                'street_address' => $pickupDetail->street_address,
            ],
            countsAsExchange: true,
        );

        self::recordExchange($thread);

        return back()->with('success', 'Pickup details accepted. Appointment confirmed.');
    }

    public static function requestPickupChange($request, $threadId, ?ContentFilterService $contentFilter = null)
    {
        $user = Auth::user();
        $thread = PostAuctionThread::findOrFail($threadId);

        if ($user->id !== $thread->buyer_id) {
            abort(403, 'Only buyer can request pickup changes.');
        }

        if ($r = self::guardClosed($thread)) { return $r; }

        $validated = $request->validated();

        if (empty($validated['requested_pickup_date']) && empty($validated['requested_pickup_time'])) {
            return back()->withErrors(['error' => 'Please provide either a new date or time.']);
        }

        $pickupDetail = PickupDetail::findOrFail($validated['pickup_detail_id']);
        if ($pickupDetail->thread_id !== $thread->id) {
            abort(403, 'Invalid pickup detail for this thread.');
        }

        $notes = $validated['additional_notes'] ?? null;
        if ($notes && $contentFilter) {
            $check = $contentFilter->filterContent($notes);
            if (! $check['is_valid']) {
                return back()->withErrors([
                    'additional_notes' => 'Notes contain prohibited content (phone numbers, emails, or links).',
                ])->withInput();
            }
            $notes = $check['filtered_content'];
        }

        $changeRequest = PickupChangeRequest::create([
            'thread_id' => $thread->id,
            'pickup_detail_id' => $pickupDetail->id,
            'buyer_id' => $user->id,
            'request_type' => PickupChangeRequest::TYPE_DATE_TIME,
            'requested_pickup_date' => $validated['requested_pickup_date'] ?? null,
            'requested_pickup_time' => $validated['requested_pickup_time'] ?? null,
            'additional_notes' => $notes,
            'status' => 'pending',
            'requested_at' => now(),
        ]);

        $pickupDetail->update(['status' => 'change_requested']);

        MessagingThreadEvent::record(
            $thread, $user,
            MessagingThreadEvent::TYPE_CHANGE_REQUESTED,
            [
                'change_request_id' => $changeRequest->id,
                'pickup_detail_id' => $pickupDetail->id,
                'requested_pickup_date' => $changeRequest->requested_pickup_date?->toDateString(),
                'requested_pickup_time' => optional($changeRequest->requested_pickup_time)?->format('H:i'),
                'additional_notes' => $notes,
            ],
            countsAsExchange: true,
        );

        self::recordExchange($thread);

        return back()->with('success', 'Change request sent to seller.');
    }

    public static function requestLocationChange($request, $threadId, ContentFilterService $contentFilter)
    {
        $user = Auth::user();
        $thread = PostAuctionThread::findOrFail($threadId);

        if ($user->id !== $thread->buyer_id) {
            abort(403, 'Only buyer can request a new location.');
        }

        if ($r = self::guardClosed($thread)) { return $r; }

        $validated = $request->validated();

        $pickupDetail = PickupDetail::findOrFail($validated['pickup_detail_id']);
        if ($pickupDetail->thread_id !== $thread->id) {
            abort(403, 'Invalid pickup detail for this thread.');
        }

        $addressFilter = $contentFilter->validateAddress($validated['requested_location']);
        if (! $addressFilter['is_valid']) {
            return back()->withErrors([
                'requested_location' => $contentFilter->addressValidationUserMessage($addressFilter, 'Requested location'),
            ])->withInput();
        }

        $notes = $validated['additional_notes'] ?? null;
        if ($notes) {
            $check = $contentFilter->filterContent($notes);
            if (! $check['is_valid']) {
                return back()->withErrors([
                    'additional_notes' => 'Notes contain prohibited content (phone numbers, emails, or links).',
                ])->withInput();
            }
            $notes = $check['filtered_content'];
        }

        $changeRequest = PickupChangeRequest::create([
            'thread_id' => $thread->id,
            'pickup_detail_id' => $pickupDetail->id,
            'buyer_id' => $user->id,
            'request_type' => PickupChangeRequest::TYPE_LOCATION,
            'requested_location' => $addressFilter['filtered_content'],
            'additional_notes' => $notes,
            'status' => 'pending',
            'requested_at' => now(),
        ]);

        $pickupDetail->update(['status' => 'change_requested']);

        MessagingThreadEvent::record(
            $thread, $user,
            MessagingThreadEvent::TYPE_LOCATION_REQUESTED,
            [
                'change_request_id' => $changeRequest->id,
                'pickup_detail_id' => $pickupDetail->id,
                'requested_location' => $changeRequest->requested_location,
                'additional_notes' => $notes,
            ],
            countsAsExchange: true,
        );

        self::recordExchange($thread);

        return back()->with('success', 'New location request sent to seller.');
    }

    public static function respondToChangeRequest($request, $changeRequestId)
    {
        $user = Auth::user();
        $changeRequest = PickupChangeRequest::with('thread')->findOrFail($changeRequestId);
        $thread = $changeRequest->thread;

        if ($user->id !== $thread->seller_id) {
            abort(403, 'Only seller can respond to change requests.');
        }

        if ($r = self::guardClosed($thread)) { return $r; }

        $validated = $request->validated();

        if ($validated['action'] === 'approve') {
            $changeRequest->approve();

            MessagingThreadEvent::record(
                $thread, $user,
                MessagingThreadEvent::TYPE_CHANGE_APPROVED,
                [
                    'change_request_id' => $changeRequest->id,
                    'request_type' => $changeRequest->request_type,
                ],
                countsAsExchange: true,
            );

            self::recordExchange($thread);

            (new NotificationService())->pickupRescheduleApproved($thread->buyer, $thread->listing);

            return back()->with('success', 'Change request approved.');
        }

        $changeRequest->counter(
            $validated['countered_pickup_date'] ?? null,
            $validated['countered_pickup_time'] ?? null
        );

        MessagingThreadEvent::record(
            $thread, $user,
            MessagingThreadEvent::TYPE_CHANGE_COUNTERED,
            [
                'change_request_id' => $changeRequest->id,
                'countered_pickup_date' => $changeRequest->countered_pickup_date?->toDateString(),
                'countered_pickup_time' => optional($changeRequest->countered_pickup_time)?->format('H:i'),
            ],
            countsAsExchange: true,
        );

        self::recordExchange($thread);

        (new NotificationService())->pickupRescheduleRejected($thread->buyer, $thread->listing);

        return back()->with('success', 'Counter offer sent to buyer.');
    }

    // ====================================================================
    //  Delivery requests
    // ====================================================================

    public static function requestDelivery($request, $threadId, ContentFilterService $contentFilter)
    {
        $user = Auth::user();
        $thread = PostAuctionThread::findOrFail($threadId);

        if ($user->id !== $thread->buyer_id) {
            abort(403, 'Only buyer can request delivery.');
        }

        if ($r = self::guardClosed($thread)) { return $r; }

        $validated = $request->validated();

        $addressFilter = $contentFilter->validateAddress($validated['delivery_address']);
        if (! $addressFilter['is_valid']) {
            return back()->withErrors([
                'delivery_address' => $contentFilter->addressValidationUserMessage($addressFilter, 'Delivery address'),
            ])->withInput();
        }

        $notes = $validated['additional_notes'] ?? null;
        if ($notes) {
            $check = $contentFilter->filterContent($notes);
            if (! $check['is_valid']) {
                return back()->withErrors([
                    'additional_notes' => 'Notes contain prohibited content (phone numbers, emails, or links).',
                ])->withInput();
            }
            $notes = $check['filtered_content'];
        }

        $delivery = PickupDeliveryRequest::create([
            'thread_id' => $thread->id,
            'buyer_id' => $user->id,
            'delivery_address' => $addressFilter['filtered_content'],
            'preferred_date' => $validated['preferred_date'] ?? null,
            'preferred_time' => $validated['preferred_time'] ?? null,
            'additional_notes' => $notes,
            'status' => PickupDeliveryRequest::STATUS_PENDING,
            'submitted_at' => now(),
        ]);

        MessagingThreadEvent::record(
            $thread, $user,
            MessagingThreadEvent::TYPE_DELIVERY_REQUESTED,
            [
                'delivery_request_id' => $delivery->id,
                'delivery_address' => $delivery->delivery_address,
                'preferred_date' => $delivery->preferred_date?->toDateString(),
                'preferred_time' => optional($delivery->preferred_time)?->format('H:i'),
                'additional_notes' => $notes,
            ],
            countsAsExchange: true,
        );

        self::recordExchange($thread);

        return back()->with('success', 'Delivery request sent to seller for approval.');
    }

    public static function respondToDeliveryRequest($request, $deliveryRequestId)
    {
        $user = Auth::user();
        $delivery = PickupDeliveryRequest::with('thread')->findOrFail($deliveryRequestId);
        $thread = $delivery->thread;

        if ($user->id !== $thread->seller_id) {
            abort(403, 'Only seller can respond to delivery requests.');
        }

        if ($r = self::guardClosed($thread)) { return $r; }

        $validated = $request->validated();

        if ($validated['action'] === 'approve') {
            $delivery->approve($validated['response_notes'] ?? null);
        } else {
            $delivery->reject($validated['response_notes'] ?? null);
        }

        MessagingThreadEvent::record(
            $thread, $user,
            MessagingThreadEvent::TYPE_DELIVERY_RESPONDED,
            [
                'delivery_request_id' => $delivery->id,
                'action' => $validated['action'],
                'response_notes' => $validated['response_notes'] ?? null,
            ],
            countsAsExchange: true,
        );

        self::recordExchange($thread);

        return back()->with('success', 'Delivery request response recorded.');
    }

    // ====================================================================
    //  Third-party / tow pickup
    // ====================================================================

    public static function authorizeThirdPartyPickup($request, $threadId, ?ContentFilterService $contentFilter = null)
    {
        $user = Auth::user();
        $thread = PostAuctionThread::findOrFail($threadId);

        if ($user->id !== $thread->buyer_id) {
            abort(403, 'Only buyer can authorize third-party pickup.');
        }

        if ($r = self::guardClosed($thread)) { return $r; }

        $validated = $request->validated();

        $notes = $validated['additional_notes'] ?? null;
        if ($notes && $contentFilter) {
            $check = $contentFilter->filterContent($notes);
            if (! $check['is_valid']) {
                return back()->withErrors([
                    'additional_notes' => 'Notes contain prohibited content (phone numbers, emails, or links).',
                ])->withInput();
            }
            $notes = $check['filtered_content'];
        }

        ThirdPartyPickup::where('thread_id', $thread->id)->update(['is_active' => false]);

        $third = ThirdPartyPickup::create([
            'thread_id' => $thread->id,
            'buyer_id' => $user->id,
            'authorized_name' => $validated['authorized_name'],
            'pickup_type' => $validated['pickup_type'],
            'additional_notes' => $notes,
            'is_active' => true,
            'authorized_at' => now(),
        ]);

        MessagingThreadEvent::record(
            $thread, $user,
            MessagingThreadEvent::TYPE_THIRD_PARTY_AUTHORIZED,
            [
                'third_party_pickup_id' => $third->id,
                'authorized_name' => $third->authorized_name,
                'pickup_type' => $third->pickup_type,
                'additional_notes' => $notes,
            ],
            countsAsExchange: true,
        );

        self::recordExchange($thread);

        return back()->with('success', 'Third-party pickup authorized successfully.');
    }

    // ====================================================================
    //  Pickup PIN confirmation
    // ====================================================================

    public static function confirmPickupWithPin($request, $threadId)
    {
        $user = Auth::user();
        $thread = PostAuctionThread::with(['listing', 'invoice'])->findOrFail($threadId);

        if ($user->id !== $thread->seller_id) {
            abort(403, 'Only seller can confirm pickup.');
        }

        if ($thread->pickup_confirmed) {
            return back()->withErrors([
                'pickup_pin' => 'Pick-up is already confirmed for this sale. Messaging is read-only.',
            ]);
        }

        $validated = $request->validated();
        $listing = $thread->listing;

        $result = (new SellerPickupCompletionService())
            ->completeAfterSellerPin($listing, $user, (string) $validated['pickup_pin']);

        if (! $result['success']) {
            return back()->withErrors([
                'pickup_pin' => $result['error'] ?? 'Unable to confirm pickup.',
            ]);
        }

        return back()->with('success', 'Pick-up confirmed. Messaging is now read-only while CayMark processes the seller payout.');
    }

    // ====================================================================
    //  Extra mockup actions
    // ====================================================================

    public static function otherRequest($request, $threadId, ContentFilterService $contentFilter)
    {
        $user = Auth::user();
        $thread = PostAuctionThread::findOrFail($threadId);

        if ($user->id !== $thread->buyer_id && $user->id !== $thread->seller_id) {
            abort(403);
        }

        if ($r = self::guardClosed($thread)) { return $r; }

        $validated = $request->validated();

        $body = $contentFilter->filterContent($validated['body']);
        if (! $body['is_valid']) {
            return back()->withErrors([
                'body' => 'Message contains prohibited content (phone numbers, emails, or links).',
            ])->withInput();
        }

        MessagingThreadEvent::record(
            $thread, $user,
            MessagingThreadEvent::TYPE_OTHER_REQUEST,
            [
                'subject' => $validated['subject'],
                'body' => $body['filtered_content'],
            ],
            countsAsExchange: true,
        );

        self::recordExchange($thread);

        return back()->with('success', 'Request submitted.');
    }

    public static function reportIssue($request, $threadId, ContentFilterService $contentFilter)
    {
        $user = Auth::user();
        $thread = PostAuctionThread::findOrFail($threadId);

        if ($user->id !== $thread->buyer_id && $user->id !== $thread->seller_id) {
            abort(403);
        }

        if ($r = self::guardClosed($thread)) { return $r; }

        $validated = $request->validated();

        $body = $contentFilter->filterContent($validated['body']);
        if (! $body['is_valid']) {
            return back()->withErrors([
                'body' => 'Message contains prohibited content (phone numbers, emails, or links).',
            ])->withInput();
        }

        $message = sprintf(
            "[Messaging Center thread #%d] %s\n\n%s",
            $thread->id,
            'Linked invoice: '.($thread->invoice?->invoice_number ?? $thread->invoice_id),
            $body['filtered_content']
        );

        $ticket = SupportTicket::create([
            'public_ticket_number' => SupportTicket::generateUniquePublicTicketNumber(),
            'user_id' => $user->id,
            'title' => $validated['category'],
            'message' => $message,
            'status' => SupportTicket::STATUS_OPEN,
        ]);

        MessagingThreadEvent::record(
            $thread, $user,
            MessagingThreadEvent::TYPE_ISSUE_REPORTED,
            [
                'support_ticket_id' => $ticket->id,
                'public_ticket_number' => $ticket->public_ticket_number,
                'category' => $ticket->title,
            ],
            countsAsExchange: false,
        );

        return back()->with('success', "Issue reported. Ticket #{$ticket->public_ticket_number} opened with CayMark Support.");
    }

    public static function requestAssistance($threadId)
    {
        $user = Auth::user();
        $thread = PostAuctionThread::findOrFail($threadId);

        if ($user->id !== $thread->buyer_id && $user->id !== $thread->seller_id) {
            abort(403);
        }

        if ($r = self::guardClosed($thread)) { return $r; }

        if (! $thread->flagged_for_admin) {
            $thread->flagForAdmin(PostAuctionThread::FLAG_MANUAL);
            app(MessagingNotifier::class)->notifyAdmin($thread);
        }

        MessagingThreadEvent::record(
            $thread, $user,
            MessagingThreadEvent::TYPE_ASSISTANCE_REQUESTED,
            [
                'reason' => PostAuctionThread::FLAG_MANUAL,
            ],
            countsAsExchange: false,
        );

        return back()->with('success', 'CayMark has been notified and may step in to help.');
    }

    public static function markReadyForPickup($threadId)
    {
        $user = Auth::user();
        $thread = PostAuctionThread::findOrFail($threadId);

        if ($user->id !== $thread->seller_id) {
            abort(403, 'Only seller can mark ready for pickup.');
        }

        if ($r = self::guardClosed($thread)) { return $r; }

        $thread->forceFill(['seller_ready_at' => now()])->save();

        MessagingThreadEvent::record(
            $thread, $user,
            MessagingThreadEvent::TYPE_READY_FOR_PICKUP,
            [],
            countsAsExchange: false,
        );

        return back()->with('success', 'Marked as ready for pickup.');
    }

    public static function confirmSaleCompleted($threadId)
    {
        $user = Auth::user();
        $thread = PostAuctionThread::findOrFail($threadId);

        if ($user->id !== $thread->buyer_id) {
            abort(403, 'Only buyer can confirm sale completion.');
        }

        if (! $thread->pickup_confirmed) {
            return back()->withErrors(['error' => 'Pickup must be confirmed before marking the sale completed.']);
        }

        // Buyer completion is recorded automatically when the seller confirms the PIN; thread is read-only after that.
        if ($thread->buyer_completion_confirmed_at) {
            return back()->with('success', 'Your side is already recorded for this sale.');
        }

        return back()->withErrors([
            'error' => 'This conversation is read-only after pick-up is confirmed. No further confirmations are required.',
        ]);
    }

    // ====================================================================
    //  Internal helpers — closed-thread guard + exchange counter + auto-flag
    // ====================================================================

    /**
     * Stop any further write action on a thread once the seller has confirmed
     * pickup. Returns a redirect-with-errors response if closed, null otherwise.
     * Callers should: `if ($r = self::guardClosed($thread)) { return $r; }`.
     */
    protected static function guardClosed(PostAuctionThread $thread)
    {
        if ($thread->pickup_confirmed) {
            return back()->withErrors([
                'thread' => 'Messaging is locked for this sale. Pick-up is confirmed — you can review the history, but no new messages or actions are accepted.',
            ]);
        }
        return null;
    }

    protected static function recordExchange(PostAuctionThread $thread): void
    {
        $thread->incrementExchange();
        $thread->refresh();
        self::checkAndFlagIfNeeded($thread);
    }

    protected static function checkAndFlagIfNeeded(PostAuctionThread $thread): void
    {
        if ($thread->flagged_for_admin) {
            return;
        }

        if (! $thread->shouldAutoFlag()) {
            return;
        }

        $reason = $thread->exchanges_count >= PostAuctionThread::MAX_EXCHANGES
            ? PostAuctionThread::FLAG_MAX_EXCHANGES
            : PostAuctionThread::FLAG_TIMEOUT_48H;

        $thread->flagForAdmin($reason);

        MessagingThreadEvent::record(
            $thread, null,
            MessagingThreadEvent::TYPE_ADMIN_FLAGGED,
            ['reason' => $reason],
            countsAsExchange: false,
            actorRole: MessagingThreadEvent::ROLE_SYSTEM,
        );

        try {
            app(MessagingNotifier::class)->notifyAdmin($thread);
        } catch (\Throwable $e) {
            Log::error('Failed to notify admin of flagged thread: '.$e->getMessage(), [
                'thread_id' => $thread->id,
            ]);
        }
    }
}
