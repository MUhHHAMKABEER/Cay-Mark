@php
    $listing = $activeThread->listing;
    $invoice = $activeThread->invoice;
    $title = trim(($listing->year ?? '') . ' ' . ($listing->make ?? '') . ' ' . ($listing->model ?? '')) ?: 'Listing #' . $activeThread->listing_id;
    $img = $listing && $listing->images?->first()
        ? (str_contains($listing->images->first()->image_path, '/')
            ? asset($listing->images->first()->image_path)
            : asset('uploads/listings/' . $listing->images->first()->image_path))
        : null;
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
            @if ($isSeller)
                <div class="meta-row">
                    <span class="material-icons-round">person_outline</span>
                    <span><strong>Buyer:</strong> Hidden until payment</span>
                </div>
            @else
                <div class="meta-row">
                    <span class="material-icons-round">storefront</span>
                    <span><strong>Sold by:</strong> {{ $activeThread->seller?->name ?? '—' }}</span>
                </div>
            @endif
            <div class="meta-row">
                <span class="material-icons-round">event</span>
                <span><strong>Auction Ended:</strong> {{ optional($invoice?->sale_date)?->format('M d, Y') ?? '—' }}</span>
            </div>
            <div class="pills">
                <span class="pill pill-pending"><span class="material-icons-round" style="font-size: 0.85rem;">schedule</span> Payment Pending</span>
                <span class="pill" style="background:#f1f5f9; color:#475569;">
                    <span class="material-icons-round" style="font-size: 0.85rem;">lock</span> Messaging Locked
                </span>
            </div>
        </div>
    </div>

    @if ($isSeller)
    {{-- SELLER: waiting for buyer to pay --}}
    <div style="background:#fef3c7; border:1px solid #fbbf24; border-radius:14px; padding:1.5rem; text-align:center; color:#92400e;">
        <span class="material-icons-round" style="font-size:2.5rem; color:#d97706;">lock_clock</span>
        <h3 style="font-weight:700; margin:0.5rem 0; font-size:1.05rem;">Awaiting payment from buyer</h3>
        <p style="font-size:0.875rem; max-width:480px; margin:0 auto;">
            Messaging will unlock once payment is completed.
        </p>
    </div>
    @else
    {{-- BUYER: prompt to complete payment --}}
    <div style="background:#eff6ff; border:1px solid #bfdbfe; border-radius:14px; padding:1.5rem; text-align:center; color:#1e40af;">
        <span class="material-icons-round" style="font-size:2.5rem; color:#2563eb;">payment</span>
        <h3 style="font-weight:700; margin:0.5rem 0; font-size:1.05rem;">Please complete your payment</h3>
        <p style="font-size:0.875rem; max-width:480px; margin:0 auto; color:#1e40af;">
            Complete your payment to unlock communication with the seller and coordinate vehicle pickup.
        </p>
        @if ($invoice && $invoice->payment_status !== 'paid')
            <a href="{{ route('buyer.payment.checkout-single', $invoice->id) }}"
               style="display:inline-block; margin-top:1rem; background:#2563eb; color:#fff; padding:0.625rem 1.5rem; border-radius:10px; text-decoration:none; font-weight:600; font-size:0.875rem;">
                Complete Payment
            </a>
        @endif
    </div>
    @endif
</div>
