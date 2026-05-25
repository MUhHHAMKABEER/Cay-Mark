@extends('layouts.dashboard')

@section('title', 'Buyer Dashboard - CayMark')

@section('content')
<style>
.notifications-scrollbar { max-height: 65vh; overflow-y: scroll !important; overflow-x: hidden; }
.notifications-scrollbar::-webkit-scrollbar { width: 6px; }
.notifications-scrollbar::-webkit-scrollbar-track { background: transparent; }
.notifications-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 6px; }
.notifications-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
.dash-no-scrollbar { scrollbar-width: none; -ms-overflow-style: none; }
.dash-no-scrollbar::-webkit-scrollbar { display: none; }
</style>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<div class="w-full h-full bg-gray-50" style="min-height: calc(100vh - 0px); padding: 0;">
    <div class="w-full h-full px-3 sm:px-4 lg:px-6 py-3">
        @php $summary = $buyerSummary ?? []; $avgPurchase = $averagePurchaseData ?? []; @endphp

        <div class="bg-white rounded-xl shadow-sm h-full" style="min-height: calc(100vh - 60px);">
            <!-- DASHBOARD TAB -->
            <div id="content-dashboard" class="tab-content dash-no-scrollbar" style="display: none; height: 100%; overflow-y: auto;">
                @php
                    $leadingCount = $currentAuctions->where('is_winning', true)->count();
                    $sortedCurrentAuctions = $currentAuctions->sortBy(function($l) {
                        $endDate = $l->getAuctionEndDate();
                        $t = $endDate ? $endDate->timestamp : PHP_INT_MAX;
                        return sprintf('%020d_%d', $t, $l->is_winning ? 0 : 1);
                    })->values();
                @endphp

                <div class="flex flex-col xl:flex-row gap-0 xl:gap-0 h-full">

                    {{-- ══ MAIN LEFT COLUMN ══ --}}
                    <div class="flex-1 min-w-0 p-4 lg:p-6 space-y-5 overflow-y-auto dash-no-scrollbar" style="max-height: calc(100vh - 60px);">

                        {{-- Welcome + Plan Card --}}
                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900">Welcome back, {{ $user->name }}!</h1>
                                <p class="text-sm text-gray-500 mt-1">Here's what's happening with your account today.</p>
                            </div>
                            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm px-5 py-4 flex flex-wrap items-center gap-5 flex-shrink-0">
                                <div>
                                    <p class="text-xs text-gray-400 font-medium uppercase tracking-wide">Buyer Plan</p>
                                    <p class="font-bold text-gray-900 text-sm mt-0.5">{{ $user->activeSubscription?->package?->name ?? 'Standard Buyer' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-400 font-medium uppercase tracking-wide">Plan Expires</p>
                                    <p class="font-bold text-blue-600 text-sm mt-0.5">{{ $user->activeSubscription?->ends_at?->format('M d, Y') ?? '—' }}</p>
                                </div>
                                <a href="#" class="inline-flex items-center px-4 py-2 rounded-xl border border-gray-200 text-gray-700 text-xs font-semibold hover:border-blue-400 hover:text-blue-600 transition whitespace-nowrap">
                                    View Plan
                                </a>
                            </div>
                        </div>

                        {{-- Quick Stats --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5 flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                                    <span class="material-icons-round text-blue-600 text-2xl">gavel</span>
                                </div>
                                <div>
                                    <p class="text-3xl font-bold text-gray-900">{{ $leadingCount }}</p>
                                    <p class="text-sm font-bold text-gray-800 mt-0.5 tracking-wide">CURRENT</p>
                                    <p class="text-xs text-gray-400 mt-0.5">Leading Auctions | Won</p>
                                </div>
                            </div>
                            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5 flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center flex-shrink-0">
                                    <span class="material-icons-round text-emerald-600 text-2xl">emoji_events</span>
                                </div>
                                <div>
                                    <p class="text-3xl font-bold text-gray-900">{{ $wonAuctions->count() }}</p>
                                    <p class="text-sm text-gray-500 mt-0.5">Won Auctions</p>
                                </div>
                            </div>
                        </div>

                        {{-- Auctions Section --}}
                        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                            {{-- Header --}}
                            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="material-icons-round text-gray-400" style="font-size:18px">gavel</span>
                                    <h3 class="font-bold text-gray-900 text-sm">My Auctions</h3>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700 text-xs font-bold">
                                        <span class="material-icons-round" style="font-size:12px">trending_up</span>
                                        Leading {{ $leadingCount }}
                                    </span>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-blue-100 text-blue-700 text-xs font-bold">
                                        <span class="material-icons-round" style="font-size:12px">emoji_events</span>
                                        Won {{ $wonAuctions->count() }}
                                    </span>
                                </div>
                            </div>
                            {{-- Tabs --}}
                            <div class="px-5 pt-4 pb-0">
                                <nav class="flex gap-2 bg-gray-100 p-1 rounded-xl border border-gray-200">
                                    <button onclick="showDashAuctionTab('current')" id="dash-auction-tab-current"
                                            class="dash-auction-tab flex-1 px-4 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-indigo-600 rounded-lg shadow-sm transition-all duration-200">
                                        <span class="material-icons-round text-sm mr-1.5 align-middle">schedule</span>
                                        Current
                                    </button>
                                    <button onclick="showDashAuctionTab('won')" id="dash-auction-tab-won"
                                            class="dash-auction-tab flex-1 px-4 py-2.5 text-sm font-semibold text-gray-600 hover:text-gray-900 rounded-lg transition-all duration-200">
                                        <span class="material-icons-round text-sm mr-1.5 align-middle">check_circle</span>
                                        Won
                                    </button>
                                </nav>
                            </div>
                            {{-- Current Bids --}}
                            <div id="dash-auction-section-current" class="dash-auction-section p-5">
                                @if($sortedCurrentAuctions->count() > 0)
                                    <div class="space-y-3">
                                        @foreach($sortedCurrentAuctions as $listing)
                                            @php
                                                $endTime = $listing->getAuctionEndDate();
                                                $img = $listing->images->first();
                                                $imgUrl = $img ? (str_contains($img->image_path, '/') ? asset($img->image_path) : asset('uploads/listings/' . $img->image_path)) : null;
                                                $isLeading = (bool) ($listing->is_winning ?? false);
                                            @endphp
                                            <div class="flex gap-4 rounded-xl border border-gray-200 p-4 hover:border-blue-200 hover:shadow-sm transition-all">
                                                <div class="w-24 h-20 rounded-lg overflow-hidden flex-shrink-0 bg-gray-100">
                                                    @if($imgUrl)
                                                        <img src="{{ $imgUrl }}" alt="{{ $listing->make }} {{ $listing->model }}" class="w-full h-full object-cover">
                                                    @else
                                                        <div class="w-full h-full flex items-center justify-center text-gray-300">
                                                            <span class="material-icons-round text-4xl">directions_car</span>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-start justify-between gap-2 mb-1">
                                                        <div>
                                                            <h4 class="font-bold text-gray-900 text-sm leading-tight">{{ $listing->year }} {{ $listing->make }} {{ $listing->model }}</h4>
                                                            <p class="text-xs text-gray-400 font-mono">Auction ID: {{ $listing->item_number ?? 'CM' . str_pad($listing->id, 6, '0', STR_PAD_LEFT) }}</p>
                                                        </div>
                                                        @if($isLeading)
                                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 text-[11px] font-bold flex-shrink-0">
                                                                <span class="material-icons-round" style="font-size:10px">trending_up</span>
                                                                Leading
                                                            </span>
                                                        @else
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 text-[11px] font-bold flex-shrink-0">Outbid</span>
                                                        @endif
                                                    </div>
                                                    @if($endTime && $endTime->isFuture())
                                                        <p class="text-xs text-red-600 font-semibold mb-1.5 flex items-center gap-1">
                                                            <span class="material-icons-round" style="font-size:11px">schedule</span>
                                                            Auction ends in: <span id="countdown-dc-{{ $listing->id }}" data-end-time="{{ $endTime->toIso8601String() }}" class="font-mono ml-1">—</span>
                                                        </p>
                                                    @endif
                                                    <div class="flex items-center gap-5 text-xs mb-2.5">
                                                        <div>
                                                            <p class="text-gray-400">Last bid by you</p>
                                                            <p class="font-bold text-gray-900 text-sm">${{ number_format($listing->user_highest_bid ?? 0, 0) }}</p>
                                                        </div>
                                                        <div>
                                                            <p class="text-gray-400">Current highest bid</p>
                                                            <p class="font-bold text-blue-600 text-sm">${{ number_format($listing->highest_bid ?? 0, 0) }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="flex gap-2">
                                                        <a href="{{ route('auction.show', $listing->getSlugOrGenerate()) }}"
                                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold transition">
                                                            <span class="material-icons-round" style="font-size:13px">gavel</span>
                                                            Bid Now
                                                        </a>
                                                        <a href="{{ route('auction.show', $listing->getSlugOrGenerate()) }}"
                                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-gray-200 bg-white hover:border-blue-300 text-gray-700 hover:text-blue-600 text-xs font-semibold transition">
                                                            View Auction
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="mt-4 text-center">
                                        <a href="{{ route('buyer.auctions') }}" class="text-blue-600 hover:text-blue-700 text-sm font-semibold">View all current bids →</a>
                                    </div>
                                @else
                                    <div class="py-10 text-center">
                                        <span class="material-icons-round text-gray-300 text-4xl block mb-2">gavel</span>
                                        <p class="text-gray-500 text-sm font-medium">No Current Bid Activity</p>
                                        <p class="text-gray-400 text-xs mt-1 mb-3">This tab tracks all active auctions you've bid on — including auctions you're leading, auctions where you've been outbid, and any open bids.</p>
                                        <a href="{{ route('Auction.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold transition">
                                            Browse Auctions
                                        </a>
                                    </div>
                                @endif
                            </div>
                            {{-- Won --}}
                            <div id="dash-auction-section-won" class="dash-auction-section hidden p-5">
                                @if($wonAuctions->count() > 0)
                                    <div class="space-y-3">
                                        @foreach($wonAuctions as $listing)
                                            @php
                                                $img = $listing->images->first();
                                                $imgUrl = $img ? (str_contains($img->image_path, '/') ? asset($img->image_path) : asset('uploads/listings/' . $img->image_path)) : null;
                                                $ps = $listing->payment_status ?? '';
                                                $statusText = match($ps) { 'paid' => 'Purchase Complete', 'awaiting_invoice' => 'Awaiting Invoice', default => 'Payment Pending' };
                                                $statusColor = match($ps) { 'paid' => 'text-emerald-600', 'awaiting_invoice' => 'text-amber-600', default => 'text-orange-600' };
                                            @endphp
                                            <div class="flex gap-4 rounded-xl border border-emerald-200 p-4 bg-emerald-50/30">
                                                <div class="w-24 h-20 rounded-lg overflow-hidden flex-shrink-0 bg-gray-100">
                                                    @if($imgUrl)
                                                        <img src="{{ $imgUrl }}" alt="{{ $listing->make }} {{ $listing->model }}" class="w-full h-full object-cover">
                                                    @else
                                                        <div class="w-full h-full flex items-center justify-center text-gray-300">
                                                            <span class="material-icons-round text-4xl">directions_car</span>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-start justify-between gap-2 mb-1">
                                                        <div>
                                                            <h4 class="font-bold text-gray-900 text-sm leading-tight">{{ $listing->year }} {{ $listing->make }} {{ $listing->model }}</h4>
                                                            <p class="text-xs text-gray-400 font-mono">Auction ID: {{ $listing->item_number ?? '—' }}</p>
                                                        </div>
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 text-[11px] font-bold flex-shrink-0">WON</span>
                                                    </div>
                                                    <div class="flex items-center gap-5 text-xs mb-2.5">
                                                        <div>
                                                            <p class="text-gray-400">Winning amount</p>
                                                            <p class="font-bold text-emerald-600 text-sm">${{ number_format($listing->final_price ?? 0, 0) }}</p>
                                                        </div>
                                                        <div>
                                                            <p class="text-gray-400">Payment</p>
                                                            <p class="font-semibold {{ $statusColor }}">{{ $statusText }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="flex gap-2 flex-wrap">
                                                        @if(($listing->primary_invoice_id ?? null) && $ps === 'paid')
                                                            <a href="{{ route('messaging.thread.show', $listing->primary_invoice_id) }}"
                                                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-teal-600 hover:bg-teal-700 text-white text-xs font-semibold transition">
                                                                <span class="material-icons-round" style="font-size:13px">forum</span>
                                                                Messaging Center
                                                            </a>
                                                            <a href="{{ route('buyer.purchase.show', $listing->primary_invoice_id) }}"
                                                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-gray-200 bg-white hover:border-blue-300 text-gray-700 text-xs font-semibold transition">
                                                                View Details
                                                            </a>
                                                        @elseif(($listing->pending_invoice_id ?? null) && $ps === 'pending')
                                                            <a href="{{ route('buyer.payment.checkout-single', ['invoiceId' => $listing->pending_invoice_id]) }}"
                                                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold transition">
                                                                Make Payment
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="py-10 text-center">
                                        <span class="material-icons-round text-gray-300 text-4xl block mb-2">emoji_events</span>
                                        <p class="text-gray-500 text-sm font-medium">No Won Auctions Yet</p>
                                        <p class="text-gray-400 text-xs mt-1">Auctions you've won will appear here.</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Watchlist --}}
                        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="material-icons-round text-gray-400" style="font-size:18px">bookmark</span>
                                    <h3 class="font-bold text-gray-900 text-sm">Watchlist</h3>
                                </div>
                                @if($savedItems->count() > 0)
                                    <a href="{{ route('buyer.saved-items') }}" class="text-blue-600 hover:text-blue-700 text-xs font-semibold">View all →</a>
                                @endif
                            </div>
                            <div class="p-5">
                                @if($savedItems->count() > 0)
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        @foreach($savedItems->take(4) as $listing)
                                            @php
                                                $endTime = $listing->getAuctionEndDate();
                                                $isActive = $endTime && $endTime->isFuture();
                                                $img = $listing->images->first();
                                                $imgUrl = $img ? (str_contains($img->image_path, '/') ? asset($img->image_path) : asset('uploads/listings/' . $img->image_path)) : null;
                                            @endphp
                                            <div class="rounded-xl border border-gray-200 overflow-hidden hover:border-blue-200 hover:shadow-sm transition-all">
                                                <div class="relative h-36 bg-gray-100 overflow-hidden">
                                                    @if($imgUrl)
                                                        <img src="{{ $imgUrl }}" alt="{{ $listing->make }} {{ $listing->model }}" class="w-full h-full object-cover">
                                                    @else
                                                        <div class="w-full h-full flex items-center justify-center text-gray-300">
                                                            <span class="material-icons-round text-5xl">directions_car</span>
                                                        </div>
                                                    @endif
                                                    @if($isActive)
                                                        <div class="absolute top-2 left-2 bg-blue-600 px-2 py-0.5 rounded-lg">
                                                            <span class="text-[10px] font-bold text-white">LIVE</span>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="p-3">
                                                    <h4 class="font-bold text-gray-900 text-sm leading-tight mb-0.5 line-clamp-1">{{ $listing->year }} {{ $listing->make }} {{ $listing->model }}</h4>
                                                    <div class="flex items-center gap-1.5 text-[11px] text-gray-400 mb-2">
                                                        @if($listing->vehicle_type)<span>{{ $listing->vehicle_type }}</span><span>·</span>@endif
                                                        @if($listing->make)<span>{{ $listing->make }}</span><span>·</span>@endif
                                                        @if($listing->year)<span>{{ $listing->year }}</span>@endif
                                                    </div>
                                                    <div class="flex items-center justify-between mb-2.5">
                                                        <div>
                                                            <p class="text-[10px] text-gray-400">Current Bid</p>
                                                            <p class="font-bold text-blue-600 text-sm">${{ number_format($listing->highest_bid ?? $listing->starting_price ?? 0, 0) }}</p>
                                                        </div>
                                                        @if($endTime)
                                                            <div class="text-right">
                                                                <p class="text-[10px] text-gray-400">Time Remaining</p>
                                                                <p class="text-[11px] font-semibold text-red-600 font-mono" id="countdown-dw-{{ $listing->id }}" data-end-time="{{ $endTime->toIso8601String() }}">—</p>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <a href="{{ route('auction.show', $listing->getSlugOrGenerate()) }}"
                                                       class="block w-full text-center px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold transition">
                                                        View Auction
                                                    </a>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="py-8 text-center">
                                        <span class="material-icons-round text-gray-300 text-4xl block mb-2">bookmark_border</span>
                                        <p class="text-gray-500 text-sm font-medium">No Saved Items</p>
                                        <p class="text-gray-400 text-xs mt-1 mb-3">Save auctions to track them here.</p>
                                        <a href="{{ route('Auction.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold transition">
                                            Browse Auctions
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>

                    </div>{{-- end main left column --}}

                    {{-- ══ RIGHT SIDEBAR ══ --}}
                    <div class="xl:w-80 flex-shrink-0 border-t xl:border-t-0 xl:border-l border-gray-200 bg-gray-50/50 xl:bg-white p-4 xl:p-5 space-y-5 overflow-y-auto dash-no-scrollbar" style="max-height: calc(100vh - 60px);">

                        {{-- Notifications --}}
                        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                                <h3 class="font-bold text-gray-900 text-sm">Notifications</h3>
                                <a href="{{ route('buyer.notifications') }}" class="text-blue-600 hover:text-blue-700 text-xs font-semibold">View all</a>
                            </div>
                            <div class="divide-y divide-gray-100">
                                @php
                                    $excludeNotifTypes = ['welcome','registration','account_created','account_active','system','onboarding'];
                                    $excludeNotifKeywords = ['welcome to caymark','registration','account is now active','account is active','account on caymark is now','registration on caymark'];
                                    $recentNotifs = $notifications->sortByDesc('created_at')
                                        ->filter(function($n) use ($excludeNotifTypes, $excludeNotifKeywords) {
                                            $d    = is_array($n->data) ? $n->data : (json_decode($n->data ?? '{}', true) ?: []);
                                            $type = strtolower($d['type'] ?? '');
                                            $msg  = strtolower($d['message'] ?? $d['title'] ?? '');
                                            if (in_array($type, $excludeNotifTypes)) return false;
                                            foreach ($excludeNotifKeywords as $kw) {
                                                if (str_contains($msg, $kw)) return false;
                                            }
                                            return true;
                                        })
                                        ->take(5);
                                    $nfIconMap  = ['bid'=>'gavel','outbid'=>'trending_down','win'=>'celebration','payment'=>'payment','auction'=>'schedule','default'=>'notifications'];
                                    $nfColorMap = ['bid'=>'text-blue-600','outbid'=>'text-amber-600','win'=>'text-emerald-600','payment'=>'text-purple-600','auction'=>'text-orange-500','default'=>'text-gray-400'];
                                @endphp
                                @forelse($recentNotifs as $notif)
                                    @php
                                        $nd = is_array($notif->data) ? $notif->data : [];
                                        $nMsg   = $nd['message'] ?? $nd['title'] ?? 'Notification';
                                        $nType  = $nd['type'] ?? 'default';
                                        $nIcon  = $nfIconMap[$nType] ?? $nfIconMap['default'];
                                        $nColor = $nfColorMap[$nType] ?? $nfColorMap['default'];
                                        $nUnread = !$notif->read_at;
                                    @endphp
                                    <div class="flex items-start gap-3 px-5 py-3.5 {{ $nUnread ? 'bg-blue-50/50' : '' }}">
                                        <span class="material-icons-round {{ $nColor }} flex-shrink-0 mt-0.5" style="font-size:18px">{{ $nIcon }}</span>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm text-gray-800 font-medium leading-snug">{{ $nMsg }}</p>
                                            <p class="text-[11px] text-gray-400 mt-0.5">{{ $notif->created_at->diffForHumans() }}</p>
                                        </div>
                                        @if($nUnread)
                                            <span class="w-2 h-2 bg-blue-600 rounded-full flex-shrink-0 mt-1.5"></span>
                                        @endif
                                    </div>
                                @empty
                                    <div class="px-5 py-6 text-center">
                                        <p class="text-gray-400 text-sm">No notifications yet.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        {{-- Support Center --}}
                        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                            <div class="px-5 py-4 border-b border-gray-100">
                                <h3 class="font-bold text-gray-900 text-sm">Support Center</h3>
                            </div>
                            <div class="p-5">
                                <p class="text-sm text-gray-500 mb-4 leading-relaxed">Submit a request and our team will respond as quickly as possible.</p>
                                <a href="{{ route('buyer.customer-support') }}"
                                   class="block w-full text-center px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold transition">
                                    Submit a Request
                                </a>
                            </div>
                        </div>

                        {{-- Quick Help --}}
                        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                            <div class="px-5 py-4 border-b border-gray-100">
                                <h3 class="font-bold text-gray-900 text-sm">Quick Help</h3>
                            </div>
                            <div class="divide-y divide-gray-100">
                                @foreach([
                                    ['icon' => 'help_outline',   'label' => 'View FAQ'],
                                    ['icon' => 'gavel',          'label' => 'Auction Guide'],
                                    ['icon' => 'person_outline', 'label' => 'Buyer Guide'],
                                    ['icon' => 'info_outline',   'label' => 'How Auctions Work'],
                                ] as $help)
                                    <a href="#" class="flex items-center justify-between px-5 py-3 hover:bg-gray-50 transition group">
                                        <div class="flex items-center gap-3">
                                            <span class="material-icons-round text-gray-400 group-hover:text-blue-600 transition" style="font-size:18px">{{ $help['icon'] }}</span>
                                            <span class="text-sm text-gray-700 font-medium group-hover:text-blue-600 transition">{{ $help['label'] }}</span>
                                        </div>
                                        <span class="material-icons-round text-gray-300 group-hover:text-blue-400 transition" style="font-size:18px">chevron_right</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        {{-- Contact Us --}}
                        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                            <div class="px-5 py-4 border-b border-gray-100">
                                <h3 class="font-bold text-gray-900 text-sm">Contact Us</h3>
                            </div>
                            <div class="p-5 space-y-3">
                                <a href="mailto:support@caymark.com" class="flex items-center gap-3 text-sm text-gray-700 hover:text-blue-600 transition">
                                    <span class="material-icons-round text-gray-400 flex-shrink-0" style="font-size:18px">mail</span>
                                    support@caymark.com
                                </a>
                                <div class="flex items-start gap-3">
                                    <span class="material-icons-round text-gray-400 flex-shrink-0 mt-0.5" style="font-size:18px">phone</span>
                                    <span class="text-sm text-gray-600 leading-relaxed">
                                        For urgent matters call or WhatsApp us at
                                        <a href="tel:+12428066275" class="font-semibold text-blue-600 hover:text-blue-700 transition">+1 (242) 806-6275</a>
                                    </span>
                                </div>
                            </div>
                        </div>

                    </div>{{-- end right sidebar --}}

                </div>{{-- end flex row --}}
            </div>{{-- end content-dashboard --}}

            <!-- USER TAB -->
            <div id="content-user" class="tab-content hidden p-6" style="height: 100%; overflow-y: auto;">
                @php
                    $emailChangePending = session('email_change_pending') || (new \App\Services\EmailChangeVerificationService())->hasPendingChange($user);
                    $pendingNewEmail    = session('email_change_new') ?? (new \App\Services\EmailChangeVerificationService())->getPendingNewEmail($user);

                    $buyerDialRows   = collect(config('phone_country_codes', []))->sortBy('label')->values();
                    $buyerMatchRows  = collect(config('phone_country_codes', []))->sortByDesc(fn ($r) => strlen((string) ($r['code'] ?? '')))->values();
                    $buyerPhoneDigits = preg_replace('/\D/', '', (string) ($user->phone ?? ''));
                    $dashPhoneCountry  = '1242';
                    $dashPhoneNational = '';
                    foreach ($buyerMatchRows as $row) {
                        $cc = (string) ($row['code'] ?? '');
                        if ($cc !== '' && $buyerPhoneDigits !== '' && str_starts_with($buyerPhoneDigits, $cc)) {
                            $dashPhoneCountry  = $cc;
                            $dashPhoneNational = substr($buyerPhoneDigits, strlen($cc)) ?: '';
                            break;
                        }
                    }
                    if ($buyerPhoneDigits !== '' && $dashPhoneNational === '' && strlen($buyerPhoneDigits) >= 10) {
                        if (str_starts_with($buyerPhoneDigits, '1242')) {
                            $dashPhoneCountry  = '1242';
                            $dashPhoneNational = substr($buyerPhoneDigits, 4) ?: $buyerPhoneDigits;
                        } elseif (strlen($buyerPhoneDigits) === 11 && str_starts_with($buyerPhoneDigits, '1')) {
                            $dashPhoneCountry  = '1';
                            $dashPhoneNational = substr($buyerPhoneDigits, 1);
                        } else {
                            $dashPhoneNational = $buyerPhoneDigits;
                        }
                    }
                @endphp

                <!-- Page header -->
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-lg shadow-blue-500/20">
                        <span class="material-icons-round text-white text-xl">manage_accounts</span>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 tracking-tight">Account Settings</h2>
                        <p class="text-sm text-gray-500">Manage your profile and security settings</p>
                    </div>
                </div>

                <!-- Flash messages -->
                @if(session('success'))
                    <div class="flex items-center gap-3 rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 mb-5 text-emerald-800">
                        <span class="material-icons-round text-emerald-600">check_circle</span>
                        <span class="font-medium text-sm">{{ session('success') }}</span>
                    </div>
                @endif
                @if($errors->any())
                    <div class="flex items-start gap-3 rounded-xl bg-red-50 border border-red-200 px-4 py-3 mb-5 text-red-800">
                        <span class="material-icons-round text-red-600 flex-shrink-0">error</span>
                        <ul class="list-disc list-inside text-sm space-y-0.5">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    </div>
                @endif

                <div class="space-y-5">

                {{-- ══════════════════════════════════════════
                     SECTION 1 — ACCOUNT INFORMATION
                ══════════════════════════════════════════ --}}
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                        <span class="material-icons-round text-gray-400" style="font-size:18px">person</span>
                        <h3 class="font-bold text-gray-900 text-sm">Account Information</h3>
                    </div>
                    <div class="p-6 space-y-5">

                        <!-- Full Name (read-only) -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Full Name</label>
                            <div class="flex items-center gap-3 rounded-xl bg-gray-50 border border-gray-200 px-4 py-3">
                                <span class="material-icons-round text-gray-400" style="font-size:18px">badge</span>
                                <span class="text-gray-900 font-medium text-sm">{{ $user->name }}</span>
                                <span class="ml-auto text-xs text-gray-400">Cannot be changed here</span>
                            </div>
                        </div>

                        <!-- Phone Number -->
                        <div>
                            <label class="flex items-center gap-2 text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                                Phone Number
                                <span class="text-red-500 normal-case tracking-normal font-semibold text-[11px]">(required to bid)</span>
                            </label>
                            <div class="rounded-xl bg-gray-50 border border-gray-200 px-4 py-4 space-y-3">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="text-sm">
                                        <p class="text-gray-500 text-xs mb-0.5">Current</p>
                                        <p class="text-gray-900 font-medium" id="dashboard_phone_display">{{ ($user->phone && $buyerPhoneDigits !== '') ? '+'.$buyerPhoneDigits : 'Not set' }}</p>
                                    </div>
                                    <div id="dash-phone-verified-badge" class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-semibold
                                        {{ $user->phone_verified_at ? 'bg-green-50 text-green-700 border border-green-200' : 'hidden bg-yellow-50 text-yellow-700 border border-yellow-200' }}">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>{{ $user->phone_verified_at ? 'Verified' : 'Not verified' }}</span>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-[minmax(10rem,14rem),minmax(0,1.5fr),auto] gap-3 items-end">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Country / area code</label>
                                        <select id="dash_phone_country" class="w-full px-3 py-2.5 rounded-xl border-2 border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white text-sm">
                                            @foreach ($buyerDialRows as $row)
                                                <option value="{{ $row['code'] }}" @if((string)($row['code'] ?? '') === $dashPhoneCountry) selected @endif>{{ $row['label'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="min-w-[180px]">
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Phone Number</label>
                                        <input type="text" id="dash_phone_input" value="{{ $dashPhoneNational }}"
                                            placeholder="e.g. (242) 555-1234"
                                            class="js-digits-only js-phone-format w-full px-4 py-2.5 rounded-xl border-2 border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                                            data-phone-country-select="#dash_phone_country"
                                            data-cm-validate="phone"
                                            inputmode="numeric" autocomplete="tel-national">
                                    </div>
                                    <div class="flex md:block">
                                        <button type="button" id="dash-send-code-btn"
                                            class="w-full md:w-auto px-4 py-2.5 rounded-xl bg-gray-200 text-gray-800 text-sm font-semibold hover:bg-gray-300 transition whitespace-nowrap">
                                            Send code
                                        </button>
                                    </div>
                                </div>
                                <input type="hidden" id="dash_phone_full" value="">
                                <div id="dash-phone-verify-row" class="grid grid-cols-1 md:grid-cols-[minmax(0,1.5fr),auto] gap-3 items-end hidden">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Verification code</label>
                                        <input type="text" id="dash_phone_code_input" placeholder="6-digit code" maxlength="6" inputmode="numeric" pattern="[0-9]*"
                                            class="js-digits-only w-full px-4 py-2.5 rounded-xl border-2 border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                                        <p class="text-[11px] text-gray-500 mt-1">Code expires in 5 minutes.</p>
                                    </div>
                                    <div class="flex md:block">
                                        <button type="button" id="dash-verify-phone-btn"
                                            class="w-full md:w-auto px-4 py-2.5 rounded-xl bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 transition whitespace-nowrap">
                                            Verify &amp; Save
                                        </button>
                                    </div>
                                </div>
                                @if(!$user->phone || !$user->phone_verified_at)
                                    <p class="text-[11px] text-red-500">You must add and verify a phone number before you can place bids.</p>
                                @else
                                    <p class="text-[11px] text-gray-400">Verified phone is used for bid alerts, security, and pickup coordination.</p>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>

                {{-- ══════════════════════════════════════════
                     SECTION 2 — EMAIL
                ══════════════════════════════════════════ --}}
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                        <span class="material-icons-round text-gray-400" style="font-size:18px">mail</span>
                        <h3 class="font-bold text-gray-900 text-sm">Email Address</h3>
                    </div>
                    <div class="p-6">
                        @if($emailChangePending)
                            <form method="POST" action="{{ route('buyer.user.update-email') }}">
                                @csrf
                                <input type="hidden" name="email" value="{{ $pendingNewEmail }}">
                                <p class="text-sm text-gray-600 mb-4">A verification code was sent to <strong>{{ $user->email }}</strong>. Enter it below to confirm the change to <strong>{{ $pendingNewEmail }}</strong>.</p>
                                <div class="flex flex-col sm:flex-row gap-3 items-start">
                                    <div>
                                        <input type="text" name="code" value="{{ old('code') }}" placeholder="000000" maxlength="6" pattern="[0-9]*" inputmode="numeric" required
                                            class="w-44 px-4 py-2.5 rounded-xl border-2 border-gray-200 bg-gray-50 text-gray-900 font-mono text-lg text-center tracking-widest placeholder-gray-400 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 transition-all">
                                        @error('code')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                                    </div>
                                    <button type="submit"
                                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold transition">
                                        <span class="material-icons-round" style="font-size:17px">check_circle</span>
                                        Confirm change
                                    </button>
                                    <a href="{{ route('buyer.user') }}"
                                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl border border-gray-300 bg-white hover:bg-gray-50 text-gray-600 text-sm font-semibold transition">
                                        Cancel
                                    </a>
                                </div>
                                <p class="text-xs text-gray-400 mt-2">Code expires in 15 minutes.</p>
                            </form>
                        @else
                            <div class="mb-3">
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Current Email</label>
                                <div class="flex items-center gap-3 rounded-xl bg-gray-50 border border-gray-200 px-4 py-3">
                                    <span class="material-icons-round text-gray-400" style="font-size:18px">alternate_email</span>
                                    <span class="text-gray-900 font-medium text-sm">{{ $user->email }}</span>
                                </div>
                            </div>
                            <form method="POST" action="{{ route('buyer.user.update-email') }}">
                                @csrf
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Change Email</label>
                                <div class="flex flex-col sm:flex-row gap-3">
                                    <div class="flex-1">
                                        <input type="email" name="email" value="{{ old('email') }}" required
                                            placeholder="Enter new email address"
                                            class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-200 bg-gray-50 text-gray-900 font-medium placeholder-gray-400 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 transition-all text-sm">
                                        @error('email')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                                    </div>
                                    <button type="submit"
                                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold transition whitespace-nowrap">
                                        <span class="material-icons-round" style="font-size:17px">send</span>
                                        Send verification code
                                    </button>
                                </div>
                                <p class="text-xs text-gray-400 mt-2">A code will be sent to your current email to approve the change.</p>
                            </form>
                        @endif
                    </div>
                </div>

                {{-- ══════════════════════════════════════════
                     SECTION 3 — ACCOUNT TYPE
                ══════════════════════════════════════════ --}}
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                        <span class="material-icons-round text-gray-400" style="font-size:18px">workspace_premium</span>
                        <h3 class="font-bold text-gray-900 text-sm">Account Type</h3>
                    </div>
                    <div class="p-6 flex items-center justify-between gap-4 flex-wrap">
                        <div>
                            <p class="font-bold text-gray-900 text-base mb-1">Buyer Account</p>
                            <p class="text-sm text-gray-500">Browse, bid, and win auctions on CayMark.</p>
                        </div>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-bold">
                            <span class="material-icons-round" style="font-size:13px">person</span>
                            Buyer
                        </span>
                    </div>
                </div>

                {{-- ══════════════════════════════════════════
                     SECTION 4 — DOCUMENTS
                ══════════════════════════════════════════ --}}
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                        <span class="material-icons-round text-gray-400" style="font-size:18px">folder_open</span>
                        <h3 class="font-bold text-gray-900 text-sm">Documents</h3>
                    </div>
                    <div class="p-6 space-y-3">
                        @if(isset($documents) && $documents->count() > 0)
                            @foreach($documents as $doc)
                                <div class="flex items-center justify-between gap-2 rounded-xl bg-gray-50 border border-gray-200 px-4 py-3">
                                    <p class="text-sm font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $doc->doc_type ?? 'Document')) }}</p>
                                    @if($doc->path ?? null)
                                        <a href="{{ route('user.document.view', $doc->id) }}" target="_blank" rel="noopener"
                                           class="text-blue-600 hover:text-blue-800 text-sm font-semibold shrink-0">View</a>
                                    @else
                                        <span class="text-gray-400 text-sm shrink-0">—</span>
                                    @endif
                                </div>
                            @endforeach
                            <p class="text-xs text-gray-400">Documents uploaded during registration. To update, contact support.</p>
                        @else
                            <div class="rounded-xl border-2 border-dashed border-gray-200 bg-gray-50 p-6 text-center">
                                <span class="material-icons-round text-gray-300 text-3xl mb-2 block">folder_open</span>
                                <p class="text-gray-500 text-sm">No documents uploaded yet.</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- ══════════════════════════════════════════
                     SECTION 5 — SECURITY (PASSWORD)
                ══════════════════════════════════════════ --}}
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                        <span class="material-icons-round text-gray-400" style="font-size:18px">lock</span>
                        <h3 class="font-bold text-gray-900 text-sm">Security</h3>
                    </div>
                    <div class="p-6 flex items-center justify-between gap-4 flex-wrap">
                        <div>
                            <p class="text-sm font-medium text-gray-700">Password</p>
                            <p class="text-xs text-gray-400 mt-0.5">Your password is encrypted and never displayed.</p>
                        </div>
                        <button type="button" onclick="showPasswordModal()"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl border border-gray-300 bg-white hover:border-blue-400 hover:text-blue-600 hover:bg-blue-50 text-sm font-semibold text-gray-700 transition-all duration-200">
                            <span class="material-icons-round" style="font-size:18px">key</span>
                            Change Password
                        </button>
                    </div>
                </div>

                </div>{{-- end space-y-5 --}}
            </div>{{-- end content-user --}}

            <!-- AUCTIONS TAB -->
            <div id="content-auctions" class="tab-content hidden p-6">
                <!-- Header -->
                <div class="mb-8">
                    <div class="flex items-center justify-between mb-1">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg shadow-indigo-500/20">
                                <span class="material-icons-round text-white text-xl">gavel</span>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900 tracking-tight">My Auctions</h2>
                                <p class="text-sm text-gray-500">Track your bidding activity and results</p>
                            </div>
                        </div>
                        <div class="hidden md:flex items-center gap-3">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-emerald-100 text-emerald-700 text-xs font-bold">
                                <span class="material-icons-round" style="font-size:13px">trending_up</span>
                                Leading {{ $currentAuctions->where('is_winning', true)->count() }}
                            </span>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-blue-100 text-blue-700 text-xs font-bold">
                                <span class="material-icons-round" style="font-size:13px">emoji_events</span>
                                Won {{ $wonAuctions->count() }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Tab Navigation -->
                <div class="mb-6">
                    <nav class="flex gap-2 bg-gray-100 p-1 rounded-xl border border-gray-200">
                        <button onclick="showAuctionSection('current')" id="auction-current" class="auction-tab-button active flex-1 px-4 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-indigo-600 rounded-lg shadow-sm transition-all duration-200">
                            <span class="material-icons-round text-sm mr-1.5 align-middle">schedule</span>
                            Current Bids
                        </button>
                        <button onclick="showAuctionSection('won')" id="auction-won" class="auction-tab-button flex-1 px-4 py-2.5 text-sm font-semibold text-gray-600 hover:text-gray-900 rounded-lg transition-all duration-200">
                            <span class="material-icons-round text-sm mr-1.5 align-middle">check_circle</span>
                            Won
                        </button>
                    </nav>
                </div>

                <div id="auction-section-current" class="auction-section">
                    @php
                        $sortedAuctionsCurrent = $currentAuctions->sortBy(function($l) {
                            $endDate = $l->getAuctionEndDate();
                            $t = $endDate ? $endDate->timestamp : PHP_INT_MAX;
                            return sprintf('%020d_%d', $t, ($l->is_winning ?? false) ? 0 : 1);
                        })->values();
                    @endphp
                    @if($sortedAuctionsCurrent->count() > 0)
                        <div class="space-y-3">
                            @foreach($sortedAuctionsCurrent as $listing)
                                @php
                                    $endTime   = $listing->getAuctionEndDate();
                                    $img       = $listing->images->first();
                                    $imgUrl    = $img ? (str_contains($img->image_path, '/') ? asset($img->image_path) : asset('uploads/listings/' . $img->image_path)) : null;
                                    $isLeading = (bool) ($listing->is_winning ?? false);
                                @endphp
                                <div class="flex gap-4 rounded-xl border {{ $isLeading ? 'border-emerald-200 bg-emerald-50/20' : 'border-gray-200' }} p-4 hover:shadow-sm transition-all">
                                    <div class="w-28 flex-shrink-0 rounded-lg overflow-hidden bg-gray-100" style="height:88px">
                                        @if($imgUrl)
                                            <img src="{{ $imgUrl }}" alt="{{ $listing->make }} {{ $listing->model }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-gray-300">
                                                <span class="material-icons-round text-4xl">directions_car</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-start justify-between gap-2 mb-1">
                                            <div>
                                                <h4 class="font-bold text-gray-900 text-base leading-tight">{{ $listing->year }} {{ $listing->make }} {{ $listing->model }}</h4>
                                                <p class="text-xs text-gray-400 font-mono">Auction ID: {{ $listing->item_number ?? 'CM' . str_pad($listing->id, 6, '0', STR_PAD_LEFT) }}</p>
                                            </div>
                                            @if($isLeading)
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700 text-xs font-bold flex-shrink-0">
                                                    <span class="material-icons-round" style="font-size:12px">trending_up</span>
                                                    Leading
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-amber-100 text-amber-700 text-xs font-bold flex-shrink-0">Outbid</span>
                                            @endif
                                        </div>
                                        @if($endTime && $endTime->isFuture())
                                            <p class="text-xs text-red-600 font-semibold mb-2 flex items-center gap-1">
                                                <span class="material-icons-round" style="font-size:12px">schedule</span>
                                                Ends in: <span id="countdown-a-{{ $listing->id }}" data-end-time="{{ $endTime->toIso8601String() }}" class="font-mono ml-1">—</span>
                                            </p>
                                        @endif
                                        <div class="flex items-center gap-6 text-xs mb-3">
                                            <div>
                                                <p class="text-gray-400">Last bid by you</p>
                                                <p class="font-bold text-gray-900 text-sm">${{ number_format($listing->user_highest_bid ?? 0, 0) }}</p>
                                            </div>
                                            <div>
                                                <p class="text-gray-400">Current highest bid</p>
                                                <p class="font-bold text-blue-600 text-sm">${{ number_format($listing->highest_bid ?? 0, 0) }}</p>
                                            </div>
                                        </div>
                                        <div class="flex gap-2">
                                            <a href="{{ route('auction.show', $listing->getSlugOrGenerate()) }}"
                                               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold transition">
                                                <span class="material-icons-round" style="font-size:14px">gavel</span>
                                                Bid Now
                                            </a>
                                            <a href="{{ route('auction.show', $listing->getSlugOrGenerate()) }}"
                                               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg border border-gray-200 bg-white hover:border-blue-300 text-gray-700 hover:text-blue-600 text-xs font-semibold transition">
                                                View Auction
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="bg-white rounded-2xl border-2 border-dashed border-gray-200 p-12 text-center">
                            <div class="w-20 h-20 mx-auto mb-4 rounded-2xl bg-gradient-to-br from-blue-100 to-indigo-100 flex items-center justify-center">
                                <span class="material-icons-round text-blue-600 text-4xl">gavel</span>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 mb-2">No Current Bid Activity</h3>
                            <p class="text-gray-500 text-sm max-w-sm mx-auto mb-4">This tab tracks every active auction you've interacted with — auctions you're currently leading, auctions where you've been outbid, and any open bids. Start bidding to see your full active bid history here.</p>
                            <a href="{{ route('Auction.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white rounded-xl font-semibold hover:bg-blue-700 transition-all">
                                <span class="material-icons-round text-lg">search</span>
                                Browse Auctions
                            </a>
                        </div>
                    @endif
                </div>

                <div id="auction-section-won" class="auction-section hidden">
                    @if($wonAuctions->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($wonAuctions as $listing)
                                @php
                                    if (($listing->payment_status ?? null) === 'paid') {
                                        $statusText = 'Purchase Complete';
                                    } elseif (($listing->payment_status ?? null) === 'awaiting_invoice') {
                                        $statusText = 'Awaiting invoice';
                                    } else {
                                        $statusText = 'Payment Pending';
                                    }
                                @endphp
                                <div class="group bg-white rounded-2xl border-2 border-emerald-200 overflow-hidden shadow-sm hover:shadow-xl hover:border-emerald-300 transition-all duration-300">
                                    <div class="relative h-52 bg-gradient-to-br from-emerald-50 to-green-100 overflow-hidden">
                                        @if($listing->images->first())
                                            <img src="{{ str_contains($listing->images->first()->image_path, '/') ? asset($listing->images->first()->image_path) : asset('uploads/listings/' . $listing->images->first()->image_path) }}" alt="" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-gray-400">
                                                <span class="material-icons-round text-6xl">directions_car</span>
                                            </div>
                                        @endif
                                        <div class="absolute top-3 right-3 bg-emerald-500 px-3 py-1 rounded-lg shadow-sm">
                                            <span class="text-xs font-bold text-white">WON</span>
                                        </div>
                                    </div>
                                    <div class="p-5">
                                        <h3 class="text-lg font-bold text-gray-900 mb-1.5 line-clamp-1">{{ $listing->year ?? '' }} {{ $listing->make ?? '' }} {{ $listing->model ?? 'Item' }}</h3>
                                        <p class="text-xs text-gray-500 mb-2 font-mono">ITEM #{{ $listing->item_number ?? '—' }}</p>
                                        <div class="flex items-baseline gap-2 mb-2">
                                            <span class="text-xs text-gray-500 font-medium">Winning bid</span>
                                            <span class="text-xl font-bold text-emerald-600">${{ number_format($listing->final_price ?? 0, 2) }}</span>
                                        </div>
                                        @if($listing->total_amount_due !== null)
                                            <div class="flex items-baseline gap-2 mb-2">
                                                <span class="text-xs text-gray-500 font-medium">Total amount due</span>
                                                <span class="text-lg font-bold text-gray-900">${{ number_format($listing->total_amount_due, 2) }}</span>
                                            </div>
                                        @endif
                                        <div class="flex items-center gap-2 text-xs text-emerald-600 font-semibold mb-4">
                                            <span class="material-icons-round text-sm">check_circle</span>
                                            <span>{{ $statusText }}</span>
                                        </div>
                                        @php
                                            $checkoutInvoiceId = $listing->pending_invoice_id;
                                            $primaryInvoiceId = $listing->primary_invoice_id ?? null;
                                        @endphp
                                        @if($checkoutInvoiceId && ($listing->payment_status ?? null) === 'pending')
                                            <a href="{{ route('buyer.payment.checkout-single', ['invoiceId' => $checkoutInvoiceId]) }}" class="block w-full text-center px-4 py-2.5 rounded-lg font-semibold text-sm mb-2 transition-all duration-200 hover:opacity-90" style="background-color: #059669; color: #ffffff;">
                                                Make Payment
                                            </a>
                                        @elseif(($listing->payment_status ?? null) === 'paid' && $primaryInvoiceId)
                                            <a href="{{ route('buyer.purchase.show', $primaryInvoiceId) }}" class="block w-full text-center px-4 py-2.5 rounded-lg font-semibold text-sm mb-2 border-2 border-blue-600 text-blue-700 hover:bg-blue-50 transition-all duration-200">
                                                View purchase &amp; pickup code
                                            </a>
                                            <a href="{{ route('messaging.thread.show', $primaryInvoiceId) }}" class="block w-full text-center px-4 py-2.5 rounded-lg font-semibold text-sm mb-2 bg-teal-600 text-white hover:bg-teal-700 transition-all duration-200">
                                                Messaging Center
                                            </a>
                                            <a href="{{ route('buyer.invoice.download', $primaryInvoiceId) }}" class="block w-full text-center py-2.5 rounded-lg border-2 font-semibold text-sm transition-all duration-200" style="border-color: #6b7280; color: #374151;">
                                                Download Invoice
                                            </a>
                                        @elseif(($listing->payment_status ?? null) === 'awaiting_invoice')
                                            <p class="text-xs text-amber-800 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2 mb-2 text-left">
                                                Your win is on file, but the payment invoice is not available yet. Refresh later or contact support with item <span class="font-mono font-semibold">#{{ $listing->item_number ?? $listing->id }}</span>.
                                            </p>
                                        @elseif(($listing->payment_status ?? null) === 'pending' && ! $checkoutInvoiceId)
                                            <p class="text-xs text-amber-800 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2 mb-2 text-left">
                                                Payment is pending, but no checkout link was found. Contact support with item <span class="font-mono font-semibold">#{{ $listing->item_number ?? $listing->id }}</span>.
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="bg-white rounded-2xl border-2 border-dashed border-gray-200 p-12 text-center">
                            <div class="w-20 h-20 mx-auto mb-4 rounded-2xl bg-gradient-to-br from-emerald-100 to-green-100 flex items-center justify-center">
                                <span class="material-icons-round text-emerald-600 text-4xl">celebration</span>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 mb-2">No Won Auctions Yet</h3>
                            <p class="text-gray-500 text-sm max-w-sm mx-auto">Auctions you've won will appear here after the auction ends.</p>
                        </div>
                    @endif
                </div>

            </div>

            <!-- SAVED ITEMS TAB -->
            <div id="content-saved" class="tab-content hidden p-6 dash-no-scrollbar" style="height: 100%; overflow-y: auto;">

                {{-- Page header --}}
                <div class="flex items-center justify-between gap-4 mb-6 flex-wrap">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-lg shadow-blue-500/20">
                            <span class="material-icons-round text-white text-xl">bookmark</span>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-gray-900 tracking-tight">Saved Items</h2>
                            <p class="text-sm text-gray-500">Your watchlist of favorite auctions</p>
                        </div>
                    </div>
                    @if($savedItems->count() > 0)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-blue-100 text-blue-700 text-xs font-bold">
                            <span class="material-icons-round" style="font-size:13px">bookmark</span>
                            {{ $savedItems->count() }} saved
                        </span>
                    @endif
                </div>

                @if($savedItems->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($savedItems as $listing)
                            @php
                                $endTime     = $listing->getAuctionEndDate();
                                $isActive    = $endTime && $endTime->isFuture();
                                $savedImg    = $listing->images->first();
                                $savedImgUrl = $savedImg ? (str_contains($savedImg->image_path ?? '', '/') ? asset($savedImg->image_path) : asset('uploads/listings/' . $savedImg->image_path)) : null;
                            @endphp
                            <div class="flex flex-col bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden hover:border-blue-200 hover:shadow-md transition-all">
                                {{-- Image --}}
                                <div class="relative h-48 bg-gray-100 overflow-hidden flex-shrink-0">
                                    @if($savedImgUrl)
                                        <img src="{{ $savedImgUrl }}" alt="{{ $listing->make }} {{ $listing->model }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-300">
                                            <span class="material-icons-round text-5xl">directions_car</span>
                                        </div>
                                    @endif
                                    {{-- Status badge top-left --}}
                                    @if($isActive)
                                        <div class="absolute top-3 left-3 inline-flex items-center gap-1 bg-blue-600 text-white text-[11px] font-bold px-2 py-1 rounded-lg">
                                            <span class="material-icons-round" style="font-size:11px">bolt</span>
                                            LIVE
                                        </div>
                                    @else
                                        <div class="absolute top-3 left-3 inline-flex items-center gap-1 bg-gray-700/70 backdrop-blur-sm text-white text-[11px] font-bold px-2 py-1 rounded-lg">
                                            Ended
                                        </div>
                                    @endif
                                    {{-- Countdown overlay bottom-left when live --}}
                                    @if($endTime && $isActive)
                                        <div class="absolute bottom-3 left-3 inline-flex items-center gap-1 bg-black/60 backdrop-blur-sm text-white text-[11px] font-bold px-2 py-1 rounded-lg">
                                            <span class="material-icons-round" style="font-size:11px">schedule</span>
                                            <span id="countdown-saved-{{ $listing->id }}" data-end-time="{{ $endTime->toIso8601String() }}">—</span>
                                        </div>
                                    @endif
                                </div>
                                {{-- Card body --}}
                                <div class="p-4 flex flex-col flex-1">
                                    <h3 class="font-bold text-gray-900 text-sm leading-tight mb-0.5">{{ $listing->year }} {{ $listing->make }} {{ $listing->model }}</h3>
                                    <p class="text-xs text-gray-400 font-mono mb-3">{{ $listing->item_number ?? 'CM' . str_pad($listing->id, 6, '0', STR_PAD_LEFT) }}</p>

                                    <div class="flex items-center justify-between mb-4">
                                        <div>
                                            <p class="text-xs text-gray-400 font-medium">Current Bid</p>
                                            <p class="text-lg font-bold text-blue-600">${{ number_format($listing->highest_bid ?? $listing->starting_price ?? 0, 0) }}</p>
                                        </div>
                                        @if($endTime && !$isActive)
                                            <div class="text-right">
                                                <p class="text-xs text-gray-400">Ended</p>
                                                <p class="text-xs font-semibold text-gray-500">{{ $endTime->format('M j, Y') }}</p>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="flex gap-2 pt-3 border-t border-gray-100 mt-auto">
                                        <a href="{{ route('auction.show', $listing->getSlugOrGenerate()) }}"
                                           class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold transition">
                                            <span class="material-icons-round" style="font-size:14px">gavel</span>
                                            {{ $isActive ? 'Place Bid' : 'View Auction' }}
                                        </a>
                                        <form method="POST" action="{{ route('listing.watchlist', $listing) }}" class="flex-1">
                                            @csrf
                                            <button type="submit"
                                                    class="w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl border border-gray-200 bg-white hover:border-red-300 hover:bg-red-50 hover:text-red-600 text-gray-600 text-xs font-semibold transition">
                                                <span class="material-icons-round" style="font-size:14px">bookmark_remove</span>
                                                Remove
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="rounded-xl border border-dashed border-gray-200 bg-gray-50 py-16 text-center">
                        <span class="material-icons-round text-gray-300 text-5xl block mb-3">bookmark_border</span>
                        <p class="text-gray-700 text-base font-semibold">No Saved Items Yet</p>
                        <p class="text-gray-400 text-sm mt-1.5 max-w-sm mx-auto mb-5">Save auctions you're interested in to track them easily. Click the bookmark icon on any auction to add it here.</p>
                        <a href="{{ route('Auction.index') }}"
                           class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold shadow-lg shadow-blue-600/25 transition">
                            <span class="material-icons-round text-lg">search</span>
                            Browse Auctions
                        </a>
                    </div>
                @endif
            </div>

            <!-- NOTIFICATIONS TAB -->
            <div id="content-notifications" class="tab-content hidden pt-6 pb-4 flex flex-col" style="min-height: 0;">
                <!-- Header -->
                <div class="mb-6 flex-shrink-0 px-6">
                    <div class="flex items-center justify-between mb-1">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center shadow-lg shadow-purple-500/20">
                                <span class="material-icons-round text-white text-xl">notifications</span>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Notifications</h2>
                                <p class="text-sm text-gray-500">Stay updated with your auction activity</p>
                            </div>
                        </div>
                        @if($notifications->count() > 0)
                            @php $unreadCount = $notifications->whereNull('read_at')->count(); @endphp
                            <div class="flex items-center gap-3">
                                @if($unreadCount > 0)
                                    <div class="unread-count-display flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-50 border border-blue-200">
                                        <span class="w-2 h-2 bg-blue-600 rounded-full animate-pulse"></span>
                                        <span class="text-sm font-semibold text-blue-700">{{ $unreadCount }} unread</span>
                                    </div>
                                    <button type="button" id="read-all-notifications-btn" onclick="markAllNotificationsAsRead()"
                                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold shadow-sm transition-all duration-200">
                                        <span class="material-icons-round text-lg">done_all</span>
                                        Read All
                                    </button>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Filter Buttons -->
                <div class="mb-4 flex items-center gap-3 flex-shrink-0 px-6">
                    <button id="notif-filter-all" onclick="setNotificationFilter('all')"
                            class="notif-filter-btn px-5 py-2.5 rounded-lg font-semibold text-sm transition-all bg-blue-600 text-white shadow-md">
                        All
                    </button>
                    <button id="notif-filter-unread" onclick="setNotificationFilter('unread')"
                            class="notif-filter-btn px-5 py-2.5 rounded-lg font-semibold text-sm transition-all bg-gray-100 text-gray-700 hover:bg-gray-200">
                        Unread
                    </button>
                    <button id="notif-filter-read" onclick="setNotificationFilter('read')"
                            class="notif-filter-btn px-5 py-2.5 rounded-lg font-semibold text-sm transition-all bg-gray-100 text-gray-700 hover:bg-gray-200">
                        Read
                    </button>
                </div>

                @if($notifications->count() > 0)
                    @php
                        // Group notifications by month and sort by date (newest first)
                        $groupedNotifications = $notifications->sortByDesc('created_at')->groupBy(function($notification) {
                            return $notification->created_at->format('F Y');
                        });
                    @endphp

                    <div class="notifications-scroll-wrapper notifications-scrollbar flex-1 min-h-0" style="max-height: 65vh; overflow-y: scroll; overflow-x: hidden;">
                    <div class="notifications-container space-y-6 py-1 px-4">
                        @foreach($groupedNotifications as $month => $monthNotifications)
                            <div class="notification-month-group" data-month="{{ $month }}">
                                <!-- Month Header -->
                                <div class="mb-4 flex items-center gap-3">
                                    <div class="h-px flex-1 bg-gradient-to-r from-transparent via-gray-300 to-transparent"></div>
                                    <h3 class="text-lg font-bold text-gray-700 px-4">{{ $month }}</h3>
                                    <div class="h-px flex-1 bg-gradient-to-r from-transparent via-gray-300 to-transparent"></div>
                                </div>

                                <!-- Notifications for this month -->
                                <div class="space-y-3">
                                    @foreach($monthNotifications->sortByDesc('created_at') as $notification)
                            @php
                                $d = is_array($notification->data) ? $notification->data : [];
                                $msg = $d['message'] ?? $d['title'] ?? 'Notification';
                                $type = $d['type'] ?? 'info';
                                $isUnread = !$notification->read_at;
                                $link = $d['link'] ?? null;
                                $actionLabel = $d['action_label'] ?? 'View details';

                                // Icon mapping based on type
                                $iconMap = [
                                    'bid' => 'gavel',
                                    'outbid' => 'trending_down',
                                    'win' => 'celebration',
                                    'payment' => 'payment',
                                    'auction' => 'schedule',
                                    'suspicious_login' => 'security',
                                    'default' => 'notifications'
                                ];
                                $icon = $iconMap[$type] ?? $iconMap['default'];

                                // Color mapping
                                $colorMap = [
                                    'bid' => ['bg' => 'bg-blue-50', 'border' => 'border-blue-200', 'icon' => 'text-blue-600', 'dot' => 'bg-blue-600'],
                                    'outbid' => ['bg' => 'bg-amber-50', 'border' => 'border-amber-200', 'icon' => 'text-amber-600', 'dot' => 'bg-amber-600'],
                                    'win' => ['bg' => 'bg-emerald-50', 'border' => 'border-emerald-200', 'icon' => 'text-emerald-600', 'dot' => 'bg-emerald-600'],
                                    'payment' => ['bg' => 'bg-purple-50', 'border' => 'border-purple-200', 'icon' => 'text-purple-600', 'dot' => 'bg-purple-600'],
                                    'suspicious_login' => ['bg' => 'bg-red-50', 'border' => 'border-red-200', 'icon' => 'text-red-600', 'dot' => 'bg-red-600'],
                                    'default' => ['bg' => 'bg-gray-50', 'border' => 'border-gray-200', 'icon' => 'text-gray-600', 'dot' => 'bg-gray-600']
                                ];
                                $colors = $colorMap[$type] ?? $colorMap['default'];
                            @endphp
                            <div class="notification-card group relative bg-white rounded-xl border-2 {{ $isUnread ? $colors['border'] . ' ' . $colors['bg'] : 'border-gray-200' }} p-4 hover:shadow-lg transition-all duration-200 {{ $isUnread ? 'shadow-sm cursor-pointer' : '' }}"
                                 data-notification-id="{{ $notification->id }}"
                                 data-is-unread="{{ $isUnread ? 'true' : 'false' }}"
                                 data-read-status="{{ $isUnread ? 'unread' : 'read' }}"
                                 data-link="{{ $link ?? '' }}"
                                 onclick="handleNotificationClick(this)">
                                <div class="flex items-start gap-4">
                                    <!-- Icon -->
                                    <div class="flex-shrink-0 w-12 h-12 rounded-xl {{ $isUnread ? $colors['bg'] : 'bg-gray-100' }} flex items-center justify-center border-2 {{ $isUnread ? $colors['border'] : 'border-gray-200' }}">
                                        <span class="material-icons-round {{ $isUnread ? $colors['icon'] : 'text-gray-400' }} text-xl">{{ $icon }}</span>
                                    </div>

                                    <!-- Content -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="flex-1">
                                                <p class="text-gray-900 font-semibold leading-snug {{ $isUnread ? 'text-gray-900' : 'text-gray-700' }}">{{ $msg }}</p>
                                                <div class="flex items-center gap-2 mt-2 flex-wrap">
                                                    <span class="text-xs text-gray-500 font-medium">{{ $notification->created_at->format('M d, Y') }}</span>
                                                    <span class="text-gray-300">•</span>
                                                    <span class="text-xs text-gray-500">{{ $notification->created_at->diffForHumans() }}</span>
                                                </div>
                                                @if($link)
                                                    <div class="mt-3">
                                                        <span class="inline-flex items-center gap-1.5 text-sm font-semibold text-blue-600 group-hover:text-blue-800">
                                                            {{ $actionLabel }}
                                                            <span class="material-icons-round text-lg">arrow_forward</span>
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                            @if($isUnread)
                                                <div class="flex-shrink-0 unread-dot">
                                                    <span class="w-2.5 h-2.5 {{ $colors['dot'] }} rounded-full inline-block"></span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                    </div>
                @else
                    <div class="bg-white rounded-2xl border-2 border-dashed border-gray-200 p-12 text-center">
                        <div class="w-20 h-20 mx-auto mb-4 rounded-2xl bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                            <span class="material-icons-round text-gray-400 text-4xl">notifications_off</span>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">No notifications yet</h3>
                        <p class="text-gray-500 text-sm max-w-sm mx-auto">You'll receive notifications for bid updates, auction wins, payment reminders, and more.</p>
                    </div>
                @endif
            </div>

            <!-- MESSAGING CENTER TAB -->
            <div id="content-messaging" class="tab-content hidden p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-2">Messaging Center</h2>
                <p class="text-gray-600 mb-6">Post-payment pickup coordination with sellers.</p>
                <div class="bg-gradient-to-br from-blue-50 via-white to-teal-50 border-2 border-teal-200 rounded-2xl p-8 text-center">
                    <div class="w-16 h-16 bg-teal-600 rounded-2xl flex items-center justify-center mx-auto mb-4" style="background-color:#0d9488;">
                        <span class="material-icons-round text-white" style="font-size: 2rem;">forum</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">All your transactions in one place</h3>
                    <p class="text-sm text-gray-600 max-w-md mx-auto mb-6">
                        Coordinate pickup or delivery, share schedules and confirm completion — all securely inside CayMark.
                        @if(($messagingThreads ?? collect())->count() > 0)
                            You currently have <strong class="text-gray-900">{{ $messagingThreads->count() }}</strong> active transaction{{ $messagingThreads->count() === 1 ? '' : 's' }}.
                        @endif
                    </p>
                    <a href="{{ route('messaging.index') }}" class="inline-flex items-center gap-2 bg-teal-600 hover:bg-teal-700 text-white font-semibold px-6 py-3 rounded-xl shadow-lg shadow-teal-600/30 transition" style="background-color:#0d9488; color:#fff;">
                        Open Messaging Center
                        <span class="material-icons-round" style="font-size: 1.1rem;">arrow_forward</span>
                    </a>
                </div>
            </div>

            <!-- CUSTOMER SUPPORT TAB -->
            <div id="content-support" class="tab-content hidden p-6 dash-no-scrollbar" style="height: 100%; overflow-y: auto;">

                {{-- Success banner --}}
                @if (session('success'))
                    <div id="support-success-banner" class="flex items-center gap-3 rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 mb-6 text-emerald-800" role="status">
                        <span class="material-icons-round text-emerald-600 flex-shrink-0">check_circle</span>
                        <span class="font-medium text-sm">{{ session('success') }}</span>
                    </div>
                @endif

                {{-- Page header --}}
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-lg shadow-blue-500/20">
                        <span class="material-icons-round text-white text-xl">support_agent</span>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 tracking-tight">Support Center</h2>
                        <p class="text-sm text-gray-500">Submit a request and our team will respond as quickly as possible.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 items-start">

                    {{-- ══ LEFT COLUMN — Form + History ══ --}}
                    <div class="lg:col-span-2 space-y-5">

                        {{-- Submit a Request --}}
                        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                                <span class="material-icons-round text-gray-400" style="font-size:18px">edit_note</span>
                                <h3 class="font-bold text-gray-900 text-sm">Submit a Request</h3>
                            </div>
                            <form method="POST" action="{{ route('buyer.customer-support.submit') }}" class="p-6 space-y-5">
                                @csrf
                                @if ($errors->has('title') || $errors->has('message'))
                                    <div class="flex items-start gap-3 rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-red-800">
                                        <span class="material-icons-round text-red-500 flex-shrink-0 text-lg">error</span>
                                        <ul class="list-disc list-inside text-sm space-y-0.5">
                                            @foreach ((array) ($errors->get('title') ?? []) as $err)<li>{{ $err }}</li>@endforeach
                                            @foreach ((array) ($errors->get('message') ?? []) as $err)<li>{{ $err }}</li>@endforeach
                                        </ul>
                                    </div>
                                @endif

                                {{-- Category --}}
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                                        What is this request about? <span class="text-red-500 normal-case tracking-normal">*</span>
                                    </label>
                                    <select name="title" required
                                        class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-200 bg-gray-50 text-gray-900 font-medium focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 transition-all text-sm">
                                        <option value="">Select a category…</option>
                                        @foreach ($supportCategories as $opt)
                                            <option value="{{ $opt }}" {{ old('title') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                        @endforeach
                                    </select>
                                    @error('title')
                                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Message --}}
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                                        Message <span class="text-red-500 normal-case tracking-normal">*</span>
                                    </label>
                                    <textarea name="message" rows="7" required maxlength="800"
                                        placeholder="Write your message here…"
                                        class="w-full px-4 py-2.5 rounded-xl border-2 border-gray-200 bg-gray-50 text-gray-900 font-medium placeholder-gray-400 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/10 transition-all resize-none text-sm">{{ old('message') }}</textarea>
                                    @error('message')
                                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <button type="submit"
                                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold shadow-lg shadow-blue-600/25 transition-all duration-200">
                                    <span class="material-icons-round" style="font-size:18px">send</span>
                                    Submit Request
                                </button>
                            </form>
                        </div>

                        {{-- Request History --}}
                        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                                <span class="material-icons-round text-gray-400" style="font-size:18px">history</span>
                                <h3 class="font-bold text-gray-900 text-sm">Request History</h3>
                            </div>
                            <div class="p-6">
                                @if($buyerSupportTickets->count() > 0)
                                    <div class="space-y-3">
                                        @foreach($buyerSupportTickets as $ticket)
                                            @php
                                                $tOpen = $ticket->status === 'open';
                                                $statusMap = [
                                                    'open'        => ['bg-blue-100 text-blue-800',   'Open'],
                                                    'in_progress' => ['bg-amber-100 text-amber-800', 'In Progress'],
                                                    'resolved'    => ['bg-emerald-100 text-emerald-800', 'Resolved'],
                                                    'closed'      => ['bg-gray-100 text-gray-600',   'Closed'],
                                                ];
                                                [$sBadge, $sLabel] = $statusMap[$ticket->status] ?? ['bg-gray-100 text-gray-600', ucfirst($ticket->status)];
                                            @endphp
                                            <div class="rounded-xl border {{ $tOpen ? 'border-blue-200 bg-blue-50/30' : 'border-gray-200 bg-gray-50/30' }} p-4">
                                                <div class="flex flex-wrap items-start justify-between gap-2 mb-2">
                                                    <div>
                                                        <h4 class="font-semibold text-gray-900 text-sm">{{ $ticket->title }}</h4>
                                                        @if($ticket->public_ticket_number)
                                                            <p class="text-xs font-mono text-gray-400 mt-0.5">#{{ $ticket->public_ticket_number }}</p>
                                                        @endif
                                                    </div>
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $sBadge }}">{{ $sLabel }}</span>
                                                </div>
                                                <p class="text-gray-600 text-sm mb-2 whitespace-pre-wrap leading-relaxed">{{ $ticket->message }}</p>
                                                <p class="text-xs text-gray-400">Submitted {{ $ticket->created_at->diffForHumans() }}</p>
                                                @if($ticket->admin_reply)
                                                    <div class="mt-3 p-3 rounded-xl bg-white border border-gray-200">
                                                        <p class="text-xs font-semibold text-gray-700 mb-1 flex items-center gap-1">
                                                            <span class="material-icons-round text-blue-500" style="font-size:14px">reply</span>
                                                            Support reply
                                                        </p>
                                                        <p class="text-sm text-gray-700">{{ $ticket->admin_reply }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="rounded-xl border border-dashed border-gray-200 bg-gray-50 py-10 text-center">
                                        <span class="material-icons-round text-gray-300 text-4xl mb-2 block">confirmation_number</span>
                                        <p class="text-gray-600 font-medium text-sm">No requests yet</p>
                                        <p class="text-gray-400 text-xs mt-1">Submit a request above and it will appear here.</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                    </div>{{-- end left column --}}

                    {{-- ══ RIGHT SIDEBAR ══ --}}
                    <div class="space-y-5">

                        {{-- Quick Help --}}
                        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                            <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-2">
                                <span class="material-icons-round text-gray-400" style="font-size:18px">lightbulb</span>
                                <h4 class="font-bold text-gray-900 text-sm">Quick Help</h4>
                            </div>
                            <div class="divide-y divide-gray-100">
                                @foreach([
                                    ['icon' => 'help_outline',   'label' => 'View FAQ'],
                                    ['icon' => 'gavel',          'label' => 'Auction Guide'],
                                    ['icon' => 'person_outline', 'label' => 'Buyer Guide'],
                                    ['icon' => 'info_outline',   'label' => 'How Auctions Work'],
                                ] as $help)
                                    <a href="#" class="flex items-center justify-between px-5 py-3 hover:bg-gray-50 transition group">
                                        <div class="flex items-center gap-3">
                                            <span class="material-icons-round text-gray-400 group-hover:text-blue-600 transition" style="font-size:18px">{{ $help['icon'] }}</span>
                                            <span class="text-sm text-gray-700 font-medium group-hover:text-blue-600 transition">{{ $help['label'] }}</span>
                                        </div>
                                        <span class="material-icons-round text-gray-300 group-hover:text-blue-400 transition" style="font-size:18px">chevron_right</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        {{-- Contact Us --}}
                        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                            <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-2">
                                <span class="material-icons-round text-gray-400" style="font-size:18px">contact_support</span>
                                <h4 class="font-bold text-gray-900 text-sm">Contact Us</h4>
                            </div>
                            <div class="p-5 space-y-3">
                                <a href="mailto:support@caymark.com"
                                   class="flex items-center gap-3 text-sm text-gray-700 hover:text-blue-600 transition group">
                                    <span class="material-icons-round text-gray-400 group-hover:text-blue-500 flex-shrink-0 transition" style="font-size:18px">mail</span>
                                    support@caymark.com
                                </a>
                                <div class="flex items-start gap-3 text-sm text-gray-700">
                                    <span class="material-icons-round text-gray-400 flex-shrink-0 mt-0.5" style="font-size:18px">phone</span>
                                    <span class="leading-snug">For urgent matters call or WhatsApp us at
                                        <a href="tel:+12428066275" class="font-semibold text-blue-600 hover:text-blue-700 transition">+1 (242) 806-6275</a>
                                    </span>
                                </div>
                            </div>
                        </div>

                    </div>{{-- end right sidebar --}}

                </div>
            </div>
            </div>
        </div>

<!-- Password Modal -->
<div id="passwordModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="text-base font-bold text-gray-900">Change Password</h3>
            <button type="button" onclick="hidePasswordModal()" class="text-gray-400 hover:text-gray-600">
                <span class="material-icons-round">close</span>
            </button>
        </div>
        <div class="p-6">
            <form method="POST" action="{{ route('buyer.user.change-password') }}">
                @csrf
                <!-- Current Password -->
                <div class="mb-4">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Current Password</label>
                    <div class="relative">
                        <input type="password" id="buyer_modal_current_password" name="current_password" required
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 pr-10 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600"
                                onclick="togglePasswordModal('buyer_modal_current_password', this)" aria-label="Toggle password visibility">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path class="eye-open" stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path class="eye-open" stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                <path class="eye-closed hidden" stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.477 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <!-- New Password -->
                <div class="mb-4">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">New Password</label>
                    <div class="relative">
                        <input type="password" id="buyer_modal_new_password" name="password" required
                               minlength="8" maxlength="15"
                               data-password-strength
                               data-cm-validate="password-register"
                               data-cm-label="New password"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 pr-10 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600"
                                onclick="togglePasswordModal('buyer_modal_new_password', this)" aria-label="Toggle password visibility">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path class="eye-open" stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path class="eye-open" stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                <path class="eye-closed hidden" stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.477 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                    <p class="text-[11px] text-gray-400 mt-1">8–15 characters · One uppercase · One number · One special character (e.g. !@#$%)</p>
                </div>
                <!-- Confirm Password -->
                <div class="mb-5">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Confirm New Password</label>
                    <div class="relative">
                        <input type="password" id="buyer_modal_confirm_password" name="password_confirmation" required
                               minlength="8" maxlength="15"
                               data-cm-match="#buyer_modal_new_password"
                               data-cm-label="Confirm password"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 pr-10 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600"
                                onclick="togglePasswordModal('buyer_modal_confirm_password', this)" aria-label="Toggle password visibility">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path class="eye-open" stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path class="eye-open" stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                <path class="eye-closed hidden" stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.477 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="flex items-center justify-between gap-3">
                    <a href="{{ route('password.request') }}" target="_blank"
                       class="text-xs text-blue-600 hover:text-blue-800">Forgot your password?</a>
                    <div class="flex gap-2">
                        <button type="button" onclick="hidePasswordModal()"
                                class="px-4 py-2 text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition">Cancel</button>
                        <button type="submit"
                                class="px-5 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-xl transition">Update Password</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

        <script>
function showTab(tabName) {
    document.querySelectorAll('.tab-content').forEach(c => { c.style.display = 'none'; });
    var el = document.getElementById('content-' + tabName);
    if (el) el.style.display = 'block';
    if (tabName === 'dashboard') setTimeout(initializeCharts, 50);
}
function showAuctionSection(section) {
    document.querySelectorAll('.auction-section').forEach(s => s.classList.add('hidden'));
    document.querySelectorAll('.auction-tab-button').forEach(b => {
        b.classList.remove('active', 'text-white', 'bg-gradient-to-r', 'from-blue-600', 'to-indigo-600', 'shadow-sm');
        b.classList.add('text-gray-600', 'hover:text-gray-900');
    });
    var el = document.getElementById('auction-section-' + section);
    if (el) el.classList.remove('hidden');
    var btn = document.getElementById('auction-' + section);
    if (btn) {
        btn.classList.add('active', 'text-white', 'bg-gradient-to-r', 'from-blue-600', 'to-indigo-600', 'shadow-sm');
        btn.classList.remove('text-gray-600', 'hover:text-gray-900');
    }
}
function showDashAuctionTab(tab) {
    document.querySelectorAll('.dash-auction-section').forEach(function(s) { s.classList.add('hidden'); });
    document.querySelectorAll('.dash-auction-tab').forEach(function(b) {
        b.classList.remove('text-white', 'bg-gradient-to-r', 'from-blue-600', 'to-indigo-600', 'shadow-sm');
        b.classList.add('text-gray-600', 'hover:text-gray-900');
    });
    var sec = document.getElementById('dash-auction-section-' + tab);
    if (sec) sec.classList.remove('hidden');
    var btn = document.getElementById('dash-auction-tab-' + tab);
    if (btn) {
        btn.classList.add('text-white', 'bg-gradient-to-r', 'from-blue-600', 'to-indigo-600', 'shadow-sm');
        btn.classList.remove('text-gray-600', 'hover:text-gray-900');
    }
}
function showPasswordModal() { document.getElementById('passwordModal').classList.remove('hidden'); }
function hidePasswordModal() { document.getElementById('passwordModal').classList.add('hidden'); }
document.getElementById('passwordModal')?.addEventListener('click', function(e) { if (e.target === this) hidePasswordModal(); });

function togglePasswordModal(inputId, btn) {
    var input = document.getElementById(inputId);
    if (!input) return;
    var showing = input.type === 'text';
    input.type = showing ? 'password' : 'text';
    btn.querySelectorAll('.eye-open').forEach(function(el) { el.classList.toggle('hidden', !showing); });
    btn.querySelectorAll('.eye-closed').forEach(function(el) { el.classList.toggle('hidden', showing); });
}

document.addEventListener('DOMContentLoaded', function() {
    var tab = @json($activeTab ?? 'dashboard');
        showTab(tab);
    if (tab === 'auctions') showAuctionSection(new URLSearchParams(window.location.search).get('section') || 'current');
    if (tab === 'support') {
        var banner = document.getElementById('support-success-banner');
        if (banner) banner.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
    setInterval(updateCountdowns, 1000);
    updateCountdowns();

    // Dashboard phone verification (auth users) – mirrors register page behaviour
    (function() {
        var sendBtn = document.getElementById('dash-send-code-btn');
        var verifyBtn = document.getElementById('dash-verify-phone-btn');
        var phoneInput = document.getElementById('dash_phone_input');
        var countrySelect = document.getElementById('dash_phone_country');
        var phoneFull = document.getElementById('dash_phone_full');
        var codeInput = document.getElementById('dash_phone_code_input');
        var verifyRow = document.getElementById('dash-phone-verify-row');
        var verifiedBadge = document.getElementById('dash-phone-verified-badge');
        var phoneDisplay = document.getElementById('dashboard_phone_display');
        if (!sendBtn || !verifyBtn || !phoneInput || !countrySelect) return;

        var csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
            document.querySelector('input[name="_token"]')?.value || '';

        var sendUrl = '{{ route("registration.phone.send-code") }}';
        var verifyUrl = '{{ route("registration.phone.verify") }}';

        function getFullPhone() {
            var code = (countrySelect && countrySelect.value) ? countrySelect.value.trim() : '';
            var num = (phoneInput && phoneInput.value) ? phoneInput.value.trim().replace(/^0+/, '') : '';
            if (!code || !num) return '';
            return '+' + code + num;
        }

        function setFullPhoneInput() {
            if (phoneFull) phoneFull.value = getFullPhone();
        }

        function cmToast(type, title, sub) {
            if (window.CaymarkUI) {
                type === 'success' ? CaymarkUI.showSuccess(title, sub || '') : CaymarkUI.showError(title, sub || '');
            } else {
                alert(title + (sub ? '\n' + sub : ''));
            }
        }

        sendBtn.addEventListener('click', function() {
            var phone = getFullPhone();
            if (!phone) {
                cmToast('error', 'Phone number required', 'Select a country code and enter your number.');
                return;
            }
            setFullPhoneInput();
            sendBtn.disabled = true;
            sendBtn.textContent = 'Sending…';
            fetch(sendUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin',
                body: JSON.stringify({ phone: phone })
            }).then(function(r) {
                return r.text().then(function(t) {
                    var j = {};
                    try { j = t ? JSON.parse(t) : {}; } catch (e) {}
                    return { ok: r.ok, status: r.status, data: j };
                });
            }).then(function(res) {
                sendBtn.disabled = false;
                sendBtn.textContent = 'Send code';
                if (res.ok && res.data && res.data.success) {
                    if (verifyRow) verifyRow.classList.remove('hidden');
                    if (codeInput) { codeInput.value = ''; codeInput.focus(); }
                    cmToast('success', 'Code sent!', res.data.message || 'Enter the 6-digit code sent to your phone.');
                    return;
                }
                var errMsg = (res.data && res.data.message) ||
                    (res.data && res.data.errors && res.data.errors.phone && res.data.errors.phone[0]) ||
                    ('Could not send SMS (HTTP ' + res.status + '). Please try again.');
                cmToast('error', 'Could not send code', errMsg);
            }).catch(function() {
                sendBtn.disabled = false;
                sendBtn.textContent = 'Send code';
                cmToast('error', 'Request failed', 'Something went wrong. Please try again.');
            });
        });

        verifyBtn.addEventListener('click', function() {
            var phone = getFullPhone();
            var code = codeInput ? codeInput.value.trim() : '';
            if (!phone || !code) {
                cmToast('error', 'Missing information', 'Enter your phone number and the 6-digit code.');
                return;
            }
            verifyBtn.disabled = true;
            verifyBtn.textContent = 'Verifying…';
            fetch(verifyUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin',
                body: JSON.stringify({ phone: phone, code: code })
            }).then(function(r) {
                return r.text().then(function(t) {
                    var j = {};
                    try { j = t ? JSON.parse(t) : {}; } catch (e) {}
                    return { ok: r.ok, status: r.status, data: j };
                });
            }).then(function(res) {
                verifyBtn.disabled = false;
                verifyBtn.textContent = 'Verify & Save';
                if (res.ok && res.data && res.data.success) {
                    if (phoneDisplay) phoneDisplay.textContent = res.data.phone || phone;
                    if (verifiedBadge) {
                        verifiedBadge.classList.remove('hidden');
                        verifiedBadge.classList.remove('bg-yellow-50', 'text-yellow-700', 'border-yellow-200');
                        verifiedBadge.classList.add('bg-green-50', 'text-green-700', 'border-green-200');
                        var span = verifiedBadge.querySelector('span');
                        if (span) span.textContent = 'Verified';
                    }
                    cmToast('success', 'Phone verified!', res.data.message || 'Your number has been confirmed and saved.');
                    return;
                }
                var errMsg = (res.data && res.data.message) ||
                    (res.data && res.data.errors && res.data.errors.code && res.data.errors.code[0]) ||
                    ('Verification failed (HTTP ' + res.status + ').');
                cmToast('error', 'Verification failed', errMsg);
            }).catch(function() {
                verifyBtn.disabled = false;
                verifyBtn.textContent = 'Verify & Save';
                cmToast('error', 'Request failed', 'Something went wrong. Please try again.');
            });
        });
    })();
});

function updateCountdowns() {
    document.querySelectorAll('[id^="countdown-"]').forEach(function(el) {
        var end = new Date(el.getAttribute('data-end-time')); var d = end - new Date();
        if (d <= 0) { el.textContent = 'Auction Ended'; return; }
        var days = Math.floor(d / 86400000), h = Math.floor((d % 86400000) / 3600000), m = Math.floor((d % 3600000) / 60000), s = Math.floor((d % 60000) / 1000);
        el.textContent = days > 0 ? days + 'd ' + h + 'h ' + m + 'm' : (h > 0 ? h + 'h ' + m + 'm ' + s + 's' : m + 'm ' + s + 's');
    });
}

function initializeCharts() {
    var spendingData = @json($spendingTrendsData ?? ['labels' => [], 'data' => []]);
    var ctx1 = document.getElementById('spendingTrendsChart');
    if (ctx1) {
        new Chart(ctx1, { type: 'line', data: { labels: spendingData.labels || [], datasets: [{ label: 'Spending ($)', data: spendingData.data || [], borderColor: 'rgb(59, 130, 246)', backgroundColor: 'rgba(59, 130, 246, 0.1)', tension: 0.4, fill: true }] }, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { callback: function(v) { return '$' + v; } } }, x: { grid: { display: false } } } } });
    }
    var winLossData = @json($winLossRatioData ?? ['labels' => [], 'data' => [], 'colors' => []]);
    var ctx2 = document.getElementById('winLossChart');
    if (ctx2) {
        new Chart(ctx2, { type: 'doughnut', data: { labels: winLossData.labels || [], datasets: [{ data: winLossData.data || [], backgroundColor: winLossData.colors || ['#10B981', '#EF4444'], borderWidth: 0 }] }, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } } });
    }
    var bidData = @json($biddingActivityData ?? ['labels' => [], 'counts' => [], 'amounts' => []]);
    var ctx3 = document.getElementById('biddingActivityChart');
    if (ctx3) {
        new Chart(ctx3, { type: 'line', data: { labels: bidData.labels || [], datasets: [{ label: 'Bid Count', data: bidData.counts || [], borderColor: 'rgb(59, 130, 246)', backgroundColor: 'rgba(59, 130, 246, 0.1)', tension: 0.4, fill: true, yAxisID: 'y' }, { label: 'Bid Amount ($)', data: bidData.amounts || [], borderColor: 'rgb(16, 185, 129)', backgroundColor: 'rgba(16, 185, 129, 0.1)', tension: 0.4, fill: true, yAxisID: 'y1' }] }, options: { responsive: true, maintainAspectRatio: false, interaction: { mode: 'index', intersect: false }, plugins: { legend: { position: 'top' } }, scales: { y: { type: 'linear', position: 'left', beginAtZero: true, title: { display: true, text: 'Bid Count' }, grid: { drawOnChartArea: true } }, y1: { type: 'linear', position: 'right', beginAtZero: true, title: { display: true, text: 'Amount ($)' }, grid: { drawOnChartArea: false }, ticks: { callback: function(v) { return '$' + v; } } }, x: { grid: { display: false } } } } });
    }
}

// Handle notification click: mark as read (if unread) and optionally navigate to link
function handleNotificationClick(element) {
    var card = element.closest ? element.closest('.notification-card') : element;
    if (!card) return;
    var notificationId = card.dataset.notificationId;
    var link = (card.dataset.link || '').trim();
    var isUnread = card.dataset.isUnread === 'true';
    if (link) {
        if (isUnread) {
            var csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || document.querySelector('input[name="_token"]')?.value || '';
            fetch('/buyer/notifications/' + notificationId + '/mark-read', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: JSON.stringify({})
            }).then(function() { window.location.href = link; }).catch(function() { window.location.href = link; });
        } else {
            window.location.href = link;
        }
        return;
    }
    if (isUnread) {
        markNotificationAsRead(notificationId, card);
    }
}

// Mark notification as read
function markNotificationAsRead(notificationId, element) {
    if (!element.dataset.isUnread || element.dataset.isUnread === 'false') {
        return; // Already read
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                      document.querySelector('input[name="_token"]')?.value || '';

    fetch(`/buyer/notifications/${notificationId}/mark-read`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({})
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove unread styling
            element.classList.remove('bg-blue-50', 'bg-amber-50', 'bg-emerald-50', 'bg-purple-50', 'border-blue-200', 'border-amber-200', 'border-emerald-200', 'border-purple-200', 'shadow-sm', 'cursor-pointer');
            element.classList.add('border-gray-200');
            element.dataset.isUnread = 'false';
            element.setAttribute('data-read-status', 'read');
            element.removeAttribute('onclick');

            // Remove unread dot
            const unreadDot = element.querySelector('.unread-dot');
            if (unreadDot) {
                unreadDot.remove();
            }

            // Update icon styling
            const iconContainer = element.querySelector('.flex-shrink-0.w-12');
            if (iconContainer) {
                iconContainer.classList.remove('bg-blue-50', 'bg-amber-50', 'bg-emerald-50', 'bg-purple-50', 'border-blue-200', 'border-amber-200', 'border-emerald-200', 'border-purple-200');
                iconContainer.classList.add('bg-gray-100', 'border-gray-200');
            }

            const icon = element.querySelector('.material-icons-round');
            if (icon) {
                icon.classList.remove('text-blue-600', 'text-amber-600', 'text-emerald-600', 'text-purple-600');
                icon.classList.add('text-gray-400');
            }

            // Update text styling
            const text = element.querySelector('.font-semibold');
            if (text) {
                text.classList.remove('text-gray-900');
                text.classList.add('text-gray-700');
            }

            // Update unread count in header
            updateUnreadCount();

            // If filtering by unread, hide this notification
            const currentFilter = window.notificationFilter || 'all';
            if (currentFilter === 'unread') {
                element.style.display = 'none';
                // Check if month group should be hidden
                const monthGroup = element.closest('.notification-month-group');
                if (monthGroup) {
                    const visibleCards = monthGroup.querySelectorAll('.notification-card[style*="display: block"], .notification-card:not([style*="display: none"])');
                    if (visibleCards.length === 0) {
                        monthGroup.style.display = 'none';
                    }
                }
            }
        }
    })
    .catch(error => {
        console.error('Error marking notification as read:', error);
    });
}

// Mark all notifications as read
function markAllNotificationsAsRead() {
    const btn = document.getElementById('read-all-notifications-btn');
    if (btn) btn.disabled = true;

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                      document.querySelector('input[name="_token"]')?.value || '';

    fetch('/buyer/notifications/mark-all-read', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({})
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.querySelectorAll('.notification-card[data-is-unread="true"]').forEach(card => {
                card.classList.remove('bg-blue-50', 'bg-amber-50', 'bg-emerald-50', 'bg-purple-50', 'border-blue-200', 'border-amber-200', 'border-emerald-200', 'border-purple-200', 'shadow-sm', 'cursor-pointer');
                card.classList.add('border-gray-200');
                card.dataset.isUnread = 'false';
                card.setAttribute('data-read-status', 'read');
                card.removeAttribute('onclick');
                const unreadDot = card.querySelector('.unread-dot');
                if (unreadDot) unreadDot.remove();
                const iconContainer = card.querySelector('.flex-shrink-0.w-12');
                if (iconContainer) {
                    iconContainer.classList.remove('bg-blue-50', 'bg-amber-50', 'bg-emerald-50', 'bg-purple-50', 'border-blue-200', 'border-amber-200', 'border-emerald-200', 'border-purple-200');
                    iconContainer.classList.add('bg-gray-100', 'border-gray-200');
                }
                const icon = card.querySelector('.material-icons-round');
                if (icon) {
                    icon.classList.remove('text-blue-600', 'text-amber-600', 'text-emerald-600', 'text-purple-600');
                    icon.classList.add('text-gray-400');
                }
                const text = card.querySelector('.font-semibold');
                if (text) {
                    text.classList.remove('text-gray-900');
                    text.classList.add('text-gray-700');
                }
            });
            const unreadDisplay = document.querySelector('.unread-count-display');
            if (unreadDisplay) unreadDisplay.style.display = 'none';
            if (btn) {
                btn.style.display = 'none';
                btn.disabled = false;
            }
            updateUnreadCount();
        }
    })
    .catch(error => {
        console.error('Error marking all as read:', error);
        if (btn) btn.disabled = false;
    });
}

// Update unread count in header and sidebar
function updateUnreadCount() {
    fetch('/buyer/notifications/unread-count')
        .then(response => response.json())
        .then(data => {
            const count = data.count || 0;

            // Update header badge
            const headerBadge = document.querySelector('.unread-count-badge');
            if (headerBadge) {
                if (count > 0) {
                    headerBadge.textContent = count;
                    headerBadge.style.display = 'flex';
                } else {
                    headerBadge.style.display = 'none';
                }
            }

            // Update sidebar badge
            const sidebarBadge = document.querySelector('.sidebar-notification-badge');
            if (sidebarBadge) {
                if (count > 0) {
                    sidebarBadge.textContent = count;
                    sidebarBadge.style.display = 'flex';
                } else {
                    sidebarBadge.style.display = 'none';
                }
            }

            // Update header unread count display
            const unreadDisplay = document.querySelector('.unread-count-display');
            if (unreadDisplay) {
                if (count > 0) {
                    unreadDisplay.innerHTML = `<span class="w-2 h-2 bg-blue-600 rounded-full animate-pulse"></span><span class="text-sm font-semibold text-blue-700">${count} unread</span>`;
                    unreadDisplay.style.display = 'flex';
                } else {
                    unreadDisplay.style.display = 'none';
                }
            }
        })
        .catch(error => {
            console.error('Error fetching unread count:', error);
        });
}

// Active-state management for notification filter buttons
function setNotificationFilter(filterType) {
    window.notificationFilter = filterType; // keep for read-marking logic
    document.querySelectorAll('.notif-filter-btn').forEach(function(btn) {
        btn.classList.remove('bg-blue-600', 'text-white', 'shadow-md');
        btn.classList.add('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
    });
    var activeBtn = document.getElementById('notif-filter-' + filterType);
    if (activeBtn) {
        activeBtn.classList.remove('bg-gray-100', 'text-gray-700', 'hover:bg-gray-200');
        activeBtn.classList.add('bg-blue-600', 'text-white', 'shadow-md');
    }
    filterNotifications(filterType);
}

// Filter notifications by read/unread status
function filterNotifications(filterType) {
    const allCards = document.querySelectorAll('.notification-card');
    const monthGroups = document.querySelectorAll('.notification-month-group');

    allCards.forEach(card => {
        const readStatus = card.getAttribute('data-read-status');

        if (filterType === 'all') {
            card.style.display = '';
        } else if (filterType === 'unread') {
            if (readStatus === 'unread') {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        } else if (filterType === 'read') {
            if (readStatus === 'read') {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        }
    });

    // Hide month groups that have no visible notifications
    monthGroups.forEach(group => {
        const cards = group.querySelectorAll('.notification-card');
        let hasVisible = false;
        cards.forEach(card => {
            if (card.style.display !== 'none') {
                hasVisible = true;
            }
        });

        if (!hasVisible) {
            group.style.display = 'none';
        } else {
            group.style.display = '';
        }
    });
}

// Update count on page load
document.addEventListener('DOMContentLoaded', function() {
    updateUnreadCount();
});
</script>

@endsection
