<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MessagingThreadEvent;
use App\Models\PostAuctionThread;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessagingFlagController extends Controller
{
    public function index(Request $request)
    {
        $threads = PostAuctionThread::with(['listing', 'buyer', 'seller', 'invoice'])
            ->where('flagged_for_admin', true)
            ->orderByDesc('flagged_at')
            ->paginate(20);

        return view('admin.messaging-flags', [
            'threads' => $threads,
        ]);
    }

    public function show($threadId)
    {
        $thread = PostAuctionThread::with([
            'listing.images',
            'buyer',
            'seller',
            'invoice',
            'latestPickupDetail',
            'changeRequests',
            'deliveryRequests',
            'activeThirdPartyPickup',
            'events.actor',
        ])->findOrFail($threadId);

        $events = $thread->events->sortBy('created_at')->values();

        return view('admin.messaging-flag-detail', [
            'thread' => $thread,
            'events' => $events,
        ]);
    }

    public function unflag($threadId)
    {
        $thread = PostAuctionThread::findOrFail($threadId);
        $thread->unflag();

        MessagingThreadEvent::record(
            $thread,
            Auth::user(),
            MessagingThreadEvent::TYPE_ADMIN_UNFLAGGED,
            ['admin_id' => Auth::id()],
            countsAsExchange: false,
            actorRole: MessagingThreadEvent::ROLE_ADMIN,
        );

        return redirect()->route('admin.messaging.flags.index')
            ->with('success', 'Thread unflagged. Buyer and seller can continue.');
    }
}
