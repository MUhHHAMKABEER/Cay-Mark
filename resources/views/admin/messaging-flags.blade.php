@extends('layouts.dashboard')

@section('title', 'Flagged Messaging Threads - Admin')

@section('content')
@php
    use App\Models\PostAuctionThread;

    $mfTotal   = $threads->total();
    $mfMax     = PostAuctionThread::where('flagged_for_admin', true)->where('flag_reason', PostAuctionThread::FLAG_MAX_EXCHANGES)->count();
    $mfTimeout = PostAuctionThread::where('flagged_for_admin', true)->where('flag_reason', PostAuctionThread::FLAG_TIMEOUT_48H)->count();
    $mfManual  = PostAuctionThread::where('flagged_for_admin', true)->where('flag_reason', PostAuctionThread::FLAG_MANUAL)->count();

    $mfReasonMeta = [
        PostAuctionThread::FLAG_MAX_EXCHANGES => ['label'=>'Max Exchanges', 'class'=>'mf-badge--max',     'icon'=>'swap_horiz'],
        PostAuctionThread::FLAG_TIMEOUT_48H   => ['label'=>'48h Timeout',   'class'=>'mf-badge--timeout', 'icon'=>'schedule'],
        PostAuctionThread::FLAG_MANUAL        => ['label'=>'Manual Request', 'class'=>'mf-badge--manual',  'icon'=>'flag'],
    ];

    $mfFilter = request('filter', '');
    $mfSearch = request('search', '');
@endphp

<style>
    :root { --mf-navy:#063466; --mf-navy-lt:#e8eef6; --mf-navy-mid:#0d4d8c; }

    /* ── Shell ─────────────────────────────────────────────────── */
    .mf-shell {
        padding: 1.5rem;
        width: 100%;
        box-sizing: border-box;
        flex: 1;
        overflow-y: auto;
        min-height: 0;
    }

    /* ── Page header ───────────────────────────────────────────── */
    .mf-header {
        background:#fff; border-radius:12px;
        padding:1.375rem 1.75rem; margin-bottom:1.25rem;
        border-left:4px solid var(--mf-navy);
        box-shadow:0 1px 4px rgba(6,52,102,0.07);
        display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:0.75rem;
    }
    .mf-header-left h1 {
        margin:0 0 0.2rem; font-size:1.35rem; font-weight:700; color:var(--mf-navy);
        display:flex; align-items:center; gap:8px;
    }
    .mf-header-left h1 .material-icons-round { font-size:22px; }
    .mf-header-left p  { margin:0; color:#64748b; font-size:0.875rem; }

    /* ── Stat cards ────────────────────────────────────────────── */
    .mf-stats { display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:1rem; margin-bottom:1.25rem; }
    .mf-stat {
        background:#fff; border-radius:12px; padding:1.1rem 1.35rem;
        box-shadow:0 1px 4px rgba(6,52,102,0.07);
        display:flex; align-items:center; gap:0.85rem;
    }
    .mf-stat-icon {
        width:42px; height:42px; border-radius:10px; flex-shrink:0;
        display:flex; align-items:center; justify-content:center;
    }
    .mf-stat-icon .material-icons-round { font-size:21px; }
    .mf-stat-icon--total   { background:#e8eef6; color:var(--mf-navy); }
    .mf-stat-icon--max     { background:#fce7f3; color:#9d174d; }
    .mf-stat-icon--timeout { background:#fef3c7; color:#92400e; }
    .mf-stat-icon--manual  { background:#dbeafe; color:#1e40af; }
    .mf-stat-label { font-size:0.72rem; font-weight:600; color:#64748b; margin-bottom:3px; text-transform:uppercase; letter-spacing:0.04em; }
    .mf-stat-value { font-size:1.45rem; font-weight:700; line-height:1; color:#0f172a; }

    /* ── Filter bar ────────────────────────────────────────────── */
    .mf-filter {
        background:#fff; border-radius:12px; padding:0.875rem 1.25rem; margin-bottom:1.25rem;
        box-shadow:0 1px 4px rgba(6,52,102,0.07);
        display:flex; flex-wrap:wrap; gap:0.625rem; align-items:center;
    }
    .mf-filter-search {
        flex:1; min-width:220px; position:relative;
    }
    .mf-filter-search .material-icons-round {
        position:absolute; left:10px; top:50%; transform:translateY(-50%);
        font-size:16px; color:#94a3b8; pointer-events:none;
    }
    .mf-filter-search input {
        width:100%; padding:0.5rem 0.875rem 0.5rem 2.1rem;
        border:1.5px solid #e2e8f0; border-radius:8px;
        font-size:0.875rem; color:#374151; outline:none; transition:border-color 0.2s;
        box-sizing:border-box;
    }
    .mf-filter-search input:focus { border-color:var(--mf-navy); }
    .mf-tabs { display:flex; gap:4px; }
    .mf-tab {
        padding:0.44rem 0.875rem; border-radius:8px; font-size:0.8rem; font-weight:600;
        border:1.5px solid #e2e8f0; background:#fff; color:#64748b;
        cursor:pointer; text-decoration:none; transition:all 0.15s; white-space:nowrap;
    }
    .mf-tab.is-active, .mf-tab:hover { background:var(--mf-navy); color:#fff; border-color:var(--mf-navy); }
    .mf-filter-btn {
        padding:0.5rem 1.125rem; border-radius:8px; font-size:0.8125rem; font-weight:600;
        border:none; cursor:pointer; display:inline-flex; align-items:center; gap:5px;
        transition:background 0.2s; text-decoration:none;
    }
    .mf-filter-btn .material-icons-round { font-size:15px; }
    .mf-filter-btn--go    { background:var(--mf-navy); color:#fff; }
    .mf-filter-btn--go:hover { background:var(--mf-navy-mid); }
    .mf-filter-btn--clr   { background:#f1f5f9; color:#475569; }
    .mf-filter-btn--clr:hover { background:#e2e8f0; }

    /* ── Table card ────────────────────────────────────────────── */
    .mf-card { background:#fff; border-radius:12px; box-shadow:0 1px 4px rgba(6,52,102,0.07); overflow:hidden; margin-bottom:1.25rem; }
    .mf-card-head {
        padding:0.9rem 1.5rem; border-bottom:1px solid #f1f5f9;
        display:flex; align-items:center; justify-content:space-between;
    }
    .mf-card-head h2 { margin:0; font-size:0.9375rem; font-weight:700; color:#0f172a; display:flex; align-items:center; gap:6px; }
    .mf-card-head h2 .material-icons-round { font-size:18px; color:var(--mf-navy); }
    .mf-chip {
        display:inline-flex; align-items:center; gap:4px;
        font-size:0.72rem; font-weight:700; padding:3px 10px; border-radius:999px;
    }
    .mf-chip--count { background:var(--mf-navy-lt); color:var(--mf-navy); }

    .mf-table { width:100%; border-collapse:collapse; }
    .mf-table thead th {
        padding:0.7rem 1.25rem; text-align:left;
        font-size:0.6875rem; font-weight:700; text-transform:uppercase;
        letter-spacing:0.06em; color:#64748b; background:#f8fafc;
        border-bottom:1px solid #f1f5f9; white-space:nowrap;
    }
    .mf-table tbody tr { border-bottom:1px solid #f8fafc; transition:background 0.1s; }
    .mf-table tbody tr:last-child { border-bottom:none; }
    .mf-table tbody tr:hover { background:#fafbfc; }
    .mf-table tbody td { padding:0.875rem 1.25rem; font-size:0.875rem; color:#374151; vertical-align:middle; }

    /* ── Urgency stripe ────────────────────────────────────────── */
    .mf-table tbody tr.mf-urgent { border-left:3px solid #ef4444; }
    .mf-table tbody tr.mf-recent { border-left:3px solid #f59e0b; }
    .mf-table tbody tr.mf-normal { border-left:3px solid transparent; }

    /* ── Reason badge ──────────────────────────────────────────── */
    .mf-badge {
        display:inline-flex; align-items:center; gap:4px;
        font-size:0.7rem; font-weight:700; padding:3px 10px; border-radius:999px;
        text-transform:uppercase; letter-spacing:0.04em; white-space:nowrap;
    }
    .mf-badge .material-icons-round { font-size:11px; }
    .mf-badge--max     { background:#fce7f3; color:#9d174d; }
    .mf-badge--timeout { background:#fef3c7; color:#92400e; }
    .mf-badge--manual  { background:#dbeafe; color:#1e40af; }
    .mf-badge--unknown { background:#f1f5f9; color:#475569; }

    /* ── Exchange progress bar ─────────────────────────────────── */
    .mf-xbar-wrap { width:80px; }
    .mf-xbar-track { height:4px; background:#e2e8f0; border-radius:999px; overflow:hidden; margin-top:4px; }
    .mf-xbar-fill  { height:100%; border-radius:999px; background:#ef4444; }
    .mf-xbar-full  { background:#ef4444; }

    /* ── User cell ─────────────────────────────────────────────── */
    .mf-user-name  { font-weight:600; color:#0f172a; }
    .mf-user-email { font-size:0.7rem; color:#94a3b8; margin-top:1px; }

    /* ── Time ago ──────────────────────────────────────────────── */
    .mf-flagged-ago { font-size:0.7rem; color:#94a3b8; margin-top:2px; }

    /* ── Action button ─────────────────────────────────────────── */
    .mf-open-btn {
        display:inline-flex; align-items:center; gap:5px;
        padding:0.45rem 1rem; border-radius:8px;
        background:var(--mf-navy); color:#fff;
        text-decoration:none; font-weight:600; font-size:0.78rem;
        transition:background 0.2s; white-space:nowrap;
    }
    .mf-open-btn:hover { background:var(--mf-navy-mid); }
    .mf-open-btn .material-icons-round { font-size:15px; }

    /* ── Empty state ───────────────────────────────────────────── */
    .mf-empty { padding:4rem 1rem; text-align:center; }
    .mf-empty-icon {
        width:64px; height:64px; border-radius:16px; background:var(--mf-navy-lt);
        display:flex; align-items:center; justify-content:center; margin:0 auto 1rem;
    }
    .mf-empty-icon .material-icons-round { font-size:32px; color:var(--mf-navy); }
    .mf-empty h3 { font-size:1.1rem; font-weight:700; color:#0f172a; margin:0 0 0.375rem; }
    .mf-empty p  { color:#64748b; font-size:0.875rem; margin:0; }

    /* ── Flash ─────────────────────────────────────────────────── */
    .mf-flash {
        display:flex; align-items:center; gap:10px;
        background:#ecfdf5; border:1px solid #10b981; color:#065f46;
        padding:0.875rem 1rem; border-radius:10px; margin-bottom:1rem;
        font-size:0.875rem; font-weight:500;
    }
    .mf-flash .material-icons-round { font-size:18px; color:#10b981; flex-shrink:0; }
</style>

<div class="mf-shell">

    {{-- ── Page header ──────────────────────────────────────────── --}}
    <div class="mf-header">
        <div class="mf-header-left">
            <h1>
                <span class="material-icons-round">flag</span>
                Flagged Messaging Threads
            </h1>
            <p>Threads escalated after the {{ \App\Models\PostAuctionThread::MAX_EXCHANGES }}-exchange or {{ \App\Models\PostAuctionThread::NEGOTIATION_WINDOW_HOURS }}-hour limit, or manually flagged by a participant.</p>
        </div>
        @if($mfTotal > 0)
            <div style="display:flex;align-items:center;gap:8px;">
                <span style="display:inline-flex;align-items:center;gap:5px;background:#fee2e2;color:#991b1b;font-weight:700;font-size:0.8rem;padding:5px 12px;border-radius:999px;">
                    <span class="material-icons-round" style="font-size:13px;">error</span>
                    {{ $mfTotal }} Needs Attention
                </span>
            </div>
        @endif
    </div>

    {{-- ── Flash message ─────────────────────────────────────────── --}}
    @if(session('success'))
        <div class="mf-flash">
            <span class="material-icons-round">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    {{-- ── Stat cards ────────────────────────────────────────────── --}}
    <div class="mf-stats">
        <div class="mf-stat">
            <div class="mf-stat-icon mf-stat-icon--total">
                <span class="material-icons-round">inbox</span>
            </div>
            <div>
                <div class="mf-stat-label">Total Flagged</div>
                <div class="mf-stat-value">{{ $mfTotal }}</div>
            </div>
        </div>
        <div class="mf-stat">
            <div class="mf-stat-icon mf-stat-icon--max">
                <span class="material-icons-round">swap_horiz</span>
            </div>
            <div>
                <div class="mf-stat-label">Max Exchanges</div>
                <div class="mf-stat-value">{{ $mfMax }}</div>
            </div>
        </div>
        <div class="mf-stat">
            <div class="mf-stat-icon mf-stat-icon--timeout">
                <span class="material-icons-round">schedule</span>
            </div>
            <div>
                <div class="mf-stat-label">48h Timeout</div>
                <div class="mf-stat-value">{{ $mfTimeout }}</div>
            </div>
        </div>
        <div class="mf-stat">
            <div class="mf-stat-icon mf-stat-icon--manual">
                <span class="material-icons-round">person_alert</span>
            </div>
            <div>
                <div class="mf-stat-label">Manual Requests</div>
                <div class="mf-stat-value">{{ $mfManual }}</div>
            </div>
        </div>
    </div>

    {{-- ── Filter bar ─────────────────────────────────────────────── --}}
    <form method="GET" action="{{ route('admin.messaging.flags.index') }}" class="mf-filter">
        <div class="mf-filter-search">
            <span class="material-icons-round">search</span>
            <input type="text" name="search" value="{{ $mfSearch }}" placeholder="Search by buyer, seller, listing…"/>
        </div>
        <div class="mf-tabs">
            <a href="{{ route('admin.messaging.flags.index') }}"
               class="mf-tab {{ $mfFilter === '' ? 'is-active' : '' }}">All</a>
            <a href="{{ route('admin.messaging.flags.index') }}?filter={{ \App\Models\PostAuctionThread::FLAG_MAX_EXCHANGES }}"
               class="mf-tab {{ $mfFilter === \App\Models\PostAuctionThread::FLAG_MAX_EXCHANGES ? 'is-active' : '' }}">Max Exchanges</a>
            <a href="{{ route('admin.messaging.flags.index') }}?filter={{ \App\Models\PostAuctionThread::FLAG_TIMEOUT_48H }}"
               class="mf-tab {{ $mfFilter === \App\Models\PostAuctionThread::FLAG_TIMEOUT_48H ? 'is-active' : '' }}">48h Timeout</a>
            <a href="{{ route('admin.messaging.flags.index') }}?filter={{ \App\Models\PostAuctionThread::FLAG_MANUAL }}"
               class="mf-tab {{ $mfFilter === \App\Models\PostAuctionThread::FLAG_MANUAL ? 'is-active' : '' }}">Manual</a>
        </div>
        <button type="submit" class="mf-filter-btn mf-filter-btn--go">
            <span class="material-icons-round">search</span> Search
        </button>
        @if($mfSearch || $mfFilter)
            <a href="{{ route('admin.messaging.flags.index') }}" class="mf-filter-btn mf-filter-btn--clr">
                <span class="material-icons-round">close</span> Clear
            </a>
        @endif
    </form>

    {{-- ── Table card ─────────────────────────────────────────────── --}}
    <div class="mf-card">
        <div class="mf-card-head">
            <h2>
                <span class="material-icons-round">forum</span>
                Flagged Threads
            </h2>
            <span class="mf-chip mf-chip--count">{{ $threads->total() }} total</span>
        </div>

        @if($threads->isEmpty())
            <div class="mf-empty">
                <div class="mf-empty-icon">
                    <span class="material-icons-round">verified</span>
                </div>
                <h3>All Clear</h3>
                <p>No messaging threads currently need admin attention.</p>
            </div>
        @else
            <table class="mf-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Listing</th>
                        <th>Buyer</th>
                        <th>Seller</th>
                        <th>Exchanges</th>
                        <th>Flagged</th>
                        <th>Reason</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($threads as $t)
                    @php
                        $reason      = $t->flag_reason ?? '';
                        $meta        = $mfReasonMeta[$reason] ?? ['label'=>str_replace('_',' ',$reason),'class'=>'mf-badge--unknown','icon'=>'help'];
                        $title       = trim(($t->listing->year ?? '').' '.($t->listing->make ?? '').' '.($t->listing->model ?? ''));
                        $flaggedAt   = $t->flagged_at;
                        $hoursAgo    = $flaggedAt ? $flaggedAt->diffInHours(now()) : null;
                        $rowClass    = $hoursAgo !== null && $hoursAgo < 2 ? 'mf-recent' : ($hoursAgo !== null && $hoursAgo >= 24 ? 'mf-urgent' : 'mf-normal');
                        $pct         = min(100, round(($t->exchanges_count / \App\Models\PostAuctionThread::MAX_EXCHANGES) * 100));
                    @endphp
                    <tr class="{{ $rowClass }}">
                        {{-- Thread # --}}
                        <td>
                            <span style="font-weight:700;color:var(--mf-navy);font-family:monospace;">#{{ $t->id }}</span>
                        </td>

                        {{-- Listing --}}
                        <td>
                            <div class="mf-user-name">{{ $title ?: 'Listing #'.$t->listing_id }}</div>
                            @if($t->invoice)
                                <div class="mf-user-email">Invoice #{{ $t->invoice->invoice_number ?? $t->invoice_id }}</div>
                            @endif
                        </td>

                        {{-- Buyer --}}
                        <td>
                            <div class="mf-user-name">{{ $t->buyer?->name ?? '—' }}</div>
                            <div class="mf-user-email">{{ $t->buyer?->email }}</div>
                        </td>

                        {{-- Seller --}}
                        <td>
                            <div class="mf-user-name">{{ $t->seller?->name ?? '—' }}</div>
                            <div class="mf-user-email">{{ $t->seller?->email }}</div>
                        </td>

                        {{-- Exchanges --}}
                        <td>
                            <div style="font-weight:700;color:#0f172a;">
                                {{ $t->exchanges_count }} <span style="color:#94a3b8;font-weight:400;">/ {{ \App\Models\PostAuctionThread::MAX_EXCHANGES }}</span>
                            </div>
                            <div class="mf-xbar-wrap">
                                <div class="mf-xbar-track">
                                    <div class="mf-xbar-fill" style="width:{{ $pct }}%"></div>
                                </div>
                            </div>
                        </td>

                        {{-- Flagged --}}
                        <td>
                            <div style="font-size:0.8rem;color:#374151;">
                                {{ $flaggedAt ? $flaggedAt->format('M d, Y') : '—' }}
                            </div>
                            <div class="mf-flagged-ago">
                                {{ $flaggedAt ? $flaggedAt->diffForHumans() : '' }}
                            </div>
                        </td>

                        {{-- Reason --}}
                        <td>
                            <span class="mf-badge {{ $meta['class'] }}">
                                <span class="material-icons-round">{{ $meta['icon'] }}</span>
                                {{ $meta['label'] }}
                            </span>
                        </td>

                        {{-- Action --}}
                        <td style="text-align:right;">
                            <a href="{{ route('admin.messaging.flags.show', $t->id) }}" class="mf-open-btn">
                                <span class="material-icons-round">open_in_new</span>
                                Review
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    {{-- ── Pagination ──────────────────────────────────────────────── --}}
    @if($threads->hasPages())
        <div style="margin-top:0.5rem;">{{ $threads->appends(request()->query())->links() }}</div>
    @endif

</div>
@endsection
