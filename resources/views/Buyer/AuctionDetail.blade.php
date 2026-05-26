@extends('layouts.welcome')

@section('content')
@php
    $listing       = $auctionListing;
    $highestBid    = $listing->bids()->where('status','active')->orderByDesc('amount')->first();
    $currentBid    = $highestBid ? (float)$highestBid->amount : (float)($listing->starting_price ?? $listing->price ?? 0);

    $endDate = $listing->auction_end_time
        ? \Carbon\Carbon::parse($listing->auction_end_time)
        : \Carbon\Carbon::parse($listing->auction_start_time ?? $listing->created_at)->addDays($listing->auction_duration ?? 7);

    $isExpired  = $endDate->isPast();
    $timeRemaining = !$isExpired ? now()->diff($endDate) : null;

    $incrementService = new \App\Services\BiddingIncrementService();
    $nextValidBid    = $incrementService->calculateMinimumNextBid($currentBid);
    $incrementAmount = $incrementService->getIncrementForBid($currentBid);

    $images = collect($listing->images ?? [])->map(function($img){
        $path = is_object($img) ? ($img->image_path ?? $img->path ?? null) : $img;
        if (!$path) return null;
        if (str_starts_with($path,'http://') || str_starts_with($path,'https://')) return $path;
        return asset('uploads/listings/'.ltrim($path,'/'));
    })->filter()->values();

    $mainImage    = $images->first() ?? asset('images/placeholder.png');
    $inWatchlist  = $inWatchlist  ?? (Auth::check() && Auth::user()->watchlist()->where('listing_id',$listing->id)->exists());
    $showOutbidBanner = $showOutbidBanner ?? false;
    $buyerFeePreview  = $buyerFeePreview  ?? ['commission' => max($nextValidBid*0.06,100)];

    $userHighestBid = Auth::check() ? $listing->bids()->where('user_id',Auth::id())->where('status','active')->max('amount') : null;
    $isWinning  = Auth::check() && $highestBid && $highestBid->user_id === Auth::id();
    $isOutbid   = Auth::check() && $userHighestBid && $highestBid && $highestBid->amount > $userHighestBid;
    $reserveMet = !$listing->reserve_price || $currentBid >= $listing->reserve_price;

    $listingTitle = strtoupper(trim(implode(' ', array_filter([
        $listing->year ?? null,
        $listing->make ?? $listing->other_make ?? null,
        $listing->model ?? $listing->other_model ?? null,
        $listing->trim ?? null,
    ]))));

    $maskedVin = method_exists($listing,'maskedVinOrHin') ? $listing->maskedVinOrHin() : ($listing->vin ? substr($listing->vin,0,10).'******' : 'N/A');

    // Video (if seller uploaded one)
    $videoUrl    = $listing->video_path ? asset('uploads/listings/'.ltrim($listing->video_path,'/')) : null;
    $totalMedia  = $images->count() + ($videoUrl ? 1 : 0);
    $videoIndex  = $videoUrl ? $images->count() : -1;

    // Countdown parts
    $cdDays  = $timeRemaining ? $timeRemaining->d : 0;
    $cdHours = $timeRemaining ? $timeRemaining->h : 0;
    $cdMins  = $timeRemaining ? $timeRemaining->i : 0;

    // Damage areas for diagram
    $damages = [];
    if ($listing->primary_damage)   $damages[] = ['zone'=>'front',  'label'=>$listing->primary_damage,   'color'=>'amber'];
    if ($listing->secondary_damage) $damages[] = ['zone'=>'rear',   'label'=>$listing->secondary_damage,  'color'=>'amber'];
@endphp

{{-- ── Page-scoped styles ─────────────────────────────────────────────── --}}
@push('styles')
<style>
/* ── Base ── */
.ad-page { background:#f0f2f5; min-height:100vh; font-family:'Inter',sans-serif; }

/* ── Page header ── */
.adh-wrap  { background:#fff; border-bottom:1px solid #e2e8f0; }
.adh-inner { max-width:1440px; margin:0 auto; padding:18px 24px; }
.adh-title { font-size:clamp(18px,2vw,24px); font-weight:800; color:#0f2752; letter-spacing:-.3px; text-transform:uppercase; line-height:1.2; }
.adh-meta  { display:flex; flex-wrap:wrap; align-items:center; gap:6px 10px; margin-top:8px; font-size:13px; color:#64748b; }
.adh-badge { display:inline-flex; align-items:center; gap:4px; background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; border-radius:20px; padding:2px 10px; font-size:12px; font-weight:600; }
.adh-sep   { color:#cbd5e1; font-size:10px; }
.adh-copy  { background:none; border:none; cursor:pointer; color:#94a3b8; padding:1px 3px; transition:color .15s; }
.adh-copy:hover { color:#1d4ed8; }

/* ── Three-column grid ── */
.ad-grid { max-width:1440px; margin:0 auto; padding:20px 24px; display:grid; gap:18px; grid-template-columns:minmax(0,2.2fr) minmax(0,1.9fr) minmax(0,1.25fr); grid-template-rows:auto 1fr; grid-template-areas:"media specs bid" "damage damage bid"; }
@media(max-width:1200px){ .ad-grid { grid-template-columns:1fr 1fr; grid-template-areas:"media bid" "specs bid" "damage damage"; } }
@media(max-width:768px) { .ad-grid { grid-template-columns:1fr; grid-template-areas:"media" "bid" "specs" "damage"; } }

/* ── Cards ── */
.ad-card { background:#fff; border-radius:10px; border:1px solid #e2e8f0; overflow:hidden; }

/* ── Media gallery ── */
#ad-media { grid-area:media; }
.gal-main  { position:relative; aspect-ratio:16/9; background:#1a1a2e; overflow:hidden; }
.gal-main img { width:100%; height:100%; object-fit:cover; display:block; transition:opacity .3s; }
.gal-arrow { position:absolute; top:50%; transform:translateY(-50%); background:rgba(0,0,0,.45); border:none; color:#fff; cursor:pointer; width:36px; height:60px; display:flex; align-items:center; justify-content:center; font-size:20px; transition:background .15s; z-index:5; }
.gal-arrow:hover { background:rgba(0,0,0,.7); }
.gal-arrow.left { left:0; border-radius:0 4px 4px 0; }
.gal-arrow.right { right:0; border-radius:4px 0 0 4px; }
.gal-badges-bl { position:absolute; bottom:10px; left:10px; display:flex; align-items:center; gap:6px; z-index:5; }
.gal-hd   { display:inline-flex; align-items:center; gap:5px; background:rgba(0,0,0,.6); color:#fff; font-size:11px; font-weight:700; padding:3px 8px; border-radius:4px; }
.gal-hd-toggle { width:28px; height:16px; background:#1d4ed8; border-radius:8px; position:relative; cursor:pointer; flex-shrink:0; }
.gal-hd-toggle::after { content:''; position:absolute; top:2px; right:2px; width:12px; height:12px; background:#fff; border-radius:50%; }
.gal-dl   { background:rgba(0,0,0,.5); border:none; color:#fff; width:28px; height:28px; border-radius:4px; cursor:pointer; display:flex; align-items:center; justify-content:center; }
.gal-dl:hover { background:rgba(0,0,0,.75); }
.gal-badges-br { position:absolute; bottom:10px; right:10px; display:flex; align-items:center; gap:6px; z-index:5; }
.gal-pill { background:rgba(0,0,0,.62); color:#fff; font-size:11px; font-weight:600; padding:3px 10px; border-radius:20px; cursor:pointer; transition:background .15s; white-space:nowrap; }
.gal-pill:hover { background:rgba(0,0,0,.8); }
.gal-thumbs { display:grid; grid-template-columns:repeat(5,1fr); gap:4px; padding:10px; background:#f8fafc; }
.gal-thumb { aspect-ratio:4/3; overflow:hidden; cursor:pointer; border:2px solid transparent; border-radius:4px; transition:border-color .15s,transform .15s; }
.gal-thumb:hover { transform:scale(1.03); }
.gal-thumb.active { border-color:#1d4ed8; box-shadow:0 0 0 2px #bfdbfe; }
.gal-thumb img { width:100%; height:100%; object-fit:cover; display:block; }

/* ── Specs column ── */
#ad-specs { grid-area:specs; display:flex; flex-direction:column; gap:14px; }
.verif-card { display:flex; align-items:stretch; gap:0; }
.verif-item { flex:1; display:flex; flex-direction:column; align-items:center; text-align:center; padding:16px 12px; border-right:1px solid #f1f5f9; }
.verif-item:last-child { border-right:none; }
.verif-icon { width:42px; height:42px; border-radius:50%; display:flex; align-items:center; justify-content:center; margin-bottom:8px; }
.verif-icon.green  { background:#dcfce7; }
.verif-icon.blue   { background:#dbeafe; }
.verif-label  { font-size:13px; font-weight:700; color:#1e293b; margin-bottom:3px; }
.verif-sub    { font-size:10.5px; color:#64748b; line-height:1.4; }
.verif-grade  { display:inline-flex; align-items:center; gap:4px; background:#1d4ed8; color:#fff; font-size:11.5px; font-weight:700; padding:3px 10px; border-radius:4px; margin-top:6px; }

/* Spec table */
.spec-table { width:100%; border-collapse:collapse; font-size:13.5px; }
.spec-table tr { border-bottom:1px solid #f1f5f9; }
.spec-table tr:last-child { border-bottom:none; }
.spec-table td { padding:9px 16px; vertical-align:top; }
.spec-key { color:#64748b; font-weight:500; width:44%; white-space:nowrap; }
.spec-val { color:#1e293b; font-weight:600; }
.spec-val a { color:#1d4ed8; text-decoration:none; font-size:12px; }
.spec-val a:hover { text-decoration:underline; }
.spec-pill { display:inline-flex; align-items:center; gap:3px; background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; border-radius:4px; font-size:11px; font-weight:600; padding:1px 7px; }
.spec-pill.green { background:#f0fdf4; color:#15803d; border-color:#bbf7d0; }
.spec-info { color:#94a3b8; cursor:pointer; font-size:14px; vertical-align:middle; }

/* ── Bidding widget ── */
#ad-bid { grid-area:bid; display:flex; flex-direction:column; gap:14px; }
.bid-widget { padding:20px; }
.bid-label  { font-size:11px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:.06em; margin-bottom:4px; }
.bid-amount { font-size:clamp(32px,4vw,46px); font-weight:800; color:#0f2752; line-height:1; }
.bid-cd-row { background:#f8fafc; border-radius:8px; padding:10px 14px; margin-top:12px; }
.bid-cd-txt { font-size:12px; color:#475569; }
.bid-cd-val { font-size:15px; font-weight:700; color:#0f2752; }
.bid-reserve-txt { font-size:12px; color:#64748b; display:flex; align-items:center; gap:4px; margin-top:5px; }
.bid-radio-row { display:flex; border:1.5px solid #e2e8f0; border-radius:8px; overflow:hidden; margin:14px 0; }
.bid-radio-btn { flex:1; padding:9px 4px; font-size:12.5px; font-weight:600; border:none; background:#fff; color:#475569; cursor:pointer; transition:background .15s,color .15s; display:flex; align-items:center; justify-content:center; gap:5px; }
.bid-radio-btn:not(:last-child) { border-right:1.5px solid #e2e8f0; }
.bid-radio-btn.active { background:#eff6ff; color:#1d4ed8; }
.bid-input-row { display:flex; border:1.5px solid #e2e8f0; border-radius:8px; overflow:hidden; margin-bottom:14px; }
.bid-stepper { width:44px; flex-shrink:0; border:none; background:#f8fafc; font-size:20px; font-weight:600; color:#374151; cursor:pointer; transition:background .15s; display:flex; align-items:center; justify-content:center; }
.bid-stepper:hover { background:#e2e8f0; }
.bid-num-input { flex:1; border:none; text-align:center; font-size:17px; font-weight:700; color:#0f2752; outline:none; min-width:0; background:#fff; }
.btn-bid-primary { width:100%; padding:13px; background:#1d4ed8; color:#fff; border:none; border-radius:8px; font-size:15px; font-weight:700; cursor:pointer; transition:background .15s,transform .1s; letter-spacing:.02em; }
.btn-bid-primary:hover:not(:disabled) { background:#1e40af; transform:translateY(-1px); }
.btn-bid-primary:disabled { opacity:.55; cursor:not-allowed; }
.btn-bid-secondary { width:100%; padding:11px; background:#fff; color:#1d4ed8; border:2px solid #1d4ed8; border-radius:8px; font-size:14px; font-weight:700; cursor:pointer; transition:background .15s; margin-top:8px; }
.btn-bid-secondary:hover { background:#eff6ff; }
.btn-bid-tertiary { width:100%; padding:11px; background:#fff; color:#374151; border:1.5px solid #cbd5e1; border-radius:8px; font-size:14px; font-weight:600; cursor:pointer; transition:background .15s; margin-top:8px; }
.btn-bid-tertiary:hover { background:#f8fafc; }
.bid-foot { border-top:1px solid #f1f5f9; padding-top:14px; margin-top:14px; }
.bid-foot-row { display:flex; justify-content:space-between; align-items:center; padding:5px 0; font-size:12.5px; border-bottom:1px solid #f8fafc; }
.bid-foot-row:last-child { border-bottom:none; }
.bid-foot-key { color:#64748b; }
.bid-foot-val { color:#1e293b; font-weight:600; }
.bid-foot-link { color:#1d4ed8; font-weight:600; text-decoration:none; font-size:12px; }
.bid-foot-link:hover { text-decoration:underline; }
.bid-status-pill { display:inline-flex; align-items:center; gap:4px; font-size:11px; font-weight:700; border-radius:20px; padding:2px 8px; }
.bid-status-pill.green { background:#dcfce7; color:#15803d; }
.bid-status-pill.orange { background:#fef3c7; color:#92400e; }
.bid-disclaimer { font-size:10.5px; color:#94a3b8; line-height:1.5; margin-top:14px; text-align:center; }

/* Sidebar ad */
.sidebar-ad { border-radius:10px; overflow:hidden; background:linear-gradient(135deg,#1e1e2e 0%,#2d1b0e 50%,#1e1e2e 100%); min-height:160px; display:flex; flex-direction:column; justify-content:flex-end; padding:20px; position:relative; }
.sidebar-ad::before { content:''; position:absolute; inset:0; background:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='60' height='60'%3E%3Ccircle cx='30' cy='30' r='28' fill='none' stroke='rgba(255,255,255,.06)' stroke-width='1'/%3E%3C/svg%3E") repeat; opacity:.5; }
.sidebar-ad-year { font-size:46px; font-weight:900; color:rgba(255,255,255,.07); letter-spacing:-2px; position:absolute; top:10px; left:12px; line-height:1; }
.sidebar-ad-tag  { font-size:10px; font-weight:700; color:#dc2626; text-transform:uppercase; letter-spacing:.12em; margin-bottom:4px; position:relative; }
.sidebar-ad-title { font-size:17px; font-weight:800; color:#fff; line-height:1.25; position:relative; margin-bottom:12px; }
.sidebar-ad-btn  { display:inline-flex; align-items:center; gap:6px; background:#fff; color:#1e1e2e; font-size:12px; font-weight:700; padding:8px 18px; border-radius:6px; text-decoration:none; position:relative; width:fit-content; transition:background .15s; }
.sidebar-ad-btn:hover { background:#f0f0f0; }

/* ── Damage diagram ── */
#ad-damage { grid-area:damage; }
.dmg-inner { display:flex; gap:0; min-height:320px; }
.dmg-left  { flex:0 0 260px; padding:20px; border-right:1px solid #f1f5f9; display:flex; flex-direction:column; gap:12px; }
.dmg-right { flex:1; display:flex; align-items:center; justify-content:center; padding:16px; position:relative; }
.dmg-group-label { font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:#94a3b8; margin-bottom:4px; }
.dmg-entry { display:flex; align-items:flex-start; gap:8px; padding:6px 0; border-bottom:1px solid #f8fafc; }
.dmg-entry:last-child { border-bottom:none; }
.dmg-dot { width:10px; height:10px; border-radius:50%; flex-shrink:0; margin-top:3px; }
.dmg-dot.red { background:#dc2626; }
.dmg-dot.amber { background:#f59e0b; }
.dmg-dot.green { background:#22c55e; }
.dmg-text { font-size:12.5px; color:#374151; line-height:1.45; }
.dmg-zone-label { font-size:11px; font-weight:700; color:#64748b; }
.dmg-no-damage { display:flex; align-items:center; gap:6px; font-size:12.5px; color:#15803d; font-weight:600; }
.dmg-section { margin-bottom:8px; }

/* Car SVG */
.car-svg-wrap { position:relative; width:200px; height:400px; }

/* ── Breadcrumbs ── */
.ad-breadcrumb { max-width:1440px; margin:0 auto; padding:14px 24px; display:flex; justify-content:space-between; align-items:center; font-size:12.5px; color:#64748b; border-top:1px solid #e2e8f0; background:#fff; margin-top:4px; }
.ad-bc-trail a  { color:#1d4ed8; text-decoration:none; }
.ad-bc-trail a:hover { text-decoration:underline; }
.ad-bc-trail span { color:#94a3b8; margin:0 5px; }

/* ── Outbid banner ── */
.outbid-banner { max-width:1440px; margin:0 auto 0; padding:0 24px; }

/* ── Bid modal ── */
.bid-modal-overlay { position:fixed; inset:0; z-index:9999; display:flex; align-items:center; justify-content:center; padding:1rem; background:rgba(15,23,42,.55); backdrop-filter:blur(6px); }
.bid-modal-overlay.hidden { display:none !important; }
.bid-modal-card { width:100%; max-width:420px; background:#fff; border-radius:18px; box-shadow:0 25px 60px -12px rgba(0,0,0,.3); padding:2rem; text-align:center; }
.bid-modal-icon { width:64px; height:64px; margin:0 auto 1.25rem; border-radius:50%; display:flex; align-items:center; justify-content:center; }
.bid-modal-icon svg { width:34px; height:34px; }
.bid-modal-icon.success { background:#dcfce7; color:#16a34a; }
.bid-modal-icon.warn    { background:#fef3c7; color:#d97706; }
.bid-modal-title { font-size:1.35rem; font-weight:800; color:#0f172a; margin:0 0 .5rem; }
.bid-modal-msg   { font-size:.95rem; color:#475569; line-height:1.6; margin:0 0 1.5rem; }
.bid-modal-ok    { padding:.75rem 2rem; font-size:1rem; font-weight:700; color:#fff; background:#1d4ed8; border:none; border-radius:10px; cursor:pointer; transition:background .2s; }
.bid-modal-ok:hover { background:#1e40af; }

/* ── Video in main area ── */
.gal-main video {
    position:absolute; inset:0; width:100%; height:100%;
    object-fit:contain; background:#000; display:none; z-index:3;
}

/* ── Video thumbnail in strip ── */
.gal-thumb--video {
    background:#111827; display:flex; align-items:center; justify-content:center;
    flex-direction:column; gap:5px; cursor:pointer;
    border:2px solid transparent; border-radius:4px; aspect-ratio:4/3;
    transition:border-color .15s, transform .15s;
}
.gal-thumb--video:hover { transform:scale(1.03); border-color:#475569; }
.gal-thumb--video.active { border-color:#1d4ed8; box-shadow:0 0 0 2px #bfdbfe; }
.gal-thumb--video .vt-play {
    width:32px; height:32px; background:rgba(255,255,255,.15); border:2px solid rgba(255,255,255,.4);
    border-radius:50%; display:flex; align-items:center; justify-content:center; flex-shrink:0;
}
.gal-thumb--video .vt-label {
    font-size:9px; font-weight:700; color:rgba(255,255,255,.7); letter-spacing:.1em; text-transform:uppercase;
}

/* ── Watchlist animation ── */
@keyframes heartPop { 0%{transform:scale(1)} 35%{transform:scale(1.3)} 60%{transform:scale(.9)} 100%{transform:scale(1)} }
.wl-btn.pop { animation:heartPop .4s ease; }
</style>
@endpush

{{-- ── Outbid Banner ── --}}
<x-ui.outbid-banner :show="$showOutbidBanner" />

{{-- ══════════════════════════════════════════
     1. PAGE HEADER
══════════════════════════════════════════ --}}
<div class="adh-wrap">
    <div class="adh-inner">
        <div class="flex flex-wrap items-start justify-between gap-4">

            {{-- Left: title + meta badges --}}
            <div class="flex-1 min-w-0">
                <h1 class="adh-title">{{ $listingTitle ?: ('Listing #'.$listing->id) }}</h1>

                <div class="adh-meta">
                    {{-- Run & Drive badge --}}
                    @if($listing->run_and_drive)
                    <span class="adh-badge">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        Run and Drive
                    </span>
                    <span class="adh-sep">•</span>
                    @endif

                    {{-- VIN --}}
                    <span>VIN:&nbsp;<span class="font-mono font-semibold text-slate-700" id="vinDisplay">{{ $maskedVin }}</span></span>
                    <button class="adh-copy" title="Copy VIN" onclick="copyText('{{ addslashes($listing->vin ?? $maskedVin) }}','VIN copied!')">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
                    </button>
                    <span class="adh-sep">|</span>

                    {{-- Lot --}}
                    <span>Lot number:&nbsp;<strong class="text-slate-700">{{ $listing->item_number ?? $listing->id }}</strong></span>
                    <span class="adh-sep">|</span>

                    {{-- Lane/Item --}}
                    <span>Lane/Item:&nbsp;<strong class="text-slate-700">-/-</strong></span>
                </div>
            </div>

            {{-- Right: watchlist + sale info --}}
            <div class="flex flex-col items-end gap-2 text-sm flex-shrink-0">
                {{-- Watchlist button --}}
                <form id="wlForm" action="{{ Auth::check() ? route('listing.watchlist', $listing->id) : '#' }}" method="POST" style="display:inline">
                    @csrf
                    <button type="{{ Auth::check() ? 'submit' : 'button' }}"
                            onclick="{{ Auth::check() ? 'submitWatchlist(event)' : "window.location='".route('login')."'" }}"
                            class="wl-btn flex items-center gap-1.5 text-sm font-semibold transition-colors {{ $inWatchlist ? 'text-red-500' : 'text-blue-600 hover:text-blue-800' }}"
                            id="wlBtn">
                        <svg id="wlHeart" width="16" height="16" viewBox="0 0 24 24" fill="{{ $inWatchlist ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                        </svg>
                        <span id="wlText">{{ $inWatchlist ? 'In Watchlist' : 'Watchlist' }}</span>
                    </button>
                </form>

                {{-- Sale name --}}
                <div class="text-right">
                    <span class="text-gray-400 text-xs">Sale Name:&nbsp;</span>
                    <span class="text-blue-600 font-semibold text-sm">{{ strtoupper($listing->seller->name ?? 'CAYMARK AUCTION') }}</span>
                </div>

                {{-- Location --}}
                <div class="text-right">
                    <span class="text-gray-400 text-xs">Location:&nbsp;</span>
                    <span class="text-blue-600 font-semibold text-sm">{{ strtoupper($listing->island ?? 'N/A') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════
     2. THREE-COLUMN GRID
══════════════════════════════════════════ --}}
<div class="ad-grid">

    {{-- ─────────────────────────────────────
         COL A: MEDIA GALLERY
    ───────────────────────────────────────── --}}
    <div class="ad-card" id="ad-media">

        {{-- Main image --}}
        <div class="gal-main" id="galMain">
            <img src="{{ $mainImage }}" alt="{{ $listingTitle }}" id="galMainImg"
                 onerror="this.src='{{ asset('images/placeholder.png') }}'"/>
            @if($videoUrl)
            <video id="galMainVideo" controls preload="metadata"
                   style="display:none;position:absolute;inset:0;width:100%;height:100%;object-fit:contain;background:#000;z-index:3;">
                <source src="{{ $videoUrl }}" type="video/mp4">
                Your browser does not support HTML5 video.
            </video>
            @endif

            {{-- Nav arrows --}}
            <button class="gal-arrow left" onclick="galNav(-1)" aria-label="Previous photo">&#8249;</button>
            <button class="gal-arrow right" onclick="galNav(1)" aria-label="Next photo">&#8250;</button>

            {{-- Bottom-left: HD toggle + download --}}
            <div class="gal-badges-bl">
                <span class="gal-hd">
                    <span>HD</span>
                    <span class="gal-hd-toggle" title="Toggle HD"></span>
                </span>
                <a href="{{ $mainImage }}" download class="gal-dl" title="Download photo">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                </a>
            </div>

            {{-- Bottom-right: damage link + counter --}}
            <div class="gal-badges-br">
                <button class="gal-pill" onclick="document.getElementById('ad-damage').scrollIntoView({behavior:'smooth'})">
                    ⚠ View damage
                </button>
                <span class="gal-pill" id="galCounter">
                    1 / {{ $totalMedia ?: 1 }}
                </span>
            </div>
        </div>

        {{-- Thumbnail grid --}}
        <div class="gal-thumbs" id="galThumbs">
            @forelse($images as $i => $img)
            <div class="gal-thumb {{ $i === 0 ? 'active' : '' }}" onclick="galSelect({{ $i }})" data-src="{{ $img }}">
                <img src="{{ $img }}" alt="Photo {{ $i+1 }}" loading="lazy"
                     onerror="this.src='{{ asset('images/placeholder.png') }}'"/>
            </div>
            @empty
            <div class="gal-thumb active">
                <img src="{{ asset('images/placeholder.png') }}" alt="No photo"/>
            </div>
            @endforelse
            @if($videoUrl)
            <div class="gal-thumb gal-thumb--video {{ $images->isEmpty() ? 'active' : '' }}"
                 onclick="galSelect({{ $images->count() }})">
                <div class="vt-play">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="rgba(255,255,255,0.9)"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                </div>
                <span class="vt-label">VIDEO</span>
            </div>
            @endif
        </div>
    </div>

    {{-- ─────────────────────────────────────
         COL B: SPECS & CONDITION
    ───────────────────────────────────────── --}}
    <div id="ad-specs">

        {{-- Verification badges card --}}
        <div class="ad-card">
            <div class="verif-card">
                {{-- Engine starts --}}
                <div class="verif-item">
                    <div class="verif-icon {{ $listing->engine_starts ? 'green' : 'blue' }}">
                        @if($listing->engine_starts)
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        @else
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#1d4ed8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        @endif
                    </div>
                    <div class="verif-label">Engine starts</div>
                    <div class="verif-sub">{{ $listing->engine_starts ? 'Verified by inspection' : 'Not confirmed' }}</div>
                </div>

                {{-- Transmission --}}
                <div class="verif-item">
                    <div class="verif-icon {{ $listing->run_and_drive ? 'green' : 'blue' }}">
                        @if($listing->run_and_drive)
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        @else
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#1d4ed8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14"/></svg>
                        @endif
                    </div>
                    <div class="verif-label">Transmission</div>
                    <div class="verif-sub">{{ $listing->run_and_drive ? 'Runs & drives' : 'Status unverified' }}</div>
                </div>

                {{-- Condition report + grade --}}
                <div class="verif-item">
                    <div class="verif-icon blue">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#1d4ed8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                    </div>
                    <a href="#" class="verif-label text-blue-600 hover:underline" style="color:#1d4ed8">View Condition Report</a>
                    <span class="verif-grade">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="currentColor"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                        Auto3rade 4.6
                    </span>
                </div>
            </div>
        </div>

        {{-- Technical spec table card --}}
        <div class="ad-card">
            <table class="spec-table">
                <tbody>
                    <tr>
                        <td class="spec-key">Title code</td>
                        <td class="spec-val">
                            <span class="spec-pill">{{ $listing->title_status_display ?? 'Clean Title' }}</span>
                            <span class="spec-info" title="Title information">ⓘ</span>
                            @if(!$listing->has_title)
                            <div style="margin-top:4px"><span class="spec-pill" style="background:#fef2f2;color:#dc2626;border-color:#fecaca;">☐ Title Absent</span></div>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="spec-key">Odometer</td>
                        <td class="spec-val">
                            {{ $listing->odometer ? number_format($listing->odometer).' mi '.($listing->odometer_estimated ? 'Estimated' : 'Actual') : 'N/A' }}
                            <span class="spec-info" title="Odometer reading">ⓘ</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="spec-key">Est. retail value</td>
                        <td class="spec-val">
                            @if($listing->estimated_retail_value)
                                ${{ number_format($listing->estimated_retail_value, 2) }}
                            @else
                                —
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="spec-key">Has key</td>
                        <td class="spec-val">
                            <span class="spec-pill {{ $listing->keys_available ? 'green' : '' }}">
                                {{ $listing->keys_available ? '✓ Yes' : '✗ No' }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="spec-key">Engine type</td>
                        <td class="spec-val">
                            {{ $listing->engine_type ?: 'N/A' }}
                            @if($listing->video_path)
                            <br>
                            <a href="{{ asset('uploads/listings/'.$listing->video_path) }}" target="_blank">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/><path d="M15.54 8.46a5 5 0 0 1 0 7.07"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14"/></svg>
                                Listen to engine
                            </a>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="spec-key">Transmission</td>
                        <td class="spec-val">{{ $listing->transmission ? ucfirst($listing->transmission) : 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="spec-key">Drivetrain</td>
                        <td class="spec-val">{{ $listing->drive_type ?: ($listing->drive_train ?: 'N/A') }}</td>
                    </tr>
                    <tr>
                        <td class="spec-key">Fuel</td>
                        <td class="spec-val">{{ $listing->fuel_type ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="spec-key">Color</td>
                        <td class="spec-val">{{ $listing->color ? ucwords(strtolower($listing->color)) : 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="spec-key">Sale date</td>
                        <td class="spec-val" style="display:flex;align-items:center;gap:6px;flex-wrap:wrap">
                            <span>{{ $endDate->format('D. M j, Y g:i A T') }}</span>
                            <a href="#" onclick="addToCalendar(event)" title="Add to calendar" style="color:#94a3b8;display:flex;align-items:center">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            </a>
                        </td>
                    </tr>
                    @if($listing->notes || $listing->description)
                    <tr>
                        <td class="spec-key" style="vertical-align:top;padding-top:11px">Notes</td>
                        <td class="spec-val" style="color:#475569;font-weight:400;line-height:1.55;font-size:12.5px">
                            {{ $listing->notes ?? $listing->description }}
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    {{-- ─────────────────────────────────────
         COL C: BIDDING WIDGET + SIDEBAR AD
    ───────────────────────────────────────── --}}
    <div id="ad-bid">

        {{-- Bidding widget --}}
        <div class="ad-card bid-widget" style="position:sticky;top:9.5rem;">

            {{-- Auction ended banner --}}
            @if($isExpired)
            <div style="background:#fee2e2;border-radius:8px;padding:10px 14px;margin-bottom:14px;display:flex;align-items:center;gap:8px">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <span style="font-size:13px;font-weight:700;color:#991b1b">This auction has ended</span>
            </div>
            @endif

            {{-- Current bid --}}
            <div class="bid-label">Current bid</div>
            <div class="bid-amount">${{ number_format($currentBid) }}</div>

            {{-- Status pills (winning / outbid) --}}
            @if(Auth::check())
            <div style="margin-top:8px">
                @if($isWinning)
                <span class="bid-status-pill green">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    You're winning
                </span>
                @elseif($isOutbid || $userHighestBid)
                <span class="bid-status-pill orange">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    Outbid
                </span>
                @endif
            </div>
            @endif

            {{-- Countdown --}}
            <div class="bid-cd-row">
                <div class="bid-cd-txt">Auction countdown</div>
                @if(!$isExpired && $timeRemaining)
                <div class="bid-cd-val" id="bidCountdown">
                    {{ $cdDays }}D {{ $cdHours }}H {{ $cdMins }}min
                </div>
                <x-ui.countdown :end="$endDate" :listing-id="$listing->id" variant="inline" />
                @else
                <div class="bid-cd-val" style="color:#dc2626">Ended</div>
                @endif

                <div class="bid-reserve-txt">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    @if(!$listing->reserve_price)
                        No reserve price set
                    @elseif($reserveMet)
                        Seller reserve met
                    @else
                        Minimum bid: Seller reserve not yet met
                    @endif
                </div>
            </div>

            @if(!$isExpired)
            {{-- Bid type radio --}}
            <div class="bid-radio-row">
                <button type="button" class="bid-radio-btn active" id="radioMaxBid" onclick="setBidType('max')">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
                    Max bid
                </button>
                <button type="button" class="bid-radio-btn" id="radioMonster" onclick="setBidType('monster')">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
                    Monster bid
                </button>
            </div>

            {{-- Bid amount stepper --}}
            @if(Auth::check() && Auth::user()->role === 'buyer')
            @php $missingReqs = Auth::user()->getMissingBidRequirements() ?? []; @endphp
            @if(count($missingReqs) > 0)
            <div style="background:#fff7ed;border:1.5px solid #fb923c;border-radius:10px;padding:14px;margin-bottom:12px">
                <div style="display:flex;align-items:center;gap:6px;margin-bottom:6px">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#ea580c" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                    <span style="font-size:13px;font-weight:700;color:#ea580c">Profile Incomplete</span>
                </div>
                <ul style="margin:0 0 10px;padding:0;list-style:none">
                    @foreach($missingReqs as $req)
                    <li style="font-size:12px;color:#78350f;display:flex;align-items:center;gap:5px;margin-bottom:3px">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                        {{ $req }}
                    </li>
                    @endforeach
                </ul>
                <a href="{{ route('buyer.user') }}" style="font-size:12px;font-weight:700;color:#ea580c;text-decoration:underline">Complete Profile →</a>
            </div>
            @else
            {{-- Stepper + bid form --}}
            <form action="{{ route('auction.bid.store', $listing->getSlugOrGenerate()) }}" method="POST" id="bidForm" data-cm-validate="off">
                @csrf
                <div class="bid-input-row">
                    <button type="button" class="bid-stepper" onclick="adjustBid(-{{ $incrementAmount }})" aria-label="Decrease">−</button>
                    <input type="number" name="amount" id="bidAmount" class="bid-num-input"
                           value="{{ $nextValidBid }}" min="{{ $nextValidBid }}" step="{{ $incrementAmount }}"/>
                    <button type="button" class="bid-stepper" onclick="adjustBid({{ $incrementAmount }})" aria-label="Increase">+</button>
                </div>

                {{-- Primary: Bid now --}}
                <button type="submit" class="btn-bid-primary" id="bidSubmitBtn">
                    Bid now
                </button>
            </form>
            @endif
            @elseif(!Auth::check())
            <div class="bid-input-row" style="pointer-events:none;opacity:.6">
                <button type="button" class="bid-stepper">−</button>
                <span class="bid-num-input">${{ number_format($nextValidBid) }}</span>
                <button type="button" class="bid-stepper">+</button>
            </div>
            <a href="{{ route('login') }}" class="btn-bid-primary" style="display:block;text-align:center;text-decoration:none">
                Login to bid
            </a>
            @endif

            {{-- Secondary: Buy it now --}}
            @if($listing->buy_it_now_price)
            <button type="button" class="btn-bid-secondary">
                Buy it now (${{ number_format($listing->buy_it_now_price) }})
            </button>
            @endif

            {{-- Tertiary: Make an offer --}}
            <button type="button" class="btn-bid-tertiary">
                Make an offer
            </button>
            @endif{{-- !isExpired --}}

            {{-- Footer estimates --}}
            <div class="bid-foot">
                <div class="bid-foot-row">
                    <span class="bid-foot-key">Shipping estimate</span>
                    <a href="#" class="bid-foot-link">Check estimate</a>
                </div>
                <div class="bid-foot-row">
                    <span class="bid-foot-key">Bidding increment</span>
                    <span class="bid-foot-val">${{ number_format($incrementAmount) }}</span>
                </div>
                <div class="bid-foot-row">
                    <span class="bid-foot-key">Eligibility</span>
                    <span>
                        @if(Auth::check() && Auth::user()->role === 'buyer' && empty($missingReqs ?? []))
                        <span class="bid-status-pill green">
                            <span style="width:7px;height:7px;border-radius:50%;background:currentColor;display:inline-block"></span>
                            Eligible
                        </span>
                        @else
                        <a href="{{ Auth::check() ? route('buyer.user') : route('login') }}" class="bid-foot-link">Check now →</a>
                        @endif
                    </span>
                </div>
                <div class="bid-foot-row">
                    <span class="bid-foot-key">Buyer fee</span>
                    <span class="bid-foot-val">${{ number_format($buyerFeePreview['commission'] ?? 0, 2) }}</span>
                </div>
            </div>

            <p class="bid-disclaimer">
                ALL SALES ARE FINAL. SOLD "AS IS, WHERE IS." CayMark is not responsible for condition, completeness, genuineness, accuracy or existence of the lot.
            </p>
        </div>

        {{-- ── Sidebar ad banner ── --}}
        <div class="sidebar-ad">
            <div class="sidebar-ad-year">25</div>
            <div class="sidebar-ad-tag">Anniversary Sale</div>
            <div class="sidebar-ad-title">25 YEARS OF<br>CAYMARK AUCTIONS</div>
            <a href="{{ route('Auction.index') }}" class="sidebar-ad-btn">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                View Inventory
            </a>
        </div>
    </div>

    {{-- ─────────────────────────────────────
         BOTTOM: EXTERIOR CONDITION
    ───────────────────────────────────────── --}}
    <div class="ad-card" id="ad-damage">
        <div style="padding:18px 20px;border-bottom:1px solid #f1f5f9">
            <h2 style="font-size:16px;font-weight:800;color:#0f2752;margin:0">Exterior condition</h2>
        </div>
        <div class="dmg-inner">

            {{-- Left: damage list --}}
            <div class="dmg-left">
                @if($listing->primary_damage || $listing->secondary_damage)

                    @if($listing->primary_damage)
                    <div class="dmg-section">
                        <div class="dmg-group-label">Primary damage</div>
                        <div class="dmg-entry">
                            <span class="dmg-dot amber"></span>
                            <div>
                                <div class="dmg-zone-label">Front</div>
                                <div class="dmg-text">{{ $listing->primary_damage }}</div>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($listing->secondary_damage)
                    <div class="dmg-section">
                        <div class="dmg-group-label">Secondary damage</div>
                        <div class="dmg-entry">
                            <span class="dmg-dot amber"></span>
                            <div>
                                <div class="dmg-zone-label">Other</div>
                                <div class="dmg-text">{{ $listing->secondary_damage }}</div>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Green "no damage" zones --}}
                    @foreach(['Front Left','Front Right','Rear Left','Rear Right'] as $zone)
                    <div class="dmg-entry">
                        <span class="dmg-dot green"></span>
                        <div>
                            <div class="dmg-zone-label">{{ $zone }}</div>
                            <div class="dmg-no-damage" style="font-size:12px">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                No highlight(s)
                            </div>
                        </div>
                    </div>
                    @endforeach

                @else
                    {{-- All zones clean --}}
                    @foreach(['Front Left','Front Right','Rear Left','Rear Right','Front','Rear'] as $zone)
                    <div class="dmg-entry">
                        <span class="dmg-dot green"></span>
                        <div>
                            <div class="dmg-zone-label">{{ $zone }}</div>
                            <div class="dmg-no-damage" style="font-size:12px">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                No highlight(s)
                            </div>
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>

            {{-- Right: car diagram SVG --}}
            <div class="dmg-right">
                <div class="car-svg-wrap">
                    <svg viewBox="0 0 200 400" xmlns="http://www.w3.org/2000/svg" width="200" height="400">
                        <defs>
                            <filter id="carShadow"><feDropShadow dx="0" dy="2" stdDeviation="3" flood-color="#00000020"/></filter>
                        </defs>

                        {{-- Main body --}}
                        <rect x="34" y="60" width="132" height="280" rx="22" fill="#dce4f0" stroke="#b0bfda" stroke-width="1.5" filter="url(#carShadow)"/>

                        {{-- Roof/cabin --}}
                        <rect x="42" y="118" width="116" height="164" rx="8" fill="#c8d4e8" stroke="#9fb0cc" stroke-width="1"/>

                        {{-- Front windscreen --}}
                        <path d="M46,118 Q100,98 154,118" fill="none" stroke="#9fb0cc" stroke-width="1.5"/>

                        {{-- Rear windscreen --}}
                        <path d="M46,282 Q100,302 154,282" fill="none" stroke="#9fb0cc" stroke-width="1.5"/>

                        {{-- Front bumper --}}
                        <rect x="40" y="42" width="120" height="22" rx="11" fill="#c8d4e8" stroke="#9fb0cc" stroke-width="1.5"/>

                        {{-- Rear bumper --}}
                        <rect x="40" y="336" width="120" height="22" rx="11" fill="#c8d4e8" stroke="#9fb0cc" stroke-width="1.5"/>

                        {{-- Headlights --}}
                        <rect x="40" y="58" width="30" height="14" rx="4" fill="#fffde7" stroke="#e0c840" stroke-width="1"/>
                        <rect x="130" y="58" width="30" height="14" rx="4" fill="#fffde7" stroke="#e0c840" stroke-width="1"/>

                        {{-- Taillights --}}
                        <rect x="40" y="328" width="30" height="14" rx="4" fill="#fee2e2" stroke="#dc2626" stroke-width="1"/>
                        <rect x="130" y="328" width="30" height="14" rx="4" fill="#fee2e2" stroke="#dc2626" stroke-width="1"/>

                        {{-- Front-left wheel --}}
                        <rect x="12" y="96" width="28" height="54" rx="7" fill="#708090" stroke="#4a5568" stroke-width="1.5"/>
                        <rect x="16" y="101" width="20" height="44" rx="5" fill="#8898a8"/>

                        {{-- Front-right wheel --}}
                        <rect x="160" y="96" width="28" height="54" rx="7" fill="#708090" stroke="#4a5568" stroke-width="1.5"/>
                        <rect x="164" y="101" width="20" height="44" rx="5" fill="#8898a8"/>

                        {{-- Rear-left wheel --}}
                        <rect x="12" y="250" width="28" height="54" rx="7" fill="#708090" stroke="#4a5568" stroke-width="1.5"/>
                        <rect x="16" y="255" width="20" height="44" rx="5" fill="#8898a8"/>

                        {{-- Rear-right wheel --}}
                        <rect x="160" y="250" width="28" height="54" rx="7" fill="#708090" stroke="#4a5568" stroke-width="1.5"/>
                        <rect x="164" y="255" width="20" height="44" rx="5" fill="#8898a8"/>

                        {{-- Door dividers --}}
                        <line x1="34" y1="190" x2="166" y2="190" stroke="#9fb0cc" stroke-width="1" stroke-dasharray="4,3"/>
                        <line x1="34" y1="220" x2="166" y2="220" stroke="#9fb0cc" stroke-width="1" stroke-dasharray="4,3"/>

                        {{-- Damage indicator dots --}}
                        @if($listing->primary_damage)
                        {{-- Front damage dot --}}
                        <circle cx="100" cy="68" r="9" fill="#f59e0b" stroke="#fff" stroke-width="2"/>
                        <text x="100" y="72" text-anchor="middle" font-size="10" font-weight="700" fill="#fff">!</text>
                        {{-- Tooltip label --}}
                        <rect x="58" y="78" width="84" height="18" rx="4" fill="#f59e0b"/>
                        <text x="100" y="91" text-anchor="middle" font-size="9" fill="#fff" font-weight="600">1 highlight(s)</text>
                        @endif

                        @if($listing->secondary_damage)
                        {{-- Rear damage dot --}}
                        <circle cx="142" cy="295" r="9" fill="#1d4ed8" stroke="#fff" stroke-width="2"/>
                        <text x="142" y="299" text-anchor="middle" font-size="10" font-weight="700" fill="#fff">!</text>
                        <rect x="100" y="306" width="84" height="18" rx="4" fill="#1d4ed8"/>
                        <text x="142" y="319" text-anchor="middle" font-size="9" fill="#fff" font-weight="600">2 highlight(s)</text>
                        @endif

                        {{-- Green dots for undamaged zones --}}
                        @if(!$listing->primary_damage)
                        <circle cx="100" cy="68" r="7" fill="#22c55e" stroke="#fff" stroke-width="2" opacity=".9"/>
                        @endif
                        @if(!$listing->secondary_damage)
                        <circle cx="142" cy="295" r="7" fill="#22c55e" stroke="#fff" stroke-width="2" opacity=".9"/>
                        @endif
                        <circle cx="58"  cy="295" r="7" fill="#22c55e" stroke="#fff" stroke-width="2" opacity=".9"/>
                        <circle cx="58"  cy="150" r="7" fill="#22c55e" stroke="#fff" stroke-width="2" opacity=".9"/>
                        <circle cx="142" cy="150" r="7" fill="#22c55e" stroke="#fff" stroke-width="2" opacity=".9"/>
                    </svg>

                    {{-- Legend --}}
                    <div style="display:flex;gap:16px;justify-content:center;margin-top:12px;font-size:11px;color:#64748b">
                        <span style="display:flex;align-items:center;gap:4px">
                            <span style="width:10px;height:10px;border-radius:50%;background:#22c55e;display:inline-block"></span>No damage
                        </span>
                        <span style="display:flex;align-items:center;gap:4px">
                            <span style="width:10px;height:10px;border-radius:50%;background:#f59e0b;display:inline-block"></span>Primary
                        </span>
                        <span style="display:flex;align-items:center;gap:4px">
                            <span style="width:10px;height:10px;border-radius:50%;background:#1d4ed8;display:inline-block"></span>Secondary
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>{{-- /ad-grid --}}

{{-- ─────────────────────────────────────
     BREADCRUMBS + TIMESTAMP
───────────────────────────────────────── --}}
<div class="ad-breadcrumb ad-bc-trail">
    <nav aria-label="Breadcrumb">
        <a href="{{ route('welcome') }}">Home</a>
        <span>›</span>
        <a href="{{ route('Auction.index') }}?makes[]={{ urlencode($listing->make ?? '') }}">{{ ucfirst(strtolower($listing->make ?? 'Auctions')) }}</a>
        <span>›</span>
        <span style="color:#1e293b;font-weight:600">{{ $listingTitle }}</span>
    </nav>
    <span style="color:#94a3b8">Last Updated: {{ now()->format('m/d/Y g:i a') }}</span>
</div>

{{-- ─────────────────────────────────────
     BID RESULT MODAL
───────────────────────────────────────── --}}
<div id="bidModal" class="bid-modal-overlay hidden" role="dialog" aria-modal="true">
    <div class="bid-modal-card">
        <div id="bidModalIcon" class="bid-modal-icon success">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
        </div>
        <h3 id="bidModalTitle" class="bid-modal-title">Success</h3>
        <p id="bidModalMsg" class="bid-modal-msg"></p>
        <button id="bidModalOk" class="bid-modal-ok">OK</button>
    </div>
</div>

{{-- ─────────────────────────────────────
     SCRIPTS
───────────────────────────────────────── --}}
@push('scripts')
<script>
window.CaymarkUIAuctionConfig = {
    buyerFeeRate: {{ \App\Services\CommissionService::BUYER_COMMISSION_RATE }},
    buyerFeeMin:  {{ \App\Services\CommissionService::BUYER_COMMISSION_MIN  }},
};

/* ── Gallery ── */
var galImages     = @json($images->values()->toArray());
var galVideoUrl   = @json($videoUrl);
var galVideoIndex = {{ $videoIndex }};
var galTotalMedia = {{ $totalMedia ?: 1 }};
var galIndex      = 0;

function galSelect(i) {
    if (galTotalMedia === 0) return;
    galIndex = ((i % galTotalMedia) + galTotalMedia) % galTotalMedia;
    var img   = document.getElementById('galMainImg');
    var video = document.getElementById('galMainVideo');
    var isVideo = (galVideoUrl && galIndex === galVideoIndex);

    if (isVideo) {
        if (img)   { img.style.display = 'none'; }
        if (video) {
            video.style.display = 'block';
            if (video.querySelector('source').getAttribute('src') !== galVideoUrl) {
                video.querySelector('source').setAttribute('src', galVideoUrl);
                video.load();
            }
        }
    } else {
        if (video) { video.pause(); video.style.display = 'none'; }
        if (img) {
            img.style.display = 'block';
            img.style.opacity = '.4';
            setTimeout(function(){ img.src = galImages[galIndex]; img.style.opacity = '1'; }, 150);
        }
    }

    document.getElementById('galCounter').textContent = (galIndex + 1) + ' / ' + galTotalMedia;
    document.querySelectorAll('.gal-thumb').forEach(function(t, j) { t.classList.toggle('active', j === galIndex); });
    var activeThumb = document.querySelector('.gal-thumb.active');
    if (activeThumb) activeThumb.scrollIntoView({ block: 'nearest', inline: 'nearest' });
}
function galNav(dir) { galSelect(galIndex + dir); }

/* ── Bid stepper ── */
function adjustBid(delta) {
    var input = document.getElementById('bidAmount');
    if (!input) return;
    var cur = parseFloat(input.value) || {{ $nextValidBid }};
    var min = {{ $nextValidBid }};
    input.value = Math.max(min, cur + delta);
}

/* ── Bid type radio ── */
function setBidType(type) {
    document.getElementById('radioMaxBid').classList.toggle('active', type==='max');
    document.getElementById('radioMonster').classList.toggle('active', type==='monster');
}

/* ── Bid modal ── */
function showBidModal(msg, isSuccess, onClose) {
    var modal = document.getElementById('bidModal');
    var icon  = document.getElementById('bidModalIcon');
    var title = document.getElementById('bidModalTitle');
    var msgEl = document.getElementById('bidModalMsg');
    if (!modal) return;
    msgEl.textContent = msg||'';
    title.textContent = isSuccess ? 'Success' : 'Notice';
    icon.className = 'bid-modal-icon '+(isSuccess?'success':'warn');
    icon.innerHTML = isSuccess
        ? '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>'
        : '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>';
    modal.classList.remove('hidden');
    document.body.style.overflow='hidden';
    function close(){ modal.classList.add('hidden'); document.body.style.overflow=''; if(typeof onClose==='function') onClose(); }
    document.getElementById('bidModalOk').onclick = close;
}

/* ── Bid form submit ── */
document.addEventListener('DOMContentLoaded', function(){
    var form = document.getElementById('bidForm');
    if (!form) return;
    form.addEventListener('submit', function(e){
        e.preventDefault();
        var amount  = parseFloat(document.getElementById('bidAmount').value);
        var minBid  = {{ $nextValidBid }};
        var vehicle = @json(trim(($listing->year??'').' '.($listing->make??'').' '.($listing->model??'')));
        if (amount < minBid){
            if(window.CaymarkUI) CaymarkUI.showError('Bid too low','Minimum bid is $'+minBid.toLocaleString());
            else showBidModal('Minimum bid is $'+minBid.toLocaleString(),false);
            return;
        }
        var submitBtn = document.getElementById('bidSubmitBtn');
        function placeBid(){
            submitBtn.disabled=true; submitBtn.textContent='Placing…';
            fetch(form.action,{
                method:'POST',
                headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Content-Type':'application/json','Accept':'application/json'},
                body:JSON.stringify({amount:amount})
            })
            .then(r=>r.json())
            .then(function(data){
                if(data.success){ showBidModal(data.message||'Bid placed!',true,function(){ location.reload(); }); }
                else { showBidModal(data.message||'Failed to place bid',false); submitBtn.disabled=false; submitBtn.textContent='Bid now'; }
            })
            .catch(function(){ showBidModal('An error occurred. Please try again.',false); submitBtn.disabled=false; submitBtn.textContent='Bid now'; });
        }
        var confirmFn = (window.CaymarkUI&&CaymarkUI.auction&&CaymarkUI.auction.confirmBid)
            ? CaymarkUI.auction.confirmBid({vehicleName:vehicle,amount:amount,buyerFee:CaymarkUI.auction.calcBuyerFee(amount)})
            : Promise.resolve(window.confirm('Place a bid of $'+amount.toLocaleString()+' on '+vehicle+'?'));
        confirmFn.then(function(ok){ if(ok) placeBid(); });
    });
});

/* ── Watchlist ── */
function submitWatchlist(e){
    e.preventDefault();
    var btn  = document.getElementById('wlBtn');
    var heart= document.getElementById('wlHeart');
    var text = document.getElementById('wlText');
    btn.classList.add('pop');
    setTimeout(function(){ btn.classList.remove('pop'); }, 500);
    fetch('{{ Auth::check() ? route("listing.watchlist", $listing->id) : "#" }}',{
        method:'POST',
        headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json','X-Requested-With':'XMLHttpRequest'},
    })
    .then(r=>r.json())
    .then(function(data){
        var added = data.added||data.in_watchlist||false;
        heart.setAttribute('fill', added?'currentColor':'none');
        btn.style.color = added?'#ef4444':'#1d4ed8';
        text.textContent= added?'In Watchlist':'Watchlist';
    })
    .catch(function(){});
}

/* ── Copy text ── */
function copyText(val, label){
    if(navigator.clipboard){ navigator.clipboard.writeText(val).then(function(){ if(window.CaymarkUI) CaymarkUI.showSuccess(label||'Copied!',''); }); }
}

/* ── Calendar ── */
function addToCalendar(e){
    e.preventDefault();
    var title = @json($listingTitle.' - CayMark Auction');
    var end   = '{{ $endDate->format('Ymd\THis\Z') }}';
    var url   = window.location.href;
    var gcUrl = 'https://calendar.google.com/calendar/render?action=TEMPLATE&text='+encodeURIComponent(title)+'&dates='+end+'/'+end+'&details='+encodeURIComponent(url);
    window.open(gcUrl,'_blank');
}
</script>
@endpush

@endsection
