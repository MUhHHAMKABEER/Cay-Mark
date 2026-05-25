@extends('layouts.admin')

@section('title', 'Listing Review - Admin')

@section('content')
@php
    $resolveListingImageUrl = function ($imagePath) {
        $path = trim((string) $imagePath);
        if ($path === '') return asset('images/placeholder-product.png');
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) return $path;
        $normalized = ltrim(str_replace('\\', '/', $path), '/');
        if (str_starts_with($normalized, 'storage/') || str_starts_with($normalized, 'uploads/')) return asset($normalized);
        return asset('uploads/listings/' . $normalized);
    };
@endphp

<style>
    :root { --navy:#063466; --navy-light:#e8eef6; --navy-mid:#0d4d8c; }

    /* ── Page header ── */
    .lr-header {
        background:#fff;
        border-radius:12px;
        padding:1.5rem 1.75rem;
        margin-bottom:1.5rem;
        border-left:4px solid var(--navy);
        box-shadow:0 1px 4px rgba(6,52,102,0.07);
    }
    .lr-header h1 { font-size:1.35rem; font-weight:700; color:var(--navy); margin:0 0 0.2rem; }
    .lr-header p  { margin:0; color:#64748b; font-size:0.875rem; }

    /* ── Stat cards ── */
    .lr-stats { display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:1rem; margin-bottom:1.5rem; }
    .lr-stat-card {
        background:#fff; border-radius:12px;
        padding:1.25rem 1.5rem;
        box-shadow:0 1px 4px rgba(6,52,102,0.07);
        display:flex; align-items:center; gap:1rem;
    }
    .lr-stat-icon {
        width:44px; height:44px; border-radius:10px;
        display:flex; align-items:center; justify-content:center; flex-shrink:0;
    }
    .lr-stat-icon .material-icons-round { font-size:22px; }
    .lr-stat-label { font-size:0.75rem; font-weight:600; color:#64748b; margin-bottom:2px; }
    .lr-stat-value { font-size:1.5rem; font-weight:700; line-height:1; color:#0f172a; }

    /* ── Filter bar ── */
    .lr-filter-bar {
        background:#fff; border-radius:12px;
        padding:1rem 1.25rem; margin-bottom:1.5rem;
        box-shadow:0 1px 4px rgba(6,52,102,0.07);
        display:flex; flex-wrap:wrap; gap:0.75rem; align-items:center;
    }
    .lr-filter-bar input[type=text] {
        flex:1; min-width:240px;
        padding:0.5rem 0.875rem;
        border:1.5px solid #e2e8f0; border-radius:8px;
        font-size:0.875rem; color:#374151; background:#fff;
        outline:none; transition:border-color 0.2s;
    }
    .lr-filter-bar input[type=text]:focus { border-color:var(--navy); }
    .lr-filter-btn {
        padding:0.5rem 1.25rem; border-radius:8px;
        font-size:0.875rem; font-weight:600; border:none; cursor:pointer;
        display:inline-flex; align-items:center; gap:6px;
        transition:background 0.2s; text-decoration:none;
    }
    .lr-filter-btn--primary { background:var(--navy); color:#fff; }
    .lr-filter-btn--primary:hover { background:var(--navy-mid); }
    .lr-filter-btn--clear { background:#f1f5f9; color:#475569; }
    .lr-filter-btn--clear:hover { background:#e2e8f0; }
    .lr-filter-btn .material-icons-round { font-size:16px; }

    /* ── Table card ── */
    .lr-card {
        background:#fff; border-radius:12px;
        box-shadow:0 1px 4px rgba(6,52,102,0.07);
        overflow:hidden; margin-bottom:1.5rem;
    }
    .lr-card-header {
        padding:1rem 1.5rem; border-bottom:1px solid #f1f5f9;
        display:flex; align-items:center; justify-content:space-between;
    }
    .lr-card-header h2 { font-size:0.9375rem; font-weight:700; color:#0f172a; margin:0; display:flex; align-items:center; gap:6px; }
    .lr-card-header h2 .material-icons-round { font-size:18px; }
    .lr-count { font-size:0.75rem; font-weight:600; color:var(--navy); background:var(--navy-light); padding:2px 10px; border-radius:999px; }
    .lr-count--red { color:#dc2626; background:#fee2e2; }

    .lr-table { width:100%; border-collapse:collapse; }
    .lr-table thead th {
        padding:0.75rem 1.25rem; text-align:left;
        font-size:0.6875rem; font-weight:700; text-transform:uppercase;
        letter-spacing:0.06em; color:#64748b; background:#f8fafc;
        border-bottom:1px solid #f1f5f9; white-space:nowrap;
    }
    .lr-table tbody tr { border-bottom:1px solid #f8fafc; transition:background 0.1s; }
    .lr-table tbody tr:last-child { border-bottom:none; }
    .lr-table tbody tr:hover { background:#fafbfc; }
    .lr-table tbody td { padding:0.875rem 1.25rem; font-size:0.875rem; color:#374151; vertical-align:middle; }

    /* ── Thumbnail ── */
    .lr-thumb {
        position:relative; width:68px; height:52px; flex-shrink:0;
        border-radius:10px; overflow:hidden;
        border:1px solid #e2e8f0; background:#f8fafc;
        box-shadow:0 1px 2px rgba(15,23,42,0.06); cursor:pointer;
    }
    .lr-thumb img { width:100%; height:100%; object-fit:cover; display:block; }
    .lr-thumb-zoom {
        position:absolute; inset:0;
        display:flex; align-items:center; justify-content:center;
        background:rgba(6,52,102,0); transition:background 0.2s;
    }
    .lr-thumb:hover .lr-thumb-zoom { background:rgba(6,52,102,0.45); }
    .lr-thumb-zoom .material-icons-round { font-size:20px; color:#fff; opacity:0; transition:opacity 0.2s; }
    .lr-thumb:hover .lr-thumb-zoom .material-icons-round { opacity:1; }
    .lr-thumb-btn { display:block; width:100%; height:100%; border:none; background:transparent; padding:0; cursor:pointer; }
    .lr-thumb-no {
        width:68px; height:52px; border-radius:10px;
        background:#f1f5f9; display:flex; align-items:center; justify-content:center; flex-shrink:0;
    }
    .lr-thumb-no .material-icons-round { font-size:22px; color:#cbd5e1; }

    /* ── Cell helpers ── */
    .lr-id    { font-size:0.75rem; font-weight:700; color:var(--navy); font-family:monospace; }
    .lr-sub   { font-size:0.72rem; color:#94a3b8; margin-top:2px; }
    .lr-name  { font-weight:600; color:#0f172a; }
    .lr-email { font-size:0.72rem; color:#94a3b8; }
    .lr-price { font-weight:700; color:#0f172a; }
    .lr-price-sub { font-size:0.72rem; color:#94a3b8; margin-top:2px; }
    .lr-date  { color:#374151; }
    .lr-date-rel { font-size:0.72rem; color:#94a3b8; margin-top:2px; }

    /* ── Action buttons ── */
    .lr-btn-review {
        display:inline-flex; align-items:center; gap:5px;
        padding:0.375rem 0.875rem;
        background:var(--navy); color:#fff;
        border-radius:7px; font-size:0.8125rem; font-weight:600;
        text-decoration:none; transition:background 0.2s; white-space:nowrap;
    }
    .lr-btn-review:hover { background:var(--navy-mid); }
    .lr-btn-review .material-icons-round { font-size:15px; }
    .lr-btn-view {
        display:inline-flex; align-items:center; gap:5px;
        padding:0.375rem 0.875rem;
        background:#f1f5f9; color:#475569;
        border-radius:7px; font-size:0.8125rem; font-weight:600;
        text-decoration:none; transition:background 0.2s; white-space:nowrap;
    }
    .lr-btn-view:hover { background:#e2e8f0; }
    .lr-btn-view .material-icons-round { font-size:15px; }

    /* ── Rejected section header ── */
    .lr-section-header {
        background:#fff; border-radius:12px;
        padding:1.25rem 1.5rem; margin-bottom:1rem;
        border-left:4px solid #dc2626;
        box-shadow:0 1px 4px rgba(6,52,102,0.07);
        display:flex; align-items:center; justify-content:space-between;
        flex-wrap:wrap; gap:0.75rem;
    }
    .lr-section-header h2 { font-size:1rem; font-weight:700; color:#0f172a; margin:0; display:flex; align-items:center; gap:8px; }
    .lr-section-header h2 .material-icons-round { font-size:20px; color:#dc2626; }
    .lr-section-header p { margin:0.2rem 0 0; color:#64748b; font-size:0.8125rem; }

    /* ── Rejection reason ── */
    .lr-rej-reason {
        display:inline-block; background:#fee2e2; color:#b91c1c;
        font-size:0.72rem; font-weight:700; padding:2px 8px; border-radius:5px;
    }
    .lr-rej-notes { font-size:0.75rem; color:#64748b; margin-top:4px; max-width:220px; }

    /* ── Empty state ── */
    .lr-empty { text-align:center; padding:3.5rem 1rem; color:#94a3b8; }
    .lr-empty .material-icons-round { font-size:48px; display:block; margin-bottom:0.75rem; opacity:0.35; }
    .lr-empty p { margin:0; font-size:0.9375rem; }

    .lr-pagination { padding:1rem 1.25rem; border-top:1px solid #f1f5f9; }

    /* ── Image preview modal ── */
    .lr-modal {
        position:fixed; inset:0;
        display:none; align-items:center; justify-content:center;
        padding:1.5rem; background:rgba(15,23,42,0.5);
        backdrop-filter:blur(3px); z-index:9999;
    }
    .lr-modal.is-open { display:flex; }
    .lr-modal-card {
        width:min(720px,100%); background:#fff;
        border-radius:16px; overflow:hidden;
        box-shadow:0 24px 60px rgba(15,23,42,0.3);
    }
    .lr-modal-img-wrap { background:#0f172a; max-height:70vh; }
    .lr-modal-img-wrap img { width:100%; max-height:70vh; object-fit:contain; display:block; }
    .lr-modal-footer {
        padding:1rem 1.25rem; border-top:1px solid #f1f5f9;
        display:flex; align-items:center; justify-content:space-between; gap:1rem;
    }
    .lr-modal-footer-info h3 { font-size:0.9375rem; font-weight:700; color:#0f172a; margin:0 0 2px; }
    .lr-modal-footer-info p  { font-size:0.8125rem; color:#64748b; margin:0; }
    .lr-modal-close-hint { font-size:0.75rem; color:#94a3b8; white-space:nowrap; }
</style>

<div>
    {{-- ── Page header ── --}}
    <div class="lr-header">
        <h1>
            <span class="material-icons-round" style="font-size:1.25rem;vertical-align:-3px;margin-right:6px">pending_actions</span>
            Listing Review
        </h1>
        <p>Review and approve pending vehicle listings before they go live</p>
    </div>

    {{-- ── Stat cards ── --}}
    <div class="lr-stats">
        <div class="lr-stat-card">
            <div class="lr-stat-icon" style="background:#fef9c3">
                <span class="material-icons-round" style="color:#a16207">hourglass_top</span>
            </div>
            <div>
                <div class="lr-stat-label">Pending Review</div>
                <div class="lr-stat-value" style="color:#a16207">{{ $pendingListings->total() }}</div>
            </div>
        </div>
        <div class="lr-stat-card">
            <div class="lr-stat-icon" style="background:#fee2e2">
                <span class="material-icons-round" style="color:#dc2626">cancel</span>
            </div>
            <div>
                <div class="lr-stat-label">Rejected</div>
                <div class="lr-stat-value" style="color:#dc2626">{{ $rejectedListings->total() }}</div>
            </div>
        </div>
        <div class="lr-stat-card">
            <div class="lr-stat-icon" style="background:#dcfce7">
                <span class="material-icons-round" style="color:#16a34a">check_circle</span>
            </div>
            <div>
                <div class="lr-stat-label">Active Live</div>
                <div class="lr-stat-value" style="color:#16a34a">{{ \App\Models\Listing::where('status','active')->count() }}</div>
            </div>
        </div>
        <div class="lr-stat-card">
            <div class="lr-stat-icon" style="background:var(--navy-light)">
                <span class="material-icons-round" style="color:var(--navy)">inventory_2</span>
            </div>
            <div>
                <div class="lr-stat-label">Total Listings</div>
                <div class="lr-stat-value" style="color:var(--navy)">{{ \App\Models\Listing::count() }}</div>
            </div>
        </div>
    </div>

    {{-- ── Search / filter ── --}}
    <form method="GET" action="{{ route('admin.listing-review') }}">
        <div class="lr-filter-bar">
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Search by make, model, VIN, listing ID or seller name…">
            <button type="submit" class="lr-filter-btn lr-filter-btn--primary">
                <span class="material-icons-round">search</span> Search
            </button>
            @if(request('search'))
            <a href="{{ route('admin.listing-review') }}" class="lr-filter-btn lr-filter-btn--clear">
                <span class="material-icons-round">close</span> Clear
            </a>
            @endif
        </div>
    </form>

    {{-- ── Pending listings table ── --}}
    <div class="lr-card">
        <div class="lr-card-header">
            <h2>
                <span class="material-icons-round" style="color:#a16207">schedule</span>
                Pending Listings
            </h2>
            <span class="lr-count">{{ $pendingListings->total() }} {{ Str::plural('listing', $pendingListings->total()) }}</span>
        </div>
        <div style="overflow-x:auto">
            <table class="lr-table">
                <thead>
                    <tr>
                        <th>Listing</th>
                        <th>Seller</th>
                        <th>Vehicle</th>
                        <th>Starting Price</th>
                        <th>Submitted</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendingListings as $listing)
                    @php
                        $mainImage   = $listing->images->first();
                        $imageUrl    = $mainImage ? $resolveListingImageUrl($mainImage->image_path) : asset('images/placeholder-product.png');
                        $vehicleName = trim(($listing->year ?? '') . ' ' . ($listing->make ?? '') . ' ' . ($listing->model ?? ''));
                    @endphp
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:10px">
                                @if($listing->images && $listing->images->count() > 0)
                                <div class="lr-thumb">
                                    <button type="button" class="lr-thumb-btn js-lr-img-trigger"
                                        data-image="{{ $imageUrl }}"
                                        data-title="{{ $vehicleName ?: 'Pending Listing' }}"
                                        data-meta="Listing #{{ $listing->id }} · {{ $listing->seller->name ?? 'N/A' }}">
                                        <img src="{{ $imageUrl }}" alt="{{ $listing->make ?? '' }} {{ $listing->model ?? '' }}">
                                        <div class="lr-thumb-zoom"><span class="material-icons-round">zoom_in</span></div>
                                    </button>
                                </div>
                                @else
                                <div class="lr-thumb-no">
                                    <span class="material-icons-round">directions_car</span>
                                </div>
                                @endif
                                <div>
                                    <div class="lr-id">#{{ $listing->id }}</div>
                                    @if($listing->subcategory)
                                    <div class="lr-sub">{{ $listing->subcategory }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="lr-name">{{ $listing->seller->name ?? '—' }}</div>
                            <div class="lr-email">{{ $listing->seller->email ?? '' }}</div>
                        </td>
                        <td>
                            <div style="font-weight:600;color:#0f172a">{{ $vehicleName ?: '—' }}</div>
                            <div class="lr-sub" style="font-family:monospace">{{ $listing->vin ? 'VIN: '.$listing->vin : 'No VIN' }}</div>
                        </td>
                        <td>
                            <div class="lr-price">${{ number_format($listing->starting_price ?? 0, 2) }}</div>
                            @if($listing->reserve_price)
                            <div class="lr-price-sub">Reserve: ${{ number_format($listing->reserve_price, 2) }}</div>
                            @endif
                            @if($listing->auction_duration)
                            <div class="lr-price-sub">{{ $listing->auction_duration }}-day auction</div>
                            @endif
                        </td>
                        <td>
                            <div class="lr-date">{{ $listing->created_at->format('M j, Y') }}</div>
                            <div class="lr-date-rel">{{ $listing->created_at->diffForHumans() }}</div>
                        </td>
                        <td>
                            <a href="{{ route('admin.listings.approval-detail', $listing->id) }}" class="lr-btn-review">
                                <span class="material-icons-round">rate_review</span> Review
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6">
                            <div class="lr-empty">
                                <span class="material-icons-round">task_alt</span>
                                <p>No pending listings — all caught up!</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($pendingListings->hasPages())
        <div class="lr-pagination">{{ $pendingListings->links() }}</div>
        @endif
    </div>

    {{-- ══ REJECTED LISTINGS ══ --}}
    <div class="lr-section-header">
        <div>
            <h2>
                <span class="material-icons-round">block</span>
                Rejected Listings
            </h2>
            <p>Listings returned to sellers for correction or re-submission</p>
        </div>
        <span class="lr-count lr-count--red">{{ $rejectedListings->total() }} {{ Str::plural('record', $rejectedListings->total()) }}</span>
    </div>

    <div class="lr-card">
        <div style="overflow-x:auto">
            <table class="lr-table">
                <thead>
                    <tr>
                        <th>Listing</th>
                        <th>Seller</th>
                        <th>Vehicle</th>
                        <th>Rejection Reason</th>
                        <th>Rejected</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rejectedListings as $listing)
                    @php
                        $rejImg    = $listing->images->first();
                        $rejImgUrl = $rejImg ? $resolveListingImageUrl($rejImg->image_path) : asset('images/placeholder-product.png');
                        $rejName   = trim(($listing->year ?? '') . ' ' . ($listing->make ?? '') . ' ' . ($listing->model ?? ''));
                    @endphp
                    <tr>
                        <td>
                            <div style="display:flex;align-items:center;gap:10px">
                                @if($listing->images && $listing->images->count() > 0)
                                <div class="lr-thumb">
                                    <button type="button" class="lr-thumb-btn js-lr-img-trigger"
                                        data-image="{{ $rejImgUrl }}"
                                        data-title="{{ $rejName ?: 'Rejected Listing' }}"
                                        data-meta="Listing #{{ $listing->id }} · {{ $listing->seller->name ?? '—' }}">
                                        <img src="{{ $rejImgUrl }}" alt="{{ $listing->make ?? '' }} {{ $listing->model ?? '' }}">
                                        <div class="lr-thumb-zoom"><span class="material-icons-round">zoom_in</span></div>
                                    </button>
                                </div>
                                @else
                                <div class="lr-thumb-no">
                                    <span class="material-icons-round">directions_car</span>
                                </div>
                                @endif
                                <div>
                                    <div class="lr-id">#{{ $listing->id }}</div>
                                    @if($listing->subcategory)
                                    <div class="lr-sub">{{ $listing->subcategory }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="lr-name">{{ $listing->seller->name ?? '—' }}</div>
                            <div class="lr-email">{{ $listing->seller->email ?? '' }}</div>
                        </td>
                        <td>
                            <div style="font-weight:600;color:#0f172a">{{ $rejName ?: '—' }}</div>
                            <div class="lr-sub" style="font-family:monospace">{{ $listing->vin ? 'VIN: '.$listing->vin : 'No VIN' }}</div>
                        </td>
                        <td>
                            @if($listing->rejection_reason)
                            <span class="lr-rej-reason">{{ $listing->rejection_reason }}</span>
                            @endif
                            @if($listing->rejection_notes)
                            <div class="lr-rej-notes">{{ Str::limit($listing->rejection_notes, 80) }}</div>
                            @endif
                            @if(!$listing->rejection_reason && !$listing->rejection_notes)
                            <span style="color:#cbd5e1">—</span>
                            @endif
                        </td>
                        <td>
                            <div class="lr-date">{{ $listing->updated_at->format('M j, Y') }}</div>
                            <div class="lr-date-rel">{{ $listing->updated_at->diffForHumans() }}</div>
                        </td>
                        <td>
                            <a href="{{ route('admin.listings.approval-detail', $listing->id) }}" class="lr-btn-view">
                                <span class="material-icons-round">visibility</span> View
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6">
                            <div class="lr-empty">
                                <span class="material-icons-round">thumb_up</span>
                                <p>No rejected listings</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($rejectedListings->hasPages())
        <div class="lr-pagination">{{ $rejectedListings->appends(request()->query())->links() }}</div>
        @endif
    </div>
</div>

{{-- ── Image quick-preview modal ── --}}
<div id="lrImgModal" class="lr-modal" aria-hidden="true">
    <div class="lr-modal-card" id="lrImgModalCard">
        <div class="lr-modal-img-wrap">
            <img id="lrImgModalImg" src="" alt="Listing preview">
        </div>
        <div class="lr-modal-footer">
            <div class="lr-modal-footer-info">
                <h3 id="lrImgModalTitle">Listing Preview</h3>
                <p id="lrImgModalMeta"></p>
            </div>
            <span class="lr-modal-close-hint">Click outside to close</span>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var modal     = document.getElementById('lrImgModal');
    var modalCard = document.getElementById('lrImgModalCard');
    var modalImg  = document.getElementById('lrImgModalImg');
    var modalTitle= document.getElementById('lrImgModalTitle');
    var modalMeta = document.getElementById('lrImgModalMeta');

    if (!modal) return;

    document.querySelectorAll('.js-lr-img-trigger').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            modalImg.src           = btn.dataset.image || '';
            modalTitle.textContent = btn.dataset.title || 'Preview';
            modalMeta.textContent  = btn.dataset.meta  || '';
            modal.classList.add('is-open');
            modal.setAttribute('aria-hidden', 'false');
        });
    });

    modal.addEventListener('click', function (e) {
        if (!modalCard.contains(e.target)) {
            modal.classList.remove('is-open');
            modal.setAttribute('aria-hidden', 'true');
            modalImg.src = '';
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            modal.classList.remove('is-open');
            modal.setAttribute('aria-hidden', 'true');
            modalImg.src = '';
        }
    });
});
</script>
@endsection
