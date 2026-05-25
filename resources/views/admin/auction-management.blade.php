@extends('layouts.admin')

@section('title', 'Auction Management - Admin')

@section('content')
@php
    /* ── Image URL helper ─────────────────────────────────── */
    $amImgUrl = function ($path) {
        $path = trim((string) ($path ?? ''));
        if ($path === '' || str_starts_with($path, 'http')) return $path ?: asset('images/placeholder-product.png');
        $p = ltrim(str_replace('\\', '/', $path), '/');
        return str_starts_with($p, 'uploads/') ? asset($p) : asset('uploads/listings/' . $p);
    };

    /* ── Global stats ─────────────────────────────────────── */
    $amTotalCount  = \App\Models\Listing::where('listing_method', 'auction')->where('status', 'approved')->count();
    $amActiveCount = \App\Models\Listing::where('listing_method', 'auction')->where('status', 'approved')
        ->where(function ($q) {
            $q->where('auction_end_time', '>', now())
              ->orWhere(function ($q2) {
                  $q2->whereNull('auction_end_time')
                     ->whereRaw('DATE_ADD(COALESCE(auction_start_time, created_at), INTERVAL COALESCE(auction_duration, 7) DAY) > NOW()');
              });
        })->count();
    $amEndedCount  = max(0, $amTotalCount - $amActiveCount);
    $amTotalBids   = \App\Models\Bid::whereHas('listing', fn ($q) => $q->where('listing_method', 'auction'))->count();

    /* ── Selected listing for inline bidding panel ────────── */
    $amSelectedListing = null;
    if ($biddingLogs !== null) {
        $amSelectedListing = \App\Models\Listing::with('seller')->find(request('auction_id'));
    }

    $amCurrentSearch = request('search', '');
    $amCurrentFilter = request('filter', '');
@endphp

<style>
    :root { --navy:#063466; --navy-light:#e8eef6; --navy-mid:#0d4d8c; }

    /* ── Header ─────────────────────────────────────────────── */
    .am-header {
        background:#fff; border-radius:12px;
        padding:1.5rem 1.75rem; margin-bottom:1.5rem;
        border-left:4px solid var(--navy);
        box-shadow:0 1px 4px rgba(6,52,102,0.07);
        display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:0.75rem;
    }
    .am-header-text h1 { font-size:1.35rem; font-weight:700; color:var(--navy); margin:0 0 0.2rem; display:flex; align-items:center; gap:8px; }
    .am-header-text p  { margin:0; color:#64748b; font-size:0.875rem; }
    .am-header-link {
        display:inline-flex; align-items:center; gap:6px;
        padding:0.5rem 1.1rem; border-radius:9px; font-size:0.8125rem; font-weight:600;
        background:var(--navy-light); color:var(--navy); text-decoration:none; transition:background 0.2s;
    }
    .am-header-link:hover { background:#cddaf0; }
    .am-header-link .material-icons-round { font-size:16px; }

    /* ── Stats ───────────────────────────────────────────────── */
    .am-stats { display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:1rem; margin-bottom:1.5rem; }
    .am-stat-card {
        background:#fff; border-radius:12px; padding:1.25rem 1.5rem;
        box-shadow:0 1px 4px rgba(6,52,102,0.07); display:flex; align-items:center; gap:1rem;
    }
    .am-stat-icon {
        width:44px; height:44px; border-radius:10px;
        display:flex; align-items:center; justify-content:center; flex-shrink:0;
    }
    .am-stat-icon .material-icons-round { font-size:22px; }
    .am-stat-label { font-size:0.75rem; font-weight:600; color:#64748b; margin-bottom:2px; }
    .am-stat-value { font-size:1.5rem; font-weight:700; line-height:1; }

    /* ── Filter bar ──────────────────────────────────────────── */
    .am-filter-bar {
        background:#fff; border-radius:12px; padding:1rem 1.25rem; margin-bottom:1.5rem;
        box-shadow:0 1px 4px rgba(6,52,102,0.07);
        display:flex; flex-wrap:wrap; gap:0.75rem; align-items:center;
    }
    .am-filter-bar input[type=text] {
        flex:1; min-width:240px; padding:0.5rem 0.875rem;
        border:1.5px solid #e2e8f0; border-radius:8px;
        font-size:0.875rem; color:#374151; outline:none; transition:border-color 0.2s;
    }
    .am-filter-bar input[type=text]:focus { border-color:var(--navy); }
    .am-filter-tabs { display:flex; gap:4px; }
    .am-filter-tab {
        padding:0.45rem 1rem; border-radius:8px; font-size:0.8125rem; font-weight:600;
        border:1.5px solid #e2e8f0; background:#fff; color:#64748b;
        cursor:pointer; text-decoration:none; transition:all 0.15s;
    }
    .am-filter-tab.is-active, .am-filter-tab:hover { background:var(--navy); color:#fff; border-color:var(--navy); }
    .am-filter-btn {
        padding:0.5rem 1.25rem; border-radius:8px; font-size:0.875rem; font-weight:600;
        border:none; cursor:pointer; display:inline-flex; align-items:center; gap:6px;
        transition:background 0.2s; text-decoration:none;
    }
    .am-filter-btn--primary { background:var(--navy); color:#fff; }
    .am-filter-btn--primary:hover { background:var(--navy-mid); }
    .am-filter-btn--clear { background:#f1f5f9; color:#475569; }
    .am-filter-btn--clear:hover { background:#e2e8f0; }
    .am-filter-btn .material-icons-round { font-size:16px; }

    /* ── Card / Table ─────────────────────────────────────────── */
    .am-card {
        background:#fff; border-radius:12px;
        box-shadow:0 1px 4px rgba(6,52,102,0.07); overflow:hidden; margin-bottom:1.5rem;
    }
    .am-card-header {
        padding:1rem 1.5rem; border-bottom:1px solid #f1f5f9;
        display:flex; align-items:center; justify-content:space-between;
    }
    .am-card-header h2 { font-size:0.9375rem; font-weight:700; color:#0f172a; margin:0; display:flex; align-items:center; gap:6px; }
    .am-card-header h2 .material-icons-round { font-size:18px; color:var(--navy); }
    .am-count { font-size:0.75rem; font-weight:600; color:var(--navy); background:var(--navy-light); padding:2px 10px; border-radius:999px; }

    .am-table { width:100%; border-collapse:collapse; }
    .am-table thead th {
        padding:0.75rem 1.25rem; text-align:left;
        font-size:0.6875rem; font-weight:700; text-transform:uppercase;
        letter-spacing:0.06em; color:#64748b; background:#f8fafc;
        border-bottom:1px solid #f1f5f9; white-space:nowrap;
    }
    .am-table tbody tr { border-bottom:1px solid #f8fafc; transition:background 0.1s; }
    .am-table tbody tr:last-child { border-bottom:none; }
    .am-table tbody tr:hover { background:#fafbfc; }
    .am-table tbody td { padding:0.875rem 1.25rem; font-size:0.875rem; color:#374151; vertical-align:middle; }

    /* ── Thumbnail ────────────────────────────────────────────── */
    .am-thumb {
        position:relative; width:68px; height:52px; flex-shrink:0;
        border-radius:10px; overflow:hidden; cursor:pointer;
        border:1px solid #e2e8f0; background:#f8fafc;
        box-shadow:0 1px 2px rgba(15,23,42,0.06);
    }
    .am-thumb img { width:100%; height:100%; object-fit:cover; display:block; }
    .am-thumb-overlay {
        position:absolute; inset:0;
        display:flex; align-items:center; justify-content:center;
        background:rgba(6,52,102,0); transition:background 0.2s;
    }
    .am-thumb:hover .am-thumb-overlay { background:rgba(6,52,102,0.45); }
    .am-thumb-overlay .material-icons-round { font-size:20px; color:#fff; opacity:0; transition:opacity 0.2s; }
    .am-thumb:hover .am-thumb-overlay .material-icons-round { opacity:1; }
    .am-thumb-btn { display:block; width:100%; height:100%; border:none; background:transparent; padding:0; cursor:pointer; }
    .am-thumb-none {
        width:68px; height:52px; border-radius:10px;
        background:#f1f5f9; display:flex; align-items:center; justify-content:center; flex-shrink:0;
    }
    .am-thumb-none .material-icons-round { font-size:22px; color:#cbd5e1; }

    /* ── Cell helpers ─────────────────────────────────────────── */
    .am-item-num  { font-size:0.75rem; font-weight:700; color:var(--navy); font-family:monospace; }
    .am-item-id   { font-size:0.7rem; color:#94a3b8; margin-top:2px; }
    .am-veh-name  { font-weight:600; color:#0f172a; }
    .am-veh-sub   { font-size:0.72rem; color:#94a3b8; margin-top:2px; }
    .am-sel-name  { font-weight:600; color:#0f172a; }
    .am-sel-email { font-size:0.72rem; color:#94a3b8; }
    .am-bid-val   { font-weight:700; color:#0f172a; }
    .am-bid-sub   { font-size:0.72rem; margin-top:2px; }
    .am-time      { color:#374151; }

    /* ── Status badges ────────────────────────────────────────── */
    .am-status {
        display:inline-flex; align-items:center; gap:5px;
        font-size:0.72rem; font-weight:700; padding:3px 10px;
        border-radius:999px; white-space:nowrap;
    }
    .am-status .material-icons-round { font-size:11px; }
    .am-status--active { background:#dcfce7; color:#15803d; }
    .am-status--ended  { background:#f1f5f9; color:#475569; }
    .am-status--ending { background:#fef3c7; color:#92400e; }
    .am-status--paused { background:#fde8d4; color:#9a3412; }

    /* ── Bid count pill ───────────────────────────────────────── */
    .am-bid-pill {
        display:inline-flex; align-items:center; justify-content:center;
        min-width:28px; padding:2px 8px; border-radius:999px;
        font-size:0.75rem; font-weight:700;
    }
    .am-bid-pill--has  { background:#dcfce7; color:#15803d; }
    .am-bid-pill--none { background:#f1f5f9; color:#64748b; }

    /* ── Action buttons ───────────────────────────────────────── */
    .am-btn {
        display:inline-flex; align-items:center; justify-content:center;
        width:32px; height:32px; border-radius:8px;
        border:none; cursor:pointer; transition:background 0.2s;
        text-decoration:none; flex-shrink:0;
    }
    .am-btn .material-icons-round { font-size:17px; }
    .am-btn--logs   { background:var(--navy-light); color:var(--navy); }
    .am-btn--logs:hover   { background:#cddaf0; }
    .am-btn--pause  { background:#fef3c7; color:#92400e; }
    .am-btn--pause:hover  { background:#fde68a; }
    .am-btn--cancel { background:#fee2e2; color:#dc2626; }
    .am-btn--cancel:hover { background:#fecaca; }
    .am-btn--view   { background:#f0fdf4; color:#15803d; }
    .am-btn--view:hover   { background:#dcfce7; }

    /* ── Empty state ──────────────────────────────────────────── */
    .am-empty { text-align:center; padding:3.5rem 1rem; color:#94a3b8; }
    .am-empty .material-icons-round { font-size:48px; display:block; margin-bottom:0.75rem; opacity:0.35; }
    .am-empty p { margin:0; font-size:0.9375rem; }
    .am-pagination { padding:1rem 1.25rem; border-top:1px solid #f1f5f9; }

    /* ── Inline Bidding Panel ─────────────────────────────────── */
    .am-bidding-panel {
        background:#fff; border-radius:12px;
        box-shadow:0 1px 4px rgba(6,52,102,0.07); overflow:hidden; margin-bottom:1.5rem;
        border-top:3px solid var(--navy);
        scroll-margin-top:80px;
    }
    .am-bidding-header {
        padding:1rem 1.5rem; border-bottom:1px solid #f1f5f9;
        display:flex; align-items:center; justify-content:space-between; gap:1rem;
        background:var(--navy-light);
    }
    .am-bidding-header h2 { font-size:1rem; font-weight:700; color:var(--navy); margin:0; display:flex; align-items:center; gap:7px; }
    .am-bidding-header h2 .material-icons-round { font-size:18px; }
    .am-bidding-header-actions { display:flex; align-items:center; gap:8px; }
    .am-panel-close {
        display:inline-flex; align-items:center; gap:5px;
        padding:0.4rem 0.875rem; border-radius:8px; font-size:0.8125rem; font-weight:600;
        background:#fff; color:#475569; text-decoration:none; border:1.5px solid #e2e8f0;
        transition:background 0.15s;
    }
    .am-panel-close:hover { background:#f1f5f9; }
    .am-panel-close .material-icons-round { font-size:15px; }
    .am-panel-full {
        display:inline-flex; align-items:center; gap:5px;
        padding:0.4rem 0.875rem; border-radius:8px; font-size:0.8125rem; font-weight:600;
        background:var(--navy); color:#fff; text-decoration:none; border:1.5px solid var(--navy);
        transition:background 0.15s;
    }
    .am-panel-full:hover { background:var(--navy-mid); }
    .am-panel-full .material-icons-round { font-size:15px; }

    .am-bidding-summary {
        display:flex; flex-wrap:wrap; gap:0.75rem; padding:1rem 1.5rem;
        border-bottom:1px solid #f1f5f9; background:#fafbfc;
    }
    .am-bidding-meta {
        display:flex; align-items:center; gap:5px;
        font-size:0.8125rem; color:#475569;
    }
    .am-bidding-meta .material-icons-round { font-size:15px; color:#94a3b8; }
    .am-bidding-meta strong { color:#0f172a; }

    .am-bl-table { width:100%; border-collapse:collapse; }
    .am-bl-table thead th {
        padding:0.625rem 1.25rem; text-align:left;
        font-size:0.6875rem; font-weight:700; text-transform:uppercase;
        letter-spacing:0.05em; color:#64748b; background:#f8fafc;
        border-bottom:1px solid #f1f5f9; white-space:nowrap;
    }
    .am-bl-table tbody tr { border-bottom:1px solid #f8fafc; transition:background 0.1s; }
    .am-bl-table tbody tr:last-child { border-bottom:none; }
    .am-bl-table tbody tr:hover { background:#fafbfc; }
    .am-bl-table tbody tr.am-bl-row--removed { opacity:0.6; }
    .am-bl-table tbody td { padding:0.75rem 1.25rem; font-size:0.875rem; color:#374151; vertical-align:middle; }

    .am-bl-badge {
        display:inline-flex; align-items:center; gap:4px;
        font-size:0.72rem; font-weight:700; padding:3px 9px;
        border-radius:999px; white-space:nowrap;
    }
    .am-bl-badge .material-icons-round { font-size:11px; }
    .am-bl-badge--winning  { background:#dcfce7; color:#15803d; }
    .am-bl-badge--outbid   { background:#fef3c7; color:#92400e; }
    .am-bl-badge--removed  { background:#fee2e2; color:#dc2626; }

    .am-bl-remove {
        display:inline-flex; align-items:center; justify-content:center;
        width:28px; height:28px; border-radius:7px;
        border:none; cursor:pointer; background:#fee2e2; color:#dc2626;
        transition:background 0.15s;
    }
    .am-bl-remove:hover { background:#fecaca; }
    .am-bl-remove .material-icons-round { font-size:15px; }

    /* ── Image modal ──────────────────────────────────────────── */
    .am-modal {
        position:fixed; inset:0;
        display:none; align-items:center; justify-content:center;
        padding:1.5rem; background:rgba(15,23,42,0.5);
        backdrop-filter:blur(3px); z-index:9999;
    }
    .am-modal.is-open { display:flex; }
    .am-modal-img-card {
        position:relative; max-width:min(860px,95vw);
        background:#000; border-radius:14px; overflow:hidden;
        box-shadow:0 24px 60px rgba(15,23,42,0.4);
    }
    .am-modal-img-card img { display:block; max-width:100%; max-height:85vh; object-fit:contain; }
    .am-modal-img-footer { background:#fff; padding:0.875rem 1.125rem; display:flex; align-items:center; justify-content:space-between; gap:1rem; }
    .am-modal-img-footer h3 { font-size:0.9375rem; font-weight:700; color:#0f172a; margin:0; }
    .am-modal-img-footer p  { margin:0; font-size:0.8125rem; color:#64748b; }
    .am-modal-close {
        position:absolute; top:0.75rem; right:0.75rem;
        width:36px; height:36px; border:none; border-radius:8px;
        background:rgba(15,23,42,0.72); color:#fff;
        cursor:pointer; display:flex; align-items:center; justify-content:center;
        z-index:2; transition:background 0.2s;
    }
    .am-modal-close:hover { background:rgba(15,23,42,0.9); }
    .am-modal-close .material-icons-round { font-size:18px; }

    /* ── Confirm dialogs ──────────────────────────────────────── */
    .am-dialog {
        position:fixed; inset:0; z-index:9999;
        display:flex; align-items:center; justify-content:center; padding:1.5rem;
    }
    .am-dialog.hidden { display:none !important; }
    .am-dialog-backdrop { position:absolute; inset:0; background:rgba(15,23,42,0.5); backdrop-filter:blur(4px); }
    .am-dialog-card {
        position:relative; width:100%; max-width:420px;
        background:#fff; border-radius:16px;
        box-shadow:0 25px 50px -12px rgba(0,0,0,0.25); padding:1.75rem;
    }
    .am-dialog-icon { width:52px; height:52px; border-radius:12px; display:flex; align-items:center; justify-content:center; margin-bottom:1rem; }
    .am-dialog-icon .material-icons-round { font-size:26px; }
    .am-dialog-title { font-size:1.125rem; font-weight:700; color:#0f172a; margin:0 0 0.4rem; }
    .am-dialog-msg   { font-size:0.9rem; color:#64748b; line-height:1.55; margin:0 0 1.25rem; }
    .am-dialog-actions { display:flex; gap:0.75rem; justify-content:flex-end; }
    .am-dialog-btn {
        padding:0.6rem 1.25rem; font-size:0.9rem; font-weight:700;
        border-radius:9px; border:none; cursor:pointer; transition:background 0.2s;
    }
    .am-dialog-btn--cancel  { background:#f1f5f9; color:#475569; }
    .am-dialog-btn--cancel:hover  { background:#e2e8f0; }
    .am-dialog-btn--danger  { background:#dc2626; color:#fff; }
    .am-dialog-btn--danger:hover  { background:#b91c1c; }
    .am-dialog-btn--warn    { background:#d97706; color:#fff; }
    .am-dialog-btn--warn:hover    { background:#b45309; }

    /* ── Remove bid dialog ────────────────────────────────────── */
    .am-bl-remove-form { display:none; }

    /* ── Responsive ───────────────────────────────────────────── */
    @media (max-width:640px) {
        .am-table thead th:nth-child(3),
        .am-table tbody td:nth-child(3) { display:none; }
        .am-table thead th:nth-child(7),
        .am-table tbody td:nth-child(7) { display:none; }
    }
</style>

<div>
    {{-- ── Page header ────────────────────────────────────────── --}}
    <div class="am-header">
        <div class="am-header-text">
            <h1>
                <span class="material-icons-round" style="font-size:1.25rem;color:var(--navy)">manage_search</span>
                Auction Management
            </h1>
            <p>Full auction history — monitor status, review bids, pause or cancel auctions</p>
        </div>
        <a href="{{ route('admin.active-listings') }}" class="am-header-link">
            <span class="material-icons-round">gavel</span>
            Active Auctions
        </a>
    </div>

    {{-- ── Flash messages ──────────────────────────────────────── --}}
    @if(session('success'))
    <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:0.875rem 1.25rem;margin-bottom:1.25rem;display:flex;align-items:center;gap:10px;color:#15803d;font-weight:600;font-size:0.875rem;">
        <span class="material-icons-round" style="font-size:18px">check_circle</span>
        {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:10px;padding:0.875rem 1.25rem;margin-bottom:1.25rem;display:flex;align-items:center;gap:10px;color:#dc2626;font-weight:600;font-size:0.875rem;">
        <span class="material-icons-round" style="font-size:18px">error</span>
        {{ session('error') }}
    </div>
    @endif

    {{-- ── Stat cards ─────────────────────────────────────────── --}}
    <div class="am-stats">
        <div class="am-stat-card">
            <div class="am-stat-icon" style="background:var(--navy-light)">
                <span class="material-icons-round" style="color:var(--navy)">inventory_2</span>
            </div>
            <div>
                <div class="am-stat-label">Total Auctions</div>
                <div class="am-stat-value" style="color:var(--navy)">{{ $amTotalCount }}</div>
            </div>
        </div>
        <div class="am-stat-card">
            <div class="am-stat-icon" style="background:#dcfce7">
                <span class="material-icons-round" style="color:#16a34a">play_circle</span>
            </div>
            <div>
                <div class="am-stat-label">Currently Active</div>
                <div class="am-stat-value" style="color:#16a34a">{{ $amActiveCount }}</div>
            </div>
        </div>
        <div class="am-stat-card">
            <div class="am-stat-icon" style="background:#f1f5f9">
                <span class="material-icons-round" style="color:#64748b">history</span>
            </div>
            <div>
                <div class="am-stat-label">Ended</div>
                <div class="am-stat-value" style="color:#64748b">{{ $amEndedCount }}</div>
            </div>
        </div>
        <div class="am-stat-card">
            <div class="am-stat-icon" style="background:#ede9fe">
                <span class="material-icons-round" style="color:#7c3aed">how_to_vote</span>
            </div>
            <div>
                <div class="am-stat-label">Total Bids</div>
                <div class="am-stat-value" style="color:#7c3aed">{{ number_format($amTotalBids) }}</div>
            </div>
        </div>
    </div>

    {{-- ── Search / filter bar (server-side GET form) ──────────── --}}
    <form method="GET" action="{{ route('admin.auctions') }}" class="am-filter-bar" id="amFilterForm">
        <input type="text" name="search" value="{{ $amCurrentSearch }}" placeholder="Search by item #, make or model…">

        <div class="am-filter-tabs">
            <a href="{{ route('admin.auctions', array_filter(['search' => $amCurrentSearch])) }}"
                class="am-filter-tab {{ $amCurrentFilter === '' ? 'is-active' : '' }}">All</a>
            <a href="{{ route('admin.auctions', array_filter(['filter' => 'active', 'search' => $amCurrentSearch])) }}"
                class="am-filter-tab {{ $amCurrentFilter === 'active' ? 'is-active' : '' }}">Active Only</a>
        </div>

        @if($amCurrentFilter)
            <input type="hidden" name="filter" value="{{ $amCurrentFilter }}">
        @endif

        <button type="submit" class="am-filter-btn am-filter-btn--primary">
            <span class="material-icons-round">search</span> Search
        </button>
        <a href="{{ route('admin.auctions') }}" class="am-filter-btn am-filter-btn--clear">
            <span class="material-icons-round">close</span> Clear
        </a>
    </form>

    {{-- ── Auctions table ──────────────────────────────────────── --}}
    <div class="am-card">
        <div class="am-card-header">
            <h2>
                <span class="material-icons-round">gavel</span>
                Auctions
                @if($amCurrentSearch || $amCurrentFilter)
                <span style="font-size:0.75rem;font-weight:400;color:#94a3b8;margin-left:4px">
                    — filtered results
                </span>
                @endif
            </h2>
            <span class="am-count">{{ $auctions->total() }} {{ Str::plural('listing', $auctions->total()) }}</span>
        </div>

        <div style="overflow-x:auto">
            <table class="am-table">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Vehicle</th>
                        <th>Seller</th>
                        <th>Status</th>
                        <th style="text-align:center">Bids</th>
                        <th>Current Bid</th>
                        <th>Auction Ends</th>
                        <th style="text-align:center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($auctions as $auction)
                    @php
                        $mainImg   = $auction->images->first();
                        $imgUrl    = $mainImg ? $amImgUrl($mainImg->image_path) : null;
                        $vehicle   = trim(($auction->year ?? '') . ' ' . ($auction->make ?? '') . ' ' . ($auction->model ?? ''));
                        $bidCount  = $auction->bids->count();
                        $highestBid = $auction->bids->max('amount') ?? $auction->starting_price ?? 0;

                        // Compute end time
                        $endTime = null;
                        if ($auction->auction_end_time) {
                            $endTime = \Carbon\Carbon::parse($auction->auction_end_time);
                        } elseif ($auction->auction_start_time && $auction->auction_duration) {
                            $endTime = \Carbon\Carbon::parse($auction->auction_start_time)->addDays((int)$auction->auction_duration);
                        }

                        $hoursLeft   = $endTime ? now()->diffInHours($endTime, false) : null;
                        $isActive    = $endTime && $endTime->isFuture();
                        $isEndingSoon = $isActive && $hoursLeft !== null && $hoursLeft <= 24;
                    @endphp
                    <tr>
                        {{-- Item number --}}
                        <td>
                            <div class="am-item-num">{{ $auction->item_number ?? 'N/A' }}</div>
                            <div class="am-item-id">#{{ $auction->id }}</div>
                        </td>

                        {{-- Vehicle + thumbnail --}}
                        <td>
                            <div style="display:flex;align-items:center;gap:10px">
                                @if($imgUrl)
                                <div class="am-thumb">
                                    <button type="button" class="am-thumb-btn js-am-img"
                                        data-image="{{ $imgUrl }}"
                                        data-title="{{ $vehicle ?: 'Listing #'.$auction->id }}"
                                        data-meta="Item {{ $auction->item_number ?? '#'.$auction->id }}">
                                        <img src="{{ $imgUrl }}" alt="{{ $vehicle }}">
                                        <div class="am-thumb-overlay">
                                            <span class="material-icons-round">zoom_in</span>
                                        </div>
                                    </button>
                                </div>
                                @else
                                <div class="am-thumb-none">
                                    <span class="material-icons-round">directions_car</span>
                                </div>
                                @endif
                                <div>
                                    <div class="am-veh-name">{{ $vehicle ?: '—' }}</div>
                                    @if($auction->subcategory)
                                    <div class="am-veh-sub">{{ $auction->subcategory }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>

                        {{-- Seller --}}
                        <td>
                            <div class="am-sel-name">{{ $auction->seller->name ?? '—' }}</div>
                            <div class="am-sel-email">{{ $auction->seller->email ?? '' }}</div>
                        </td>

                        {{-- Status --}}
                        <td>
                            @if($isEndingSoon)
                                <span class="am-status am-status--ending">
                                    <span class="material-icons-round">timer</span>
                                    Ending Soon
                                </span>
                            @elseif($isActive)
                                <span class="am-status am-status--active">
                                    <span class="material-icons-round">play_circle</span>
                                    Active
                                </span>
                            @else
                                <span class="am-status am-status--ended">
                                    <span class="material-icons-round">history</span>
                                    Ended
                                </span>
                            @endif
                        </td>

                        {{-- Bids count --}}
                        <td style="text-align:center">
                            <span class="am-bid-pill {{ $bidCount > 0 ? 'am-bid-pill--has' : 'am-bid-pill--none' }}">
                                {{ $bidCount }}
                            </span>
                        </td>

                        {{-- Current / Highest bid --}}
                        <td>
                            <div class="am-bid-val">${{ number_format($highestBid, 2) }}</div>
                            @if($bidCount === 0)
                            <div class="am-bid-sub" style="color:#94a3b8">
                                Start: ${{ number_format($auction->starting_price ?? 0, 2) }}
                            </div>
                            @else
                            <div class="am-bid-sub" style="color:#16a34a">
                                {{ $bidCount }} {{ Str::plural('bid', $bidCount) }}
                            </div>
                            @endif
                        </td>

                        {{-- Auction end time --}}
                        <td>
                            @if($endTime)
                            <div class="am-time" style="{{ $isEndingSoon ? 'color:#d97706;font-weight:600' : '' }}">
                                {{ $endTime->format('M j, Y') }}
                                <span style="font-size:0.72rem;color:#94a3b8;display:block">{{ $endTime->format('g:i A') }}</span>
                            </div>
                            <div style="font-size:0.72rem;color:{{ $isActive ? '#94a3b8' : '#dc2626' }};margin-top:2px">
                                {{ $endTime->diffForHumans() }}
                            </div>
                            @else
                            <span style="color:#cbd5e1">—</span>
                            @endif
                        </td>

                        {{-- Actions --}}
                        <td>
                            <div style="display:flex;align-items:center;justify-content:center;gap:6px">
                                {{-- View full listing detail --}}
                                <a href="{{ route('admin.listings.approval-detail', $auction->id) }}"
                                    class="am-btn am-btn--view" title="View listing detail">
                                    <span class="material-icons-round">visibility</span>
                                </a>
                                {{-- View bidding logs (dedicated page) --}}
                                <a href="{{ route('admin.auctions.bidding-logs', $auction->id) }}"
                                    class="am-btn am-btn--logs" title="View bidding logs">
                                    <span class="material-icons-round">format_list_numbered</span>
                                </a>
                                {{-- Pause auction --}}
                                @if($isActive)
                                <button type="button"
                                    class="am-btn am-btn--pause" title="Pause auction"
                                    onclick="amOpenPause({{ $auction->id }}, '{{ addslashes($vehicle ?: '#'.$auction->id) }}')">
                                    <span class="material-icons-round">pause_circle</span>
                                </button>
                                @endif
                                {{-- Cancel auction --}}
                                <button type="button"
                                    class="am-btn am-btn--cancel" title="Cancel auction"
                                    onclick="amOpenCancel({{ $auction->id }}, '{{ addslashes($vehicle ?: '#'.$auction->id) }}')">
                                    <span class="material-icons-round">cancel</span>
                                </button>
                            </div>

                            {{-- Hidden pause form --}}
                            <form id="am-pause-form-{{ $auction->id }}" method="POST"
                                action="{{ route('admin.auctions.toggle-status', $auction->id) }}" style="display:none">
                                @csrf
                                <input type="hidden" name="action" value="pause">
                            </form>

                            {{-- Hidden cancel form --}}
                            <form id="am-cancel-form-{{ $auction->id }}" method="POST"
                                action="{{ route('admin.auctions.cancel', $auction->id) }}" style="display:none">
                                @csrf
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8">
                            <div class="am-empty">
                                <span class="material-icons-round">gavel</span>
                                <p>No auctions found{{ $amCurrentSearch ? ' matching "'.$amCurrentSearch.'"' : '' }}</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($auctions->hasPages())
        <div class="am-pagination">
            {{ $auctions->appends(request()->query())->links() }}
        </div>
        @endif
    </div>

    {{-- ── Inline Bidding Logs Panel ───────────────────────────── --}}
    @if($biddingLogs !== null)
    <div class="am-bidding-panel" id="am-bidding-panel">
        {{-- Panel header --}}
        <div class="am-bidding-header">
            <h2>
                <span class="material-icons-round">format_list_numbered</span>
                Bidding Logs
                @if($amSelectedListing)
                — <span style="font-weight:400;font-size:0.9rem">
                    {{ trim(($amSelectedListing->year ?? '').' '.($amSelectedListing->make ?? '').' '.($amSelectedListing->model ?? '')) ?: '#'.$amSelectedListing->id }}
                </span>
                @endif
            </h2>
            <div class="am-bidding-header-actions">
                @if($amSelectedListing)
                <a href="{{ route('admin.auctions.bidding-logs', $amSelectedListing->id) }}" class="am-panel-full">
                    <span class="material-icons-round">open_in_full</span>
                    Full View
                </a>
                @endif
                <a href="{{ route('admin.auctions', array_filter(['search' => $amCurrentSearch, 'filter' => $amCurrentFilter])) }}"
                    class="am-panel-close">
                    <span class="material-icons-round">close</span>
                    Close
                </a>
            </div>
        </div>

        {{-- Listing meta summary --}}
        @if($amSelectedListing)
        <div class="am-bidding-summary">
            <span class="am-bidding-meta">
                <span class="material-icons-round">tag</span>
                <span>Item <strong>{{ $amSelectedListing->item_number ?? '#'.$amSelectedListing->id }}</strong></span>
            </span>
            <span class="am-bidding-meta">
                <span class="material-icons-round">person</span>
                <span>Seller: <strong>{{ $amSelectedListing->seller->name ?? '—' }}</strong></span>
            </span>
            <span class="am-bidding-meta">
                <span class="material-icons-round">how_to_vote</span>
                <span>Total bids: <strong>{{ $biddingLogs->count() }}</strong></span>
            </span>
            <span class="am-bidding-meta">
                <span class="material-icons-round">price_check</span>
                <span>Highest: <strong>${{ $biddingLogs->where('status','!=','removed')->max('amount') ? number_format($biddingLogs->where('status','!=','removed')->max('amount'), 2) : '—' }}</strong></span>
            </span>
            @if($amSelectedListing->auction_end_time)
            <span class="am-bidding-meta">
                <span class="material-icons-round">schedule</span>
                <span>Ends: <strong>{{ \Carbon\Carbon::parse($amSelectedListing->auction_end_time)->format('M j, Y g:i A') }}</strong></span>
            </span>
            @endif
        </div>
        @endif

        {{-- Bids table --}}
        <div style="overflow-x:auto">
            @if($biddingLogs->isEmpty())
            <div class="am-empty">
                <span class="material-icons-round">how_to_vote</span>
                <p>No bids placed on this auction yet</p>
            </div>
            @else
            <table class="am-bl-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Bidder</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Placed At</th>
                        <th style="text-align:center">Remove</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($biddingLogs as $index => $bid)
                    @php
                        $isRemoved = isset($bid->status) && $bid->status === 'removed';
                        $isTop     = $index === 0 && !$isRemoved;
                    @endphp
                    <tr class="{{ $isRemoved ? 'am-bl-row--removed' : '' }}">
                        <td>
                            <span style="font-size:0.75rem;font-weight:700;color:{{ $isTop ? '#15803d' : '#94a3b8' }};font-family:monospace">
                                #{{ $bid->id }}
                            </span>
                            <div style="font-size:0.7rem;color:#cbd5e1;margin-top:1px">rank {{ $index + 1 }}</div>
                        </td>
                        <td>
                            <div style="font-weight:600;color:#0f172a">{{ $bid->user->name ?? '—' }}</div>
                            <div style="font-size:0.72rem;color:#94a3b8">{{ $bid->user->email ?? '' }}</div>
                        </td>
                        <td>
                            <span style="font-weight:700;font-size:0.9375rem;color:{{ $isTop ? '#15803d' : '#374151' }}">
                                ${{ number_format($bid->amount, 2) }}
                            </span>
                            @if($isTop)
                            <div style="font-size:0.7rem;color:#16a34a;margin-top:1px">Highest Bid</div>
                            @endif
                        </td>
                        <td>
                            @if($isRemoved)
                                <span class="am-bl-badge am-bl-badge--removed">
                                    <span class="material-icons-round">block</span>Removed
                                </span>
                            @elseif($isTop)
                                <span class="am-bl-badge am-bl-badge--winning">
                                    <span class="material-icons-round">emoji_events</span>Winning
                                </span>
                            @else
                                <span class="am-bl-badge am-bl-badge--outbid">
                                    <span class="material-icons-round">arrow_downward</span>Outbid
                                </span>
                            @endif
                        </td>
                        <td>
                            <div style="font-size:0.8125rem;color:#374151">
                                {{ $bid->created_at->format('M j, Y') }}
                                <span style="font-size:0.72rem;color:#94a3b8;display:block">{{ $bid->created_at->format('g:i A') }}</span>
                            </div>
                        </td>
                        <td style="text-align:center">
                            @if(!$isRemoved)
                            <button type="button"
                                class="am-bl-remove"
                                title="Remove this bid"
                                onclick="amBlOpenRemove({{ $bid->id }}, '{{ addslashes($bid->user->name ?? 'Unknown') }}', '{{ number_format($bid->amount, 2) }}')">
                                <span class="material-icons-round">remove_circle</span>
                            </button>
                            {{-- Hidden remove form --}}
                            <form id="am-bl-remove-form-{{ $bid->id }}" method="POST"
                                action="{{ route('admin.bids.remove', $bid->id) }}"
                                class="am-bl-remove-form">
                                @csrf
                            </form>
                            @else
                            <span style="font-size:0.72rem;color:#cbd5e1">—</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </div>
    @endif

</div>{{-- /outer div --}}

{{-- ── Image preview modal ─────────────────────────────────── --}}
<div id="amImgModal" class="am-modal" aria-hidden="true">
    <div class="am-modal-img-card" id="amImgCard" onclick="event.stopPropagation()">
        <button type="button" class="am-modal-close" onclick="amCloseImg()">
            <span class="material-icons-round">close</span>
        </button>
        <img id="amImgModalSrc" src="" alt="Listing photo">
        <div class="am-modal-img-footer">
            <div>
                <h3 id="amImgModalTitle"></h3>
                <p id="amImgModalMeta"></p>
            </div>
            <span style="font-size:0.75rem;color:#94a3b8;white-space:nowrap">Click outside to close</span>
        </div>
    </div>
</div>

{{-- ── Cancel auction dialog ───────────────────────────────── --}}
<div id="amCancelDialog" class="am-dialog hidden" aria-hidden="true">
    <div class="am-dialog-backdrop" onclick="amCloseCancel()"></div>
    <div class="am-dialog-card">
        <div class="am-dialog-icon" style="background:#fee2e2">
            <span class="material-icons-round" style="color:#dc2626">cancel</span>
        </div>
        <h3 class="am-dialog-title">Cancel this auction?</h3>
        <p class="am-dialog-msg" id="amCancelMsg">
            This will immediately cancel the auction. All active bids will be voided. This cannot be undone.
        </p>
        <div class="am-dialog-actions">
            <button type="button" class="am-dialog-btn am-dialog-btn--cancel" onclick="amCloseCancel()">Keep Auction</button>
            <button type="button" class="am-dialog-btn am-dialog-btn--danger" id="amCancelConfirmBtn">Yes, Cancel It</button>
        </div>
    </div>
</div>

{{-- ── Pause auction dialog ─────────────────────────────────── --}}
<div id="amPauseDialog" class="am-dialog hidden" aria-hidden="true">
    <div class="am-dialog-backdrop" onclick="amClosePause()"></div>
    <div class="am-dialog-card">
        <div class="am-dialog-icon" style="background:#fef3c7">
            <span class="material-icons-round" style="color:#d97706">pause_circle</span>
        </div>
        <h3 class="am-dialog-title">Pause this auction?</h3>
        <p class="am-dialog-msg" id="amPauseMsg">
            The auction will be paused and hidden from active listings. You can resume it from the listing detail page.
        </p>
        <div class="am-dialog-actions">
            <button type="button" class="am-dialog-btn am-dialog-btn--cancel" onclick="amClosePause()">Cancel</button>
            <button type="button" class="am-dialog-btn am-dialog-btn--warn" id="amPauseConfirmBtn">Pause Auction</button>
        </div>
    </div>
</div>

{{-- ── Remove bid dialog ────────────────────────────────────── --}}
<div id="amBlRemoveDialog" class="am-dialog hidden" aria-hidden="true">
    <div class="am-dialog-backdrop" onclick="amBlCloseRemove()"></div>
    <div class="am-dialog-card">
        <div class="am-dialog-icon" style="background:#fee2e2">
            <span class="material-icons-round" style="color:#dc2626">remove_circle</span>
        </div>
        <h3 class="am-dialog-title">Remove this bid?</h3>
        <p class="am-dialog-msg" id="amBlRemoveMsg">
            This bid will be marked as removed. The bidder may re-bid unless further action is taken.
        </p>
        <div class="am-dialog-actions">
            <button type="button" class="am-dialog-btn am-dialog-btn--cancel" onclick="amBlCloseRemove()">Cancel</button>
            <button type="button" class="am-dialog-btn am-dialog-btn--danger" id="amBlRemoveConfirmBtn">Remove Bid</button>
        </div>
    </div>
</div>

<script>
// ── Image modal ───────────────────────────────────────────────────────────────
(function () {
    var modal     = document.getElementById('amImgModal');
    var modalCard = document.getElementById('amImgCard');
    var imgEl     = document.getElementById('amImgModalSrc');
    var titleEl   = document.getElementById('amImgModalTitle');
    var metaEl    = document.getElementById('amImgModalMeta');
    if (!modal) return;

    document.querySelectorAll('.js-am-img').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            imgEl.src           = btn.dataset.image || '';
            titleEl.textContent = btn.dataset.title || '';
            metaEl.textContent  = btn.dataset.meta  || '';
            modal.classList.add('is-open');
            modal.setAttribute('aria-hidden', 'false');
        });
    });
    modal.addEventListener('click', function (e) {
        if (!modalCard.contains(e.target)) amCloseImg();
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') { amCloseImg(); amCloseCancel(); amClosePause(); amBlCloseRemove(); }
    });
})();

function amCloseImg() {
    var m = document.getElementById('amImgModal');
    if (m) { m.classList.remove('is-open'); m.setAttribute('aria-hidden', 'true'); }
}

// ── Cancel dialog ─────────────────────────────────────────────────────────────
var _amCancelId = null;
function amOpenCancel(id, title) {
    _amCancelId = id;
    var msg = document.getElementById('amCancelMsg');
    if (msg) msg.textContent = 'This will immediately cancel the auction for "' + title + '". All active bids will be voided. This cannot be undone.';
    var dlg = document.getElementById('amCancelDialog');
    if (dlg) { dlg.classList.remove('hidden'); dlg.setAttribute('aria-hidden', 'false'); }
}
function amCloseCancel() {
    var dlg = document.getElementById('amCancelDialog');
    if (dlg) { dlg.classList.add('hidden'); dlg.setAttribute('aria-hidden', 'true'); }
    _amCancelId = null;
}
document.getElementById('amCancelConfirmBtn').addEventListener('click', function () {
    if (_amCancelId) {
        var form = document.getElementById('am-cancel-form-' + _amCancelId);
        if (form) form.submit();
    }
});

// ── Pause dialog ──────────────────────────────────────────────────────────────
var _amPauseId = null;
function amOpenPause(id, title) {
    _amPauseId = id;
    var msg = document.getElementById('amPauseMsg');
    if (msg) msg.textContent = 'The auction for "' + title + '" will be paused. Bidders will not be able to place new bids until it is resumed.';
    var dlg = document.getElementById('amPauseDialog');
    if (dlg) { dlg.classList.remove('hidden'); dlg.setAttribute('aria-hidden', 'false'); }
}
function amClosePause() {
    var dlg = document.getElementById('amPauseDialog');
    if (dlg) { dlg.classList.add('hidden'); dlg.setAttribute('aria-hidden', 'true'); }
    _amPauseId = null;
}
document.getElementById('amPauseConfirmBtn').addEventListener('click', function () {
    if (_amPauseId) {
        var form = document.getElementById('am-pause-form-' + _amPauseId);
        if (form) form.submit();
    }
});

// ── Remove bid dialog ─────────────────────────────────────────────────────────
var _amBlRemoveId = null;
function amBlOpenRemove(bidId, bidder, amount) {
    _amBlRemoveId = bidId;
    var msg = document.getElementById('amBlRemoveMsg');
    if (msg) msg.textContent = 'Remove the $' + amount + ' bid placed by "' + bidder + '"? The bid will be marked as removed.';
    var dlg = document.getElementById('amBlRemoveDialog');
    if (dlg) { dlg.classList.remove('hidden'); dlg.setAttribute('aria-hidden', 'false'); }
}
function amBlCloseRemove() {
    var dlg = document.getElementById('amBlRemoveDialog');
    if (dlg) { dlg.classList.add('hidden'); dlg.setAttribute('aria-hidden', 'true'); }
    _amBlRemoveId = null;
}
document.getElementById('amBlRemoveConfirmBtn').addEventListener('click', function () {
    if (_amBlRemoveId) {
        var form = document.getElementById('am-bl-remove-form-' + _amBlRemoveId);
        if (form) form.submit();
    }
});

// ── Auto-scroll to bidding panel ──────────────────────────────────────────────
@if($biddingLogs !== null)
document.addEventListener('DOMContentLoaded', function () {
    var panel = document.getElementById('am-bidding-panel');
    if (panel) {
        setTimeout(function () {
            panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }, 150);
    }
});
@endif
</script>
@endsection
