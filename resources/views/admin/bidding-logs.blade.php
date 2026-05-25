@extends('layouts.admin')

@section('title', 'Bidding Logs - Admin')

@section('content')
@php
    $vehicle    = trim(($listing->year ?? '') . ' ' . ($listing->make ?? '') . ' ' . ($listing->model ?? ''));
    $highestBid = $bids->where('status', '!=', 'removed')->max('amount') ?? 0;
    $activeBids = $bids->where('status', 'active')->count();
    $removedBids= $bids->where('status', 'removed')->count();
    $uniqueBidders = $bids->pluck('user_id')->unique()->count();
@endphp

<style>
    :root { --navy:#063466; --navy-light:#e8eef6; --navy-mid:#0d4d8c; }

    /* ── Header ── */
    .bl-header {
        background:#fff; border-radius:12px;
        padding:1.25rem 1.5rem; margin-bottom:1.25rem;
        border-left:4px solid var(--navy);
        box-shadow:0 1px 4px rgba(6,52,102,0.07);
        display:flex; align-items:center; justify-content:space-between;
        flex-wrap:wrap; gap:0.75rem;
    }
    .bl-header h1 { font-size:1.2rem; font-weight:700; color:var(--navy); margin:0 0 0.2rem; }
    .bl-header p  { margin:0; color:#64748b; font-size:0.8125rem; }
    .bl-back {
        display:inline-flex; align-items:center; gap:5px;
        padding:0.5rem 1rem; background:var(--navy); color:#fff;
        border-radius:8px; font-size:0.8125rem; font-weight:600;
        text-decoration:none; transition:background 0.2s;
    }
    .bl-back:hover { background:var(--navy-mid); }
    .bl-back .material-icons-round { font-size:16px; }

    /* ── Stats strip ── */
    .bl-stats { display:grid; grid-template-columns:repeat(auto-fit,minmax(155px,1fr)); gap:1rem; margin-bottom:1.25rem; }
    .bl-stat-card {
        background:#fff; border-radius:12px;
        padding:1.1rem 1.25rem;
        box-shadow:0 1px 4px rgba(6,52,102,0.07);
        display:flex; align-items:center; gap:0.875rem;
    }
    .bl-stat-icon { width:40px; height:40px; border-radius:9px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .bl-stat-icon .material-icons-round { font-size:20px; }
    .bl-stat-label { font-size:0.72rem; font-weight:600; color:#64748b; margin-bottom:2px; }
    .bl-stat-value { font-size:1.35rem; font-weight:700; line-height:1; }

    /* ── Listing summary card ── */
    .bl-summary {
        background:#fff; border-radius:12px;
        padding:1.25rem 1.5rem; margin-bottom:1.25rem;
        box-shadow:0 1px 4px rgba(6,52,102,0.07);
        display:flex; flex-wrap:wrap; gap:1.5rem; align-items:flex-start;
    }
    .bl-summary-info { flex:1; min-width:200px; }
    .bl-summary-info h2 { font-size:1rem; font-weight:700; color:#0f172a; margin:0 0 0.5rem; }
    .bl-summary-row { display:flex; gap:0.5rem; flex-wrap:wrap; margin-bottom:0.375rem; }
    .bl-summary-label { font-size:0.75rem; font-weight:700; text-transform:uppercase; letter-spacing:0.04em; color:#94a3b8; min-width:80px; }
    .bl-summary-val   { font-size:0.875rem; font-weight:600; color:#374151; }

    /* ── Alert banner ── */
    .bl-alert {
        background:#fef2f2; border-left:4px solid #dc2626;
        border-radius:10px; padding:1rem 1.25rem; margin-bottom:1.25rem;
    }
    .bl-alert-title { font-size:0.875rem; font-weight:700; color:#b91c1c; margin:0 0 0.5rem; display:flex; align-items:center; gap:6px; }
    .bl-alert-title .material-icons-round { font-size:18px; }
    .bl-alert-item { font-size:0.8125rem; color:#7f1d1d; padding:3px 0; display:flex; align-items:flex-start; gap:6px; }
    .bl-alert-item::before { content:'•'; flex-shrink:0; color:#dc2626; }

    /* ── Table card ── */
    .bl-card { background:#fff; border-radius:12px; box-shadow:0 1px 4px rgba(6,52,102,0.07); overflow:hidden; }
    .bl-card-header {
        padding:1rem 1.5rem; border-bottom:1px solid #f1f5f9;
        display:flex; align-items:center; justify-content:space-between;
    }
    .bl-card-header h2 { font-size:0.9375rem; font-weight:700; color:#0f172a; margin:0; display:flex; align-items:center; gap:6px; }
    .bl-card-header h2 .material-icons-round { font-size:18px; color:var(--navy); }
    .bl-count { font-size:0.75rem; font-weight:600; color:var(--navy); background:var(--navy-light); padding:2px 10px; border-radius:999px; }

    .bl-table { width:100%; border-collapse:collapse; }
    .bl-table thead th {
        padding:0.75rem 1.25rem; text-align:left;
        font-size:0.6875rem; font-weight:700; text-transform:uppercase;
        letter-spacing:0.06em; color:#64748b; background:#f8fafc;
        border-bottom:1px solid #f1f5f9; white-space:nowrap;
    }
    .bl-table tbody tr { border-bottom:1px solid #f8fafc; transition:background 0.1s; }
    .bl-table tbody tr:last-child { border-bottom:none; }
    .bl-table tbody tr:hover { background:#fafbfc; }
    .bl-table tbody tr.bl-row--removed { opacity:0.6; }
    .bl-table tbody td { padding:0.875rem 1.25rem; font-size:0.875rem; color:#374151; vertical-align:middle; }
    .bl-table tbody tr.bl-row--flag { background:#fef9f9; }

    /* ── Cell helpers ── */
    .bl-bid-num  { font-size:0.75rem; font-weight:700; color:var(--navy); font-family:monospace; }
    .bl-rank     { font-size:0.7rem; color:#94a3b8; margin-top:2px; }
    .bl-bidder   { font-weight:600; color:#0f172a; }
    .bl-email    { font-size:0.72rem; color:#94a3b8; }
    .bl-amount   { font-weight:700; font-size:0.9375rem; color:#0f172a; }
    .bl-amount--top { color:#16a34a; }
    .bl-time     { color:#374151; }
    .bl-time-rel { font-size:0.72rem; color:#94a3b8; margin-top:2px; }

    /* ── Status badge ── */
    .bl-badge {
        display:inline-flex; align-items:center; gap:3px;
        font-size:0.72rem; font-weight:700; padding:3px 9px; border-radius:999px;
    }
    .bl-badge--active   { background:#dcfce7; color:#16a34a; }
    .bl-badge--removed  { background:#fee2e2; color:#dc2626; }
    .bl-badge--outbid   { background:#f1f5f9; color:#64748b; }
    .bl-badge--winning  { background:#dbeafe; color:#2563eb; }
    .bl-badge--flag     { background:#fef3c7; color:#92400e; }

    /* ── Remove bid button ── */
    .bl-btn-remove {
        display:inline-flex; align-items:center; gap:4px;
        padding:0.375rem 0.75rem; background:#fee2e2; color:#dc2626;
        border:none; border-radius:7px; font-size:0.8rem; font-weight:600;
        cursor:pointer; transition:background 0.2s; white-space:nowrap;
    }
    .bl-btn-remove:hover { background:#fecaca; }
    .bl-btn-remove .material-icons-round { font-size:14px; }

    /* ── Empty ── */
    .bl-empty { text-align:center; padding:3rem 1rem; color:#94a3b8; }
    .bl-empty .material-icons-round { font-size:44px; display:block; margin-bottom:0.75rem; opacity:0.35; }
    .bl-empty p { margin:0; font-size:0.9375rem; }

    /* ── Remove dialog ── */
    .bl-dialog {
        position:fixed; inset:0; z-index:9999;
        display:flex; align-items:center; justify-content:center; padding:1.5rem;
    }
    .bl-dialog.hidden { display:none !important; }
    .bl-dialog-backdrop { position:absolute; inset:0; background:rgba(15,23,42,0.5); backdrop-filter:blur(4px); }
    .bl-dialog-card {
        position:relative; width:100%; max-width:420px;
        background:#fff; border-radius:16px;
        box-shadow:0 25px 50px -12px rgba(0,0,0,0.25); padding:1.75rem;
    }
    .bl-dialog-icon { width:52px; height:52px; border-radius:12px; display:flex; align-items:center; justify-content:center; margin-bottom:1rem; }
    .bl-dialog-icon .material-icons-round { font-size:26px; }
    .bl-dialog-title { font-size:1.125rem; font-weight:700; color:#0f172a; margin:0 0 0.4rem; }
    .bl-dialog-msg   { font-size:0.9rem; color:#64748b; line-height:1.55; margin:0 0 1.5rem; }
    .bl-dialog-actions { display:flex; gap:0.75rem; justify-content:flex-end; }
    .bl-dialog-btn { padding:0.6rem 1.25rem; font-size:0.9rem; font-weight:700; border-radius:9px; border:none; cursor:pointer; transition:background 0.2s; }
    .bl-dialog-btn--cancel { background:#f1f5f9; color:#475569; }
    .bl-dialog-btn--cancel:hover { background:#e2e8f0; }
    .bl-dialog-btn--danger { background:#dc2626; color:#fff; }
    .bl-dialog-btn--danger:hover { background:#b91c1c; }
</style>

{{-- ── Header ── --}}
<div class="bl-header">
    <div>
        <h1>
            <span class="material-icons-round" style="font-size:1.1rem;vertical-align:-3px;margin-right:5px">format_list_numbered</span>
            Bidding Logs
        </h1>
        <p>
            {{ $vehicle ?: 'Listing #'.$listing->id }}
            · Item {{ $listing->item_number ?? '#'.$listing->id }}
        </p>
    </div>
    <a href="{{ route('admin.active-listings') }}" class="bl-back">
        <span class="material-icons-round">arrow_back</span> Active Auctions
    </a>
</div>

{{-- ── Stat strip ── --}}
<div class="bl-stats">
    <div class="bl-stat-card">
        <div class="bl-stat-icon" style="background:var(--navy-light)">
            <span class="material-icons-round" style="color:var(--navy)">gavel</span>
        </div>
        <div>
            <div class="bl-stat-label">Total Bids</div>
            <div class="bl-stat-value" style="color:var(--navy)">{{ $bids->count() }}</div>
        </div>
    </div>
    <div class="bl-stat-card">
        <div class="bl-stat-icon" style="background:#dcfce7">
            <span class="material-icons-round" style="color:#16a34a">price_check</span>
        </div>
        <div>
            <div class="bl-stat-label">Highest Bid</div>
            <div class="bl-stat-value" style="color:#16a34a">${{ number_format($highestBid, 2) }}</div>
        </div>
    </div>
    <div class="bl-stat-card">
        <div class="bl-stat-icon" style="background:#e0e7ff">
            <span class="material-icons-round" style="color:#4338ca">group</span>
        </div>
        <div>
            <div class="bl-stat-label">Unique Bidders</div>
            <div class="bl-stat-value" style="color:#4338ca">{{ $uniqueBidders }}</div>
        </div>
    </div>
    <div class="bl-stat-card">
        <div class="bl-stat-icon" style="background:#fee2e2">
            <span class="material-icons-round" style="color:#dc2626">remove_circle</span>
        </div>
        <div>
            <div class="bl-stat-label">Removed</div>
            <div class="bl-stat-value" style="color:#dc2626">{{ $removedBids }}</div>
        </div>
    </div>
</div>

{{-- ── Listing summary ── --}}
<div class="bl-summary">
    <div class="bl-summary-info">
        <h2>{{ $vehicle ?: 'Unlisted Vehicle' }}</h2>
        <div class="bl-summary-row">
            <span class="bl-summary-label">Seller</span>
            <span class="bl-summary-val">{{ $listing->seller->name ?? '—' }} · {{ $listing->seller->email ?? '' }}</span>
        </div>
        <div class="bl-summary-row">
            <span class="bl-summary-label">Item #</span>
            <span class="bl-summary-val" style="font-family:monospace">{{ $listing->item_number ?? 'N/A' }}</span>
        </div>
        <div class="bl-summary-row">
            <span class="bl-summary-label">Start Price</span>
            <span class="bl-summary-val">${{ number_format($listing->starting_price ?? 0, 2) }}</span>
        </div>
        <div class="bl-summary-row">
            <span class="bl-summary-label">Ends</span>
            <span class="bl-summary-val">
                @if($listing->auction_end_time)
                    {{ \Carbon\Carbon::parse($listing->auction_end_time)->format('M j, Y g:i A') }}
                    ({{ \Carbon\Carbon::parse($listing->auction_end_time)->diffForHumans() }})
                @else
                    —
                @endif
            </span>
        </div>
    </div>
    <div style="display:flex;gap:0.75rem;flex-wrap:wrap;align-self:flex-start">
        <a href="{{ route('admin.listings.approval-detail', $listing->id) }}"
            style="display:inline-flex;align-items:center;gap:5px;padding:0.5rem 1rem;background:var(--navy-light);color:var(--navy);border-radius:8px;font-size:0.8125rem;font-weight:600;text-decoration:none;transition:background 0.2s">
            <span class="material-icons-round" style="font-size:16px">article</span>
            Full Detail
        </a>
        <form method="POST" action="{{ route('admin.auctions.cancel', $listing->id) }}" style="display:inline"
            onsubmit="return confirm('Cancel this auction? All active bids will be voided.')">
            @csrf
            <button type="submit"
                style="display:inline-flex;align-items:center;gap:5px;padding:0.5rem 1rem;background:#fee2e2;color:#dc2626;border:none;border-radius:8px;font-size:0.8125rem;font-weight:600;cursor:pointer;transition:background 0.2s">
                <span class="material-icons-round" style="font-size:16px">cancel</span>
                Cancel Auction
            </button>
        </form>
    </div>
</div>

{{-- ── Irregular activity alert ── --}}
@if(!empty($irregularActivity))
<div class="bl-alert">
    <div class="bl-alert-title">
        <span class="material-icons-round">warning</span>
        {{ count($irregularActivity) }} Suspicious Bidding {{ Str::plural('Pattern', count($irregularActivity)) }} Detected
    </div>
    @foreach($irregularActivity as $alert)
    <div class="bl-alert-item">{{ $alert['message'] }}</div>
    @endforeach
</div>
@endif

{{-- ── Bidding log table ── --}}
<div class="bl-card">
    <div class="bl-card-header">
        <h2>
            <span class="material-icons-round">format_list_numbered</span>
            Bid History
        </h2>
        <span class="bl-count">{{ $bids->count() }} {{ Str::plural('bid', $bids->count()) }}</span>
    </div>
    <div style="overflow-x:auto">
        <table class="bl-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Bidder</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Placed At</th>
                    <th style="text-align:center">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bids as $index => $bid)
                @php
                    $isTop      = $index === 0 && $bid->status !== 'removed';
                    $isRemoved  = $bid->status === 'removed';
                    $isFlag     = collect($irregularActivity)->contains('bid_id', $bid->id);
                    $rowClass   = $isRemoved ? 'bl-row--removed' : ($isFlag ? 'bl-row--flag' : '');
                @endphp
                <tr class="{{ $rowClass }}">
                    <td>
                        <div class="bl-bid-num">#{{ $bid->id }}</div>
                        <div class="bl-rank">Rank {{ $index + 1 }}</div>
                    </td>
                    <td>
                        <div class="bl-bidder">{{ $bid->user->name ?? '—' }}</div>
                        <div class="bl-email">{{ $bid->user->email ?? '' }}</div>
                    </td>
                    <td>
                        <div class="bl-amount {{ $isTop ? 'bl-amount--top' : '' }}">${{ number_format($bid->amount, 2) }}</div>
                        @if($isTop)
                        <div style="font-size:0.72rem;color:#16a34a;margin-top:2px;font-weight:600">↑ Highest Bid</div>
                        @endif
                    </td>
                    <td>
                        @if($isFlag)
                            <span class="bl-badge bl-badge--flag">
                                <span class="material-icons-round" style="font-size:11px">warning</span>
                                Flagged
                            </span>
                        @elseif($isTop)
                            <span class="bl-badge bl-badge--winning">
                                <span class="material-icons-round" style="font-size:11px">emoji_events</span>
                                Winning
                            </span>
                        @elseif($isRemoved)
                            <span class="bl-badge bl-badge--removed">Removed</span>
                        @elseif($bid->status === 'active')
                            <span class="bl-badge bl-badge--outbid">Outbid</span>
                        @else
                            <span class="bl-badge bl-badge--outbid">{{ ucfirst($bid->status ?? 'active') }}</span>
                        @endif
                    </td>
                    <td>
                        <div class="bl-time">{{ $bid->created_at->format('M j, Y') }}</div>
                        <div class="bl-time-rel">
                            {{ $bid->created_at->format('g:i A') }}
                            · {{ $bid->created_at->diffForHumans() }}
                        </div>
                    </td>
                    <td style="text-align:center">
                        @if(!$isRemoved)
                        <button type="button" class="bl-btn-remove"
                            onclick="blOpenRemove({{ $bid->id }}, '{{ addslashes($bid->user->name ?? 'bidder') }}', '{{ number_format($bid->amount, 2) }}')">
                            <span class="material-icons-round">remove_circle</span>
                            Remove
                        </button>
                        <form id="bl-remove-form-{{ $bid->id }}" method="POST"
                            action="{{ route('admin.bids.remove', $bid->id) }}" style="display:none">
                            @csrf
                        </form>
                        @else
                        <span style="font-size:0.75rem;color:#94a3b8">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div class="bl-empty">
                            <span class="material-icons-round">gavel</span>
                            <p>No bids placed yet on this auction</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ── Remove bid confirmation dialog ── --}}
<div id="blRemoveDialog" class="bl-dialog hidden" aria-hidden="true">
    <div class="bl-dialog-backdrop" onclick="blCloseRemove()"></div>
    <div class="bl-dialog-card">
        <div class="bl-dialog-icon" style="background:#fee2e2">
            <span class="material-icons-round" style="color:#dc2626">remove_circle</span>
        </div>
        <h3 class="bl-dialog-title">Remove this bid?</h3>
        <p class="bl-dialog-msg" id="blRemoveMsg">This will mark the bid as removed and recalculate the auction standings.</p>
        <div class="bl-dialog-actions">
            <button type="button" class="bl-dialog-btn bl-dialog-btn--cancel" onclick="blCloseRemove()">Cancel</button>
            <button type="button" class="bl-dialog-btn bl-dialog-btn--danger" id="blRemoveConfirmBtn">Remove Bid</button>
        </div>
    </div>
</div>

<script>
    function blOpenRemove(bidId, bidder, amount) {
        document.getElementById('blRemoveMsg').textContent =
            'Remove bid of $' + amount + ' placed by ' + bidder + '? This action updates auction standings.';
        document.getElementById('blRemoveConfirmBtn').onclick = function () {
            document.getElementById('bl-remove-form-' + bidId).submit();
        };
        document.getElementById('blRemoveDialog').classList.remove('hidden');
    }
    function blCloseRemove() {
        document.getElementById('blRemoveDialog').classList.add('hidden');
    }
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') blCloseRemove();
    });
</script>
@endsection
