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
        <aside class="hidden lg:flex flex-col flex-shrink-0 bg-white border border-outline-variant rounded-xl overflow-hidden shadow-sm sticky top-4"
               style="width:260px; max-height:calc(100vh - 6rem)">

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
                    <div class="inline-flex border border-outline-variant rounded-lg overflow-hidden">
                        <button type="button"
                            @click="viewMode = 'grid'"
                            :class="viewMode === 'grid' ? 'bg-primary text-on-primary' : 'bg-white text-on-surface-variant hover:bg-surface-container-low'"
                            class="inline-flex items-center gap-1.5 px-3 py-2 text-label-md font-label-md transition-colors">
                            <span class="material-symbols-outlined text-[17px]">grid_view</span>
                            <span class="hidden sm:inline">Grid</span>
                        </button>
                        <button type="button"
                            @click="viewMode = 'detail'"
                            :class="viewMode === 'detail' ? 'bg-primary text-on-primary' : 'bg-white text-on-surface-variant hover:bg-surface-container-low'"
                            class="inline-flex items-center gap-1.5 px-3 py-2 text-label-md font-label-md transition-colors border-l border-outline-variant">
                            <span class="material-symbols-outlined text-[17px]">view_list</span>
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
                    class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-4">
                    @for($i = 0; $i < 8; $i++)
                    <div class="bg-white border border-outline-variant rounded-xl overflow-hidden flex flex-col">
                        <div class="shimmer" style="height:176px"></div>
                        <div class="p-3.5 flex-1 flex flex-col gap-2.5">
                            <div class="h-4 shimmer rounded-md" style="width:72%"></div>
                            <div class="h-3 shimmer rounded-md" style="width:48%"></div>
                            <div class="grid grid-cols-2 gap-x-2 gap-y-2 mt-1">
                                <div class="h-3 shimmer rounded-md"></div>
                                <div class="h-3 shimmer rounded-md"></div>
                                <div class="h-3 shimmer rounded-md"></div>
                                <div class="h-3 shimmer rounded-md"></div>
                            </div>
                            <div class="mt-auto pt-3 border-t border-outline-variant flex items-center justify-between gap-2">
                                <div class="space-y-1.5">
                                    <div class="h-2 shimmer rounded-md" style="width:52px"></div>
                                    <div class="h-5 shimmer rounded-md" style="width:72px"></div>
                                </div>
                                <div class="h-9 shimmer rounded-lg" style="width:76px"></div>
                            </div>
                        </div>
                    </div>
                    @endfor
                </div>

                {{-- List skeleton --}}
                <div x-show="isLoading && viewMode === 'detail'" x-cloak class="flex flex-col gap-4">
                    @for($i = 0; $i < 5; $i++)
                    <div class="bg-white border border-outline-variant rounded-xl overflow-hidden flex flex-col md:flex-row">
                        <div class="flex-shrink-0 shimmer" style="min-height:220px; width:100%"
                            :style="window.innerWidth >= 768 ? 'width:38%;min-height:0;height:auto' : ''"></div>
                        <div class="flex-1 p-5 flex flex-col gap-4">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex-1 space-y-2">
                                    <div class="h-5 shimmer rounded-md" style="width:68%"></div>
                                    <div class="h-4 shimmer rounded-md" style="width:44%"></div>
                                </div>
                                <div class="h-5 shimmer rounded-md flex-shrink-0" style="width:20px"></div>
                            </div>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                @for($j = 0; $j < 4; $j++)
                                <div class="space-y-1.5">
                                    <div class="h-2 shimmer rounded-md" style="width:60%"></div>
                                    <div class="h-3 shimmer rounded-md" style="width:80%"></div>
                                </div>
                                @endfor
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <div class="h-6 shimmer rounded-lg" style="width:88px"></div>
                                <div class="h-6 shimmer rounded-lg" style="width:72px"></div>
                                <div class="h-6 shimmer rounded-lg" style="width:96px"></div>
                            </div>
                            <div class="mt-auto pt-4 border-t border-outline-variant flex items-center justify-between gap-3">
                                <div class="space-y-1.5">
                                    <div class="h-2 shimmer rounded-md" style="width:64px"></div>
                                    <div class="h-6 shimmer rounded-md" style="width:88px"></div>
                                </div>
                                <div class="flex gap-2">
                                    <div class="h-9 shimmer rounded-lg" style="width:96px"></div>
                                    <div class="h-9 shimmer rounded-lg" style="width:40px"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endfor
                </div>

                {{-- Grid view --}}
                <div x-show="!isLoading && viewMode === 'grid'" x-cloak
                    class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-4">
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
                        <div class="vehicle-card bg-white border border-outline-variant rounded-xl overflow-hidden flex flex-col">
                            <div class="img-wrap bg-surface-container-high">
                                <img src="{{ $imgUrl }}"
                                    alt="{{ $listing->year }} {{ $listing->make }} {{ $listing->model }}"
                                    class="w-full h-44 object-cover cursor-pointer transition-transform duration-300 hover:scale-105"
                                    loading="lazy"
                                    onerror="this.onerror=null;this.src='{{ asset('images/placeholder-car.png') }}';"
                                    onclick="openImageModal('{{ $imgUrl }}')"/>
                                <div class="img-overlay">
                                    <button onclick="openImageModal('{{ $imgUrl }}')"
                                        class="bg-white text-primary text-label-md font-label-md px-4 py-1.5 rounded-lg hover:bg-surface-container transition-colors text-sm">
                                        View Photo
                                    </button>
                                </div>
                                <div class="absolute top-2 left-2 flex flex-col gap-1 z-10">
                                    @if($listing->featured)
                                        <span class="bg-secondary-fixed-dim text-primary text-[10px] font-bold px-2 py-0.5 rounded-sm uppercase tracking-widest">Featured</span>
                                    @endif
                                    <x-ui.ending-soon-badge :end="$endDate" />
                                    <x-ui.countdown :end="$endDate" :listing-id="$listing->id" variant="grid" />
                                </div>
                                <div class="absolute top-2 right-2 z-10">
                                    <x-ui.watchlist-heart
                                        :listing="$listing"
                                        :in-watchlist="$likedListingIds->contains($listing->id)"
                                        :likes-count="$listing->likes_count ?? 0"/>
                                </div>
                            </div>

                            <div class="p-3.5 flex-1 flex flex-col">
                                <h3 class="font-headline-sm text-headline-sm text-primary mb-2.5 line-clamp-2 leading-snug" style="font-size:16px">
                                    {{ $listing->year }} {{ $listing->make }} {{ $listing->model }}
                                </h3>
                                <div class="grid grid-cols-2 gap-y-1.5 gap-x-2 mb-3">
                                    <div class="flex items-center gap-1 text-on-surface-variant min-w-0" style="font-size:12px">
                                        <span class="material-symbols-outlined text-outline flex-shrink-0" style="font-size:13px">speed</span>
                                        <span class="truncate">{{ $listing->odometer ? number_format($listing->odometer).' km' : 'N/A' }}</span>
                                    </div>
                                    <div class="flex items-center gap-1 text-on-surface-variant min-w-0" style="font-size:12px">
                                        <span class="material-symbols-outlined text-outline flex-shrink-0" style="font-size:13px">location_on</span>
                                        <span class="truncate">{{ $listing->island ?? 'N/A' }}</span>
                                    </div>
                                    <div class="flex items-center gap-1 text-on-surface-variant min-w-0" style="font-size:12px">
                                        <span class="material-symbols-outlined text-outline flex-shrink-0" style="font-size:13px">receipt</span>
                                        <span class="truncate">{{ $listing->title_status_display }}</span>
                                    </div>
                                    <div class="flex items-center gap-1 text-on-surface-variant min-w-0" style="font-size:12px">
                                        <span class="material-symbols-outlined text-outline flex-shrink-0" style="font-size:13px">event</span>
                                        <span class="truncate">{{ $listing->sale_date ? \Carbon\Carbon::parse($listing->sale_date)->format('M d, Y') : 'N/A' }}</span>
                                    </div>
                                </div>
                                <div class="mt-auto pt-3 border-t border-outline-variant flex items-center justify-between gap-2">
                                    <div>
                                        <p class="text-on-surface-variant" style="font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:.05em">Current Bid</p>
                                        <p class="text-primary font-bold" style="font-size:17px">${{ number_format($listing->current_bid ?? 0) }}</p>
                                    </div>
                                    <a href="{{ route('auction.show', $listing->getSlugOrGenerate()) }}"
                                        class="bg-secondary-container text-on-secondary-container font-label-md text-label-md px-3.5 py-2 rounded-lg hover:bg-secondary transition-colors whitespace-nowrap text-sm">
                                        Bid Now
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-20 text-center">
                            <span class="material-symbols-outlined text-outline text-[60px] mb-3 block">search_off</span>
                            <h3 class="font-headline-sm text-headline-sm text-primary mb-2">No listings found</h3>
                            <p class="text-body-md font-body-md text-on-surface-variant">Try adjusting your filters to find more results.</p>
                            <button type="button" @click="clearAllFilters()"
                                class="mt-4 bg-primary text-on-primary font-label-md text-label-md px-5 py-2 rounded-lg hover:bg-primary-container transition-colors">
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

<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script>
function filterData() {
    return {
        isLoading: false,
        viewMode: 'detail',
        activeFilters: false,
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
