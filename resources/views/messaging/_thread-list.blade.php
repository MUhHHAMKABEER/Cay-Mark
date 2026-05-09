@php
    $currentUser = $currentUser ?? auth()->user();
@endphp
<div class="messaging-aside-header">
    <h2>{{ $currentUser && $currentUser->id && $threads->isNotEmpty() && $threads->first()->seller_id === $currentUser->id ? 'My Sales' : 'My Transactions' }}</h2>
</div>
<div style="padding: 0.75rem;">
    @forelse ($threads as $thread)
        @php
            $listing = $thread->listing;
            $invoice = $thread->invoice;
            $img = $listing && $listing->images?->first()
                ? (str_contains($listing->images->first()->image_path, '/')
                    ? asset($listing->images->first()->image_path)
                    : asset('uploads/listings/' . $listing->images->first()->image_path))
                : null;
            $title = trim(($listing->year ?? '') . ' ' . ($listing->make ?? '') . ' ' . ($listing->model ?? '')) ?: 'Listing #' . $thread->listing_id;

            if ($thread->pickup_confirmed) {
                $pillCopy = 'Sold • Completed'; $pillClass = 'pill-completed';
            } elseif ($invoice && $invoice->payment_status !== 'paid') {
                $pillCopy = 'Awaiting Payment'; $pillClass = 'pill-pending';
            } elseif (! $thread->latestPickupDetail) {
                $pillCopy = 'Awaiting Seller Schedule'; $pillClass = 'pill-pending';
            } else {
                $pillCopy = 'Payment Completed'; $pillClass = 'pill-paid';
            }
            $isActive = $invoice && $activeId === $invoice->id;
        @endphp
        <a href="{{ $invoice ? route('messaging.thread.show', $invoice->id) : '#' }}" class="thread-card {{ $isActive ? 'active' : '' }}">
            @if ($img)
                <img src="{{ $img }}" alt="">
            @else
                <div style="width:64px;height:64px;border-radius:10px;background:#e2e8f0;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <span class="material-icons-round" style="color:#94a3b8;">directions_car</span>
                </div>
            @endif
            <div class="meta">
                <div class="title">{{ $title }}</div>
                <span class="pill {{ $pillClass }}">{{ $pillCopy }}</span>
                <div class="date">{{ $thread->updated_at?->format('M d, Y') }}</div>
            </div>
        </a>
    @empty
        <div style="padding: 2rem 1rem; text-align: center; color: #94a3b8; font-size: 0.875rem;">
            No transactions yet.
        </div>
    @endforelse
</div>
