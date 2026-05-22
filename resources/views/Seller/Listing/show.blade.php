@extends('layouts.Seller')

@section('title', 'Listing – ' . ($listing->year ?? '') . ' ' . ($listing->make ?? '') . ' ' . ($listing->model ?? ''))

@section('content')
@php
    // ── Image URL helper ──────────────────────────────────────
    // Files are stored in public/uploads/listings/ as bare filenames.
    $resolveImgUrl = function ($img) {
        $path = is_object($img) ? ($img->image_path ?? null) : $img;
        if (!$path) return null;
        if (str_starts_with($path, 'http')) return $path;
        // Already contains a directory separator → use as-is
        if (str_contains($path, '/')) return asset($path);
        // Bare filename → prepend storage folder
        return asset('uploads/listings/' . $path);
    };

    // Use coverPhoto first, then fall back to images collection
    $coverPhotoModel = $listing->coverPhoto ?? $listing->images->first();
    $allImages = collect($listing->images ?? []);
    // Put cover first if it exists and isn't already in the collection
    if ($coverPhotoModel && !$allImages->contains('id', $coverPhotoModel->id ?? null)) {
        $allImages->prepend($coverPhotoModel);
    }
    $images    = $allImages->map(fn($img) => $resolveImgUrl($img))->filter()->values();
    $mainImage = $images->first() ?? null;

    // ── Damage display labels ─────────────────────────────────
    $damageLabels = config('listing_damage_types.allowed', []);
    $fmtDamage = fn($key) => $key ? ($damageLabels[$key] ?? ucwords(strtolower(str_replace('_', ' ', $key)))) : null;

    // ── Auction timing ────────────────────────────────────────
    $startDate = $listing->auction_start_time ?? null;
    $endDate   = $listing->auction_end_time
        ?? ($startDate ? \Carbon\Carbon::parse($startDate)->addDays($listing->auction_duration ?? 7) : null);
    $isExpired = $endDate ? $endDate->isPast() : false;

    // ── Bid info ──────────────────────────────────────────────
    $highestBid = $listing->bids()->where('status', 'active')->orderByDesc('amount')->first();
    $currentBid = $highestBid ? (float) $highestBid->amount : null;

    // ── Highlights fields ─────────────────────────────────────
    $yesNo = fn($val) => match(strtolower((string)$val)) { 'yes', '1', 'true' => 'Yes', 'no', '0', 'false' => 'No', default => 'N/A' };
@endphp

<style>
.slv-page    { background: #f1f5f9; min-height: 100vh; padding: 1.75rem 0 3rem; }
.slv-card    { background: #fff; border-radius: 14px; padding: 24px; box-shadow: 0 2px 10px rgba(0,0,0,.07); margin-bottom: 20px; }
.slv-title   { font-size: 1.1rem; font-weight: 700; color: #1e293b; margin-bottom: 1rem; padding-bottom: .6rem; border-bottom: 2px solid #e2e8f0; display: flex; align-items: center; gap: .5rem; }
.info-grid   { display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: .875rem; }
.info-item   .lbl { font-size: 11px; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 3px; }
.info-item   .val { font-size: 15px; font-weight: 600; color: #1e293b; }
.dmg-pill    { display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; border-radius: 8px; font-size: .875rem; font-weight: 600; }
.thumb       { width: 68px; height: 52px; border-radius: 6px; overflow: hidden; cursor: pointer; border: 2px solid transparent; flex-shrink: 0; }
.thumb:hover, .thumb.active { border-color: #2563eb; }
.thumb img   { width: 100%; height: 100%; object-fit: cover; }
.bid-row     { display: flex; align-items: baseline; justify-content: space-between; border-bottom: 1px solid #f1f5f9; padding: .5rem 0; }
.bid-row:last-child { border-bottom: none; }
.bid-lbl     { font-size: .8125rem; color: #64748b; font-weight: 500; }
.bid-val     { font-size: 1rem; font-weight: 700; color: #1e293b; }
</style>

<div class="slv-page">
  <div class="container mx-auto px-4" style="max-width: 1140px;">

    {{-- Breadcrumb --}}
    <nav class="text-sm mb-5 text-gray-500 flex flex-wrap items-center gap-x-1.5">
        <a href="{{ route('seller.dashboard') }}" class="hover:text-blue-600 transition">Dashboard</a>
        <span>/</span>
        <a href="{{ route('seller.auctions') }}" class="hover:text-blue-600 transition">My Auctions</a>
        <span>/</span>
        <span class="text-gray-800 font-semibold truncate">{{ $listing->year }} {{ $listing->make }} {{ $listing->model }}</span>
    </nav>

    {{-- Title + View Public Page button --}}
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 leading-tight">
                {{ $listing->year }} {{ $listing->make }} {{ $listing->model }}{{ $listing->trim ? ' ' . $listing->trim : '' }}
            </h1>
            <p class="text-sm text-gray-500 mt-1">
                Item #{{ $listing->item_number ?? 'CM' . str_pad($listing->id, 6, '0', STR_PAD_LEFT) }}
                &nbsp;·&nbsp;
                <span class="font-semibold {{ $listing->status === 'approved' ? 'text-emerald-600' : ($listing->status === 'pending' ? 'text-amber-600' : 'text-gray-600') }}">
                    {{ ucfirst($listing->status ?? 'N/A') }}
                </span>
            </p>
        </div>
        @if($listing->slug)
            <a href="{{ route('auction.show', $listing) }}" target="_blank"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-xl hover:bg-blue-700 shadow-sm transition whitespace-nowrap">
                <span class="material-icons text-lg">open_in_new</span>
                View Public Page
            </a>
        @endif
    </div>

    {{-- Analytics strip --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <div class="slv-card !p-4 text-center !mb-0">
            <p class="text-2xl font-extrabold text-gray-900">{{ number_format($viewCount ?? 0) }}</p>
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 mt-1">Views</p>
        </div>
        <div class="slv-card !p-4 text-center !mb-0">
            <p class="text-2xl font-extrabold text-gray-900">{{ number_format($totalBids ?? 0) }}</p>
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 mt-1">Total Bids</p>
        </div>
        <div class="slv-card !p-4 text-center !mb-0">
            <p class="text-2xl font-extrabold {{ $isExpired ? 'text-red-500' : 'text-emerald-600' }}">
                @if(!$isExpired && $endDate)
                    @php $diff = now()->diff($endDate); @endphp
                    @if($diff->days > 0) {{ $diff->days }}d {{ $diff->h }}h
                    @elseif($diff->h > 0) {{ $diff->h }}h {{ $diff->i }}m
                    @else {{ $diff->i }}m {{ $diff->s }}s @endif
                @else
                    Ended
                @endif
            </p>
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 mt-1">Time Remaining</p>
        </div>
        <div class="slv-card !p-4 text-center !mb-0">
            <p class="text-2xl font-extrabold text-blue-600">
                {{ $currentBid ? '$' . number_format($currentBid, 0) : '—' }}
            </p>
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 mt-1">Current Bid</p>
        </div>
    </div>

    {{-- Main layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-5">

      {{-- ── Left column ──────────────────────────────────── --}}
      <div class="lg:col-span-3 space-y-5">

        {{-- Photos --}}
        <div class="slv-card">
            <h2 class="slv-title"><span class="material-icons-round text-blue-500">photo_library</span>Photos</h2>
            @if($images->count() > 0)
                <img src="{{ $mainImage }}" alt="Main photo" id="mainPhoto"
                     class="w-full rounded-xl object-cover" style="max-height:420px">
                <div class="flex flex-wrap gap-2 mt-3">
                    @foreach($images as $i => $url)
                        <div class="thumb {{ $i === 0 ? 'active' : '' }}" onclick="changePhoto('{{ $url }}',{{ $i }})" data-i="{{ $i }}">
                            <img src="{{ $url }}" alt="Photo {{ $i+1 }}" loading="lazy">
                        </div>
                    @endforeach
                </div>
                <p class="text-xs text-gray-400 mt-2">{{ $images->count() }} photo(s) uploaded</p>
            @else
                <div class="text-center py-12 bg-gray-50 rounded-xl">
                    <span class="material-icons-round text-gray-300" style="font-size:48px">image_not_supported</span>
                    <p class="text-gray-500 mt-2 text-sm">No photos uploaded</p>
                </div>
            @endif
        </div>

        {{-- Highlights --}}
        <div class="slv-card">
            <h2 class="slv-title"><span class="material-icons-round text-purple-500">star</span>Highlights</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                <div class="bg-slate-50 rounded-xl p-3 flex flex-col gap-1">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Runs & Drives</span>
                    <span class="font-bold text-slate-800">{{ $yesNo($listing->run_and_drive) }}</span>
                </div>
                <div class="bg-slate-50 rounded-xl p-3 flex flex-col gap-1">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Engine Starts</span>
                    <span class="font-bold text-slate-800">{{ $yesNo($listing->engine_starts) }}</span>
                </div>
                <div class="bg-slate-50 rounded-xl p-3 flex flex-col gap-1">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Has Keys</span>
                    <span class="font-bold text-slate-800">{{ $listing->keys_available ? 'Yes' : 'No' }}</span>
                </div>
                <div class="bg-slate-50 rounded-xl p-3 flex flex-col gap-1">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Title Status</span>
                    <span class="font-bold text-slate-800">{{ $listing->title_status_display ?? ucfirst($listing->title_status ?? 'N/A') }}</span>
                </div>
                <div class="bg-slate-50 rounded-xl p-3 flex flex-col gap-1">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Condition</span>
                    <span class="font-bold text-slate-800">{{ ucfirst($listing->condition ?? 'N/A') }}</span>
                </div>
                <div class="bg-slate-50 rounded-xl p-3 flex flex-col gap-1">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Odometer</span>
                    <span class="font-bold text-slate-800">
                        @if($listing->odometer)
                            {{ number_format($listing->odometer) }} km{{ $listing->odometer_estimated ? '*' : '' }}
                        @else
                            N/A
                        @endif
                    </span>
                </div>
            </div>
        </div>

        {{-- Damage Information --}}
        <div class="slv-card">
            <h2 class="slv-title"><span class="material-icons-round text-amber-500">warning</span>Damage Information</h2>
            @php
                $pd = $fmtDamage($listing->primary_damage);
                $sd = $fmtDamage($listing->secondary_damage);
            @endphp
            @if($pd || $sd)
                <div class="space-y-3">
                    @if($pd)
                        <div class="flex items-start gap-3 bg-amber-50 border border-amber-200 rounded-xl p-3">
                            <span class="material-icons-round text-amber-500 flex-shrink-0" style="font-size:20px">report_problem</span>
                            <div>
                                <p class="text-xs font-semibold text-amber-700 uppercase tracking-wide mb-0.5">Primary Damage</p>
                                <p class="font-bold text-amber-900 text-sm">{{ $pd }}</p>
                            </div>
                        </div>
                    @endif
                    @if($sd && strtoupper($listing->secondary_damage) !== 'NONE')
                        <div class="flex items-start gap-3 bg-orange-50 border border-orange-200 rounded-xl p-3">
                            <span class="material-icons-round text-orange-400 flex-shrink-0" style="font-size:20px">info</span>
                            <div>
                                <p class="text-xs font-semibold text-orange-600 uppercase tracking-wide mb-0.5">Secondary Damage</p>
                                <p class="font-bold text-orange-800 text-sm">{{ $sd }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            @else
                <p class="text-sm text-gray-500">No damage reported.</p>
            @endif
        </div>

        {{-- Additional Notes --}}
        @if($listing->additional_notes)
        <div class="slv-card">
            <h2 class="slv-title"><span class="material-icons-round text-gray-400">notes</span>Additional Notes</h2>
            <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $listing->additional_notes }}</p>
        </div>
        @endif

      </div>{{-- end left --}}

      {{-- ── Right column ─────────────────────────────────── --}}
      <div class="lg:col-span-2 space-y-5">

        {{-- Bid Information --}}
        <div class="slv-card border-2 border-blue-100">
            <h2 class="slv-title"><span class="material-icons-round text-blue-500">gavel</span>Bid Information</h2>
            <div class="space-y-0">
                <div class="bid-row">
                    <span class="bid-lbl">Current Bid</span>
                    <span class="bid-val text-blue-600">{{ $currentBid ? '$' . number_format($currentBid, 2) : 'No bids yet' }}</span>
                </div>
                <div class="bid-row">
                    <span class="bid-lbl">Starting Bid</span>
                    <span class="bid-val">${{ number_format($listing->starting_price ?? 0, 2) }}</span>
                </div>
                <div class="bid-row">
                    <span class="bid-lbl">Reserve Price</span>
                    <span class="bid-val">{{ $listing->reserve_price ? '$' . number_format($listing->reserve_price, 2) : '—' }}</span>
                </div>
                <div class="bid-row">
                    <span class="bid-lbl">Buy Now Price</span>
                    <span class="bid-val text-emerald-600">{{ $listing->buy_now_price ? '$' . number_format($listing->buy_now_price, 2) : '—' }}</span>
                </div>
                <div class="bid-row">
                    <span class="bid-lbl">Sale Date (Started)</span>
                    <span class="bid-val text-sm">{{ $startDate ? \Carbon\Carbon::parse($startDate)->format('M d, Y') : 'Not started' }}</span>
                </div>
                <div class="bid-row">
                    <span class="bid-lbl">Auction Ends</span>
                    <span class="bid-val text-sm {{ $isExpired ? 'text-red-500' : '' }}">
                        {{ $endDate ? \Carbon\Carbon::parse($endDate)->format('M d, Y g:i A') : '—' }}
                        @if($isExpired) <span class="text-xs font-normal">(ended)</span> @endif
                    </span>
                </div>
            </div>
        </div>

        {{-- Vehicle Information --}}
        <div class="slv-card">
            <h2 class="slv-title"><span class="material-icons-round text-slate-500">directions_car</span>Vehicle Information</h2>
            <div class="info-grid">
                <div class="info-item"><p class="lbl">Type</p><p class="val">{{ $listing->vehicle_type ?? $listing->major_category ?? 'N/A' }}</p></div>
                <div class="info-item"><p class="lbl">Year</p><p class="val">{{ $listing->year ?? 'N/A' }}</p></div>
                <div class="info-item"><p class="lbl">Make</p><p class="val">{{ $listing->make ?? 'N/A' }}</p></div>
                <div class="info-item"><p class="lbl">Model</p><p class="val">{{ $listing->model ?? 'N/A' }}</p></div>
                <div class="info-item"><p class="lbl">Trim</p><p class="val">{{ $listing->trim ?? 'N/A' }}</p></div>
                <div class="info-item"><p class="lbl">Body Style</p><p class="val">{{ $listing->body_style ?? $listing->subcategory ?? 'N/A' }}</p></div>
                <div class="info-item"><p class="lbl">Exterior Color</p><p class="val">{{ $listing->color ?? 'N/A' }}</p></div>
                <div class="info-item"><p class="lbl">Interior Color</p><p class="val">{{ $listing->interior_color ?? 'N/A' }}</p></div>
                <div class="info-item"><p class="lbl">Engine</p><p class="val">{{ $listing->engine_type ?? 'N/A' }}</p></div>
                <div class="info-item"><p class="lbl">Cylinders</p><p class="val">{{ $listing->cylinders ?? 'N/A' }}</p></div>
                <div class="info-item"><p class="lbl">Transmission</p><p class="val">{{ $listing->transmission ? ucfirst($listing->transmission) : 'N/A' }}</p></div>
                <div class="info-item"><p class="lbl">Drive Type</p><p class="val">{{ $listing->drive_type ?? 'N/A' }}</p></div>
                <div class="info-item"><p class="lbl">Fuel Type</p><p class="val">{{ $listing->fuel_type ?? 'N/A' }}</p></div>
                <div class="info-item"><p class="lbl">VIN / HIN</p><p class="val font-mono text-sm">{{ $listing->maskedVinOrHin() }}</p></div>
            </div>
        </div>

        {{-- More Information --}}
        <div class="slv-card">
            <h2 class="slv-title"><span class="material-icons-round text-slate-400">info</span>More Information</h2>
            <div class="info-grid">
                <div class="info-item">
                    <p class="lbl">Location (Island)</p>
                    <p class="val">{{ $listing->island ?? 'N/A' }}</p>
                </div>
                @if($listing->item_number)
                <div class="info-item">
                    <p class="lbl">Item Number</p>
                    <p class="val">{{ $listing->item_number }}</p>
                </div>
                @endif
                <div class="info-item">
                    <p class="lbl">Listing Method</p>
                    <p class="val">{{ ucfirst($listing->listing_method ?? 'Auction') }}</p>
                </div>
                <div class="info-item">
                    <p class="lbl">Duration</p>
                    <p class="val">{{ $listing->auction_duration ? $listing->auction_duration . ' days' : 'N/A' }}</p>
                </div>
            </div>
        </div>

        {{-- Delete button (no edit) --}}
        @if(!in_array($listing->status, ['sold']))
        <form method="POST" action="{{ route('seller.listings.destroy', $listing->id) }}"
              onsubmit="return confirm('Are you sure you want to remove this listing? This cannot be undone.')">
            @csrf
            @method('DELETE')
            <button type="submit"
                    class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-red-50 border border-red-200 text-red-700 text-sm font-semibold rounded-xl hover:bg-red-100 transition">
                <span class="material-icons-round text-lg">delete_outline</span>
                Remove Listing
            </button>
        </form>
        @endif

      </div>{{-- end right --}}
    </div>{{-- end grid --}}

  </div>{{-- end container --}}
</div>

@if($images->count() > 0)
<script>
function changePhoto(src, idx) {
    document.getElementById('mainPhoto').src = src;
    document.querySelectorAll('.thumb').forEach(function(el) {
        el.classList.toggle('active', +el.dataset.i === idx);
    });
}
</script>
@endif
@endsection
