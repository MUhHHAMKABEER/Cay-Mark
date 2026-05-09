@extends('layouts.dashboard')

@section('title', 'Messaging Thread #' . $thread->id . ' - Admin')

@section('content')
<style>
    .mfd-shell { padding: 1.5rem; max-width: 1100px; margin: 0 auto; }
    .mfd-card { background:#fff; border:1px solid #e2e8f0; border-radius:16px; padding: 1.25rem; margin-bottom: 1rem; }
    .mfd-row { display:flex; gap:1rem; align-items:center; flex-wrap: wrap; }
    .mfd-label { font-size:0.7rem; color:#64748b; font-weight:700; text-transform: uppercase; letter-spacing:0.06em; }
    .mfd-value { font-size:0.9rem; color:#0f172a; font-weight:600; }
    .mfd-event { background:#fff; border:1px solid #e2e8f0; border-left:4px solid #cbd5e1; border-radius:10px; padding:12px 16px; margin-bottom:8px; }
    .mfd-event.from-buyer { border-left-color:#3b82f6; background:#eff6ff; }
    .mfd-event.from-seller { border-left-color:#14b8a6; background:#f0fdfa; }
    .mfd-event.from-system { border-left-color:#94a3b8; background:#f8fafc; }
    .mfd-event-head { display:flex; justify-content:space-between; align-items:center; gap:1rem; flex-wrap: wrap; margin-bottom:4px; font-size:0.85rem; font-weight:600; color:#0f172a; }
    .mfd-event-head .when { font-size:0.7rem; color:#94a3b8; }
    .mfd-event pre { background:#f1f5f9; padding:8px; border-radius:6px; font-size:0.75rem; color:#475569; margin-top:8px; overflow-x: auto; }
</style>

<div class="mfd-shell">
    <a href="{{ route('admin.messaging.flags.index') }}" style="display:inline-flex; align-items:center; gap:6px; color:#2563eb; text-decoration:none; font-weight:600; margin-bottom:1rem; font-size:0.875rem;">
        <span class="material-icons-round" style="font-size:1rem;">arrow_back</span> Back to flagged threads
    </a>

    <h1 style="font-size:1.4rem; font-weight:700; color:#0f172a; margin-bottom:0.5rem;">Thread #{{ $thread->id }} <span style="font-size:0.85rem; color:#64748b; font-weight:500;">({{ str_replace('_', ' ', $thread->flag_reason ?? '—') }})</span></h1>
    <p style="color:#64748b; font-size:0.9rem; margin-bottom:1.5rem;">Read-only view. Buyer and seller can keep editing while you review.</p>

    @if (session('success'))
        <div style="background:#ecfdf5; border:1px solid #10b981; color:#065f46; padding:0.875rem 1rem; border-radius:12px; margin-bottom:1rem;">{{ session('success') }}</div>
    @endif

    <div class="mfd-card">
        <h3 style="font-weight:700; font-size:1rem; margin-bottom:0.75rem;">Transaction Snapshot</h3>
        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:1rem;">
            <div>
                <div class="mfd-label">Listing</div>
                <div class="mfd-value">{{ trim(($thread->listing->year ?? '') . ' ' . ($thread->listing->make ?? '') . ' ' . ($thread->listing->model ?? '')) ?: 'Listing #' . $thread->listing_id }}</div>
            </div>
            <div>
                <div class="mfd-label">Invoice</div>
                <div class="mfd-value">#{{ $thread->invoice?->invoice_number ?? $thread->invoice_id }}</div>
            </div>
            <div>
                <div class="mfd-label">Buyer</div>
                <div class="mfd-value">{{ $thread->buyer?->name ?? '—' }} <div style="font-size:0.75rem; color:#94a3b8; font-weight:500;">{{ $thread->buyer?->email }}</div></div>
            </div>
            <div>
                <div class="mfd-label">Seller</div>
                <div class="mfd-value">{{ $thread->seller?->name ?? '—' }} <div style="font-size:0.75rem; color:#94a3b8; font-weight:500;">{{ $thread->seller?->email }}</div></div>
            </div>
            <div>
                <div class="mfd-label">Exchanges</div>
                <div class="mfd-value">{{ $thread->exchanges_count }} of {{ \App\Models\PostAuctionThread::MAX_EXCHANGES }}</div>
            </div>
            <div>
                <div class="mfd-label">First exchange</div>
                <div class="mfd-value">{{ optional($thread->first_exchange_at)?->format('M d, Y g:i A') ?? '—' }}</div>
            </div>
            <div>
                <div class="mfd-label">Last exchange</div>
                <div class="mfd-value">{{ optional($thread->last_exchange_at)?->format('M d, Y g:i A') ?? '—' }}</div>
            </div>
            <div>
                <div class="mfd-label">Pickup confirmed</div>
                <div class="mfd-value">{{ $thread->pickup_confirmed ? 'Yes — ' . optional($thread->pickup_confirmed_at)->format('M d, Y g:i A') : 'No' }}</div>
            </div>
        </div>
    </div>

    <div class="mfd-card">
        <h3 style="font-weight:700; font-size:1rem; margin-bottom:0.75rem;">Conversation Timeline</h3>
        @forelse ($events as $event)
            <div class="mfd-event from-{{ $event->actor_role }}">
                <div class="mfd-event-head">
                    <span><strong>{{ ucfirst($event->actor_role) }}</strong> · {{ str_replace('_', ' ', $event->type) }} {{ $event->counts_as_exchange ? '(counts as exchange)' : '' }}</span>
                    <span class="when">{{ $event->created_at?->format('M d, Y g:i A') }}</span>
                </div>
                @if (! empty($event->payload))
                    <pre>{{ json_encode($event->payload, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) }}</pre>
                @endif
            </div>
        @empty
            <p style="color:#94a3b8; font-size:0.875rem;">No events recorded yet.</p>
        @endforelse
    </div>

    @if ($thread->flagged_for_admin)
        <form method="POST" action="{{ route('admin.messaging.flags.unflag', $thread->id) }}" style="text-align:right;">
            @csrf
            <button type="submit" style="background:#10b981; color:#fff; border:none; padding:0.625rem 1.25rem; border-radius:10px; font-weight:600; cursor:pointer;">
                <span class="material-icons-round" style="font-size:1rem; vertical-align:middle;">check_circle</span> Clear Flag
            </button>
        </form>
    @endif
</div>
@endsection
