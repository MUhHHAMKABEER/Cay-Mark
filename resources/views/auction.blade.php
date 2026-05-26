@extends('layouts.welcome')
@section('content')

<style>
    /* ── Vehicle finder sidebar ── */
    .vf-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: #44474f; margin-bottom: .35rem; display: block; }
    .vf-input {
        width: 100%; padding: .5rem .75rem; border: 1px solid #c4c6d0; border-radius: .375rem;
        font-size: .8125rem; background: #fff; color: #1c1b1b; transition: border-color .15s, box-shadow .15s;
    }
    .vf-input:focus { outline: none; border-color: #002452; box-shadow: 0 0 0 3px rgba(0,36,82,.12); }
    .vf-input:hover:not(:focus) { border-color: #747780; }
    .vf-row { margin-bottom: 1.1rem; }
    .seg-ctrl { display: flex; border: 1px solid #c4c6d0; border-radius: .375rem; overflow: hidden; }
    .seg-ctrl button { flex: 1; padding: .45rem .5rem; font-size: .7rem; font-weight: 700; border: none; background: #fff; color: #44474f; cursor: pointer; transition: background .15s, color .15s; }
    .seg-ctrl button:not(:last-child) { border-right: 1px solid #c4c6d0; }
    .seg-ctrl button.active { background: #002452; color: #fff; }
    .seg-ctrl button:hover:not(.active) { background: #f0eded; color: #1c1b1b; }
    /* Range slider */
    input[type="range"].odo-range { appearance: none; -webkit-appearance: none; width: 100%; height: 5px; background: #e5e2e1; border-radius: 3px; outline: none; }
    input[type="range"].odo-range::-webkit-slider-thumb { -webkit-appearance: none; width: 16px; height: 16px; background: #002452; border-radius: 50%; cursor: pointer; box-shadow: 0 1px 4px rgba(0,36,82,.35); }
    input[type="range"].odo-range::-moz-range-thumb { width: 16px; height: 16px; background: #002452; border-radius: 50%; cursor: pointer; border: none; }
    .year-row { display: flex; gap: .4rem; align-items: center; }
    .year-row select { flex: 1; }
    .year-sep { font-size: .8rem; color: #747780; }
    /* Checkbox lists */
    .vf-checkbox-list { display: flex; flex-direction: column; gap: .25rem; max-height: 110px; overflow-y: auto; padding-right: 2px; }
    .vf-checkbox-list::-webkit-scrollbar { width: 3px; }
    .vf-checkbox-list::-webkit-scrollbar-thumb { background: #c4c6d0; border-radius: 2px; }
    .vf-check-item { display: flex; align-items: center; gap: .45rem; cursor: pointer; padding: .15rem 0; }
    .vf-check-item span { font-size: .8rem; color: #1c1b1b; line-height: 1.3; }
    .vf-check-item:hover span { color: #002452; }
    .vf-checkbox { width: 14px; height: 14px; border-radius: 3px; cursor: pointer; accent-color: #002452; flex-shrink: 0; }
    /* Field wrapper with clear button */
    .vf-field-wrap { position: relative; display: flex; align-items: center; }
    .vf-field-wrap .vf-input { flex: 1; padding-right: 2rem; }
    .vf-clear-btn { position: absolute; right: .4rem; top: 50%; transform: translateY(-50%); display: flex; align-items: center; justify-content: center; width: 20px; height: 20px; border: none; background: #e5e2e1; border-radius: 50%; cursor: pointer; color: #44474f; padding: 0; transition: background .15s, color .15s; }
    .vf-clear-btn:hover { background: #002452; color: #fff; }
    .vf-clear-btn .material-symbols-outlined { font-size: 13px; line-height: 1; }
    /* Action buttons */
    .vf-btn-search { flex: 1; display: flex; align-items: center; justify-content: center; gap: .35rem; padding: .6rem .5rem; font-size: .8rem; font-weight: 700; border: none; cursor: pointer; background: #e5c363; color: #002452; border-radius: .375rem; transition: background .15s; }
    .vf-btn-search:hover { background: #d4b356; }
    .vf-btn-reset { padding: .6rem .75rem; font-size: .8rem; font-weight: 600; border: 1px solid #c4c6d0; background: #fff; color: #44474f; border-radius: .375rem; cursor: pointer; transition: background .15s; white-space: nowrap; }
    .vf-btn-reset:hover { background: #f0eded; }
    /* Vehicle cards */
    .vehicle-card { transition: transform .2s ease, box-shadow .2s ease; }
    .vehicle-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,36,82,.1); }
    /* Skeleton shimmer */
    .shimmer { background: linear-gradient(90deg, #f0eded 25%, #e5e2e1 50%, #f0eded 75%); background-size: 200% 100%; animation: shimmer 1.5s infinite; }
    @keyframes shimmer { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }
    /* Image hover overlay */
    .img-wrap { position: relative; overflow: hidden; }
    .img-overlay { position: absolute; inset: 0; background: rgba(0,36,82,.4); opacity: 0; transition: opacity .2s; display: flex; align-items: center; justify-content: center; }
    .img-wrap:hover .img-overlay { opacity: 1; }
    /* Cursor blink */
    .cursor-blink { animation: cblink .8s step-end infinite; }
    @keyframes cblink { 50% { opacity: 0; } }
    /* Sidebar scrollbar */
    .filter-scroll::-webkit-scrollbar { width: 4px; }
    .filter-scroll::-webkit-scrollbar-track { background: #f6f3f2; }
    .filter-scroll::-webkit-scrollbar-thumb { background: #c4c6d0; border-radius: 2px; }
    /* ── New card styles ── */
    .cm-img-wrap { overflow: hidden; }
    .cm-img-wrap img { transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1); }
    .cm-card:hover .cm-img-wrap img { transform: scale(1.04); }
    .btn-bid {
        background-color: #D99B16;
        background-image: linear-gradient(135deg, #E5A820 0%, #C78A0E 100%);
        color: #fff;
        font-weight: 800;
        letter-spacing: 0.07em;
        text-transform: uppercase;
        transition: filter 0.15s ease, transform 0.1s ease;
    }
    .btn-bid:hover  { filter: brightness(1.08); }
    .btn-bid:active { transform: scale(0.97); }
    .spec-label {
        font-size: 8.5px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: #94a3b8;
        display: block;
    }
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>

<div class="bg-surface" id="cm-auction-pull-root" x-data="filterData()" x-init="initFilters()" @cm-pull-refresh.window="applyFilters()">

    {{-- ── Full-width page header ──────────────────────────────────── --}}
    <div class="bg-primary px-4 sm:px-6 lg:px-10 py-7">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <p class="text-[10px] font-bold text-secondary-fixed-dim uppercase tracking-[.3em] mb-1">CayMark Exchange</p>
                <h1 class="font-headline-lg text-headline-lg text-on-primary leading-none">VEHICLE AUCTIONS</h1>
            </div>
            <div class="inline-flex items-center gap-2 bg-white/10 border border-white/20 rounded-lg px-3.5 py-2 self-start sm:self-auto">
                <span class="material-symbols-outlined text-secondary-fixed-dim text-[17px]">analytics</span>
                <span class="text-on-primary text-label-sm font-label-sm">
                    <span class="results-count sr-only" data-full-text="Showing {{ $auctions->firstItem() ?? 0 }}–{{ $auctions->lastItem() ?? 0 }} of {{ $auctions->total() }}"></span>
                    <span id="results-count-typed"></span><span id="results-count-cursor" class="cursor-blink text-secondary-fixed-dim">|</span>
                </span>
            </div>
        </div>
    </div>

    {{-- ── Two-column body ─────────────────────────────────────────── --}}
    <div class="flex gap-5 px-4 sm:px-6 lg:px-8 py-5" style="min-height:calc(100vh - 5rem); align-items:flex-start">

        {{-- ══ LEFT: Vehicle Finder sidebar ════════════════════════════ --}}
        <aside class="hidden lg:flex flex-col flex-shrink-0 bg-white border border-outline-variant rounded-xl overflow-hidden shadow-sm sticky top-[9.5rem]"
               style="width:260px; max-height:calc(100vh - 10.5rem)">

            {{-- Sidebar header --}}
            <div class="bg-primary px-5 py-4 flex items-center justify-between flex-shrink-0">
                <span class="text-on-primary font-label-md text-label-md uppercase tracking-widest">Vehicle Finder</span>
                <span class="material-symbols-outlined text-secondary-fixed-dim text-[20px]">tune</span>
            </div>

            {{-- Scrollable filter body --}}
            <div class="flex-1 overflow-y-auto filter-scroll p-4" style="overscroll-behavior:contain">
                @include('partials.auction-vehicle-finder-fields')
            </div>
        </aside>

        {{-- ══ RIGHT: Listings area ════════════════════════════════════ --}}
        <div class="flex-1 min-w-0 flex flex-col gap-4">

            {{-- Controls bar --}}
            <div class="flex flex-wrap items-center justify-between gap-3 px-4 py-3 border border-outline-variant bg-white rounded-xl shadow-sm">
                <div class="flex flex-wrap items-center gap-2">
                    {{-- View toggle --}}
                    <div class="inline-flex items-center rounded-lg border border-slate-200 bg-white overflow-hidden p-0.5 gap-0.5">
                        <button type="button"
                            @click="viewMode = 'grid'"
                            :class="viewMode === 'grid' ? 'bg-slate-800 text-white' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-100'"
                            class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-md text-[11.5px] font-bold uppercase tracking-wide transition-all duration-150">
                            <svg width="14" height="14" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
                                <rect x="1" y="1" width="6" height="6" rx="1"/><rect x="9" y="1" width="6" height="6" rx="1"/>
                                <rect x="1" y="9" width="6" height="6" rx="1"/><rect x="9" y="9" width="6" height="6" rx="1"/>
                            </svg>
                            <span class="hidden sm:inline">Grid</span>
                        </button>
                        <button type="button"
                            @click="viewMode = 'detail'"
                            :class="viewMode === 'detail' ? 'bg-slate-800 text-white' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-100'"
                            class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-md text-[11.5px] font-bold uppercase tracking-wide transition-all duration-150">
                            <svg width="14" height="14" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
                                <rect x="1" y="2" width="14" height="2.5" rx="1"/><rect x="1" y="6.75" width="14" height="2.5" rx="1"/><rect x="1" y="11.5" width="14" height="2.5" rx="1"/>
                            </svg>
                            <span class="hidden sm:inline">List</span>
                        </button>
                    </div>

                    {{-- Active filters pill --}}
                    <div x-show="activeFilters" x-transition
                        class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-secondary-container text-on-secondary-container rounded-lg text-label-sm font-label-sm">
                        <span class="material-symbols-outlined text-[13px]">filter_alt</span>
                        Filters active
                        <button type="button" @click="clearAllFilters()" class="ml-0.5 opacity-70 hover:opacity-100">
                            <span class="material-symbols-outlined text-[13px]">close</span>
                        </button>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    {{-- Sort --}}
                    <span class="text-label-sm font-label-sm text-on-surface-variant whitespace-nowrap hidden sm:inline">Sort by</span>
                    <div class="relative" x-data="{ sortOpen: false }" @click.outside="sortOpen = false">
                        <button type="button"
                            @click="sortOpen = !sortOpen"
                            class="inline-flex items-center gap-2 border border-outline-variant bg-white rounded-lg py-1.5 pl-3 pr-9 text-label-sm font-label-sm text-on-surface min-w-[160px] hover:border-outline transition-colors relative">
                            <span x-text="sortBy === 'newest' ? 'Newest First' : sortBy === 'oldest' ? 'Oldest First' : sortBy === 'price_low' ? 'Price: Low → High' : 'Price: High → Low'">Newest First</span>
                            <span class="material-symbols-outlined absolute right-2 top-1/2 -translate-y-1/2 text-outline text-[18px] transition-transform" :class="sortOpen && 'rotate-180'">expand_more</span>
                        </button>
                        <div x-show="sortOpen" x-cloak x-transition
                            class="absolute right-0 top-full mt-1 z-50 bg-white border border-outline-variant rounded-lg shadow-lg py-1 min-w-full" style="display:none">
                            @foreach([['newest','Newest First'],['oldest','Oldest First'],['price_low','Price: Low → High'],['price_high','Price: High → Low']] as [$val,$label])
                            <button type="button"
                                @click="sortBy = '{{ $val }}'; applyFilters(); sortOpen = false"
                                :class="sortBy === '{{ $val }}' ? 'bg-surface-container-low text-primary' : 'text-on-surface hover:bg-surface-container'"
                                class="flex w-full items-center justify-between px-4 py-2 text-left text-label-sm font-label-sm transition-colors">
                                <span>{{ $label }}</span>
                                <span x-show="sortBy === '{{ $val }}'" class="material-symbols-outlined text-secondary-fixed-dim text-[15px]">check</span>
                            </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Mobile filters button --}}
                    <button type="button"
                        class="lg:hidden cm-mobile-filters-btn inline-flex items-center gap-1.5 bg-primary text-on-primary px-3 py-1.5 rounded-lg text-label-md font-label-md hover:bg-primary-container transition-colors"
                        aria-controls="cm-auction-filter-sheet"
                        aria-expanded="false"
                        data-cm-open-sheet="cm-auction-filter-sheet">
                        <span class="material-symbols-outlined text-[17px]">tune</span>
                        Filters
                    </button>
                </div>
            </div>

            {{-- Mobile filter sheet --}}
            <x-ui.filter-bottom-sheet id="cm-auction-filter-sheet" title="Vehicle Finder">
                <div class="p-4">
                    @include('partials.auction-vehicle-finder-fields')
                </div>
            </x-ui.filter-bottom-sheet>

            {{-- Listings --}}
            <div class="flex-1">

                {{-- Grid skeleton --}}
                <div x-show="isLoading && viewMode === 'grid'" x-cloak
                    class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-5">
                    @for($i = 0; $i < 8; $i++)
                    <div class="bg-white border border-gray-200 flex flex-col" style="border-radius:0">
                        <div class="shimmer" style="height:200px"></div>
                        <div class="shimmer" style="height:52px"></div>
                        <div class="p-4 grid grid-cols-2 gap-3 flex-1">
                            @for($j = 0; $j < 4; $j++)
                            <div class="space-y-1.5">
                                <div class="h-2 shimmer" style="width:55%"></div>
                                <div class="h-3 shimmer" style="width:75%"></div>
                            </div>
                            @endfor
                        </div>
                        <div class="border-t border-gray-100 px-4 py-3 flex items-center justify-between">
                            <div class="space-y-1.5">
                                <div class="h-2 shimmer" style="width:56px"></div>
                                <div class="h-5 shimmer" style="width:72px"></div>
                            </div>
                            <div class="h-9 shimmer" style="width:84px"></div>
                        </div>
                    </div>
                    @endfor
                </div>

                {{-- List skeleton --}}
                <div x-show="isLoading && viewMode === 'detail'" x-cloak class="flex flex-col gap-5">
                    @for($i = 0; $i < 5; $i++)
                    <div class="bg-white border border-gray-200 flex flex-col md:flex-row" style="border-radius:0">
                        <div class="shimmer flex-shrink-0" style="height:220px;width:100%"></div>
                        <div class="flex-1 flex flex-col">
                            <div class="shimmer" style="height:60px"></div>
                            <div class="px-5 py-4 grid grid-cols-2 sm:grid-cols-4 gap-4 border-b border-gray-100">
                                @for($j = 0; $j < 4; $j++)
                                <div class="space-y-1.5">
                                    <div class="h-2 shimmer" style="width:60%"></div>
                                    <div class="h-3 shimmer" style="width:80%"></div>
                                </div>
                                @endfor
                            </div>
                            <div class="px-5 py-3 flex gap-2 border-b border-gray-100">
                                <div class="h-6 shimmer" style="width:88px"></div>
                                <div class="h-6 shimmer" style="width:72px"></div>
                            </div>
                            <div class="mt-auto px-5 py-4 flex items-center justify-between gap-3">
                                <div class="space-y-1.5">
                                    <div class="h-2 shimmer" style="width:64px"></div>
                                    <div class="h-6 shimmer" style="width:88px"></div>
                                </div>
                                <div class="flex gap-2">
                                    <div class="h-10 shimmer" style="width:100px"></div>
                                    <div class="h-10 shimmer" style="width:40px"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endfor
                </div>

                {{-- Grid view --}}
                <div x-show="!isLoading && viewMode === 'grid'" x-cloak
                    class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-5">
                    @forelse($auctions as $listing)
                        @php
                            $img = $listing->images->first();
                            if (!$img) {
                                $imgUrl = asset('images/placeholder-car.png');
                            } else {
                                $p = $img->image_path ?? '';
                                $imgUrl = str_starts_with($p,'http') ? $p
                                    : (str_contains($p,'/') ? asset(ltrim($p,'/'))
                                    : (str_starts_with($p,'listings/') ? asset('storage/'.$p)
                                    : asset('uploads/listings/'.$p)));
                            }
                            $endDate = $listing->getAuctionEndDate();
                        @endphp
                        <article
                            class="cm-card bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden
                                   flex flex-col hover:shadow-md hover:border-gray-200/80 transition-shadow duration-200">

                            {{-- Image — 16:9 aspect ratio --}}
                            <div class="cm-img-wrap relative w-full bg-gray-100 flex-none" style="padding-top:56.25%">
                                <img
                                    src="{{ $imgUrl }}"
                                    alt="{{ $listing->year }} {{ $listing->make }} {{ $listing->model }}"
                                    class="absolute inset-0 w-full h-full object-cover"
                                    loading="lazy"
                                    onerror="this.onerror=null;this.src='{{ asset('images/placeholder-car.png') }}';">

                                {{-- Badges top-left --}}
                                <div class="absolute top-0 left-0 flex flex-col z-10">
                                    @if($listing->featured)
                                        <span class="bg-secondary-fixed-dim text-primary text-[9px] font-bold px-2.5 py-1 uppercase tracking-widest">Featured</span>
                                    @endif
                                    <x-ui.ending-soon-badge :end="$endDate" />
                                </div>

                                {{-- Watchlist top-right --}}
                                <div class="absolute top-2.5 right-2.5 z-10">
                                    <x-ui.watchlist-heart
                                        :listing="$listing"
                                        :in-watchlist="$likedListingIds->contains($listing->id)"
                                        :likes-count="$listing->likes_count ?? 0"/>
                                </div>

                                {{-- Countdown bottom --}}
                                <div class="absolute bottom-0 left-0 right-0 z-10">
                                    <x-ui.countdown :end="$endDate" :listing-id="$listing->id" variant="grid" />
                                </div>
                            </div>{{-- /image --}}

                            {{-- Card body --}}
                            <div class="p-4 flex flex-col flex-1 min-w-0">

                                {{-- Title + Location --}}
                                <div class="mb-2.5">
                                    <h3 class="font-bold text-slate-900 text-[14.5px] leading-snug line-clamp-2">
                                        <a href="{{ route('auction.show', $listing->getSlugOrGenerate()) }}"
                                           class="hover:text-blue-700 transition-colors duration-150">
                                            {{ $listing->year }} {{ $listing->make }} {{ $listing->model }}
                                        </a>
                                    </h3>
                                    @if($listing->island)
                                    <div class="flex items-center gap-1 mt-[5px]">
                                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none"
                                             stroke="#94a3b8" stroke-width="2.5"
                                             stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0 1 18 0z"/>
                                            <circle cx="12" cy="10" r="3"/>
                                        </svg>
                                        <span class="text-[11px] text-slate-400 font-medium">{{ $listing->island }}</span>
                                    </div>
                                    @endif
                                </div>

                                {{-- Spec tags --}}
                                @if($listing->fuel_type || $listing->transmission)
                                <div class="flex flex-wrap gap-1.5 mb-3">
                                    @if($listing->fuel_type)
                                    <span class="inline-flex items-center gap-[4px] bg-gray-100 text-slate-600
                                                 text-[10.5px] font-semibold px-2.5 py-[4px] rounded-full whitespace-nowrap">
                                        @if(strtolower($listing->fuel_type) === 'electric')
                                        <svg width="9" height="9" viewBox="0 0 24 24" fill="none"
                                             stroke="currentColor" stroke-width="2.5"
                                             stroke-linecap="round" stroke-linejoin="round">
                                            <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
                                        </svg>
                                        @else
                                        <svg width="9" height="9" viewBox="0 0 24 24" fill="none"
                                             stroke="currentColor" stroke-width="2.2"
                                             stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M3 22V9a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v13"/>
                                            <path d="M3 14h14"/><path d="M9 22v-4a3 3 0 0 1 6 0v4"/>
                                        </svg>
                                        @endif
                                        {{ ucfirst(strtolower($listing->fuel_type)) }}
                                    </span>
                                    @endif
                                    @if($listing->transmission)
                                    <span class="inline-flex items-center gap-[4px] bg-gray-100 text-slate-600
                                                 text-[10.5px] font-semibold px-2.5 py-[4px] rounded-full whitespace-nowrap">
                                        {{ ucfirst(strtolower($listing->transmission)) }}
                                    </span>
                                    @endif
                                </div>
                                @endif

                                {{-- 2 × 2 Specs Grid --}}
                                <div class="grid grid-cols-2 gap-x-4 gap-y-3 pb-3.5 mb-3.5 border-b border-gray-100">
                                    <div>
                                        <span class="spec-label mb-[3px]">Odometer</span>
                                        <p class="text-[13px] font-bold text-slate-800">{{ $listing->odometer ? number_format($listing->odometer).' mi' : 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <span class="spec-label mb-[3px]">Condition</span>
                                        <p class="text-[13px] font-bold text-slate-800">{{ ucfirst($listing->condition ?? 'N/A') }}</p>
                                    </div>
                                    <div>
                                        <span class="spec-label mb-[3px]">Title</span>
                                        <p class="text-[13px] font-bold text-slate-800">{{ $listing->title_status_display ?? 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <span class="spec-label mb-[3px]">Sale Date</span>
                                        <p class="text-[13px] font-bold text-slate-800">
                                            {{ $listing->sale_date ? \Carbon\Carbon::parse($listing->sale_date)->format('M d, Y') : 'N/A' }}
                                        </p>
                                    </div>
                                </div>

                                {{-- Bottom: Bid + Action --}}
                                <div class="flex items-end justify-between mt-auto gap-3">
                                    <div class="min-w-0">
                                        <p class="text-[22px] font-extrabold text-slate-900 leading-none">
                                            ${{ number_format($listing->current_bid ?? 0) }}
                                        </p>
                                        <span class="spec-label mt-[5px]">Current Bid</span>
                                    </div>
                                    <a href="{{ route('auction.show', $listing->getSlugOrGenerate()) }}"
                                       class="btn-bid flex-none inline-flex items-center justify-center
                                              px-4 py-2.5 rounded-lg text-[12px]
                                              focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-1 focus-visible:ring-yellow-500">
                                        Bid Now
                                    </a>
                                </div>

                            </div>{{-- /card body --}}
                        </article>{{-- /grid card --}}
                    @empty
                        <div class="col-span-full py-20 text-center">
                            <span class="material-symbols-outlined text-gray-300 text-[60px] mb-3 block">search_off</span>
                            <h3 class="text-xl font-bold text-primary uppercase tracking-tight mb-2">No listings found</h3>
                            <p class="text-gray-500 text-sm mb-5">Try adjusting your filters to find more results.</p>
                            <button type="button" @click="clearAllFilters()"
                                class="bg-primary text-white font-bold uppercase tracking-widest text-sm px-6 py-3 hover:bg-[#003377] transition-colors"
                                style="border-radius:0">
                                Clear Filters
                            </button>
                        </div>
                    @endforelse
                </div>

                {{-- List view --}}
                <div x-show="!isLoading && viewMode === 'detail'" x-cloak class="flex flex-col gap-4" id="auctionListings">
                    @include('partials.auction-listings', ['auctions' => $auctions])
                </div>

                {{-- Pagination --}}
                <div class="mt-8 flex justify-center" id="auctionPagination">
                    @include('partials.auction-pagination', ['auctions' => $auctions])
                </div>

            </div>{{-- /listings --}}
        </div>{{-- /right --}}
    </div>{{-- /two-column --}}
</div>{{-- /root --}}

{{-- Image modal --}}
<div id="imageModal" class="fixed inset-0 z-[999] bg-black/80 hidden items-center justify-center p-4 opacity-0 transition-opacity duration-300">
    <button onclick="closeImageModal()" class="absolute top-4 right-4 text-white/70 hover:text-white transition-colors">
        <span class="material-symbols-outlined text-[36px]">close</span>
    </button>
    <img id="modalImage" src="" alt="Vehicle photo" class="max-w-full max-h-[90vh] object-contain rounded-xl shadow-2xl"/>
</div>

<script>
function filterData() {
    return {
        isLoading: false,
        viewMode: 'detail',
        activeFilters: false,
        searchQuery: @json(request('search', '')),
        selectedFilters: { vehicle_type: '{{ request('vehicle_type', '') }}' },
        titleCondition: '{{ request('title_condition') ? (is_array(request('title_condition')) ? request('title_condition')[0] : request('title_condition')) : '' }}',
        condition: '{{ request('condition', '') }}',
        makeSingle: @json(is_array(request('makes')) ? (request('makes')[0] ?? '') : (request('makes') ?? '')),
        modelSingle: @json(is_array(request('models')) ? (request('models')[0] ?? '') : (request('models') ?? '')),
        locationSingle: @json(is_array(request('location')) ? (request('location')[0] ?? '') : (request('location') ?? '')),
        fuelTypeMulti: @json(request('fuel_type', [])),
        colorMulti: @json(request('colors', [])),
        damageTypeMulti: @json(request('damage_type', [])),
        yearFrom: {{ request('year_from', 1990) }},
        yearTo: {{ request('year_to', date('Y') + 1) }},
        odometerMin: 0,
        odometerMax: {{ request('odometer_max', $filterOptions['odometer_max'] ?? 250000) }},
        sortBy: '{{ request('sort', 'newest') }}',
        allMakes: @json($filterOptions['makes']),
        allModels: @json($filterOptions['models']),

        checkActiveFilters() {
            this.activeFilters = !!(
                this.titleCondition || this.condition ||
                this.selectedFilters.vehicle_type ||
                this.makeSingle || this.modelSingle || this.locationSingle ||
                (this.fuelTypeMulti && this.fuelTypeMulti.length) ||
                (this.colorMulti && this.colorMulti.length) ||
                (this.damageTypeMulti && this.damageTypeMulti.length) ||
                this.yearFrom > 1990 || this.yearTo < {{ date('Y') + 1 }} ||
                this.odometerMax < {{ $filterOptions['odometer_max'] ?? 250000 }}
            );
        },

        initFilters() {
            if (typeof localStorage !== 'undefined' && localStorage.getItem('auctionViewMode')) {
                this.viewMode = localStorage.getItem('auctionViewMode');
            }
            this.$watch('viewMode', value => {
                this.isLoading = true;
                if (typeof localStorage !== 'undefined') localStorage.setItem('auctionViewMode', value);
                setTimeout(() => { this.isLoading = false; }, 600);
            });
            this.checkActiveFilters();
            window.__auctionRefresh = () => this.applyFilters();
        },

        clearAllFilters() {
            this.searchQuery = '';
            this.selectedFilters.vehicle_type = '';
            this.titleCondition = '';
            this.condition = '';
            this.makeSingle = '';
            this.modelSingle = '';
            this.locationSingle = '';
            this.fuelTypeMulti = [];
            this.colorMulti = [];
            this.damageTypeMulti = [];
            this.yearFrom = 1990;
            this.yearTo = {{ date('Y') + 1 }};
            this.odometerMin = 0;
            this.odometerMax = {{ $filterOptions['odometer_max'] ?? 250000 }};
            this.sortBy = 'newest';
            this.activeFilters = false;
            this.applyFilters();
        },

        applyFilters() {
            this.isLoading = true;
            const params = new URLSearchParams();
            const self = this;
            if (this.searchQuery && this.searchQuery.trim()) params.append('search', this.searchQuery.trim());
            if (this.titleCondition) params.append('title_condition[]', this.titleCondition);
            if (this.condition) params.append('condition', this.condition);
            if (this.selectedFilters.vehicle_type) params.append('vehicle_type', this.selectedFilters.vehicle_type);
            if (this.makeSingle && this.makeSingle.trim()) params.append('makes[]', this.makeSingle.trim());
            if (this.modelSingle && this.modelSingle.trim()) params.append('models[]', this.modelSingle.trim());
            if (this.locationSingle) params.append('location[]', this.locationSingle);
            (this.fuelTypeMulti || []).forEach(v => { if (v) params.append('fuel_type[]', v); });
            (this.colorMulti || []).forEach(v => { if (v) params.append('colors[]', v); });
            (this.damageTypeMulti || []).forEach(v => { if (v) params.append('damage_type[]', v); });
            if (this.yearFrom && this.yearFrom > 1990) params.append('year_from', this.yearFrom);
            if (this.yearTo && this.yearTo < {{ date('Y') + 1 }}) params.append('year_to', this.yearTo);
            if (this.odometerMax && this.odometerMax < {{ $filterOptions['odometer_max'] ?? 250000 }}) params.append('odometer_max', this.odometerMax);
            if (this.sortBy) params.append('sort', this.sortBy);
            const newUrl = '{{ route('Auction.index') }}' + (params.toString() ? '?' + params.toString() : '');
            window.history.pushState({}, '', newUrl);
            return fetch(newUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('auctionListings').innerHTML = data.html;
                        document.getElementById('auctionPagination').innerHTML = data.pagination;
                        const el = document.querySelector('.results-count');
                        if (el) { el.setAttribute('data-full-text', `Showing 1–${data.count} of ${data.count}`); typeResultsCount(); }
                        if (window.CaymarkUI && CaymarkUI.auction) {
                            CaymarkUI.auction.initCountdowns(document.getElementById('auctionListings'));
                            CaymarkUI.auction.initWatchlistHearts(document.getElementById('auctionListings'));
                        }
                    }
                    self.isLoading = false;
                    self.checkActiveFilters();
                })
                .catch(() => { self.isLoading = false; });
        }
    }
}

function openImageModal(url) {
    document.getElementById('modalImage').src = url;
    const m = document.getElementById('imageModal');
    m.classList.remove('hidden'); m.classList.add('flex');
    setTimeout(() => m.classList.add('opacity-100'), 10);
}
function closeImageModal() {
    const m = document.getElementById('imageModal');
    m.classList.remove('opacity-100');
    setTimeout(() => { m.classList.add('hidden'); m.classList.remove('flex'); }, 300);
}
function typeResultsCount() {
    const el = document.querySelector('.results-count');
    const typed = document.getElementById('results-count-typed');
    const cursor = document.getElementById('results-count-cursor');
    if (!el || !typed || !cursor) return;
    const text = el.getAttribute('data-full-text') || '';
    typed.textContent = ''; cursor.style.visibility = 'visible';
    let i = 0;
    function tick() {
        if (i < text.length) { typed.textContent += text[i++]; setTimeout(tick, 38); }
        else { cursor.style.visibility = 'hidden'; }
    }
    tick();
}
document.addEventListener('DOMContentLoaded', function () {
    typeResultsCount();
    if (window.CaymarkUI && CaymarkUI.auction) { CaymarkUI.auction.initCountdowns(); CaymarkUI.auction.initWatchlistHearts(); }
    if (window.CaymarkUI && CaymarkUI.mobile) {
        CaymarkUI.mobile.initPullToRefresh(
            document.getElementById('cm-auction-pull-root'),
            function () { return typeof window.__auctionRefresh === 'function' ? window.__auctionRefresh() : void 0; }
        );
    }
});
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeImageModal(); });
</script>
@endsection
