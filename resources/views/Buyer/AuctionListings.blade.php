@extends('layouts.welcome')

@section('title', 'Vehicle Auctions — CayMark')

@push('styles')
<style>
/* ── Prevent Alpine.js flash ── */
[x-cloak] { display: none !important; }

/* ── Monospaced countdown digits ── */
.cm-countdown {
    font-family: 'Courier New', ui-monospace, 'Roboto Mono', monospace;
    font-variant-numeric: tabular-nums;
    font-feature-settings: "tnum";
    letter-spacing: 0.02em;
}

/* ── Card hover image scale ── */
.cm-img-wrap { overflow: hidden; }
.cm-img-wrap img { transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1); }
.cm-card:hover .cm-img-wrap img { transform: scale(1.04); }

/* ── Gold BID NOW button ── */
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

/* ── View-switcher button active state ── */
.vs-btn-active {
    background-color: #fff;
    color: #1e293b;
    box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.06);
}
.vs-btn-idle {
    color: #94a3b8;
}
.vs-btn-idle:hover { color: #475569; background-color: rgba(241,245,249,0.7); }

/* ── Heart watchlist button ── */
.btn-heart {
    background: rgba(255,255,255,0.92);
    backdrop-filter: blur(6px);
    -webkit-backdrop-filter: blur(6px);
    transition: background 0.15s ease, transform 0.15s ease;
}
.btn-heart:hover { background: #fff; transform: scale(1.12); }
.btn-heart:hover svg { stroke: #ef4444; }

/* ── Spec label micro-type ── */
.spec-label {
    font-size: 8.5px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: #94a3b8;
}

/* ── Line-clamp for long titles ── */
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endpush

@section('content')

{{-- ══════════════════════════════════════════════════════════════
     SAMPLE DATA  (replace with controller-injected $listings)
══════════════════════════════════════════════════════════════ --}}
@php
$vehicles = [
    [
        'id'           => 1,
        'title'        => '2012 Cadillac CTS Sedan',
        'location'     => 'Rimini',
        'image'        => asset('images/placeholder.png'),
        'odometer'     => '87,432 mi',
        'condition'    => 'Used',
        'title_status' => 'Clean',
        'sale_date'    => 'Jun 2, 2025',
        'tags'         => ['Gasoline', 'Manual', 'All Over'],
        'bid'          => 0,
        'countdown'    => '02h:18m:59s',
        'slug'         => '#',
    ],
    [
        'id'           => 2,
        'title'        => '2019 BMW 5 Series 530i xDrive',
        'location'     => 'Nassau',
        'image'        => asset('images/placeholder.png'),
        'odometer'     => '44,210 mi',
        'condition'    => 'Used',
        'title_status' => 'Salvage',
        'sale_date'    => 'Jun 4, 2025',
        'tags'         => ['Gasoline', 'Automatic'],
        'bid'          => 4500,
        'countdown'    => '01h:42m:11s',
        'slug'         => '#',
    ],
    [
        'id'           => 3,
        'title'        => '2017 Toyota Camry SE',
        'location'     => 'Bridgetown',
        'image'        => asset('images/placeholder.png'),
        'odometer'     => '63,800 mi',
        'condition'    => 'Good',
        'title_status' => 'Clean',
        'sale_date'    => 'Jun 5, 2025',
        'tags'         => ['Gasoline', 'Automatic', 'All Over'],
        'bid'          => 1200,
        'countdown'    => '03h:05m:33s',
        'slug'         => '#',
    ],
    [
        'id'           => 4,
        'title'        => '2015 Ford Mustang GT Premium',
        'location'     => 'Kingston',
        'image'        => asset('images/placeholder.png'),
        'odometer'     => '52,000 mi',
        'condition'    => 'Fair',
        'title_status' => 'Clean',
        'sale_date'    => 'Jun 6, 2025',
        'tags'         => ['Gasoline', 'Manual'],
        'bid'          => 7800,
        'countdown'    => '05h:30m:00s',
        'slug'         => '#',
    ],
    [
        'id'           => 5,
        'title'        => '2020 Honda Civic Sport Hatchback',
        'location'     => 'Roseau',
        'image'        => asset('images/placeholder.png'),
        'odometer'     => '21,540 mi',
        'condition'    => 'Excellent',
        'title_status' => 'Clean',
        'sale_date'    => 'Jun 7, 2025',
        'tags'         => ['Gasoline', 'CVT'],
        'bid'          => 9200,
        'countdown'    => '06h:15m:22s',
        'slug'         => '#',
    ],
    [
        'id'           => 6,
        'title'        => '2016 Nissan Altima 2.5 S',
        'location'     => 'Port of Spain',
        'image'        => asset('images/placeholder.png'),
        'odometer'     => '78,900 mi',
        'condition'    => 'Used',
        'title_status' => 'Rebuilt',
        'sale_date'    => 'Jun 8, 2025',
        'tags'         => ['Gasoline', 'Automatic', 'All Over'],
        'bid'          => 3100,
        'countdown'    => '08h:00m:45s',
        'slug'         => '#',
    ],
    [
        'id'           => 7,
        'title'        => '2018 Jeep Wrangler Sport 4×4',
        'location'     => 'Castries',
        'image'        => asset('images/placeholder.png'),
        'odometer'     => '39,200 mi',
        'condition'    => 'Good',
        'title_status' => 'Clean',
        'sale_date'    => 'Jun 9, 2025',
        'tags'         => ['Gasoline', 'Manual', 'All Over'],
        'bid'          => 11000,
        'countdown'    => '11h:20m:10s',
        'slug'         => '#',
    ],
    [
        'id'           => 8,
        'title'        => '2022 Tesla Model 3 Long Range AWD',
        'location'     => 'Basseterre',
        'image'        => asset('images/placeholder.png'),
        'odometer'     => '12,100 mi',
        'condition'    => 'Excellent',
        'title_status' => 'Clean',
        'sale_date'    => 'Jun 10, 2025',
        'tags'         => ['Electric', 'Automatic'],
        'bid'          => 22500,
        'countdown'    => '23h:59m:59s',
        'slug'         => '#',
    ],
];
@endphp

{{-- ══════════════════════════════════════════════════════════════
     PAGE WRAPPER
══════════════════════════════════════════════════════════════ --}}
<div class="bg-slate-50 min-h-screen pb-16">

    {{-- ── Page section header ── --}}
    <div class="bg-white border-b border-slate-200">
        <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 py-5">
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Live Auctions</h1>
            <p class="text-sm text-slate-500 mt-0.5">Bid on certified vehicles. All auctions are final.</p>
        </div>
    </div>

    <div class="max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 pt-6">

        {{-- ══════════════════════════════════════════
             ALPINE.JS ROOT — manages view state
        ══════════════════════════════════════════ --}}
        <div x-data="{ view: 'grid' }">

            {{-- ── TOOLBAR ─────────────────────────────────────────── --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">

                {{-- Left: result count --}}
                <p class="text-sm text-slate-600">
                    Showing
                    <span class="font-semibold text-slate-900">{{ count($vehicles) }}</span>
                    vehicles
                </p>

                {{-- Right: sort + view toggle --}}
                <div class="flex items-center gap-2.5 self-start sm:self-auto">

                    {{-- Sort dropdown --}}
                    <select
                        class="h-9 text-sm border border-gray-200 rounded-lg px-3 bg-white text-slate-700 shadow-sm
                               focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 cursor-pointer">
                        <option>Ending Soonest</option>
                        <option>Newest Listed</option>
                        <option>Price: Low to High</option>
                        <option>Price: High to Low</option>
                    </select>

                    {{-- ── VIEW SWITCHER ── --}}
                    <div
                        class="inline-flex items-center border border-gray-200 rounded-lg bg-gray-100/60 p-[3px] gap-[2px]"
                        role="group"
                        aria-label="View switcher">

                        {{-- Grid button --}}
                        <button
                            type="button"
                            @click="view = 'grid'"
                            :class="view === 'grid' ? 'vs-btn-active' : 'vs-btn-idle'"
                            class="vs-btn inline-flex items-center gap-1.5 px-3 py-[6px] rounded-md text-[13px] font-semibold
                                   transition-all duration-150 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
                            :aria-pressed="view === 'grid'"
                            aria-label="Grid view">
                            {{-- 2 × 2 grid icon --}}
                            <svg width="13" height="13" viewBox="0 0 13 13" fill="currentColor" aria-hidden="true">
                                <rect x="0"    y="0"    width="5.5" height="5.5" rx="1.3"/>
                                <rect x="7.5"  y="0"    width="5.5" height="5.5" rx="1.3"/>
                                <rect x="0"    y="7.5"  width="5.5" height="5.5" rx="1.3"/>
                                <rect x="7.5"  y="7.5"  width="5.5" height="5.5" rx="1.3"/>
                            </svg>
                            <span class="hidden sm:inline">Grid</span>
                        </button>

                        {{-- List button --}}
                        <button
                            type="button"
                            @click="view = 'list'"
                            :class="view === 'list' ? 'vs-btn-active' : 'vs-btn-idle'"
                            class="vs-btn inline-flex items-center gap-1.5 px-3 py-[6px] rounded-md text-[13px] font-semibold
                                   transition-all duration-150 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
                            :aria-pressed="view === 'list'"
                            aria-label="List view">
                            {{-- 3-line burger icon --}}
                            <svg width="13" height="11" viewBox="0 0 13 11" fill="currentColor" aria-hidden="true">
                                <rect x="0" y="0"   width="13" height="2"   rx="1"/>
                                <rect x="0" y="4.5" width="13" height="2"   rx="1"/>
                                <rect x="0" y="9"   width="13" height="2"   rx="1"/>
                            </svg>
                            <span class="hidden sm:inline">List</span>
                        </button>

                    </div>{{-- /view switcher --}}
                </div>
            </div>{{-- /toolbar --}}


            {{-- ══════════════════════════════════════════════════════
                 CONFIGURATION A  ·  GRID VIEW
            ══════════════════════════════════════════════════════ --}}
            <div
                x-show="view === 'grid'"
                x-cloak
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-1"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 translate-y-1"
                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">

                @foreach($vehicles as $v)
                {{-- ─── GRID CARD ─────────────────────────────────── --}}
                <article
                    class="cm-card bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden
                           flex flex-col hover:shadow-md hover:border-gray-200/80 transition-shadow duration-200">

                    {{-- Image block — 16:9 aspect ratio, full-width --}}
                    <div class="cm-img-wrap relative w-full aspect-video bg-gray-100 flex-none">
                        <img
                            src="{{ $v['image'] }}"
                            alt="{{ $v['title'] }}"
                            class="w-full h-full object-cover"
                            loading="lazy">

                        {{-- ❤ Watchlist — top right --}}
                        <button
                            type="button"
                            class="btn-heart absolute top-2.5 right-2.5 z-10 w-8 h-8 rounded-full
                                   flex items-center justify-center shadow
                                   focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-400"
                            aria-label="Add to watchlist">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
                                 stroke="#64748b" stroke-width="2.5"
                                 stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06
                                         a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78
                                         1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                            </svg>
                        </button>

                        {{-- ⏱ Countdown — bottom right --}}
                        <div class="absolute bottom-2.5 right-2.5 z-10 inline-flex items-center gap-1
                                    bg-black/70 backdrop-blur-sm text-white
                                    px-2.5 py-[5px] rounded-full">
                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2.5"
                                 stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"/>
                                <polyline points="12 6 12 12 16 14"/>
                            </svg>
                            <span class="cm-countdown text-[10px] font-medium">
                                {{ $v['countdown'] }} remaining
                            </span>
                        </div>
                    </div>{{-- /image --}}

                    {{-- Card body --}}
                    <div class="p-4 flex flex-col flex-1 min-w-0">

                        {{-- Title + Location --}}
                        <div class="mb-2.5">
                            <h3 class="font-bold text-slate-900 text-[14.5px] leading-snug line-clamp-2">
                                <a href="{{ $v['slug'] }}"
                                   class="hover:text-blue-700 transition-colors duration-150">
                                    {{ $v['title'] }}
                                </a>
                            </h3>
                            <div class="flex items-center gap-1 mt-[5px]">
                                {{-- Map-pin icon --}}
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none"
                                     stroke="#94a3b8" stroke-width="2.5"
                                     stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0 1 18 0z"/>
                                    <circle cx="12" cy="10" r="3"/>
                                </svg>
                                <span class="text-[11px] text-slate-400 font-medium">{{ $v['location'] }}</span>
                            </div>
                        </div>

                        {{-- Spec pills / tags --}}
                        <div class="flex flex-wrap gap-1.5 mb-3">
                            @foreach($v['tags'] as $tag)
                            <span class="inline-flex items-center gap-[4px] bg-gray-100 text-slate-600
                                         text-[10.5px] font-semibold px-2.5 py-[4px] rounded-full whitespace-nowrap">
                                @if($tag === 'Gasoline')
                                {{-- Fuel pump icon --}}
                                <svg width="9" height="9" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2.2"
                                     stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 22V9a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v13"/>
                                    <path d="M3 14h14"/>
                                    <path d="M9 22v-4a3 3 0 0 1 6 0v4"/>
                                    <path d="M17 5l2-1.5M19 9a2.5 2.5 0 0 1 2.5 2.5v2
                                             A2.5 2.5 0 0 1 19 16h-2"/>
                                </svg>
                                @elseif($tag === 'Electric')
                                {{-- Lightning bolt icon --}}
                                <svg width="9" height="9" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2.5"
                                     stroke-linecap="round" stroke-linejoin="round">
                                    <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
                                </svg>
                                @endif
                                {{ $tag }}
                            </span>
                            @endforeach
                        </div>

                        {{-- ── 2 × 2 Specs Grid ── --}}
                        <div class="grid grid-cols-2 gap-x-4 gap-y-3 pb-3.5 mb-3.5 border-b border-gray-100">

                            {{-- Odometer --}}
                            <div>
                                <p class="spec-label mb-[3px]">Odometer</p>
                                <p class="text-[13px] font-bold text-slate-800">{{ $v['odometer'] }}</p>
                            </div>

                            {{-- Condition --}}
                            <div>
                                <p class="spec-label mb-[3px]">Condition</p>
                                <div class="flex items-center gap-1">
                                    <span class="text-[13px] font-bold text-slate-800">{{ $v['condition'] }}</span>
                                    {{-- Wrench icon --}}
                                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none"
                                         stroke="#94a3b8" stroke-width="2"
                                         stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0
                                                 l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91
                                                 a2.12 2.12 0 0 1-3-3l6.91-6.91
                                                 a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
                                    </svg>
                                </div>
                            </div>

                            {{-- Title status --}}
                            <div>
                                <p class="spec-label mb-[3px]">Title</p>
                                <p class="text-[13px] font-bold text-slate-800">{{ $v['title_status'] }}</p>
                            </div>

                            {{-- Sale Date --}}
                            <div>
                                <p class="spec-label mb-[3px]">Sale Date</p>
                                <p class="text-[13px] font-bold text-slate-800">{{ $v['sale_date'] }}</p>
                            </div>

                        </div>{{-- /2×2 specs --}}

                        {{-- ── Bottom: Bid + Action ── --}}
                        <div class="flex items-end justify-between mt-auto gap-3">
                            <div class="min-w-0">
                                <p class="text-[22px] font-extrabold text-slate-900 leading-none">
                                    ${{ number_format($v['bid']) }}
                                </p>
                                <p class="spec-label mt-[5px]">Current Bid</p>
                            </div>
                            <a href="{{ $v['slug'] }}"
                               class="btn-bid flex-none inline-flex items-center justify-center
                                      px-4 py-2.5 rounded-lg text-[12px]
                                      focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-1 focus-visible:ring-yellow-500">
                                Bid Now
                            </a>
                        </div>

                    </div>{{-- /card body --}}
                </article>{{-- /grid card --}}
                @endforeach

            </div>{{-- /grid view --}}


            {{-- ══════════════════════════════════════════════════════
                 CONFIGURATION B  ·  LIST VIEW
            ══════════════════════════════════════════════════════ --}}
            <div
                x-show="view === 'list'"
                x-cloak
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-1"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 translate-y-1"
                class="flex flex-col gap-3">

                @foreach($vehicles as $v)
                {{-- ─── LIST CARD ─────────────────────────────────── --}}
                <article
                    class="cm-card bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden
                           flex hover:shadow-md hover:border-gray-200/80 transition-shadow duration-200">

                    {{-- ── LEFT COLUMN · Image (~30%) ── --}}
                    <div class="cm-img-wrap relative flex-none w-[30%] min-w-[200px] max-w-[340px] self-stretch">
                        <img
                            src="{{ $v['image'] }}"
                            alt="{{ $v['title'] }}"
                            class="absolute inset-0 w-full h-full object-cover"
                            loading="lazy">

                        {{-- ❤ Watchlist — top right --}}
                        <button
                            type="button"
                            class="btn-heart absolute top-3 right-3 z-10 w-8 h-8 rounded-full
                                   flex items-center justify-center shadow
                                   focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-400"
                            aria-label="Add to watchlist">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none"
                                 stroke="#64748b" stroke-width="2.5"
                                 stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06
                                         a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78
                                         1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                            </svg>
                        </button>

                        {{-- ⏱ Countdown — bottom right --}}
                        <div class="absolute bottom-3 right-3 z-10 inline-flex items-center gap-1
                                    bg-black/70 backdrop-blur-sm text-white
                                    px-2.5 py-[5px] rounded-full">
                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2.5"
                                 stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"/>
                                <polyline points="12 6 12 12 16 14"/>
                            </svg>
                            <span class="cm-countdown text-[10px] font-medium">
                                {{ $v['countdown'] }} remaining
                            </span>
                        </div>
                    </div>{{-- /left column --}}

                    {{-- ── CENTER COLUMN · Details ── --}}
                    <div class="flex-1 min-w-0 px-5 py-4 flex flex-col justify-center gap-0">

                        {{-- Vehicle title --}}
                        <h3 class="font-bold text-slate-900 text-lg leading-snug">
                            <a href="{{ $v['slug'] }}"
                               class="hover:text-blue-700 transition-colors duration-150">
                                {{ $v['title'] }}
                            </a>
                        </h3>

                        {{-- Location --}}
                        <div class="flex items-center gap-1 mt-[5px] mb-4">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none"
                                 stroke="#94a3b8" stroke-width="2.5"
                                 stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0 1 18 0z"/>
                                <circle cx="12" cy="10" r="3"/>
                            </svg>
                            <span class="text-xs text-slate-400 font-medium">{{ $v['location'] }}</span>
                        </div>

                        {{-- ── Specs: 1 × 4 horizontal row ── --}}
                        <div class="grid grid-cols-4 gap-4 pb-3.5 mb-3.5 border-b border-gray-100/80">

                            {{-- Odometer --}}
                            <div>
                                <p class="spec-label mb-1">Odometer</p>
                                <p class="text-sm font-bold text-slate-800">{{ $v['odometer'] }}</p>
                            </div>

                            {{-- Condition --}}
                            <div>
                                <p class="spec-label mb-1">Condition</p>
                                <div class="flex items-center gap-1">
                                    <span class="text-sm font-bold text-slate-800">{{ $v['condition'] }}</span>
                                    {{-- Wrench icon --}}
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none"
                                         stroke="#94a3b8" stroke-width="2"
                                         stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0
                                                 l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91
                                                 a2.12 2.12 0 0 1-3-3l6.91-6.91
                                                 a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
                                    </svg>
                                </div>
                            </div>

                            {{-- Title status --}}
                            <div>
                                <p class="spec-label mb-1">Title</p>
                                <p class="text-sm font-bold text-slate-800">{{ $v['title_status'] }}</p>
                            </div>

                            {{-- Sale date --}}
                            <div>
                                <p class="spec-label mb-1">Sale Date</p>
                                <p class="text-sm font-bold text-slate-800">{{ $v['sale_date'] }}</p>
                            </div>

                        </div>{{-- /1×4 specs --}}

                        {{-- Spec pills / tags --}}
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($v['tags'] as $tag)
                            <span class="inline-flex items-center gap-[4px] bg-gray-100 text-slate-600
                                         text-[10.5px] font-semibold px-2.5 py-[4px] rounded-full whitespace-nowrap">
                                @if($tag === 'Gasoline')
                                <svg width="9" height="9" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2.2"
                                     stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 22V9a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v13"/>
                                    <path d="M3 14h14"/>
                                    <path d="M9 22v-4a3 3 0 0 1 6 0v4"/>
                                    <path d="M17 5l2-1.5M19 9a2.5 2.5 0 0 1 2.5 2.5v2
                                             A2.5 2.5 0 0 1 19 16h-2"/>
                                </svg>
                                @elseif($tag === 'Electric')
                                <svg width="9" height="9" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="2.5"
                                     stroke-linecap="round" stroke-linejoin="round">
                                    <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
                                </svg>
                                @endif
                                {{ $tag }}
                            </span>
                            @endforeach
                        </div>

                    </div>{{-- /center column --}}

                    {{-- ── RIGHT COLUMN · Pricing & Action ── --}}
                    <div class="flex-none w-52 border-l border-gray-200/60 flex flex-col
                                items-center justify-center px-6 py-5 gap-3.5">

                        {{-- Bid price block --}}
                        <div class="text-center">
                            <p class="text-[26px] font-extrabold text-slate-900 leading-none tracking-tight">
                                ${{ number_format($v['bid']) }}
                            </p>
                            <p class="spec-label mt-1.5">Current Bid</p>
                        </div>

                        {{-- BID NOW primary button --}}
                        <a href="{{ $v['slug'] }}"
                           class="btn-bid w-full inline-flex items-center justify-center
                                  py-2.5 rounded-lg text-sm
                                  focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-1 focus-visible:ring-yellow-500">
                            Bid Now
                        </a>

                        {{-- Details secondary link --}}
                        <a href="{{ $v['slug'] }}"
                           class="inline-flex items-center gap-1 text-[11px] font-semibold
                                  text-slate-400 hover:text-slate-600 transition-colors duration-150">
                            {{-- Document icon --}}
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2"
                                 stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12
                                         a2 2 0 0 0 2-2V8z"/>
                                <polyline points="14 2 14 8 20 8"/>
                                <line x1="16" y1="13" x2="8" y2="13"/>
                                <line x1="16" y1="17" x2="8" y2="17"/>
                                <polyline points="10 9 9 9 8 9"/>
                            </svg>
                            Details
                        </a>

                    </div>{{-- /right column --}}

                </article>{{-- /list card --}}
                @endforeach

            </div>{{-- /list view --}}

        </div>{{-- /x-data --}}

    </div>{{-- /max-w container --}}
</div>{{-- /page wrapper --}}

@endsection
