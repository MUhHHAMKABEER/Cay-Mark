@extends('layouts.admin')

@section('title', 'Active Auctions - Admin')

@section('content')
@php
    $listingImageUrl = function ($path) {
        $path = trim((string) ($path ?? ''));
        if ($path === '' || str_starts_with($path, 'http')) return $path ?: asset('images/placeholder-product.png');
        $p = ltrim(str_replace('\\', '/', $path), '/');
        return str_starts_with($p, 'uploads/') ? asset($p) : asset('uploads/listings/' . $p);
    };
    $noBids = ($listingStats['total_active'] ?? 0) - ($listingStats['with_bids'] ?? 0);
@endphp

<style>
    :root { --navy:#063466; --navy-light:#e8eef6; --navy-mid:#0d4d8c; }

    /* ── Header ── */
    .aa-header {
        background:#fff; border-radius:12px;
        padding:1.5rem 1.75rem; margin-bottom:1.5rem;
        border-left:4px solid var(--navy);
        box-shadow:0 1px 4px rgba(6,52,102,0.07);
    }
    .aa-header h1 { font-size:1.35rem; font-weight:700; color:var(--navy); margin:0 0 0.2rem; }
    .aa-header p  { margin:0; color:#64748b; font-size:0.875rem; }

    /* ── Stat cards ── */
    .aa-stats { display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:1rem; margin-bottom:1.5rem; }
    .aa-stat-card {
        background:#fff; border-radius:12px;
        padding:1.25rem 1.5rem;
        box-shadow:0 1px 4px rgba(6,52,102,0.07);
        display:flex; align-items:center; gap:1rem;
    }
    .aa-stat-icon {
        width:44px; height:44px; border-radius:10px;
        display:flex; align-items:center; justify-content:center; flex-shrink:0;
    }
    .aa-stat-icon .material-icons-round { font-size:22px; }
    .aa-stat-label { font-size:0.75rem; font-weight:600; color:#64748b; margin-bottom:2px; }
    .aa-stat-value { font-size:1.5rem; font-weight:700; line-height:1; }

    /* ── Filter bar ── */
    .aa-filter-bar {
        background:#fff; border-radius:12px;
        padding:1rem 1.25rem; margin-bottom:1.5rem;
        box-shadow:0 1px 4px rgba(6,52,102,0.07);
        display:flex; flex-wrap:wrap; gap:0.75rem; align-items:center;
    }
    .aa-filter-bar input[type=text] {
        flex:1; min-width:240px;
        padding:0.5rem 0.875rem;
        border:1.5px solid #e2e8f0; border-radius:8px;
        font-size:0.875rem; color:#374151;
        outline:none; transition:border-color 0.2s;
    }
    .aa-filter-bar input[type=text]:focus { border-color:var(--navy); }
    .aa-filter-btn {
        padding:0.5rem 1.25rem; border-radius:8px;
        font-size:0.875rem; font-weight:600; border:none; cursor:pointer;
        display:inline-flex; align-items:center; gap:6px;
        transition:background 0.2s; text-decoration:none;
    }
    .aa-filter-btn--primary { background:var(--navy); color:#fff; }
    .aa-filter-btn--primary:hover { background:var(--navy-mid); }
    .aa-filter-btn--clear { background:#f1f5f9; color:#475569; }
    .aa-filter-btn--clear:hover { background:#e2e8f0; }
    .aa-filter-btn .material-icons-round { font-size:16px; }

    /* ── Table card ── */
    .aa-card {
        background:#fff; border-radius:12px;
        box-shadow:0 1px 4px rgba(6,52,102,0.07); overflow:hidden;
    }
    .aa-card-header {
        padding:1rem 1.5rem; border-bottom:1px solid #f1f5f9;
        display:flex; align-items:center; justify-content:space-between;
    }
    .aa-card-header h2 { font-size:0.9375rem; font-weight:700; color:#0f172a; margin:0; display:flex; align-items:center; gap:6px; }
    .aa-card-header h2 .material-icons-round { font-size:18px; color:var(--navy); }
    .aa-count { font-size:0.75rem; font-weight:600; color:var(--navy); background:var(--navy-light); padding:2px 10px; border-radius:999px; }

    .aa-table { width:100%; border-collapse:collapse; }
    .aa-table thead th {
        padding:0.75rem 1.25rem; text-align:left;
        font-size:0.6875rem; font-weight:700; text-transform:uppercase;
        letter-spacing:0.06em; color:#64748b; background:#f8fafc;
        border-bottom:1px solid #f1f5f9; white-space:nowrap;
    }
    .aa-table tbody tr { border-bottom:1px solid #f8fafc; transition:background 0.1s; }
    .aa-table tbody tr:last-child { border-bottom:none; }
    .aa-table tbody tr:hover { background:#fafbfc; }
    .aa-table tbody td { padding:0.875rem 1.25rem; font-size:0.875rem; color:#374151; vertical-align:middle; }

    /* ── Thumbnail ── */
    .aa-thumb {
        position:relative; width:68px; height:52px; flex-shrink:0;
        border-radius:10px; overflow:hidden; cursor:pointer;
        border:1px solid #e2e8f0; background:#f8fafc;
        box-shadow:0 1px 2px rgba(15,23,42,0.06);
    }
    .aa-thumb img { width:100%; height:100%; object-fit:cover; display:block; }
    .aa-thumb-overlay {
        position:absolute; inset:0;
        display:flex; align-items:center; justify-content:center;
        background:rgba(6,52,102,0); transition:background 0.2s;
    }
    .aa-thumb:hover .aa-thumb-overlay { background:rgba(6,52,102,0.45); }
    .aa-thumb-overlay .material-icons-round { font-size:20px; color:#fff; opacity:0; transition:opacity 0.2s; }
    .aa-thumb:hover .aa-thumb-overlay .material-icons-round { opacity:1; }
    .aa-thumb-btn { display:block; width:100%; height:100%; border:none; background:transparent; padding:0; cursor:pointer; }
    .aa-thumb-none {
        width:68px; height:52px; border-radius:10px;
        background:#f1f5f9; display:flex; align-items:center; justify-content:center; flex-shrink:0;
    }
    .aa-thumb-none .material-icons-round { font-size:22px; color:#cbd5e1; }

    /* ── Cell helpers ── */
    .aa-item-num  { font-size:0.75rem; font-weight:700; color:var(--navy); font-family:monospace; }
    .aa-item-id   { font-size:0.7rem; color:#94a3b8; margin-top:2px; }
    .aa-veh-name  { font-weight:600; color:#0f172a; }
    .aa-veh-sub   { font-size:0.72rem; color:#94a3b8; margin-top:2px; }
    .aa-sel-name  { font-weight:600; color:#0f172a; }
    .aa-sel-email { font-size:0.72rem; color:#94a3b8; }
    .aa-bid-val   { font-weight:700; color:#0f172a; }
    .aa-bid-sub   { font-size:0.72rem; margin-top:2px; }
    .aa-time      { color:#374151; }
    .aa-time-rel  { font-size:0.72rem; color:#94a3b8; margin-top:2px; }

    /* ── Badges ── */
    .aa-badge {
        display:inline-flex; align-items:center; gap:4px;
        font-size:0.72rem; font-weight:700; padding:3px 9px;
        border-radius:999px; white-space:nowrap;
    }
    .aa-badge--ending { background:#fef3c7; color:#92400e; }
    .aa-badge--bids   { background:#dcfce7; color:#15803d; }
    .aa-badge--nobids { background:#f1f5f9; color:#64748b; }

    /* ── Bid count pill ── */
    .aa-bid-count {
        display:inline-flex; align-items:center; justify-content:center;
        min-width:28px; padding:2px 8px;
        border-radius:999px; font-size:0.75rem; font-weight:700;
    }

    /* ── Action buttons ── */
    .aa-btn {
        display:inline-flex; align-items:center; justify-content:center;
        width:32px; height:32px; border-radius:8px;
        border:none; cursor:pointer; transition:background 0.2s;
        text-decoration:none; flex-shrink:0;
    }
    .aa-btn .material-icons-round { font-size:17px; }
    .aa-btn--view   { background:var(--navy-light); color:var(--navy); }
    .aa-btn--view:hover   { background:#cddaf0; }
    .aa-btn--extend { background:#fef3c7; color:#92400e; }
    .aa-btn--extend:hover { background:#fde68a; }
    .aa-btn--delete { background:#fee2e2; color:#dc2626; }
    .aa-btn--delete:hover { background:#fecaca; }

    /* ── Empty state ── */
    .aa-empty { text-align:center; padding:3.5rem 1rem; color:#94a3b8; }
    .aa-empty .material-icons-round { font-size:48px; display:block; margin-bottom:0.75rem; opacity:0.35; }
    .aa-empty p { margin:0; font-size:0.9375rem; }
    .aa-pagination { padding:1rem 1.25rem; border-top:1px solid #f1f5f9; }

    /* ── Image modal ── */
    .aa-modal {
        position:fixed; inset:0;
        display:none; align-items:center; justify-content:center;
        padding:1.5rem; background:rgba(15,23,42,0.5);
        backdrop-filter:blur(3px); z-index:9999;
    }
    .aa-modal.is-open { display:flex; }
    .aa-modal-img-card {
        position:relative; max-width:min(860px,95vw);
        background:#000; border-radius:14px; overflow:hidden;
        box-shadow:0 24px 60px rgba(15,23,42,0.4);
    }
    .aa-modal-img-card img { display:block; max-width:100%; max-height:85vh; object-fit:contain; }
    .aa-modal-img-footer { background:#fff; padding:0.875rem 1.125rem; display:flex; align-items:center; justify-content:space-between; gap:1rem; }
    .aa-modal-img-footer h3 { font-size:0.9375rem; font-weight:700; color:#0f172a; margin:0; }
    .aa-modal-img-footer p  { margin:0; font-size:0.8125rem; color:#64748b; }
    .aa-modal-close {
        position:absolute; top:0.75rem; right:0.75rem;
        width:36px; height:36px; border:none; border-radius:8px;
        background:rgba(15,23,42,0.72); color:#fff;
        cursor:pointer; display:flex; align-items:center; justify-content:center;
        z-index:2; transition:background 0.2s;
    }
    .aa-modal-close:hover { background:rgba(15,23,42,0.9); }
    .aa-modal-close .material-icons-round { font-size:18px; }

    /* ── Confirm / Extend modals ── */
    .aa-dialog {
        position:fixed; inset:0; z-index:9999;
        display:flex; align-items:center; justify-content:center; padding:1.5rem;
    }
    .aa-dialog.hidden { display:none !important; }
    .aa-dialog-backdrop { position:absolute; inset:0; background:rgba(15,23,42,0.5); backdrop-filter:blur(4px); }
    .aa-dialog-card {
        position:relative; width:100%; max-width:420px;
        background:#fff; border-radius:16px;
        box-shadow:0 25px 50px -12px rgba(0,0,0,0.25); padding:1.75rem;
    }
    .aa-dialog-icon { width:52px; height:52px; border-radius:12px; display:flex; align-items:center; justify-content:center; margin-bottom:1rem; }
    .aa-dialog-icon .material-icons-round { font-size:26px; }
    .aa-dialog-title { font-size:1.125rem; font-weight:700; color:#0f172a; margin:0 0 0.4rem; }
    .aa-dialog-msg   { font-size:0.9rem; color:#64748b; line-height:1.55; margin:0 0 1.25rem; }
    .aa-dialog-actions { display:flex; gap:0.75rem; justify-content:flex-end; }
    .aa-dialog-btn {
        padding:0.6rem 1.25rem; font-size:0.9rem; font-weight:700;
        border-radius:9px; border:none; cursor:pointer; transition:background 0.2s;
    }
    .aa-dialog-btn--cancel  { background:#f1f5f9; color:#475569; }
    .aa-dialog-btn--cancel:hover  { background:#e2e8f0; }
    .aa-dialog-btn--danger  { background:#dc2626; color:#fff; }
    .aa-dialog-btn--danger:hover  { background:#b91c1c; }
    .aa-dialog-btn--primary { background:var(--navy); color:#fff; }
    .aa-dialog-btn--primary:hover { background:var(--navy-mid); }
    .aa-extend-input {
        width:100%; padding:0.5rem 0.75rem;
        border:1.5px solid #e2e8f0; border-radius:8px;
        font-size:0.9375rem; color:#374151; outline:none;
        transition:border-color 0.2s; margin-bottom:0.5rem;
    }
    .aa-extend-input:focus { border-color:var(--navy); }
    .aa-extend-label { display:block; font-size:0.8125rem; font-weight:600; color:#374151; margin-bottom:5px; }
    .aa-extend-hint  { font-size:0.75rem; color:#94a3b8; margin:0 0 1.25rem; }
</style>

<div>
    {{-- ── Page header ── --}}
    <div class="aa-header">
        <h1>
            <span class="material-icons-round" style="font-size:1.25rem;vertical-align:-3px;margin-right:6px">gavel</span>
            Active Auctions
        </h1>
        <p>All live auction listings sorted by soonest ending — take action before time runs out</p>
    </div>

    {{-- ── Stat cards ── --}}
    <div class="aa-stats">
        <div class="aa-stat-card">
            <div class="aa-stat-icon" style="background:var(--navy-light)">
                <span class="material-icons-round" style="color:var(--navy)">inventory_2</span>
            </div>
            <div>
                <div class="aa-stat-label">Total Active</div>
                <div class="aa-stat-value" style="color:var(--navy)">{{ $listingStats['total_active'] ?? 0 }}</div>
            </div>
        </div>
        <div class="aa-stat-card">
            <div class="aa-stat-icon" style="background:#dcfce7">
                <span class="material-icons-round" style="color:#16a34a">price_check</span>
            </div>
            <div>
                <div class="aa-stat-label">With Bids</div>
                <div class="aa-stat-value" style="color:#16a34a">{{ $listingStats['with_bids'] ?? 0 }}</div>
            </div>
        </div>
        <div class="aa-stat-card">
            <div class="aa-stat-icon" style="background:#f1f5f9">
                <span class="material-icons-round" style="color:#64748b">remove_shopping_cart</span>
            </div>
            <div>
                <div class="aa-stat-label">No Bids Yet</div>
                <div class="aa-stat-value" style="color:#64748b">{{ max(0, $noBids) }}</div>
            </div>
        </div>
        <div class="aa-stat-card">
            <div class="aa-stat-icon" style="background:#fef3c7">
                <span class="material-icons-round" style="color:#d97706">timer</span>
            </div>
            <div>
                <div class="aa-stat-label">Ending Soon</div>
                <div class="aa-stat-value" style="color:#d97706">{{ $listingStats['ending_soon'] ?? 0 }}</div>
                <div style="font-size:0.7rem;color:#94a3b8;margin-top:1px">Within 24 h</div>
            </div>
        </div>
    </div>

    {{-- ── Search filter ── --}}
    <div class="aa-filter-bar">
        <input type="text"
            id="aaSearchInput"
            placeholder="Search by item number, make, model or seller…"
            value="">
        <button type="button" class="aa-filter-btn aa-filter-btn--primary" onclick="aaDoFilter()">
            <span class="material-icons-round">search</span> Search
        </button>
        <button type="button" class="aa-filter-btn aa-filter-btn--clear" onclick="aaClearFilter()">
            <span class="material-icons-round">close</span> Clear
        </button>
    </div>

    {{-- ── Active auctions table ── --}}
    <div class="aa-card">
        <div class="aa-card-header">
            <h2>
                <span class="material-icons-round">gavel</span>
                Live Auctions
            </h2>
            <span class="aa-count">{{ $activeListings->total() }} {{ Str::plural('auction', $activeListings->total()) }}</span>
        </div>
        <div style="overflow-x:auto">
            <table class="aa-table">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Vehicle</th>
                        <th>Seller</th>
                        <th>Current Bid</th>
                        <th>Ends</th>
                        <th style="text-align:center">Bids</th>
                        <th style="text-align:center">Actions</th>
                    </tr>
                </thead>
                <tbody id="aa-tbody">
                    @forelse($activeListings as $listing)
                    @php
                        $mainImg    = $listing->images->first();
                        $imgUrl     = $mainImg ? $listingImageUrl($mainImg->image_path) : null;
                        $vehicle    = trim(($listing->year ?? '') . ' ' . ($listing->make ?? '') . ' ' . ($listing->model ?? ''));
                        $bidCount   = $listing->bids->count();
                        $highestBid = $listing->bids->max('amount') ?? $listing->starting_price ?? 0;
                        $endTime    = $listing->auction_end_time
                            ? \Carbon\Carbon::parse($listing->auction_end_time)
                            : ($listing->auction_start_time && $listing->auction_duration
                                ? \Carbon\Carbon::parse($listing->auction_start_time)->addDays((int)$listing->auction_duration)
                                : null);
                        $hoursLeft  = $endTime ? now()->diffInHours($endTime, false) : null;
                        $endingSoon = $hoursLeft !== null && $hoursLeft > 0 && $hoursLeft <= 24;
                        $searchStr  = strtolower(
                            ($listing->item_number ?? '') . ' ' . ($listing->id ?? '') . ' ' .
                            ($listing->year ?? '') . ' ' . ($listing->make ?? '') . ' ' .
                            ($listing->model ?? '') . ' ' . ($listing->subcategory ?? '') . ' ' .
                            ($listing->seller->name ?? '') . ' ' . ($listing->seller->email ?? '')
                        );
                    @endphp
                    <tr data-aa-search="{{ $searchStr }}">
                        <td>
                            <div class="aa-item-num">{{ $listing->item_number ?? 'N/A' }}</div>
                            <div class="aa-item-id">#{{ $listing->id }}</div>
                        </td>
                        <td>
                            <div style="display:flex;align-items:center;gap:10px">
                                @if($imgUrl)
                                <div class="aa-thumb">
                                    <button type="button" class="aa-thumb-btn js-aa-img"
                                        data-image="{{ $imgUrl }}"
                                        data-title="{{ $vehicle ?: 'Listing #'.$listing->id }}"
                                        data-meta="Item {{ $listing->item_number ?? '#'.$listing->id }}">
                                        <img src="{{ $imgUrl }}" alt="{{ $vehicle }}">
                                        <div class="aa-thumb-overlay"><span class="material-icons-round">zoom_in</span></div>
                                    </button>
                                </div>
                                @else
                                <div class="aa-thumb-none"><span class="material-icons-round">directions_car</span></div>
                                @endif
                                <div>
                                    <div class="aa-veh-name">{{ $vehicle ?: '—' }}</div>
                                    @if($listing->subcategory)
                                    <div class="aa-veh-sub">{{ $listing->subcategory }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="aa-sel-name">{{ $listing->seller->name ?? '—' }}</div>
                            <div class="aa-sel-email">{{ $listing->seller->email ?? '' }}</div>
                        </td>
                        <td>
                            <div class="aa-bid-val">${{ number_format($highestBid, 2) }}</div>
                            @if($bidCount === 0)
                            <div class="aa-bid-sub" style="color:#94a3b8">Start: ${{ number_format($listing->starting_price ?? 0, 2) }}</div>
                            @else
                            <div class="aa-bid-sub" style="color:#16a34a">{{ $bidCount }} {{ Str::plural('bid', $bidCount) }}</div>
                            @endif
                        </td>
                        <td>
                            @if($endTime)
                            <div class="aa-time" style="{{ $endingSoon ? 'color:#d97706;font-weight:600' : '' }}">
                                {{ $endTime->format('M j, Y') }}
                                <span style="font-size:0.72rem;color:#94a3b8;display:block">{{ $endTime->format('g:i A') }}</span>
                            </div>
                            <div class="aa-time-rel" style="{{ $endingSoon ? 'color:#d97706' : '' }}">
                                {{ $endTime->diffForHumans() }}
                            </div>
                            @if($endingSoon)
                            <span class="aa-badge aa-badge--ending" style="margin-top:4px">
                                <span class="material-icons-round" style="font-size:11px">timer</span>
                                Ending Soon
                            </span>
                            @endif
                            @else
                            <span style="color:#cbd5e1">—</span>
                            @endif
                        </td>
                        <td style="text-align:center">
                            <span class="aa-bid-count {{ $bidCount > 0 ? 'aa-badge--bids' : 'aa-badge--nobids' }}">
                                {{ $bidCount }}
                            </span>
                        </td>
                        <td>
                            <div style="display:flex;align-items:center;justify-content:center;gap:6px">
                                <a href="{{ route('admin.listings.approval-detail', $listing->id) }}"
                                    class="aa-btn aa-btn--view" title="View details">
                                    <span class="material-icons-round">visibility</span>
                                </a>
                                <button type="button"
                                    class="aa-btn aa-btn--extend" title="Extend auction"
                                    onclick="aaOpenExtend({{ $listing->id }}, '{{ addslashes($vehicle ?: '#'.$listing->id) }}')">
                                    <span class="material-icons-round">more_time</span>
                                </button>
                                <button type="button"
                                    class="aa-btn aa-btn--delete" title="Delete listing"
                                    onclick="aaOpenDelete({{ $listing->id }}, '{{ addslashes($vehicle ?: '#'.$listing->id) }}')">
                                    <span class="material-icons-round">delete</span>
                                </button>
                            </div>
                            {{-- Hidden delete form ── submitted by the dialog confirm button --}}
                            <form id="aa-delete-form-{{ $listing->id }}" method="POST"
                                action="{{ route('admin.listings.delete', $listing->id) }}" style="display:none">
                                @csrf
                                @method('DELETE')
                            </form>
                            {{-- Hidden extend form ── submitted by the dialog confirm button --}}
                            <form id="aa-extend-form-{{ $listing->id }}" method="POST"
                                action="{{ route('admin.listings.extend-auction', $listing->id) }}" style="display:none">
                                @csrf
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr id="aa-empty-row">
                        <td colspan="7">
                            <div class="aa-empty">
                                <span class="material-icons-round">gavel</span>
                                <p>No active auctions right now</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                    {{-- JS filter "no match" row ── hidden until filter runs --}}
                    @if($activeListings->isNotEmpty())
                    <tr id="aa-no-match-row" style="display:none">
                        <td colspan="7">
                            <div class="aa-empty">
                                <span class="material-icons-round">search_off</span>
                                <p>No auctions match your search</p>
                            </div>
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
        @if($activeListings->hasPages())
        <div class="aa-pagination">{{ $activeListings->links() }}</div>
        @endif
    </div>
</div>

{{-- ── Image preview modal ── --}}
<div id="aaImgModal" class="aa-modal" aria-hidden="true">
    <div class="aa-modal-img-card" id="aaImgCard" onclick="event.stopPropagation()">
        <button type="button" class="aa-modal-close" onclick="aaCloseImg()">
            <span class="material-icons-round">close</span>
        </button>
        <img id="aaImgModalSrc" src="" alt="Listing photo">
        <div class="aa-modal-img-footer">
            <div>
                <h3 id="aaImgModalTitle"></h3>
                <p id="aaImgModalMeta"></p>
            </div>
            <span style="font-size:0.75rem;color:#94a3b8;white-space:nowrap">Click outside to close</span>
        </div>
    </div>
</div>

{{-- ── Delete confirmation dialog ── --}}
<div id="aaDeleteDialog" class="aa-dialog hidden" aria-hidden="true">
    <div class="aa-dialog-backdrop" onclick="aaCloseDelete()"></div>
    <div class="aa-dialog-card">
        <div class="aa-dialog-icon" style="background:#fee2e2">
            <span class="material-icons-round" style="color:#dc2626">delete_forever</span>
        </div>
        <h3 class="aa-dialog-title">Delete this listing?</h3>
        <p class="aa-dialog-msg" id="aaDeleteMsg">This action cannot be undone. The listing and all associated bids will be permanently removed.</p>
        <div class="aa-dialog-actions">
            <button type="button" class="aa-dialog-btn aa-dialog-btn--cancel" onclick="aaCloseDelete()">Cancel</button>
            <button type="button" class="aa-dialog-btn aa-dialog-btn--danger" id="aaDeleteConfirmBtn">Delete</button>
        </div>
    </div>
</div>

{{-- ── Extend auction dialog ── --}}
<div id="aaExtendDialog" class="aa-dialog hidden" aria-hidden="true">
    <div class="aa-dialog-backdrop" onclick="aaCloseExtend()"></div>
    <div class="aa-dialog-card">
        <div class="aa-dialog-icon" style="background:#fef3c7">
            <span class="material-icons-round" style="color:#d97706">more_time</span>
        </div>
        <h3 class="aa-dialog-title">Extend Auction</h3>
        <p class="aa-dialog-msg" id="aaExtendMsg">Enter how many hours to add to this auction's end time.</p>
        <label class="aa-extend-label">Additional Hours</label>
        <input type="number" id="aaExtendHours" class="aa-extend-input" min="1" max="168" value="24">
        <p class="aa-extend-hint">Max 168 hours (7 days)</p>
        <div class="aa-dialog-actions">
            <button type="button" class="aa-dialog-btn aa-dialog-btn--cancel" onclick="aaCloseExtend()">Cancel</button>
            <button type="button" class="aa-dialog-btn aa-dialog-btn--primary" id="aaExtendConfirmBtn">Extend</button>
        </div>
    </div>
</div>

<script>
// ── Image modal ──────────────────────────────────────────────────────────────
(function() {
    var modal     = document.getElementById('aaImgModal');
    var modalCard = document.getElementById('aaImgCard');
    var imgEl     = document.getElementById('aaImgModalSrc');
    var titleEl   = document.getElementById('aaImgModalTitle');
    var metaEl    = document.getElementById('aaImgModalMeta');
    if (!modal) return;

    document.querySelectorAll('.js-aa-img').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            imgEl.src            = btn.dataset.image || '';
            titleEl.textContent  = btn.dataset.title || '';
            metaEl.textContent   = btn.dataset.meta  || '';
            modal.classList.add('is-open');
            modal.setAttribute('aria-hidden', 'false');
        });
    });
    modal.addEventListener('click', function(e) {
        if (!modalCard.contains(e.target)) aaCloseImg();
    });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') { aaCloseImg(); aaCloseDelete(); aaCloseExtend(); }
    });
})();

function aaCloseImg() {
    var modal = document.getElementById('aaImgModal');
    modal.classList.remove('is-open');
    modal.setAttribute('aria-hidden', 'true');
    document.getElementById('aaImgModalSrc').src = '';
}

// ── Delete dialog ────────────────────────────────────────────────────────────
var _aaDeleteId = null;
function aaOpenDelete(id, title) {
    _aaDeleteId = id;
    document.getElementById('aaDeleteMsg').textContent =
        'Permanently delete "' + title + '"? This cannot be undone.';
    document.getElementById('aaDeleteConfirmBtn').onclick = function() {
        document.getElementById('aa-delete-form-' + id).submit();
    };
    document.getElementById('aaDeleteDialog').classList.remove('hidden');
}
function aaCloseDelete() {
    document.getElementById('aaDeleteDialog').classList.add('hidden');
    _aaDeleteId = null;
}

// ── Extend dialog ────────────────────────────────────────────────────────────
var _aaExtendId = null;
function aaOpenExtend(id, title) {
    _aaExtendId = id;
    document.getElementById('aaExtendMsg').textContent =
        'Adding hours to "' + title + '".';
    document.getElementById('aaExtendHours').value = 24;
    document.getElementById('aaExtendConfirmBtn').onclick = function() {
        var hours = parseInt(document.getElementById('aaExtendHours').value, 10);
        if (!hours || hours < 1 || hours > 168) {
            alert('Please enter a value between 1 and 168 hours.');
            return;
        }
        // Inject hours into the hidden form and submit
        var form = document.getElementById('aa-extend-form-' + id);
        var inp  = form.querySelector('input[name="hours"]');
        if (!inp) { inp = document.createElement('input'); inp.type = 'hidden'; inp.name = 'hours'; form.appendChild(inp); }
        inp.value = hours;
        form.submit();
    };
    document.getElementById('aaExtendDialog').classList.remove('hidden');
}
function aaCloseExtend() {
    document.getElementById('aaExtendDialog').classList.add('hidden');
    _aaExtendId = null;
}

// ── Client-side search filter ────────────────────────────────────────────────
function aaDoFilter() {
    var q     = document.getElementById('aaSearchInput').value.trim().toLowerCase();
    var rows  = document.querySelectorAll('#aa-tbody tr[data-aa-search]');
    var shown = 0;
    rows.forEach(function(row) {
        var match = !q || row.dataset.aaSearch.indexOf(q) !== -1;
        row.style.display = match ? '' : 'none';
        if (match) shown++;
    });
    var noMatch = document.getElementById('aa-no-match-row');
    if (noMatch) noMatch.style.display = (shown === 0 && q) ? '' : 'none';
}
function aaClearFilter() {
    document.getElementById('aaSearchInput').value = '';
    aaDoFilter();
}
document.getElementById('aaSearchInput').addEventListener('input', aaDoFilter);
document.getElementById('aaSearchInput').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') aaDoFilter();
});
</script>
@endsection
