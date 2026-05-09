@php
    /** @var \App\Models\PostAuctionThread $activeThread */
    $listing = $activeThread->listing;
    $invoice = $activeThread->invoice;
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
                <span class="pill pill-completed"><span class="material-icons-round" style="font-size: 0.85rem;">check_circle</span> Completed</span>
                <span class="pill" style="background:#f1f5f9; color:#475569;">
                    <span class="material-icons-round" style="font-size: 0.85rem;">lock</span> Messaging Closed
                </span>
            </div>
        </div>
    </div>

    <div style="background: #ecfdf5; border: 1px solid #10b981; border-radius: 14px; padding: 1.5rem; text-align: center; color: #065f46; margin-bottom: 1.25rem;">
        <span class="material-icons-round" style="font-size: 2.5rem; color: #059669;">verified</span>
        <h3 style="font-weight: 700; margin: 0.5rem 0; font-size: 1.05rem;">Transaction complete — messaging is closed</h3>
        <p style="font-size: 0.875rem; max-width: 520px; margin: 0 auto;">
            The buyer's pickup code has been verified and the sale is finalized.
            @if ($isSeller)
                Your payout has been initiated and is now being processed by CayMark finance.
            @else
                Thank you for using CayMark. If you need help with anything related to this purchase, please contact support.
            @endif
        </p>
    </div>

    @if ($events->count() > 0)
        <div class="convo-section-title">
            <span class="material-icons-round">forum</span> Conversation history
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
    <div class="exchange-counter" style="color:#065f46;">
        <span class="material-icons-round" style="font-size:1rem; vertical-align:middle;">lock</span>
        Thread is closed — no further actions are accepted.
    </div>
</div>
