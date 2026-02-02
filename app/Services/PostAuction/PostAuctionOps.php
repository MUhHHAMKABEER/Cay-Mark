<?php

namespace App\Services\PostAuction;

class PostAuctionOps
{
    public static function sendPickupDetails($request, $threadId, $contentFilter)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $thread = \App\Models\PostAuctionThread::findOrFail($threadId);

        if ($user->id !== $thread->seller_id) {
            abort(403, 'Only seller can send pickup details.');
        }

        $validated = $request->validated();

        $addressFilter = $contentFilter->validateAddress($validated['street_address']);
        if (!$addressFilter['is_valid']) {
            return back()->withErrors([
                'street_address' => 'Address contains prohibited content (phone numbers, emails, or links).',
            ])->withInput();
        }

        if (!empty($validated['directions_notes'])) {
            $notesFilter = $contentFilter->filterContent($validated['directions_notes']);
            if (!$notesFilter['is_valid']) {
                return back()->withErrors([
                    'directions_notes' => 'Notes contain prohibited content (phone numbers, emails, or links).',
                ])->withInput();
            }
            $validated['directions_notes'] = $notesFilter['filtered_content'];
        }

        $pickupDetail = \App\Models\PickupDetail::create([
            'thread_id' => $thread->id,
            'seller_id' => $user->id,
            'pickup_date' => $validated['pickup_date'],
            'pickup_time' => $validated['pickup_time'],
            'street_address' => $addressFilter['filtered_content'],
            'directions_notes' => $validated['directions_notes'],
            'status' => 'pending',
            'submitted_at' => now(),
        ]);

        $notificationService = new \App\Services\NotificationService();
        $notificationService->pickupInstructionsAvailable($thread->buyer, $thread->listing);

        \Log::info('Pickup details sent', [
            'thread_id' => $thread->id,
            'pickup_detail_id' => $pickupDetail->id,
        ]);

        return back()->with('success', 'Pickup details sent successfully.');
    }

    public static function requestPickupChange($request, $threadId)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $thread = \App\Models\PostAuctionThread::findOrFail($threadId);

        if ($user->id !== $thread->buyer_id) {
            abort(403, 'Only buyer can request pickup changes.');
        }

        $validated = $request->validated();

        if (empty($validated['requested_pickup_date']) && empty($validated['requested_pickup_time'])) {
            return back()->withErrors(['error' => 'Please provide either a new date or time.']);
        }

        $pickupDetail = \App\Models\PickupDetail::findOrFail($validated['pickup_detail_id']);
        if ($pickupDetail->thread_id !== $thread->id) {
            abort(403, 'Invalid pickup detail for this thread.');
        }

        $changeRequest = \App\Models\PickupChangeRequest::create([
            'thread_id' => $thread->id,
            'pickup_detail_id' => $pickupDetail->id,
            'buyer_id' => $user->id,
            'requested_pickup_date' => $validated['requested_pickup_date'] ?? null,
            'requested_pickup_time' => $validated['requested_pickup_time'] ?? null,
            'status' => 'pending',
            'requested_at' => now(),
        ]);

        $pickupDetail->update(['status' => 'change_requested']);

        \Log::info('Pickup change requested', [
            'thread_id' => $thread->id,
            'change_request_id' => $changeRequest->id,
        ]);

        return back()->with('success', 'Change request sent to seller.');
    }

    public static function respondToChangeRequest($request, $changeRequestId)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $changeRequest = \App\Models\PickupChangeRequest::with('thread')->findOrFail($changeRequestId);

        if ($user->id !== $changeRequest->thread->seller_id) {
            abort(403, 'Only seller can respond to change requests.');
        }

        $validated = $request->validated();

        if ($validated['action'] === 'approve') {
            $changeRequest->approve();
            $notificationService = new \App\Services\NotificationService();
            $notificationService->pickupRescheduleApproved($changeRequest->thread->buyer, $changeRequest->thread->listing);

            return back()->with('success', 'Change request approved.');
        }

        $changeRequest->counter(
            $validated['countered_pickup_date'],
            $validated['countered_pickup_time']
        );

        $notificationService = new \App\Services\NotificationService();
        $notificationService->pickupRescheduleRejected($changeRequest->thread->buyer, $changeRequest->thread->listing);

        return back()->with('success', 'Counter offer sent to buyer.');
    }

    public static function authorizeThirdPartyPickup($request, $threadId)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $thread = \App\Models\PostAuctionThread::findOrFail($threadId);

        if ($user->id !== $thread->buyer_id) {
            abort(403, 'Only buyer can authorize third-party pickup.');
        }

        $validated = $request->validated();

        \App\Models\ThirdPartyPickup::where('thread_id', $thread->id)
            ->update(['is_active' => false]);

        $thirdPartyPickup = \App\Models\ThirdPartyPickup::create([
            'thread_id' => $thread->id,
            'buyer_id' => $user->id,
            'authorized_name' => $validated['authorized_name'],
            'pickup_type' => $validated['pickup_type'],
            'is_active' => true,
            'authorized_at' => now(),
        ]);

        \Log::info('Third-party pickup authorized', [
            'thread_id' => $thread->id,
            'third_party_pickup_id' => $thirdPartyPickup->id,
        ]);

        return back()->with('success', 'Third-party pickup authorized successfully.');
    }

    public static function confirmPickupWithPin($request, $threadId)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $thread = \App\Models\PostAuctionThread::with(['listing', 'invoice'])->findOrFail($threadId);

        if ($user->id !== $thread->seller_id) {
            abort(403, 'Only seller can confirm pickup.');
        }

        $validated = $request->validated();
        $listing = $thread->listing;
        $pin = $validated['pickup_pin'];

        if (!$listing->verifyPickupPin($pin)) {
            \Log::warning('Invalid pickup PIN attempted', [
                'thread_id' => $thread->id,
                'seller_id' => $user->id,
                'attempted_pin' => $pin,
            ]);

            return back()->withErrors([
                'pickup_pin' => 'Invalid PIN. Please verify the PIN with the buyer.',
            ]);
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($thread, $listing) {
            $thread->update([
                'pickup_confirmed' => true,
                'pickup_confirmed_at' => now(),
            ]);

            $listing->update([
                'pickup_confirmed' => true,
                'pickup_confirmed_at' => now(),
                'pickup_confirmed_by' => $thread->seller_id,
            ]);

            $payoutService = new \App\Services\PayoutService();
            $payoutService->createPayoutAfterPickup($thread->invoice, $listing);

            \Log::info('Pickup confirmed with PIN', [
                'thread_id' => $thread->id,
                'listing_id' => $listing->id,
                'invoice_id' => $thread->invoice_id,
            ]);
        });

        return back()->with('success', 'Pickup confirmed! Payout process has been initiated.');
    }
}

