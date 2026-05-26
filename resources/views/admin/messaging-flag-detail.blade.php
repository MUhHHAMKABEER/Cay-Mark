@extends('layouts.dashboard')

@section('title', 'Thread #' . $thread->id . ' — Messaging Flag')

@section('content')
@php
    use App\Models\PostAuctionThread;

    $reason  = $thread->flag_reason ?? '';
    $reasonMeta = [
        PostAuctionThread::FLAG_MAX_EXCHANGES => ['label'=>'Max Exchanges Reached', 'class'=>'mfd-badge--max',     'icon'=>'swap_horiz',   'desc'=>'This thread was flagged because the maximum of '.PostAuctionThread::MAX_EXCHANGES.' exchanges was reached.'],
        PostAuctionThread::FLAG_TIMEOUT_48H   => ['label'=>'48-Hour Timeout',       'class'=>'mfd-badge--timeout', 'icon'=>'schedule',     'desc'=>'This thread was flagged because '.PostAuctionThread::NEGOTIATION_WINDOW_HOURS.' hours elapsed since the first exchange.'],
        PostAuctionThread::FLAG_MANUAL        => ['label'=>'Manual Request',         'class'=>'mfd-badge--manual',  'icon'=>'flag',         'desc'=>'A buyer or seller manually requested admin assistance on this thread.'],
    ];
    $meta = $reasonMeta[$reason] ?? ['label'=>str_replace('_',' ',$reason ?: 'Unknown'),'class'=>'mfd-badge--unknown','icon'=>'help','desc'=>'No additional information.'];

    $title      = trim(($thread->listing->year ?? '').' '.($thread->listing->make ?? '').' '.($thread->listing->model ?? ''));
    $pct        = min(100, round(($thread->exchanges_count / PostAuctionThread::MAX_EXCHANGES) * 100));
    $remaining  = max(0, PostAuctionThread::MAX_EXCHANGES - $thread->exchanges_count);

    // Group events by actor role for timeline
    $roleColors = ['buyer'=>'mfd-ev--buyer','seller'=>'mfd-ev--seller','admin'=>'mfd-ev--admin','system'=>'mfd-ev--system'];
    $roleLabels = ['buyer'=>'Buyer','seller'=>'Seller','admin'=>'Admin','system'=>'System'];
    $roleIcons  = ['buyer'=>'person','seller'=>'storefront','admin'=>'shield','system'=>'smart_toy'];
@endphp

<style>
    :root { --mfd-navy:#063466; --mfd-navy-lt:#e8eef6; --mfd-navy-mid:#0d4d8c; }

    /* ── Shell ─────────────────────────────────────────────────── */
    .mfd-shell { padding:1.5rem; width:100%; box-sizing:border-box; }

    /* ── Back link ─────────────────────────────────────────────── */
    .mfd-back {
        display:inline-flex; align-items:center; gap:6px;
        color:var(--mfd-navy); text-decoration:none; font-weight:600;
        font-size:0.875rem; margin-bottom:1.25rem; transition:opacity 0.15s;
    }
    .mfd-back:hover { opacity:0.75; }
    .mfd-back .material-icons-round { font-size:18px; }

    /* ── Page title bar ────────────────────────────────────────── */
    .mfd-title-bar {
        background:#fff; border-radius:12px; padding:1.25rem 1.75rem;
        border-left:4px solid var(--mfd-navy); margin-bottom:1.25rem;
        box-shadow:0 1px 4px rgba(6,52,102,0.07);
        display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:0.75rem;
    }
    .mfd-title-bar h1 {
        margin:0 0 0.25rem; font-size:1.3rem; font-weight:700; color:var(--mfd-navy);
        display:flex; align-items:center; gap:8px;
    }
    .mfd-title-bar h1 .material-icons-round { font-size:22px; }
    .mfd-title-bar p  { margin:0; color:#64748b; font-size:0.875rem; }

    /* ── Reason badge ──────────────────────────────────────────── */
    .mfd-badge {
        display:inline-flex; align-items:center; gap:5px;
        font-size:0.75rem; font-weight:700; padding:4px 12px; border-radius:999px;
        text-transform:uppercase; letter-spacing:0.05em; white-space:nowrap;
    }
    .mfd-badge .material-icons-round { font-size:13px; }
    .mfd-badge--max     { background:#fce7f3; color:#9d174d; }
    .mfd-badge--timeout { background:#fef3c7; color:#92400e; }
    .mfd-badge--manual  { background:#dbeafe; color:#1e40af; }
    .mfd-badge--unknown { background:#f1f5f9; color:#475569; }
    .mfd-badge--cleared { background:#dcfce7; color:#15803d; }

    /* ── Two-column layout ─────────────────────────────────────── */
    .mfd-grid { display:grid; grid-template-columns:1fr 340px; gap:1.25rem; align-items:start; }
    @media (max-width:900px) { .mfd-grid { grid-template-columns:1fr; } }

    /* ── Generic card ──────────────────────────────────────────── */
    .mfd-card {
        background:#fff; border-radius:12px;
        box-shadow:0 1px 4px rgba(6,52,102,0.07); overflow:hidden; margin-bottom:1.25rem;
    }
    .mfd-card-head {
        padding:0.9rem 1.5rem; border-bottom:1px solid #f1f5f9;
        display:flex; align-items:center; justify-content:space-between;
    }
    .mfd-card-head h2 {
        margin:0; font-size:0.9375rem; font-weight:700; color:#0f172a;
        display:flex; align-items:center; gap:6px;
    }
    .mfd-card-head h2 .material-icons-round { font-size:18px; color:var(--mfd-navy); }
    .mfd-card-body { padding:1.25rem 1.5rem; }

    /* ── Snapshot grid ─────────────────────────────────────────── */
    .mfd-snapshot { display:grid; grid-template-columns:repeat(auto-fit,minmax(190px,1fr)); gap:1rem; }
    .mfd-field-label { font-size:0.7rem; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:3px; }
    .mfd-field-value { font-size:0.9rem; font-weight:600; color:#0f172a; }
    .mfd-field-sub   { font-size:0.72rem; color:#94a3b8; font-weight:400; margin-top:1px; }

    /* ── Exchange bar ──────────────────────────────────────────── */
    .mfd-xbar-wrap { margin-top:0.5rem; }
    .mfd-xbar-track { height:6px; background:#e2e8f0; border-radius:999px; overflow:hidden; }
    .mfd-xbar-fill  { height:100%; border-radius:999px; transition:width 0.4s ease; }
    .mfd-xbar-fill--full    { background:#ef4444; }
    .mfd-xbar-fill--partial { background:#f59e0b; }
    .mfd-xbar-label { display:flex; justify-content:space-between; font-size:0.7rem; color:#94a3b8; margin-top:4px; }

    /* ── Status pill ───────────────────────────────────────────── */
    .mfd-pill {
        display:inline-flex; align-items:center; gap:4px;
        font-size:0.72rem; font-weight:700; padding:2px 9px; border-radius:999px;
    }
    .mfd-pill .material-icons-round { font-size:11px; }
    .mfd-pill--yes   { background:#dcfce7; color:#15803d; }
    .mfd-pill--no    { background:#f1f5f9; color:#64748b; }
    .mfd-pill--warn  { background:#fef3c7; color:#92400e; }

    /* ── Timeline ──────────────────────────────────────────────── */
    .mfd-timeline { display:flex; flex-direction:column; gap:10px; }
    .mfd-ev {
        border-radius:10px; border:1px solid #e2e8f0;
        border-left-width:3px; overflow:hidden;
    }
    .mfd-ev--buyer  { border-left-color:#3b82f6; background:#eff6ff; }
    .mfd-ev--seller { border-left-color:#14b8a6; background:#f0fdfa; }
    .mfd-ev--admin  { border-left-color:#8b5cf6; background:#f5f3ff; }
    .mfd-ev--system { border-left-color:#94a3b8; background:#f8fafc; }

    .mfd-ev-head {
        display:flex; justify-content:space-between; align-items:flex-start; gap:0.75rem;
        padding:10px 14px 6px;
    }
    .mfd-ev-actor {
        display:flex; align-items:center; gap:7px;
        font-weight:600; font-size:0.875rem; color:#0f172a;
    }
    .mfd-ev-actor-icon {
        width:26px; height:26px; border-radius:6px;
        display:flex; align-items:center; justify-content:center; flex-shrink:0;
    }
    .mfd-ev-actor-icon .material-icons-round { font-size:14px; }
    .mfd-ev-actor-icon--buyer  { background:#dbeafe; color:#2563eb; }
    .mfd-ev-actor-icon--seller { background:#ccfbf1; color:#0d9488; }
    .mfd-ev-actor-icon--admin  { background:#ede9fe; color:#7c3aed; }
    .mfd-ev-actor-icon--system { background:#f1f5f9; color:#64748b; }

    .mfd-ev-type {
        font-size:0.7rem; font-weight:700; color:#475569;
        background:#fff; padding:2px 8px; border-radius:999px;
        border:1px solid #e2e8f0; white-space:nowrap;
    }
    .mfd-ev-type--exchange { background:#dbeafe; border-color:#bfdbfe; color:#1d4ed8; }
    .mfd-ev-when { font-size:0.7rem; color:#94a3b8; flex-shrink:0; margin-top:2px; }

    .mfd-ev-payload {
        margin:0 14px 10px;
        background:rgba(255,255,255,0.6); border:1px solid #e2e8f0;
        border-radius:6px; padding:8px 10px;
        font-size:0.75rem; color:#475569; font-family:monospace;
        overflow-x:auto; white-space:pre-wrap; word-break:break-all;
        max-height:140px; overflow-y:auto;
    }

    /* ── Sidebar cards ─────────────────────────────────────────── */
    .mfd-sidebar { display:flex; flex-direction:column; gap:1.25rem; }

    .mfd-action-card {
        background:#fff; border-radius:12px;
        box-shadow:0 1px 4px rgba(6,52,102,0.07);
        overflow:hidden;
    }
    .mfd-action-head {
        padding:0.9rem 1.25rem; border-bottom:1px solid #f1f5f9;
        display:flex; align-items:center; gap:6px;
        font-size:0.9375rem; font-weight:700; color:#0f172a;
    }
    .mfd-action-head .material-icons-round { font-size:18px; color:var(--mfd-navy); }
    .mfd-action-body { padding:1.125rem 1.25rem; }

    .mfd-unflag-btn {
        display:flex; align-items:center; justify-content:center; gap:8px;
        width:100%; padding:0.75rem 1rem;
        background:#10b981; color:#fff;
        border:none; border-radius:10px; cursor:pointer;
        font-weight:700; font-size:0.9rem; transition:background 0.2s;
    }
    .mfd-unflag-btn:hover { background:#059669; }
    .mfd-unflag-btn .material-icons-round { font-size:18px; }
    .mfd-unflag-note { font-size:0.78rem; color:#64748b; text-align:center; margin-top:0.625rem; line-height:1.5; }

    .mfd-already-cleared {
        display:flex; align-items:center; justify-content:center; gap:6px;
        background:#f0fdf4; border:1px solid #bbf7d0; color:#15803d;
        padding:0.75rem 1rem; border-radius:10px;
        font-weight:600; font-size:0.875rem;
    }
    .mfd-already-cleared .material-icons-round { font-size:18px; }

    /* ── Info row in sidebar ───────────────────────────────────── */
    .mfd-info-row {
        display:flex; justify-content:space-between; align-items:baseline;
        padding:0.5rem 0; border-bottom:1px solid #f8fafc;
        font-size:0.82rem;
    }
    .mfd-info-row:last-child { border-bottom:none; }
    .mfd-info-key   { color:#64748b; font-weight:500; }
    .mfd-info-val   { font-weight:600; color:#0f172a; text-align:right; }

    /* ── Flash ─────────────────────────────────────────────────── */
    .mfd-flash {
        display:flex; align-items:center; gap:10px;
        background:#ecfdf5; border:1px solid #10b981; color:#065f46;
        padding:0.875rem 1rem; border-radius:10px; margin-bottom:1rem;
        font-size:0.875rem; font-weight:500;
    }
    .mfd-flash .material-icons-round { font-size:18px; color:#10b981; flex-shrink:0; }

    /* ── Reason context banner ─────────────────────────────────── */
    .mfd-reason-banner {
        display:flex; align-items:flex-start; gap:10px;
        background:#fffbeb; border:1px solid #fde68a; border-radius:10px;
        padding:0.875rem 1rem; margin-bottom:1.25rem; font-size:0.875rem; color:#78350f;
    }
    .mfd-reason-banner .material-icons-round { font-size:20px; color:#f59e0b; flex-shrink:0; margin-top:1px; }
    .mfd-reason-banner strong { display:block; margin-bottom:2px; font-weight:700; }
</style>

<div class="mfd-shell">

    {{-- ── Back link ─────────────────────────────────────────────── --}}
    <a href="{{ route('admin.messaging.flags.index') }}" class="mfd-back">
        <span class="material-icons-round">arrow_back</span>
        Back to Flagged Threads
    </a>

    {{-- ── Flash ─────────────────────────────────────────────────── --}}
    @if(session('success'))
        <div class="mfd-flash">
            <span class="material-icons-round">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    {{-- ── Page title bar ─────────────────────────────────────────── --}}
    <div class="mfd-title-bar">
        <div>
            <h1>
                <span class="material-icons-round">forum</span>
                Thread #{{ $thread->id }}
                <span style="font-size:0.8rem;color:#64748b;font-weight:500;">&mdash; {{ $title ?: 'Listing #'.$thread->listing_id }}</span>
            </h1>
            <p>Read-only admin view. Buyer and seller can continue editing while you review.</p>
        </div>
        <span class="mfd-badge {{ $meta['class'] }}">
            <span class="material-icons-round">{{ $meta['icon'] }}</span>
            {{ $meta['label'] }}
        </span>
    </div>

    {{-- ── Reason context banner ──────────────────────────────────── --}}
    <div class="mfd-reason-banner">
        <span class="material-icons-round">info</span>
        <div>
            <strong>Why this thread was flagged</strong>
            {{ $meta['desc'] }}
            @if($thread->flagged_at)
                Flagged {{ $thread->flagged_at->diffForHumans() }} on {{ $thread->flagged_at->format('M d, Y \a\t g:i A') }}.
            @endif
        </div>
    </div>

    {{-- ── Two-column grid ─────────────────────────────────────────── --}}
    <div class="mfd-grid">

        {{-- ────── LEFT COLUMN ────── --}}
        <div>

            {{-- Transaction snapshot --}}
            <div class="mfd-card">
                <div class="mfd-card-head">
                    <h2><span class="material-icons-round">receipt_long</span> Transaction Snapshot</h2>
                </div>
                <div class="mfd-card-body">
                    <div class="mfd-snapshot">

                        <div>
                            <div class="mfd-field-label">Listing</div>
                            <div class="mfd-field-value">{{ $title ?: 'Listing #'.$thread->listing_id }}</div>
                            @if($thread->listing->island ?? null)
                                <div class="mfd-field-sub">{{ $thread->listing->island }}</div>
                            @endif
                        </div>

                        <div>
                            <div class="mfd-field-label">Invoice</div>
                            <div class="mfd-field-value">#{{ $thread->invoice?->invoice_number ?? $thread->invoice_id }}</div>
                            @if($thread->invoice?->amount ?? null)
                                <div class="mfd-field-sub">${{ number_format($thread->invoice->amount, 2) }}</div>
                            @endif
                        </div>

                        <div>
                            <div class="mfd-field-label">Buyer</div>
                            <div class="mfd-field-value">{{ $thread->buyer?->name ?? '—' }}</div>
                            <div class="mfd-field-sub">{{ $thread->buyer?->email }}</div>
                        </div>

                        <div>
                            <div class="mfd-field-label">Seller</div>
                            <div class="mfd-field-value">{{ $thread->seller?->name ?? '—' }}</div>
                            <div class="mfd-field-sub">{{ $thread->seller?->email }}</div>
                        </div>

                        <div>
                            <div class="mfd-field-label">First Exchange</div>
                            <div class="mfd-field-value">
                                {{ optional($thread->first_exchange_at)?->format('M d, Y') ?? '—' }}
                            </div>
                            <div class="mfd-field-sub">{{ optional($thread->first_exchange_at)?->format('g:i A') }}</div>
                        </div>

                        <div>
                            <div class="mfd-field-label">Last Exchange</div>
                            <div class="mfd-field-value">
                                {{ optional($thread->last_exchange_at)?->format('M d, Y') ?? '—' }}
                            </div>
                            <div class="mfd-field-sub">{{ optional($thread->last_exchange_at)?->format('g:i A') }}</div>
                        </div>

                        <div>
                            <div class="mfd-field-label">Pickup Confirmed</div>
                            <div class="mfd-field-value" style="margin-top:2px;">
                                @if($thread->pickup_confirmed)
                                    <span class="mfd-pill mfd-pill--yes">
                                        <span class="material-icons-round">check_circle</span>
                                        Yes
                                    </span>
                                    <div class="mfd-field-sub">{{ optional($thread->pickup_confirmed_at)?->format('M d, Y g:i A') }}</div>
                                @else
                                    <span class="mfd-pill mfd-pill--no">
                                        <span class="material-icons-round">radio_button_unchecked</span>
                                        Not yet
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div>
                            <div class="mfd-field-label">Thread Unlocked</div>
                            <div class="mfd-field-value" style="margin-top:2px;">
                                @if($thread->is_unlocked)
                                    <span class="mfd-pill mfd-pill--yes">
                                        <span class="material-icons-round">lock_open</span>
                                        Yes
                                    </span>
                                @else
                                    <span class="mfd-pill mfd-pill--warn">
                                        <span class="material-icons-round">lock</span>
                                        Locked
                                    </span>
                                @endif
                            </div>
                        </div>

                    </div>

                    {{-- Exchange progress --}}
                    <div style="margin-top:1.25rem;padding-top:1.25rem;border-top:1px solid #f1f5f9;">
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;">
                            <span style="font-size:0.8rem;font-weight:700;color:#374151;">Exchange Usage</span>
                            <span style="font-size:0.8rem;font-weight:700;color:#0f172a;">
                                {{ $thread->exchanges_count }} / {{ \App\Models\PostAuctionThread::MAX_EXCHANGES }}
                                <span style="font-weight:400;color:#94a3b8;">({{ $remaining }} remaining)</span>
                            </span>
                        </div>
                        <div class="mfd-xbar-track">
                            <div class="mfd-xbar-fill {{ $pct >= 100 ? 'mfd-xbar-fill--full' : 'mfd-xbar-fill--partial' }}" style="width:{{ $pct }}%"></div>
                        </div>
                        <div class="mfd-xbar-label">
                            <span>0</span>
                            <span>{{ \App\Models\PostAuctionThread::MAX_EXCHANGES }} max</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Conversation timeline --}}
            <div class="mfd-card">
                <div class="mfd-card-head">
                    <h2><span class="material-icons-round">timeline</span> Conversation Timeline</h2>
                    <span style="font-size:0.75rem;color:#64748b;font-weight:500;">{{ $events->count() }} event{{ $events->count() !== 1 ? 's' : '' }}</span>
                </div>
                <div class="mfd-card-body">
                    @if($events->isEmpty())
                        <div style="text-align:center;padding:2rem;color:#94a3b8;font-size:0.875rem;">
                            <span class="material-icons-round" style="font-size:2rem;display:block;margin-bottom:0.5rem;color:#cbd5e1;">chat_bubble_outline</span>
                            No events recorded yet.
                        </div>
                    @else
                        <div class="mfd-timeline">
                            @foreach($events as $event)
                            @php
                                $role        = $event->actor_role ?? 'system';
                                $evClass     = $roleColors[$role]  ?? 'mfd-ev--system';
                                $evLabel     = $roleLabels[$role]  ?? ucfirst($role);
                                $evIcon      = $roleIcons[$role]   ?? 'smart_toy';
                                $evTypeClean = str_replace('_', ' ', $event->type ?? '');
                                $isExchange  = (bool) ($event->counts_as_exchange ?? false);
                            @endphp
                            <div class="mfd-ev {{ $evClass }}">
                                <div class="mfd-ev-head">
                                    <div class="mfd-ev-actor">
                                        <div class="mfd-ev-actor-icon mfd-ev-actor-icon--{{ $role }}">
                                            <span class="material-icons-round">{{ $evIcon }}</span>
                                        </div>
                                        <div>
                                            <div>{{ $evLabel }}</div>
                                            <div style="font-size:0.7rem;color:#64748b;font-weight:400;">
                                                {{ $event->created_at?->format('M d, Y · g:i A') }}
                                            </div>
                                        </div>
                                    </div>
                                    <div style="display:flex;align-items:center;gap:6px;flex-shrink:0;">
                                        <span class="mfd-ev-type {{ $isExchange ? 'mfd-ev-type--exchange' : '' }}">
                                            {{ $evTypeClean }}
                                        </span>
                                        @if($isExchange)
                                            <span style="font-size:0.68rem;color:#64748b;white-space:nowrap;">counts as exchange</span>
                                        @endif
                                    </div>
                                </div>
                                @if(!empty($event->payload))
                                    <pre class="mfd-ev-payload">{{ json_encode($event->payload, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) }}</pre>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

        </div>

        {{-- ────── RIGHT COLUMN (sidebar) ────── --}}
        <div class="mfd-sidebar">

            {{-- Admin action card --}}
            <div class="mfd-action-card">
                <div class="mfd-action-head">
                    <span class="material-icons-round">admin_panel_settings</span>
                    Admin Action
                </div>
                <div class="mfd-action-body">
                    @if($thread->flagged_for_admin)
                        <form method="POST" action="{{ route('admin.messaging.flags.unflag', $thread->id) }}">
                            @csrf
                            <button type="submit" class="mfd-unflag-btn">
                                <span class="material-icons-round">check_circle</span>
                                Clear Flag &amp; Unblock
                            </button>
                        </form>
                        <p class="mfd-unflag-note">
                            Clearing the flag will allow the buyer and seller to continue their negotiation thread.
                        </p>
                    @else
                        <div class="mfd-already-cleared">
                            <span class="material-icons-round">verified</span>
                            Flag already cleared
                        </div>
                        <p class="mfd-unflag-note">
                            This thread has been resolved and the participants can communicate freely.
                        </p>
                    @endif
                </div>
            </div>

            {{-- Thread info card --}}
            <div class="mfd-action-card">
                <div class="mfd-action-head">
                    <span class="material-icons-round">info</span>
                    Thread Info
                </div>
                <div class="mfd-action-body">
                    <div class="mfd-info-row">
                        <span class="mfd-info-key">Thread ID</span>
                        <span class="mfd-info-val">#{{ $thread->id }}</span>
                    </div>
                    <div class="mfd-info-row">
                        <span class="mfd-info-key">Flag reason</span>
                        <span class="mfd-info-val">
                            <span class="mfd-badge {{ $meta['class'] }}" style="font-size:0.65rem;padding:2px 8px;">
                                <span class="material-icons-round">{{ $meta['icon'] }}</span>
                                {{ $meta['label'] }}
                            </span>
                        </span>
                    </div>
                    <div class="mfd-info-row">
                        <span class="mfd-info-key">Flagged at</span>
                        <span class="mfd-info-val">{{ optional($thread->flagged_at)?->format('M d, Y') ?? '—' }}</span>
                    </div>
                    <div class="mfd-info-row">
                        <span class="mfd-info-key">Status</span>
                        <span class="mfd-info-val">
                            @if($thread->flagged_for_admin)
                                <span class="mfd-pill mfd-pill--warn">
                                    <span class="material-icons-round">flag</span>
                                    Active
                                </span>
                            @else
                                <span class="mfd-pill mfd-pill--yes">
                                    <span class="material-icons-round">check_circle</span>
                                    Cleared
                                </span>
                            @endif
                        </span>
                    </div>
                    <div class="mfd-info-row">
                        <span class="mfd-info-key">Exchanges</span>
                        <span class="mfd-info-val">{{ $thread->exchanges_count }} / {{ \App\Models\PostAuctionThread::MAX_EXCHANGES }}</span>
                    </div>
                    <div class="mfd-info-row">
                        <span class="mfd-info-key">Pickup done</span>
                        <span class="mfd-info-val">{{ $thread->pickup_confirmed ? 'Yes' : 'No' }}</span>
                    </div>
                    <div class="mfd-info-row">
                        <span class="mfd-info-key">Total events</span>
                        <span class="mfd-info-val">{{ $events->count() }}</span>
                    </div>
                </div>
            </div>

            {{-- Quick links card --}}
            <div class="mfd-action-card">
                <div class="mfd-action-head">
                    <span class="material-icons-round">link</span>
                    Quick Links
                </div>
                <div class="mfd-action-body" style="display:flex;flex-direction:column;gap:8px;">
                    @if($thread->invoice_id)
                        <a href="{{ route('admin.invoice-log') }}?search={{ $thread->invoice?->invoice_number ?? $thread->invoice_id }}"
                           style="display:flex;align-items:center;gap:7px;padding:0.5rem 0.75rem;border-radius:8px;background:var(--mfd-navy-lt);color:var(--mfd-navy);text-decoration:none;font-weight:600;font-size:0.82rem;transition:background 0.15s;">
                            <span class="material-icons-round" style="font-size:16px;">receipt</span>
                            View Invoice #{{ $thread->invoice?->invoice_number ?? $thread->invoice_id }}
                        </a>
                    @endif
                    @if($thread->listing_id)
                        <a href="{{ route('admin.auctions') }}?search={{ $thread->listing_id }}"
                           style="display:flex;align-items:center;gap:7px;padding:0.5rem 0.75rem;border-radius:8px;background:var(--mfd-navy-lt);color:var(--mfd-navy);text-decoration:none;font-weight:600;font-size:0.82rem;transition:background 0.15s;">
                            <span class="material-icons-round" style="font-size:16px;">gavel</span>
                            View Auction Listing
                        </a>
                    @endif
                    @if($thread->buyer_id)
                        <a href="{{ route('admin.users') }}?search={{ $thread->buyer?->email }}"
                           style="display:flex;align-items:center;gap:7px;padding:0.5rem 0.75rem;border-radius:8px;background:var(--mfd-navy-lt);color:var(--mfd-navy);text-decoration:none;font-weight:600;font-size:0.82rem;transition:background 0.15s;">
                            <span class="material-icons-round" style="font-size:16px;">person</span>
                            View Buyer Account
                        </a>
                    @endif
                    @if($thread->seller_id)
                        <a href="{{ route('admin.users') }}?search={{ $thread->seller?->email }}"
                           style="display:flex;align-items:center;gap:7px;padding:0.5rem 0.75rem;border-radius:8px;background:var(--mfd-navy-lt);color:var(--mfd-navy);text-decoration:none;font-weight:600;font-size:0.82rem;transition:background 0.15s;">
                            <span class="material-icons-round" style="font-size:16px;">storefront</span>
                            View Seller Account
                        </a>
                    @endif
                </div>
            </div>

        </div>
    </div>

</div>
@endsection
