@php
    /** @var \App\Models\PostAuctionThread $activeThread */
    $listing = $activeThread->listing;
    $invoice = $activeThread->invoice;
    $payout = $invoice?->payout;
    $payoutComplete = $payout && in_array($payout->status, ['sent', 'paid_successfully'], true);
    $title = trim(($listing->year ?? '') . ' ' . ($listing->make ?? '') . ' ' . ($listing->model ?? '')) ?: 'Listing #' . $activeThread->listing_id;
    $img = $listing && $listing->images?->first()
        ? (str_contains($listing->images->first()->image_path, '/')
            ? asset($listing->images->first()->image_path)
            : asset('uploads/listings/' . $listing->images->first()->image_path))
        : null;
    $confirmedAt = $activeThread->pickup_confirmed_at;
@endphp

<div class="messaging-topbar">
    <a href="{{ route('messaging.index') }}" class="back-link">
        <span class="material-icons-round" style="font-size: 1rem;">arrow_back</span>
        Back to conversations
    </a>
    <div style="font-size: 1rem; font-weight: 700; color: #0f172a;">{{ $title }}</div>
    <div style="display:flex; align-items:center; gap: 0.875rem;">
        @if ($invoice)
            <span class="order-num">{{ $isSeller ? 'Sale #S-' : 'Purchase #O-' }}{{ $invoice->invoice_number ?? $invoice->id }}</span>
        @endif
        @if ($listing)
            <a href="{{ route('listing.show', $listing->id) }}" class="view-item">
                View Item <span class="material-icons-round" style="font-size: 0.95rem;">open_in_new</span>
            </a>
        @endif
    </div>
</div>

<div class="messaging-main-body">
    <div class="header-card">
        @if ($img)
            <img src="{{ $img }}" alt="" class="car-thumb">
        @else
            <div style="width:140px; height:100px; border-radius: 10px; background: #e2e8f0; display:flex; align-items:center; justify-content:center;">
                <span class="material-icons-round" style="color:#94a3b8;">directions_car</span>
            </div>
        @endif
        <div>
            <div style="font-size: 1.05rem; font-weight: 700; color: #0f172a;">{{ $title }}</div>
            <div class="meta-row">
                <span class="material-icons-round">event</span>
                <span><strong>Pickup confirmed:</strong> {{ optional($confirmedAt)->format('M d, Y g:i A') ?? '—' }}</span>
            </div>
            <div class="pills">
                @if ($payoutComplete)
                    <span class="pill pill-completed"><span class="material-icons-round" style="font-size: 0.85rem;">check_circle</span> Payout completed</span>
                @else
                    <span class="pill pill-paid"><span class="material-icons-round" style="font-size: 0.85rem;">payments</span> Ready for payout</span>
                @endif
                <span class="pill" style="background:#f1f5f9; color:#475569;">
                    <span class="material-icons-round" style="font-size: 0.85rem;">lock</span> Messaging read-only
                </span>
            </div>
        </div>
    </div>

    @if ($payoutComplete)
        <div style="background: #ecfdf5; border: 1px solid #10b981; border-radius: 14px; padding: 1.5rem; text-align: center; color: #065f46; margin-bottom: 1.25rem;">
            <span class="material-icons-round" style="font-size: 2.5rem; color: #059669;">verified</span>
            <h3 style="font-weight: 700; margin: 0.5rem 0; font-size: 1.05rem;">Transaction complete</h3>
            <p style="font-size: 0.875rem; max-width: 560px; margin: 0 auto;">
                CayMark has recorded the seller payout. This thread stays available below as a <strong>read-only</strong> record (messages and forms submitted — no new actions).
            </p>
        </div>
    @else
        <div style="background: #eff6ff; border: 1px solid #93c5fd; border-radius: 14px; padding: 1.5rem; text-align: center; color: #1e3a8a; margin-bottom: 1.25rem;">
            <span class="material-icons-round" style="font-size: 2.5rem; color: #2563eb;">lock</span>
            <h3 style="font-weight: 700; margin: 0.5rem 0; font-size: 1.05rem;">Pick-up confirmed — messaging is read-only</h3>
            <p style="font-size: 0.875rem; max-width: 560px; margin: 0 auto;">
                The seller entered a valid pick-up code. <strong>You cannot send new messages, edit details, or submit new actions here.</strong> Review the timeline and forms below. CayMark finance will pay the seller; you&apos;ll see the final status once that payout is marked complete.
            </p>
            @if ($isSeller)
                <p style="font-size: 0.8rem; margin: 0.75rem 0 0; color: #334155;">Your payout is queued for processing.</p>
            @endif
        </div>
    @endif

    @if ($events->count() > 0)
        <div class="convo-section-title">
            <span class="material-icons-round">forum</span> Conversation &amp; pickup history (read-only)
        </div>
        @foreach ($events as $event)
            @include('messaging._event', ['event' => $event])
        @endforeach
    @endif
</div>

<div class="footer-card">
    <div class="help">
        <span class="help-title">Need Help?</span>
        <span class="help-row"><span class="material-icons-round">phone</span> {{ config('support.phone', config('support.inbox', 'support@caymark.com')) }}</span>
        <span class="help-row"><span class="material-icons-round">email</span> {{ config('support.inbox', 'support@caymark.com') }}</span>
    </div>
    <div class="exchange-counter" style="color:#64748b;">
        <span class="material-icons-round" style="font-size:1rem; vertical-align:middle;">visibility</span>
        Read-only — history preserved; no new messages or actions.
    </div>
</div>
