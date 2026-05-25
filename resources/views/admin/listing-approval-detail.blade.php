@extends('layouts.admin')

@section('title', 'Listing Review Detail - Admin')

@section('content')
@php
    $isPending  = $listing->status === 'pending';
    $statusColor = match($listing->status) {
        'pending'  => ['bg' => '#fef9c3', 'fg' => '#a16207'],
        'active'   => ['bg' => '#dcfce7', 'fg' => '#16a34a'],
        'approved' => ['bg' => '#dbeafe', 'fg' => '#2563eb'],
        'sold'     => ['bg' => '#f3e8ff', 'fg' => '#7c3aed'],
        'rejected' => ['bg' => '#fee2e2', 'fg' => '#dc2626'],
        default    => ['bg' => '#f1f5f9', 'fg' => '#475569'],
    };
@endphp

<style>
    :root { --navy:#063466; --navy-light:#e8eef6; --navy-mid:#0d4d8c; }

    /* ── Header ── */
    .lrd-header {
        background:#fff; border-radius:12px;
        padding:1.25rem 1.5rem; margin-bottom:1.25rem;
        border-left:4px solid var(--navy);
        box-shadow:0 1px 4px rgba(6,52,102,0.07);
        display:flex; align-items:center; justify-content:space-between;
        flex-wrap:wrap; gap:0.75rem;
    }
    .lrd-header h1 { font-size:1.2rem; font-weight:700; color:var(--navy); margin:0 0 0.2rem; }
    .lrd-header p  { margin:0; color:#64748b; font-size:0.8125rem; }
    .lrd-back-btn {
        display:inline-flex; align-items:center; gap:5px;
        padding:0.5rem 1rem; background:#f1f5f9; color:#475569;
        border-radius:8px; font-size:0.8125rem; font-weight:600;
        text-decoration:none; transition:background 0.2s; white-space:nowrap;
    }
    .lrd-back-btn:hover { background:#e2e8f0; }
    .lrd-back-btn .material-icons-round { font-size:16px; }
    .lrd-back-btn--navy { background:var(--navy); color:#fff; }
    .lrd-back-btn--navy:hover { background:var(--navy-mid); color:#fff; }

    /* ── Alerts ── */
    .lrd-alert {
        border-radius:10px; padding:0.875rem 1.125rem; margin-bottom:1.25rem;
        display:flex; align-items:flex-start; gap:10px;
    }
    .lrd-alert--success { background:#f0fdf4; border-left:4px solid #16a34a; }
    .lrd-alert--error   { background:#fef2f2; border-left:4px solid #dc2626; }
    .lrd-alert .material-icons-round { font-size:18px; flex-shrink:0; margin-top:1px; }
    .lrd-alert--success .material-icons-round { color:#16a34a; }
    .lrd-alert--error   .material-icons-round { color:#dc2626; }
    .lrd-alert p { margin:0; font-size:0.875rem; }
    .lrd-alert--success p { color:#15803d; }
    .lrd-alert--error   p { color:#b91c1c; }

    /* ── Layout grid ── */
    .lrd-grid { display:grid; grid-template-columns:1fr; gap:1.25rem; }
    @media(min-width:1024px) { .lrd-grid { grid-template-columns:2fr 1fr; } }
    .lrd-col-main { display:flex; flex-direction:column; gap:1.25rem; }
    .lrd-col-side { display:flex; flex-direction:column; gap:1.25rem; }

    /* ── Card ── */
    .lrd-card {
        background:#fff; border-radius:12px;
        box-shadow:0 1px 4px rgba(6,52,102,0.07); overflow:hidden;
    }
    .lrd-card-header {
        padding:0.875rem 1.25rem; border-bottom:1px solid #f1f5f9;
        display:flex; align-items:center; gap:8px;
    }
    .lrd-card-header h2 {
        font-size:0.875rem; font-weight:700; color:var(--navy);
        margin:0; display:flex; align-items:center; gap:6px;
    }
    .lrd-card-header h2 .material-icons-round { font-size:17px; }
    .lrd-card-body { padding:1.25rem; }

    /* ── Info grid (vehicle details) ── */
    .lrd-info-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:0.875rem 1.5rem; }
    .lrd-field label {
        display:block; font-size:0.7rem; font-weight:700;
        text-transform:uppercase; letter-spacing:0.05em; color:#94a3b8; margin-bottom:3px;
    }
    .lrd-field p { margin:0; font-size:0.875rem; font-weight:600; color:#0f172a; }
    .lrd-field p.mono { font-family:monospace; }

    /* ── Photos grid ── */
    .lrd-photos { display:grid; grid-template-columns:repeat(auto-fill,minmax(155px,1fr)); gap:0.875rem; }
    .lrd-photo {
        border-radius:10px; overflow:hidden; border:1px solid #e2e8f0;
        aspect-ratio:4/3; cursor:pointer; position:relative;
    }
    .lrd-photo img { width:100%; height:100%; object-fit:cover; display:block; transition:transform 0.25s; }
    .lrd-photo:hover img { transform:scale(1.04); }
    .lrd-photo-overlay {
        position:absolute; inset:0;
        background:rgba(6,52,102,0); transition:background 0.2s;
        display:flex; align-items:center; justify-content:center;
    }
    .lrd-photo:hover .lrd-photo-overlay { background:rgba(6,52,102,0.42); }
    .lrd-photo-overlay .material-icons-round { font-size:28px; color:#fff; opacity:0; transition:opacity 0.2s; }
    .lrd-photo:hover .lrd-photo-overlay .material-icons-round { opacity:1; }

    /* ── Sidebar info rows ── */
    .lrd-info-rows { display:flex; flex-direction:column; gap:0.875rem; }
    .lrd-info-row label {
        display:block; font-size:0.7rem; font-weight:700;
        text-transform:uppercase; letter-spacing:0.05em; color:#94a3b8; margin-bottom:2px;
    }
    .lrd-info-row p { margin:0; font-size:0.875rem; font-weight:600; color:#0f172a; word-break:break-word; }

    /* ── Status badge ── */
    .lrd-status-badge {
        display:inline-flex; align-items:center; gap:4px;
        font-size:0.75rem; font-weight:700; padding:4px 10px; border-radius:999px;
    }

    /* ── Divider ── */
    .lrd-divider { border:none; border-top:1px solid #f1f5f9; margin:1rem 0; }

    /* ── Action form elements ── */
    .lrd-form-label { display:block; font-size:0.8125rem; font-weight:600; color:#374151; margin-bottom:5px; }
    .lrd-select, .lrd-textarea {
        width:100%; padding:0.5rem 0.75rem;
        border:1.5px solid #e2e8f0; border-radius:8px;
        font-size:0.875rem; color:#374151; background:#fff;
        outline:none; transition:border-color 0.2s; font-family:inherit;
        margin-bottom:0.875rem;
    }
    .lrd-select:focus, .lrd-textarea:focus { border-color:var(--navy); }
    .lrd-textarea { resize:vertical; min-height:72px; }

    /* ── Action buttons ── */
    .lrd-btn-approve {
        width:100%; padding:0.75rem; background:#16a34a; color:#fff;
        border:none; border-radius:10px; font-size:0.9375rem; font-weight:700;
        cursor:pointer; display:flex; align-items:center; justify-content:center;
        gap:7px; transition:background 0.2s; margin-bottom:0.875rem;
    }
    .lrd-btn-approve:hover { background:#15803d; }
    .lrd-btn-approve .material-icons-round { font-size:19px; }
    .lrd-btn-reject {
        width:100%; padding:0.75rem; background:#dc2626; color:#fff;
        border:none; border-radius:10px; font-size:0.9375rem; font-weight:700;
        cursor:pointer; display:flex; align-items:center; justify-content:center;
        gap:7px; transition:background 0.2s;
    }
    .lrd-btn-reject:hover { background:#b91c1c; }
    .lrd-btn-reject .material-icons-round { font-size:19px; }
    .lrd-reject-label {
        font-size:0.8125rem; font-weight:700; color:#0f172a;
        margin:0 0 0.625rem; display:flex; align-items:center; gap:5px;
    }
    .lrd-reject-label .material-icons-round { font-size:16px; color:#dc2626; }

    /* ── Duplicate VIN warning ── */
    .lrd-vin-warn {
        background:#fef9c3; border:1px solid #fde68a; border-radius:8px;
        padding:0.625rem 0.875rem; font-size:0.8125rem; font-weight:600; color:#92400e;
        display:flex; align-items:center; gap:6px; margin-top:1rem;
    }
    .lrd-vin-warn .material-icons-round { font-size:16px; }

    /* ── No photos empty ── */
    .lrd-no-photos { text-align:center; padding:2.5rem 1rem; color:#94a3b8; }
    .lrd-no-photos .material-icons-round { font-size:40px; display:block; margin-bottom:8px; opacity:0.35; }
    .lrd-no-photos p { margin:0; font-size:0.875rem; }

    /* ── Confirmation modals ── */
    .lrd-modal {
        position:fixed; inset:0; z-index:9999;
        display:flex; align-items:center; justify-content:center; padding:1.5rem;
    }
    .lrd-modal.hidden { display:none !important; }
    .lrd-modal-backdrop {
        position:absolute; inset:0;
        background:rgba(15,23,42,0.5); backdrop-filter:blur(4px);
    }
    .lrd-modal-card {
        position:relative; width:100%; max-width:440px;
        background:#fff; border-radius:16px;
        box-shadow:0 25px 50px -12px rgba(0,0,0,0.25); padding:1.75rem;
    }
    .lrd-modal-icon {
        width:52px; height:52px; border-radius:12px;
        display:flex; align-items:center; justify-content:center; margin-bottom:1rem;
    }
    .lrd-modal-icon .material-icons-round { font-size:26px; }
    .lrd-modal-title { font-size:1.125rem; font-weight:700; color:#0f172a; margin:0 0 0.4rem; }
    .lrd-modal-msg   { font-size:0.9rem; color:#64748b; line-height:1.55; margin:0 0 1.5rem; }
    .lrd-modal-actions { display:flex; gap:0.75rem; justify-content:flex-end; }
    .lrd-modal-btn {
        padding:0.6rem 1.25rem; font-size:0.9rem; font-weight:700;
        border-radius:9px; border:none; cursor:pointer; transition:background 0.2s;
    }
    .lrd-modal-btn--cancel  { background:#f1f5f9; color:#475569; }
    .lrd-modal-btn--cancel:hover  { background:#e2e8f0; }
    .lrd-modal-btn--approve { background:#16a34a; color:#fff; }
    .lrd-modal-btn--approve:hover { background:#15803d; }
    .lrd-modal-btn--reject  { background:#dc2626; color:#fff; }
    .lrd-modal-btn--reject:hover  { background:#b91c1c; }

    /* ── Image fullscreen modal ── */
    .lrd-img-modal-card {
        position:relative; max-width:min(900px,95vw);
        background:#000; border-radius:14px; overflow:hidden;
        box-shadow:0 25px 50px -12px rgba(0,0,0,0.4);
    }
    .lrd-img-modal-card img { display:block; max-width:100%; max-height:88vh; object-fit:contain; }
    .lrd-img-close {
        position:absolute; top:0.875rem; right:0.875rem;
        width:38px; height:38px; border:none; border-radius:9px;
        background:rgba(15,23,42,0.72); color:#fff;
        cursor:pointer; display:flex; align-items:center; justify-content:center;
        z-index:2; transition:background 0.2s;
    }
    .lrd-img-close:hover { background:rgba(15,23,42,0.92); }
    .lrd-img-close .material-icons-round { font-size:18px; }

    /* ── Photo count pill ── */
    .lrd-pill {
        font-size:0.72rem; font-weight:600; color:var(--navy);
        background:var(--navy-light); padding:2px 8px; border-radius:999px; margin-left:4px;
    }
</style>

{{-- ── Header ── --}}
<div class="lrd-header">
    <div>
        <h1>
            <span class="material-icons-round" style="font-size:1.1rem;vertical-align:-3px;margin-right:5px">{{ $isPending ? 'rate_review' : 'article' }}</span>
            {{ $isPending ? 'Listing Approval Detail' : 'Listing Detail' }}
        </h1>
        <p>{{ $isPending ? 'Review all vehicle details before approving or rejecting' : 'View full listing information' }}</p>
    </div>
    <div style="display:flex;gap:8px;flex-wrap:wrap">
        @if(!$isPending)
        <a href="{{ route('admin.active-listings') }}" class="lrd-back-btn">
            <span class="material-icons-round">arrow_back</span> Active Auctions
        </a>
        @endif
        <a href="{{ route('admin.listing-review') }}" class="lrd-back-btn {{ $isPending ? 'lrd-back-btn--navy' : '' }}">
            <span class="material-icons-round">list</span>
            {{ $isPending ? 'Back to Review' : 'Listing Review' }}
        </a>
    </div>
</div>

{{-- ── Alerts ── --}}
@if(session('success'))
<div class="lrd-alert lrd-alert--success">
    <span class="material-icons-round">check_circle</span>
    <p>{{ session('success') }}</p>
</div>
@endif
@if(session('error'))
<div class="lrd-alert lrd-alert--error">
    <span class="material-icons-round">error</span>
    <p>{{ session('error') }}</p>
</div>
@endif

{{-- ── Two-column grid ── --}}
<div class="lrd-grid">

    {{-- ── Left: main content ── --}}
    <div class="lrd-col-main">

        {{-- Vehicle Information --}}
        <div class="lrd-card">
            <div class="lrd-card-header">
                <h2><span class="material-icons-round">directions_car</span> Vehicle Information</h2>
            </div>
            <div class="lrd-card-body">
                <div class="lrd-info-grid">
                    <div class="lrd-field"><label>Year</label><p>{{ $listing->year ?? '—' }}</p></div>
                    <div class="lrd-field"><label>Make</label><p>{{ $listing->make ?? '—' }}</p></div>
                    <div class="lrd-field"><label>Model</label><p>{{ $listing->model ?? '—' }}</p></div>
                    <div class="lrd-field"><label>Trim</label><p>{{ $listing->trim ?? '—' }}</p></div>
                    <div class="lrd-field"><label>VIN / HIN</label><p class="mono">{{ $listing->vin ?? '—' }}</p></div>
                    <div class="lrd-field"><label>Exterior Color</label><p>{{ $listing->color ?? '—' }}</p></div>
                    <div class="lrd-field"><label>Interior Color</label><p>{{ $listing->interior_color ?? '—' }}</p></div>
                    <div class="lrd-field"><label>Island</label><p>{{ $listing->island ?? '—' }}</p></div>
                    <div class="lrd-field"><label>Title Status</label><p>{{ $listing->title_status_display ?? '—' }}</p></div>
                    <div class="lrd-field"><label>Primary Damage</label><p>{{ $listing->primary_damage ?: '—' }}</p></div>
                    <div class="lrd-field"><label>Secondary Damage</label><p>{{ $listing->secondary_damage ?: '—' }}</p></div>
                    <div class="lrd-field"><label>Keys Available</label><p>{{ $listing->keys_available ? 'Yes' : 'No' }}</p></div>
                    <div class="lrd-field">
                        <label>Odometer</label>
                        <p>
                            @if($listing->odometer)
                                {{ number_format($listing->odometer) }} mi
                                @if($listing->odometer_estimated ?? false)
                                    <span style="color:#d97706;font-size:0.75rem;font-weight:600">(Estimated)</span>
                                @endif
                            @else
                                —
                            @endif
                        </p>
                    </div>
                    <div class="lrd-field"><label>Fuel Type</label><p>{{ $listing->fuel_type ?? '—' }}</p></div>
                    <div class="lrd-field"><label>Transmission</label><p>{{ $listing->transmission ?? '—' }}</p></div>
                    <div class="lrd-field"><label>Engine Type</label><p>{{ $listing->engine_type ?? '—' }}</p></div>
                </div>
            </div>
        </div>

        {{-- Auction Settings --}}
        <div class="lrd-card">
            <div class="lrd-card-header">
                <h2><span class="material-icons-round">gavel</span> Auction Settings</h2>
            </div>
            <div class="lrd-card-body">
                <div class="lrd-info-grid">
                    <div class="lrd-field"><label>Duration</label><p>{{ $listing->auction_duration ?? '—' }} days</p></div>
                    <div class="lrd-field"><label>Starting Price</label><p>${{ number_format($listing->starting_price ?? 0, 2) }}</p></div>
                    <div class="lrd-field"><label>Reserve Price</label><p>${{ number_format($listing->reserve_price ?? 0, 2) }}</p></div>
                    <div class="lrd-field"><label>Buy Now Price</label><p>${{ number_format($listing->buy_now_price ?? 0, 2) }}</p></div>
                </div>
            </div>
        </div>

        {{-- Photos --}}
        <div class="lrd-card">
            <div class="lrd-card-header">
                <h2>
                    <span class="material-icons-round">photo_library</span>
                    Photos
                    @if($listing->images && $listing->images->count() > 0)
                    <span class="lrd-pill">{{ $listing->images->count() }}</span>
                    @endif
                </h2>
            </div>
            <div class="lrd-card-body">
                @if($listing->images && $listing->images->count() > 0)
                <div class="lrd-photos">
                    @foreach($listing->images as $image)
                    <div class="lrd-photo" onclick="lrdOpenImg('{{ asset('uploads/listings/' . $image->image_path) }}')">
                        <img src="{{ asset('uploads/listings/' . $image->image_path) }}" alt="Listing photo">
                        <div class="lrd-photo-overlay"><span class="material-icons-round">zoom_in</span></div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="lrd-no-photos">
                    <span class="material-icons-round">image_not_supported</span>
                    <p>No photos uploaded yet</p>
                </div>
                @endif
            </div>
        </div>

    </div>{{-- /col-main --}}

    {{-- ── Right: sidebar ── --}}
    <div class="lrd-col-side">

        {{-- Listing Details --}}
        <div class="lrd-card">
            <div class="lrd-card-header">
                <h2><span class="material-icons-round">info</span> Listing Details</h2>
            </div>
            <div class="lrd-card-body">
                <div class="lrd-info-rows">
                    <div class="lrd-info-row">
                        <label>Listing ID</label>
                        <p style="font-family:monospace">#{{ $listing->id }}</p>
                    </div>
                    <div class="lrd-info-row">
                        <label>Status</label>
                        <p>
                            <span class="lrd-status-badge"
                                style="background:{{ $statusColor['bg'] }};color:{{ $statusColor['fg'] }}">
                                {{ strtoupper(str_replace('_', ' ', $listing->status ?? 'unknown')) }}
                            </span>
                        </p>
                    </div>
                    <div class="lrd-info-row">
                        <label>Submitted</label>
                        <p>{{ $listing->created_at->format('M d, Y g:i A') }}</p>
                    </div>
                    <div class="lrd-info-row">
                        <label>Last Updated</label>
                        <p>{{ $listing->updated_at->diffForHumans() }}</p>
                    </div>
                </div>
                @if($listing->duplicate_vin_flag)
                <div class="lrd-vin-warn">
                    <span class="material-icons-round">warning</span>
                    Duplicate VIN detected
                </div>
                @endif
            </div>
        </div>

        {{-- Seller Information --}}
        <div class="lrd-card">
            <div class="lrd-card-header">
                <h2><span class="material-icons-round">person</span> Seller Information</h2>
            </div>
            <div class="lrd-card-body">
                <div class="lrd-info-rows">
                    <div class="lrd-info-row">
                        <label>Name</label>
                        <p>{{ $listing->seller->name ?? '—' }}</p>
                    </div>
                    <div class="lrd-info-row">
                        <label>Email</label>
                        <p>{{ $listing->seller->email ?? '—' }}</p>
                    </div>
                    <div class="lrd-info-row">
                        <label>Phone</label>
                        <p>{{ $listing->seller->phone ?? '—' }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Actions (pending only) --}}
        @if($isPending)
        <div class="lrd-card">
            <div class="lrd-card-header">
                <h2><span class="material-icons-round">admin_panel_settings</span> Actions</h2>
            </div>
            <div class="lrd-card-body">

                {{-- Approve --}}
                <form method="POST" action="{{ route('admin.listings.approve', $listing) }}" id="approveForm">
                    @csrf
                    <button type="button" class="lrd-btn-approve" onclick="lrdOpenApprove()">
                        <span class="material-icons-round">check_circle</span> Approve Listing
                    </button>
                </form>

                <hr class="lrd-divider">

                {{-- Reject --}}
                <p class="lrd-reject-label">
                    <span class="material-icons-round">cancel</span> Reject Listing
                </p>
                <form method="POST" action="{{ route('admin.listings.reject', $listing) }}" id="rejectForm">
                    @csrf
                    <label class="lrd-form-label">
                        Rejection Reason <span style="color:#dc2626">*</span>
                    </label>
                    <select name="rejection_reason" required class="lrd-select">
                        <option value="">Select a reason…</option>
                        <option value="Poor quality photos">Poor quality photos</option>
                        <option value="Missing required information">Missing required information</option>
                        <option value="Invalid VIN/HIN">Invalid VIN/HIN</option>
                        <option value="Duplicate listing">Duplicate listing</option>
                        <option value="Prohibited item">Prohibited item</option>
                        <option value="Incorrect category">Incorrect category</option>
                        <option value="Other (specify in notes)">Other (specify in notes)</option>
                    </select>
                    <label class="lrd-form-label">
                        Notes <span style="color:#94a3b8;font-weight:400">(optional)</span>
                    </label>
                    <textarea name="rejection_notes" class="lrd-textarea"
                        placeholder="Add any additional context for the seller…"></textarea>
                    <button type="button" class="lrd-btn-reject" onclick="lrdOpenReject()">
                        <span class="material-icons-round">cancel</span> Reject Listing
                    </button>
                </form>

            </div>
        </div>
        @endif

    </div>{{-- /col-side --}}
</div>{{-- /grid --}}

{{-- ── Approve confirmation modal ── --}}
<div id="lrdApproveModal" class="lrd-modal hidden" aria-hidden="true">
    <div class="lrd-modal-backdrop" onclick="lrdCloseApprove()"></div>
    <div class="lrd-modal-card">
        <div class="lrd-modal-icon" style="background:#dcfce7">
            <span class="material-icons-round" style="color:#16a34a">check_circle</span>
        </div>
        <h3 class="lrd-modal-title">Approve this listing?</h3>
        <p class="lrd-modal-msg">The listing will go live immediately and buyers will be able to place bids. This action cannot be undone.</p>
        <div class="lrd-modal-actions">
            <button type="button" class="lrd-modal-btn lrd-modal-btn--cancel" onclick="lrdCloseApprove()">Cancel</button>
            <button type="button" class="lrd-modal-btn lrd-modal-btn--approve"
                onclick="document.getElementById('approveForm').submit()">Approve</button>
        </div>
    </div>
</div>

{{-- ── Reject confirmation modal ── --}}
<div id="lrdRejectModal" class="lrd-modal hidden" aria-hidden="true">
    <div class="lrd-modal-backdrop" onclick="lrdCloseReject()"></div>
    <div class="lrd-modal-card">
        <div class="lrd-modal-icon" style="background:#fee2e2">
            <span class="material-icons-round" style="color:#dc2626">cancel</span>
        </div>
        <h3 class="lrd-modal-title">Reject this listing?</h3>
        <p class="lrd-modal-msg">The seller will be notified with your reason and notes. They may correct and re-submit the listing.</p>
        <div class="lrd-modal-actions">
            <button type="button" class="lrd-modal-btn lrd-modal-btn--cancel" onclick="lrdCloseReject()">Cancel</button>
            <button type="button" class="lrd-modal-btn lrd-modal-btn--reject"
                onclick="document.getElementById('rejectForm').submit()">Reject</button>
        </div>
    </div>
</div>

{{-- ── Image fullscreen modal ── --}}
<div id="lrdImgModal" class="lrd-modal hidden" aria-hidden="true">
    <div class="lrd-modal-backdrop" onclick="lrdCloseImg()"></div>
    <div class="lrd-img-modal-card" onclick="event.stopPropagation()">
        <button type="button" class="lrd-img-close" onclick="lrdCloseImg()">
            <span class="material-icons-round">close</span>
        </button>
        <img id="lrdModalImg" src="" alt="Full size photo">
    </div>
</div>

<script>
    function lrdOpenApprove()  { document.getElementById('lrdApproveModal').classList.remove('hidden'); }
    function lrdCloseApprove() { document.getElementById('lrdApproveModal').classList.add('hidden'); }
    function lrdOpenReject()   { document.getElementById('lrdRejectModal').classList.remove('hidden'); }
    function lrdCloseReject()  { document.getElementById('lrdRejectModal').classList.add('hidden'); }

    function lrdOpenImg(src) {
        document.getElementById('lrdModalImg').src = src;
        document.getElementById('lrdImgModal').classList.remove('hidden');
    }
    function lrdCloseImg() {
        document.getElementById('lrdImgModal').classList.add('hidden');
        document.getElementById('lrdModalImg').src = '';
    }

    document.addEventListener('keydown', function (e) {
        if (e.key !== 'Escape') return;
        lrdCloseApprove();
        lrdCloseReject();
        lrdCloseImg();
    });
</script>
@endsection
