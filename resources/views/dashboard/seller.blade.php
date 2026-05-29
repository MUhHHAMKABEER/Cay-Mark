@extends('layouts.dashboard')

@section('title', 'Seller Dashboard - CayMark')

@section('content')
<style>
/* ── Finances info tooltip ───────────────────────────────── */
.fin-tip-wrap {
    position: relative;
    display: inline-flex;
    align-items: center;
    cursor: help;
}
.fin-tip {
    visibility: hidden;
    opacity: 0;
    position: absolute;
    left: calc(100% + 8px);
    top: 50%;
    transform: translateY(-50%);
    width: 220px;
    background: #1e293b;
    color: #f8fafc;
    font-size: 11.5px;
    line-height: 1.55;
    border-radius: 8px;
    padding: 8px 11px;
    z-index: 9999;
    pointer-events: none;
    white-space: normal;
    box-shadow: 0 8px 24px rgba(0,0,0,0.22);
    transition: opacity 0.15s, visibility 0.15s;
}
/* Arrow pointing left toward the icon */
.fin-tip::before {
    content: '';
    position: absolute;
    right: 100%;
    top: 50%;
    transform: translateY(-50%);
    border: 5px solid transparent;
    border-right-color: #1e293b;
}
.fin-tip-wrap:hover .fin-tip {
    visibility: visible;
    opacity: 1;
}

.notifications-scrollbar { max-height: 65vh; overflow-y: scroll !important; overflow-x: hidden; }
.notifications-scrollbar::-webkit-scrollbar { width: 6px; }
.notifications-scrollbar::-webkit-scrollbar-track { background: transparent; }
.notifications-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 6px; }
.notifications-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

.dash-no-scrollbar { scrollbar-width: none; -ms-overflow-style: none; }
.dash-no-scrollbar::-webkit-scrollbar { display: none; }

.seller-auctions-view-toggle-btn { transition: background 0.15s ease, color 0.15s ease; }
.seller-auctions-view-toggle-btn.is-active { background: #fff; color: #2563eb; box-shadow: 0 1px 3px rgba(15,23,42,0.08), 0 0 0 1px rgba(229,231,235,0.9); }
.seller-auctions-view-toggle-btn:not(.is-active) { color: #6b7280; }
.seller-auctions-view-toggle-btn:not(.is-active):hover { color: #374151; }
/* My Auctions — list view mode */
#content-auctions.seller-auctions-view-list .seller-auction-grid { display: flex !important; flex-direction: column; gap: 0.75rem; }
#content-auctions.seller-auctions-view-list .s-card { flex-direction: row !important; }
@media (min-width: 768px) {
    #content-auctions.seller-auctions-view-list .s-card-img { width: 10rem; min-width: 10rem; height: auto; min-height: 7rem; }
}
</style>
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns@3.0.0/dist/chartjs-adapter-date-fns.bundle.min.js"></script>
<div class="w-full h-full bg-gray-50" style="min-height: calc(100vh - 0px); padding: 0;">
    <div class="w-full h-full px-3 sm:px-4 lg:px-6 py-3">
        
        <!-- Content Area - No horizontal tabs, controlled by sidebar -->
        <div class="bg-white rounded-xl shadow-sm h-full" style="min-height: calc(100vh - 60px);">
            <!-- DASHBOARD TAB (Main Overview with Charts) -->
            <div id="content-dashboard" class="tab-content p-4" style="display: none; height: 100%; overflow-y: auto;">
                @if(!$user->business_license_path)
                {{-- ═══ CASUAL / INDIVIDUAL SELLER DASHBOARD ═══ --}}
                @php
                    $casualUnread        = isset($notifications) ? $notifications->whereNull('read_at')->count() : 0;
                    $casualPendingPayout = collect($pendingPayouts ?? [])->sum('net_payout');
                    $casualActivity      = $recentAuctionActivity ?? [];
                    $casualListings      = $topActiveListings ?? collect();
                @endphp

                <!-- ── Welcome Header ── -->
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 tracking-tight">
                            Welcome Back, {{ $user->name }}! 👋
                        </h2>
                        <div class="flex flex-wrap items-center gap-2 mt-2">
                            <span class="inline-flex items-center px-3 py-1 bg-gray-100 rounded-full text-xs font-semibold text-gray-600">
                                Casual Seller Account
                            </span>
                            <a href="{{ route('upgrade.membership') }}"
                               class="inline-flex items-center gap-1.5 px-3 py-1 bg-white border border-gray-300 rounded-full text-xs font-semibold text-gray-700 transition-all duration-200 shadow-sm"
                               onmouseover="this.style.borderColor='#063466';this.style.color='#063466';this.style.backgroundColor='#eff6ff';"
                               onmouseout="this.style.borderColor='';this.style.color='';this.style.backgroundColor='';">
                                <span class="material-icons-round" style="font-size:13px">arrow_upward</span>
                                Upgrade to Business Seller
                            </a>
                        </div>
                    </div>
                    <x-ui.notification-bell
                        :user="$user"
                        :notifications="($notifications ?? collect())->take(4)"
                        :unread-count="$casualUnread"
                        :notifications-url="route('seller.notifications')"
                    />
                </div>

                <!-- ── Row 1: Quick Stats (left) + Finances Overview (right) ── -->
                <div class="grid grid-cols-1 lg:grid-cols-5 gap-4 mb-4">

                    <!-- Quick Stats — 3 cards -->
                    <div class="lg:col-span-3 grid grid-cols-3 gap-3">
                        <!-- Active Listings -->
                        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 flex flex-col gap-2">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                    <span class="material-icons-round text-blue-600" style="font-size:16px">trending_up</span>
                                </div>
                                <span class="text-xs font-semibold text-gray-500">Active Listings</span>
                            </div>
                            <p class="text-3xl font-bold text-gray-900 leading-none">{{ $auctionSummary['current_count'] ?? 0 }}</p>
                            <p class="text-xs text-gray-400">Live listings</p>
                        </div>
                        <!-- Pending -->
                        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 flex flex-col gap-2">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-amber-100 flex items-center justify-center flex-shrink-0">
                                    <span class="material-icons-round text-amber-500" style="font-size:16px">schedule</span>
                                </div>
                                <span class="text-xs font-semibold text-gray-500">Pending</span>
                            </div>
                            <p class="text-3xl font-bold text-amber-500 leading-none">{{ $pendingListingsCount ?? 0 }}</p>
                            <p class="text-xs text-gray-400">Awaiting approval</p>
                        </div>
                        <!-- Sold -->
                        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 flex flex-col gap-2">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0">
                                    <span class="material-icons-round text-emerald-500" style="font-size:16px">check_circle</span>
                                </div>
                                <span class="text-xs font-semibold text-gray-500">Sold</span>
                            </div>
                            <p class="text-3xl font-bold text-emerald-500 leading-none">{{ $completedSalesCount ?? 0 }}</p>
                            <p class="text-xs text-gray-400">Completed sales</p>
                        </div>
                    </div>

                    <!-- Finances Overview -->
                    <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-sm font-bold text-gray-900">Finances Overview</h3>
                            <span class="material-icons-round text-gray-400" style="font-size:18px">arrow_forward</span>
                        </div>
                        <!-- Total Earnings -->
                        <div class="mb-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-1.5">
                                    <span class="text-sm font-medium text-gray-700">Total Earnings</span>
                                    <div class="relative group/te">
                                        <span class="material-icons-round text-gray-400 cursor-help" style="font-size:14px">info</span>
                                        <div class="pointer-events-none absolute left-5 -top-1 z-20 w-52 rounded-lg bg-gray-900 text-white text-xs px-3 py-2 opacity-0 group-hover/te:opacity-100 transition-opacity duration-200 shadow-xl">
                                            Total amount paid out to you by Caymark.
                                        </div>
                                    </div>
                                </div>
                                <span class="text-base font-bold text-emerald-600">${{ number_format($totalEarnings ?? 0, 2) }}</span>
                            </div>
                            <p class="text-xs text-gray-400 mt-0.5">Total amount paid out to you by Caymark.</p>
                        </div>
                        <div class="border-t border-gray-100 pt-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-1.5">
                                    <span class="text-sm font-medium text-gray-700">Pending Payout</span>
                                    <div class="relative group/pp">
                                        <span class="material-icons-round text-gray-400 cursor-help" style="font-size:14px">info</span>
                                        <div class="pointer-events-none absolute left-5 -top-1 z-20 w-52 rounded-lg bg-gray-900 text-white text-xs px-3 py-2 opacity-0 group-hover/pp:opacity-100 transition-opacity duration-200 shadow-xl">
                                            Funds currently awaiting payout processing.
                                        </div>
                                    </div>
                                </div>
                                <span class="text-base font-bold text-amber-500">${{ number_format($casualPendingPayout, 2) }}</span>
                            </div>
                            <p class="text-xs text-gray-400 mt-0.5">Funds currently awaiting payout processing.</p>
                        </div>
                    </div>

                </div>{{-- end row 1 --}}

                <!-- ── Row 2: Recent Activity ── -->
                <div class="mb-4">

                    <!-- Recent Activity -->
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                                <span class="material-icons-round text-blue-500" style="font-size:16px">bolt</span>
                                Recent Activity
                            </h3>
                            <button onclick="showTab('content-auctions')" class="text-xs font-semibold text-blue-600 hover:text-blue-800 transition-colors">View All Activity</button>
                        </div>
                        @if(count($casualActivity) === 0)
                            <div class="flex flex-col items-center justify-center py-8 text-center">
                                <span class="material-icons-round text-gray-300 mb-2" style="font-size:36px">history</span>
                                <p class="text-sm text-gray-400">No auction activity yet.</p>
                            </div>
                        @else
                            <div class="divide-y divide-gray-50">
                                @foreach($casualActivity as $casualEvent)
                                    <div class="flex items-center gap-3 py-2.5">
                                        <div class="flex-shrink-0 w-7 h-7 rounded-full flex items-center justify-center
                                            @if(($casualEvent['color'] ?? '') === 'blue') bg-blue-100
                                            @elseif(($casualEvent['color'] ?? '') === 'green') bg-green-100
                                            @elseif(($casualEvent['color'] ?? '') === 'purple') bg-purple-100
                                            @elseif(($casualEvent['color'] ?? '') === 'orange') bg-orange-100
                                            @elseif(($casualEvent['color'] ?? '') === 'red') bg-red-100
                                            @else bg-gray-100 @endif">
                                            <span class="material-icons-round
                                                @if(($casualEvent['color'] ?? '') === 'blue') text-blue-600
                                                @elseif(($casualEvent['color'] ?? '') === 'green') text-green-600
                                                @elseif(($casualEvent['color'] ?? '') === 'purple') text-purple-600
                                                @elseif(($casualEvent['color'] ?? '') === 'orange') text-orange-600
                                                @elseif(($casualEvent['color'] ?? '') === 'red') text-red-600
                                                @else text-gray-500 @endif"
                                                style="font-size:13px">{{ $casualEvent['icon'] ?? 'circle' }}</span>
                                        </div>
                                        <p class="flex-1 text-sm text-gray-700 leading-snug">{{ $casualEvent['description'] ?? '' }}</p>
                                        <span class="flex-shrink-0 text-xs text-gray-400 whitespace-nowrap">
                                            @if(isset($casualEvent['timestamp']))
                                                {{ ($casualEvent['timestamp'] instanceof \Carbon\Carbon ? $casualEvent['timestamp'] : \Carbon\Carbon::parse($casualEvent['timestamp']))->diffForHumans() }}
                                            @endif
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>


                </div>{{-- end row 2 --}}

                <!-- ── Row 3: My Listings (full width) ── -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                            <span class="material-icons-round text-gray-500" style="font-size:17px">directions_car</span>
                            My Listings
                        </h3>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('seller.auctions') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-800 transition-colors">View All Listings</a>
                            <a href="{{ route('seller.listings.create') }}"
                               class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg transition-colors duration-200">
                                <span class="material-icons-round" style="font-size:13px">add</span>
                                Create Listing
                            </a>
                        </div>
                    </div>
                    @if($casualListings->isEmpty())
                        <div class="flex flex-col items-center justify-center py-12 text-center">
                            <span class="material-icons-round text-gray-300 mb-2" style="font-size:48px">directions_car</span>
                            <p class="text-sm text-gray-400 mb-3">No active listings yet.</p>
                            <a href="{{ route('seller.listings.create') }}"
                               class="inline-flex items-center gap-1 text-sm font-semibold text-blue-600 hover:text-blue-800">
                                <span class="material-icons-round" style="font-size:15px">add</span>
                                Create your first listing
                            </a>
                        </div>
                    @else
                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                            @foreach($casualListings as $cl)
                            @php
                                $clImg    = $cl->images->first();
                                $clImage  = $clImg ? (str_contains($clImg->image_path ?? '', '/') ? asset($clImg->image_path) : asset('uploads/listings/' . $clImg->image_path)) : null;
                                $clTitle  = trim(implode(' ', array_filter([$cl->year, $cl->make, $cl->model]))) ?: 'Vehicle #' . $cl->id;
                                $clTopBid = $cl->bids()->max('amount');
                            @endphp
                            <a href="{{ route('seller.listings.show', $cl->id) }}"
                               class="bg-white rounded-xl border border-gray-200 hover:border-blue-300 hover:shadow-md overflow-hidden transition-all duration-200 group block">
                                <!-- Image -->
                                <div class="relative w-full" style="padding-top:62%">
                                    @if($clImage)
                                        <img src="{{ $clImage }}" alt="{{ $clTitle }}"
                                             class="absolute inset-0 w-full h-full object-cover">
                                    @else
                                        <div class="absolute inset-0 bg-gray-100 flex items-center justify-center">
                                            <span class="material-icons-round text-gray-400" style="font-size:32px">directions_car</span>
                                        </div>
                                    @endif
                                    <!-- LIVE badge -->
                                    <span class="absolute top-2 left-2 px-2 py-0.5 bg-emerald-500 text-white text-[10px] font-bold rounded-md tracking-wide">LIVE</span>
                                    <!-- Menu dots -->
                                    <button onclick="event.preventDefault()" class="absolute top-2 right-2 w-6 h-6 rounded-full bg-black/30 hover:bg-black/50 flex items-center justify-center transition-colors">
                                        <span class="material-icons-round text-white" style="font-size:14px">more_horiz</span>
                                    </button>
                                </div>
                                <!-- Card body -->
                                <div class="p-3">
                                    <p class="text-sm font-bold text-gray-900 truncate group-hover:text-blue-700 mb-2">{{ $clTitle }}</p>
                                    <div class="grid grid-cols-2 gap-x-2 mb-2">
                                        <p class="text-[11px] text-gray-400 font-medium">Current Bid</p>
                                        <p class="text-[11px] text-gray-400 font-medium">Time Left</p>
                                        <p class="text-sm font-bold text-emerald-600">
                                            @if($clTopBid) ${{ number_format($clTopBid, 0) }} @else <span class="text-gray-400 font-normal text-xs">No bids</span> @endif
                                        </p>
                                        <p class="text-sm font-semibold text-gray-800">
                                            @if($cl->ends_at)
                                                <span class="dash-countdown" data-ends="{{ $cl->ends_at->toIso8601String() }}">—</span>
                                            @else —
                                            @endif
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-3 pt-2 border-t border-gray-100">
                                        <span class="flex items-center gap-1 text-[11px] text-gray-400">
                                            <span class="material-icons-round" style="font-size:12px">visibility</span>
                                            {{ number_format($cl->view_count ?? 0) }} Views
                                        </span>
                                        <span class="flex items-center gap-1 text-[11px] text-gray-400">
                                            <span class="material-icons-round" style="font-size:12px">gavel</span>
                                            {{ $cl->bids_count ?? 0 }} Bids
                                        </span>
                                    </div>
                                </div>
                            </a>
                            @endforeach
                        </div>
                    @endif
                </div>{{-- end My Listings --}}
                @else
                {{-- BUSINESS SELLER: Professional Dashboard --}}
                @php
                    $perfData = $performanceInsightsData ?? [];
                    $unreadCount = isset($notifications) ? $notifications->whereNull('read_at')->count() : 0;
                @endphp

                <!-- ── Dashboard Header: Welcome + Plan + Notification Bell ── -->
                <div class="flex items-start justify-between mb-5">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 tracking-tight">
                            Welcome back, {{ $user->name }}!
                        </h2>
                        <p class="text-sm text-gray-500 mt-0.5">Here's what's happening with your account today.</p>
                        @php
                            // Compute plan expiry: prefer ends_at, fall back to starts_at + package duration_days
                            $planExpiry = null;
                            if (isset($activeSubscription) && $activeSubscription) {
                                if ($activeSubscription->ends_at) {
                                    $planExpiry = $activeSubscription->ends_at;
                                } elseif ($activeSubscription->starts_at && optional($activeSubscription->package)->duration_days) {
                                    $planExpiry = $activeSubscription->starts_at->copy()->addDays($activeSubscription->package->duration_days);
                                }
                            }
                        @endphp
                        <div class="flex flex-col gap-1 mt-3">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-blue-50 border border-blue-200 rounded-full text-sm font-semibold text-blue-800 self-start">
                                <span class="material-icons-round" style="font-size:14px">workspace_premium</span>
                                Business Seller Plan
                            </span>
                            @if($planExpiry)
                                <span class="inline-flex items-center gap-1 text-xs text-gray-500 font-medium pl-1">
                                    <span class="material-icons-round" style="font-size:13px;color:#9ca3af">event</span>
                                    Plan expires: {{ $planExpiry->format('M d, Y') }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <a href="{{ route('seller.notifications') }}"
                       class="relative flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-xl bg-gray-100 hover:bg-gray-200 transition"
                       title="Notifications">
                        <span class="material-icons-round text-gray-600 text-xl">notifications</span>
                        @if($unreadCount > 0)
                            <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full ring-2 ring-white"></span>
                        @endif
                    </a>
                </div>

                <!-- ── Main two-column layout ── -->
                <div class="grid grid-cols-1 xl:grid-cols-5 gap-4">

                    <!-- LEFT COLUMN (3/5): Stats → Activity → Auctions → Quick Actions -->
                    <div class="xl:col-span-3 space-y-4">

                        <!-- Quick Stats Row -->
                        <div class="grid grid-cols-3 gap-3">
                            <!-- Active -->
                            <div class="bg-white border border-blue-100 rounded-xl p-4 shadow-sm">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="material-icons-round text-blue-500" style="font-size:18px">bolt</span>
                                    <span class="text-xs font-semibold text-blue-600 uppercase tracking-wide">Active</span>
                                </div>
                                <p class="text-3xl font-extrabold text-gray-900 leading-none">{{ $auctionSummary['current_count'] ?? 0 }}</p>
                                <p class="text-xs text-gray-500 mt-1">Live auctions</p>
                            </div>
                            <!-- Pending -->
                            <div class="bg-white border border-amber-100 rounded-xl p-4 shadow-sm">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="material-icons-round text-amber-500" style="font-size:18px">hourglass_top</span>
                                    <span class="text-xs font-semibold text-amber-600 uppercase tracking-wide">Pending</span>
                                </div>
                                <p class="text-3xl font-extrabold text-gray-900 leading-none">{{ $pendingListingsCount ?? 0 }}</p>
                                <p class="text-xs text-gray-500 mt-1">Awaiting approval / payment</p>
                            </div>
                            <!-- Sold -->
                            <div class="bg-white border border-green-100 rounded-xl p-4 shadow-sm">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="material-icons-round text-green-500" style="font-size:18px">check_circle</span>
                                    <span class="text-xs font-semibold text-green-600 uppercase tracking-wide">Sold</span>
                                </div>
                                <p class="text-3xl font-extrabold text-gray-900 leading-none">{{ $completedSalesCount ?? 0 }}</p>
                                <p class="text-xs text-gray-500 mt-1">Completed auctions</p>
                            </div>
                        </div>

                        <!-- Recent Activity -->
                        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="font-bold text-gray-900 flex items-center gap-2 text-sm">
                                    <span class="material-icons-round text-blue-500" style="font-size:18px">timeline</span>
                                    Recent Activity
                                </h3>
                                <a href="{{ route('seller.auctions') }}"
                                   class="text-xs text-blue-600 hover:underline font-semibold">View All Activity</a>
                            </div>
                            @if(isset($recentAuctionActivity) && $recentAuctionActivity->isNotEmpty())
                                <div class="space-y-3">
                                    @foreach($recentAuctionActivity as $activity)
                                        @php
                                            $cm = [
                                                'blue'   => ['bg' => 'bg-blue-50',   'text' => 'text-blue-600'],
                                                'green'  => ['bg' => 'bg-green-50',  'text' => 'text-green-600'],
                                                'orange' => ['bg' => 'bg-orange-50', 'text' => 'text-orange-500'],
                                                'red'    => ['bg' => 'bg-red-50',    'text' => 'text-red-600'],
                                                'purple' => ['bg' => 'bg-purple-50', 'text' => 'text-purple-600'],
                                            ];
                                            $c = $cm[$activity['color']] ?? $cm['blue'];
                                        @endphp
                                        <div class="flex items-start gap-3">
                                            <div class="w-8 h-8 {{ $c['bg'] }} rounded-lg flex items-center justify-center flex-shrink-0">
                                                <span class="material-icons-round {{ $c['text'] }}" style="font-size:16px">{{ $activity['icon'] }}</span>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm text-gray-800 font-medium leading-snug">{{ $activity['description'] }}</p>
                                                <p class="text-xs text-gray-400 mt-0.5">{{ $activity['timestamp']->diffForHumans() }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-sm text-gray-400 italic">No recent auction activity yet.</p>
                            @endif
                        </div>

                        <!-- Auctions Preview (top 4 by activity) -->
                        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
                            <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
                                <div>
                                    <h3 class="font-bold text-gray-900 text-sm">Your Auctions</h3>
                                    @if(isset($topActiveListings) && $topActiveListings->isNotEmpty())
                                        <p class="text-xs text-gray-400">Showing top activity</p>
                                    @endif
                                </div>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('seller.auctions') }}"
                                       class="text-xs text-blue-600 hover:underline font-semibold">View All Auctions</a>
                                    <a href="{{ route('seller.listings.create') }}"
                                       class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold text-white transition hover:opacity-90"
                                       style="background: linear-gradient(135deg, #063466 0%, #1e3a8a 100%);">
                                        <span class="material-icons-round" style="font-size:14px">add</span>
                                        Create Listing
                                    </a>
                                </div>
                            </div>
                            @if(isset($topActiveListings) && $topActiveListings->isNotEmpty())
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                    @foreach($topActiveListings as $tl)
                                        @php
                                            $tlImg = $tl->images->first();
                                            $tlUrl = $tlImg ? (str_contains($tlImg->image_path ?? '', '/') ? asset($tlImg->image_path) : asset('uploads/listings/' . $tlImg->image_path)) : null;
                                            $tlEnd = $tl->auction_end_time ?? ($tl->auction_start_time ? \Carbon\Carbon::parse($tl->auction_start_time)->addDays($tl->auction_duration ?? 7) : null);
                                        @endphp
                                        <div class="border border-gray-200 rounded-xl overflow-hidden hover:shadow-md transition group">
                                            <div class="relative h-24 bg-gray-100 overflow-hidden">
                                                @if($tlUrl)
                                                    <img src="{{ $tlUrl }}"
                                                         alt="{{ $tl->make }}"
                                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                                @else
                                                    <div class="w-full h-full flex items-center justify-center">
                                                        <span class="material-icons-round text-gray-300 text-3xl">directions_car</span>
                                                    </div>
                                                @endif
                                                <span class="absolute top-1.5 left-1.5 bg-green-500 text-white text-xs font-bold px-1.5 py-0.5 rounded-md leading-none">LIVE</span>
                                                <button class="absolute top-1.5 right-1.5 w-5 h-5 flex items-center justify-center rounded-full bg-white/70">
                                                    <span class="material-icons-round text-gray-500" style="font-size:13px">star_border</span>
                                                </button>
                                            </div>
                                            <div class="p-2.5">
                                                <p class="text-xs font-semibold text-gray-900 truncate leading-tight">{{ $tl->year }} {{ $tl->make }} {{ $tl->model }}</p>
                                                <p class="text-sm font-bold text-blue-700 mt-0.5">${{ number_format($tl->current_bid ?? $tl->starting_price ?? 0, 0) }}</p>
                                                @if($tlEnd && $tlEnd->isFuture())
                                                    <p class="text-xs text-gray-400 mt-0.5 dash-countdown" data-end="{{ $tlEnd->toIso8601String() }}">—</p>
                                                @endif
                                                <div class="flex items-center gap-2 mt-1.5 text-xs text-gray-400">
                                                    <span class="flex items-center gap-0.5">
                                                        <span class="material-icons-round" style="font-size:11px">visibility</span>
                                                        {{ number_format($tl->view_count ?? 0) }}
                                                    </span>
                                                    <span class="flex items-center gap-0.5">
                                                        <span class="material-icons-round" style="font-size:11px">gavel</span>
                                                        {{ $tl->bids_count ?? 0 }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8 text-gray-400">
                                    <span class="material-icons-round text-4xl mb-2 block">inventory_2</span>
                                    <p class="text-sm">No live auctions right now.</p>
                                    <a href="{{ route('seller.listings.create') }}" class="text-sm text-blue-600 hover:underline mt-1 inline-block font-medium">Create your first listing</a>
                                </div>
                            @endif
                        </div>

                    </div>{{-- /LEFT COLUMN --}}

                    <!-- RIGHT COLUMN (2/5): Finances → Performance Insights → Quick Actions -->
                    <div class="xl:col-span-2 space-y-4">

                        <!-- Finances Panel -->
                        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="font-bold text-gray-900 flex items-center gap-2 text-sm">
                                    <span class="material-icons-round text-green-500" style="font-size:18px">account_balance_wallet</span>
                                    Finances
                                </h3>
                                <a href="{{ route('seller.account') }}"
                                   class="text-xs text-blue-600 hover:underline font-semibold flex items-center gap-0.5">
                                    View Finances
                                    <span class="material-icons-round" style="font-size:14px">arrow_forward</span>
                                </a>
                            </div>
                            <div class="space-y-3">
                                <!-- Total Earnings -->
                                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-sm font-semibold text-gray-700">Total Earnings</span>
                                        <span class="fin-tip-wrap">
                                            <span class="material-icons-round text-gray-300 hover:text-gray-500 transition" style="font-size:15px">info_outline</span>
                                            <span class="fin-tip">Total amount successfully paid out to you by CayMark.</span>
                                        </span>
                                    </div>
                                    <span class="text-base font-bold text-green-600">${{ number_format($totalEarnings ?? 0, 0) }}</span>
                                </div>
                                <!-- To Be Received -->
                                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-sm font-semibold text-gray-700">To Be Received</span>
                                        <span class="fin-tip-wrap">
                                            <span class="material-icons-round text-gray-300 hover:text-gray-500 transition" style="font-size:15px">info_outline</span>
                                            <span class="fin-tip">Completed sales awaiting pickup confirmation or payout processing.</span>
                                        </span>
                                    </div>
                                    <span class="text-base font-bold text-blue-600">${{ number_format($toBeReceived ?? 0, 0) }}</span>
                                </div>
                                <!-- Pending Payout -->
                                <div class="flex items-center justify-between py-2">
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-sm font-semibold text-gray-700">Pending Payout</span>
                                        <span class="fin-tip-wrap">
                                            <span class="material-icons-round text-gray-300 hover:text-gray-500 transition" style="font-size:15px">info_outline</span>
                                            <span class="fin-tip">Funds currently being processed for payout.</span>
                                        </span>
                                    </div>
                                    <span class="text-base font-bold text-amber-600">${{ number_format(collect($pendingPayouts ?? [])->sum('net_payout'), 0) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Performance Insights -->
                        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="font-bold text-gray-900 flex items-center gap-2 text-sm">
                                    <span class="material-icons-round text-blue-500" style="font-size:18px">trending_up</span>
                                    Performance Insights
                                </h3>
                                <select id="perf-period-select"
                                        onchange="updateInsightsChart()"
                                        class="text-xs border border-gray-200 rounded-lg px-2 py-1 bg-gray-50 text-gray-600 focus:outline-none focus:ring-1 focus:ring-blue-400 cursor-pointer">
                                    <option value="week">This Week</option>
                                    <option value="month">This Month</option>
                                    <option value="year">This Year</option>
                                </select>
                            </div>
                            <!-- Metric Tabs -->
                            <div class="flex flex-wrap gap-1 mb-3">
                                @foreach(['views' => 'Views', 'bids' => 'Bids', 'watchlist_adds' => 'Watchlist Adds', 'sales' => 'Sales'] as $metric => $label)
                                    <button type="button"
                                            onclick="switchInsightsMetric('{{ $metric }}')"
                                            id="insights-tab-{{ $metric }}"
                                            class="px-2.5 py-1 text-xs font-semibold rounded-lg transition-all {{ $metric === 'bids' ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-500 hover:text-gray-800 hover:bg-gray-100' }}">
                                        {{ $label }}
                                    </button>
                                @endforeach
                            </div>
                            <!-- Chart Canvas -->
                            <div class="relative h-36">
                                <canvas id="insightsChart"></canvas>
                            </div>
                            <!-- Summary strip -->
                            <div class="mt-3 pt-3 border-t border-gray-100 flex items-end justify-between">
                                <div>
                                    <p id="insights-summary-label" class="text-xs text-gray-500 font-medium">Total Bids</p>
                                    <p id="insights-summary-value" class="text-2xl font-extrabold text-gray-900 leading-tight">—</p>
                                    <p id="insights-summary-sub" class="text-xs text-gray-400 mt-0.5">—</p>
                                </div>
                                <div id="insights-views-total" class="hidden text-right">
                                    <p class="text-xs text-gray-500">Total Views (all time)</p>
                                    <p class="text-xl font-bold text-gray-900">{{ number_format($performanceInsightsData['total_views'] ?? 0) }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm">
                            <h3 class="font-bold text-gray-900 text-sm mb-3">Quick Actions</h3>
                            <div class="space-y-1">
                                <a href="{{ route('seller.listings.create') }}"
                                   class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-gray-50 transition group">
                                    <div class="w-9 h-9 bg-blue-50 rounded-lg flex items-center justify-center group-hover:bg-blue-100 transition">
                                        <span class="material-icons-round text-blue-600" style="font-size:18px">add_circle_outline</span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">Create New Listing</p>
                                        <p class="text-xs text-gray-400">Start a new auction</p>
                                    </div>
                                </a>
                                <a href="{{ route('seller.auctions') }}"
                                   class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-gray-50 transition group">
                                    <div class="w-9 h-9 bg-purple-50 rounded-lg flex items-center justify-center group-hover:bg-purple-100 transition">
                                        <span class="material-icons-round text-purple-600" style="font-size:18px">gavel</span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">View My Auctions</p>
                                        <p class="text-xs text-gray-400">Manage your active listings</p>
                                    </div>
                                </a>
                                <a href="{{ route('messaging.index') }}"
                                   class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-gray-50 transition group">
                                    <div class="w-9 h-9 bg-teal-50 rounded-lg flex items-center justify-center group-hover:bg-teal-100 transition">
                                        <span class="material-icons-round text-teal-600" style="font-size:18px">forum</span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-gray-900">Messaging Center</p>
                                        <p class="text-xs text-gray-400">View your conversations</p>
                                    </div>
                                    @if(($messagingThreads ?? collect())->count() > 0)
                                        <span class="flex-shrink-0 min-w-5 h-5 px-1 bg-teal-600 text-white text-xs font-bold rounded-full flex items-center justify-center">
                                            {{ min(($messagingThreads ?? collect())->count(), 9) }}
                                        </span>
                                    @endif
                                </a>
                                <a href="{{ route('seller.account') }}"
                                   class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-gray-50 transition group">
                                    <div class="w-9 h-9 bg-amber-50 rounded-lg flex items-center justify-center group-hover:bg-amber-100 transition">
                                        <span class="material-icons-round text-amber-600" style="font-size:18px">bar_chart</span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">Reports</p>
                                        <p class="text-xs text-gray-400">View your performance</p>
                                    </div>
                                </a>
                            </div>
                        </div>

                    </div>{{-- /RIGHT COLUMN --}}
                </div>{{-- /grid --}}

                <!-- Performance Insights JS data + chart init -->
                <script>
                (function () {
                    const perfData = @json($perfData);
                    let currentPeriod = 'week';
                    let currentMetric = 'bids';
                    let insightsChart = null;

                    function getMetricData(period, metric) {
                        const pd = perfData[period] || {};
                        if (metric === 'bids') return pd.bids || [];
                        if (metric === 'watchlist_adds') return pd.watchlistAdds || [];
                        if (metric === 'sales') return pd.sales || [];
                        return []; // views — handled separately
                    }

                    function getMetricLabel(metric) {
                        return { views: 'Views', bids: 'Bids', watchlist_adds: 'Watchlist Adds', sales: 'Sales' }[metric] || metric;
                    }

                    function sumArr(arr) { return (arr || []).reduce((a, b) => a + b, 0); }

                    function buildChart() {
                        const ctx = document.getElementById('insightsChart');
                        if (!ctx) return;
                        if (insightsChart) insightsChart.destroy();

                        const isViews = currentMetric === 'views';
                        const pd = perfData[currentPeriod] || {};
                        const labels = pd.labels || [];
                        const values = isViews ? labels.map(() => 0) : getMetricData(currentPeriod, currentMetric);

                        insightsChart = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels,
                                datasets: [{
                                    data: values,
                                    borderColor: '#2563eb',
                                    backgroundColor: 'rgba(37,99,235,0.08)',
                                    borderWidth: 2,
                                    pointRadius: labels.length > 15 ? 0 : 3,
                                    pointHoverRadius: 5,
                                    fill: true,
                                    tension: 0.4,
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: { legend: { display: false }, tooltip: { mode: 'index', intersect: false } },
                                scales: {
                                    x: { grid: { display: false }, ticks: { font: { size: 10 }, maxTicksLimit: 7 } },
                                    y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' }, ticks: { font: { size: 10 }, precision: 0 } }
                                }
                            }
                        });

                        // Update summary strip
                        const total = isViews ? (perfData.total_views || 0) : sumArr(values);
                        document.getElementById('insights-summary-label').textContent = 'Total ' + getMetricLabel(currentMetric);
                        document.getElementById('insights-summary-value').textContent = total.toLocaleString();
                        document.getElementById('insights-summary-sub').textContent = currentPeriod === 'week' ? 'This week' : currentPeriod === 'month' ? 'This month' : 'This year';
                        document.getElementById('insights-views-total').classList.toggle('hidden', !isViews);
                    }

                    window.updateInsightsChart = function () {
                        currentPeriod = document.getElementById('perf-period-select').value;
                        buildChart();
                    };

                    window.switchInsightsMetric = function (metric) {
                        currentMetric = metric;
                        ['views', 'bids', 'watchlist_adds', 'sales'].forEach(m => {
                            const btn = document.getElementById('insights-tab-' + m);
                            if (!btn) return;
                            if (m === metric) {
                                btn.className = btn.className.replace(/text-gray-\d+\s+hover:[^\s]+\s+hover:[^\s]+/g, '').trim();
                                btn.classList.add('bg-blue-600', 'text-white', 'shadow-sm');
                            } else {
                                btn.classList.remove('bg-blue-600', 'text-white', 'shadow-sm');
                                btn.classList.add('text-gray-500', 'hover:text-gray-800', 'hover:bg-gray-100');
                            }
                        });
                        buildChart();
                    };

                    // Mini countdown timers for auction cards
                    function fmtCountdown(ms) {
                        if (ms <= 0) return 'Ended';
                        const s = Math.floor(ms / 1000);
                        const d = Math.floor(s / 86400);
                        const h = Math.floor((s % 86400) / 3600);
                        const m = Math.floor((s % 3600) / 60);
                        if (d > 0) return d + 'd ' + h + 'h left';
                        if (h > 0) return h + 'h ' + m + 'm left';
                        return m + 'm ' + (s % 60) + 's left';
                    }

                    function tickCountdowns() {
                        document.querySelectorAll('.dash-countdown[data-end]').forEach(function (el) {
                            const end = new Date(el.dataset.end).getTime();
                            el.textContent = fmtCountdown(end - Date.now());
                        });
                    }

                    // Expose buildChart so initializeCharts() can call it after the tab is shown
                    window._buildInsightsChart = buildChart;

                    document.addEventListener('DOMContentLoaded', function () {
                        tickCountdowns();
                        setInterval(tickCountdowns, 30000);
                    });
                })();
                </script>
                @endif

                @if(!empty($activityTimeline))
                <div class="mt-6 max-w-2xl">
                    <x-ui.activity-timeline :items="$activityTimeline" title="Recent seller activity" />
                </div>
                @endif
            </div>

            <!-- USER TAB — Account Settings -->
            <div id="content-user" class="tab-content hidden p-6" style="height: 100%; overflow-y: auto;">
                @php
                    $emailSvc        = new \App\Services\EmailChangeVerificationService();
                    $emailPending    = session('email_change_pending') || $emailSvc->hasPendingChange($user);
                    $emailPendingNew = session('email_change_new') ?? $emailSvc->getPendingNewEmail($user);
                    // Show Renew button only within 30 days of expiry or already expired
                    // ends_at may be null (no-expiry subscription) — skip the date check in that case
                    $showRenew = isset($activeSubscription) && $activeSubscription &&
                                 $activeSubscription->ends_at &&
                                 $activeSubscription->ends_at->diffInDays(now(), false) >= -30;
                @endphp

                <!-- Page header -->
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-lg shadow-blue-500/20">
                        <span class="material-icons-round text-white text-xl">manage_accounts</span>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 tracking-tight">Account Settings</h2>
                        <p class="text-sm text-gray-500">Manage your profile, security, and payout information</p>
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

                @php
                    $sellerDialRows = collect(config('phone_country_codes', []))->sortBy('label')->values();
                    $sellerMatchRows = collect(config('phone_country_codes', []))->sortByDesc(fn ($r) => strlen((string) ($r['code'] ?? '')))->values();
                    $sellerPhoneDigits = preg_replace('/\D/', '', (string) ($user->phone ?? ''));
                    $sellerDefaultCountry = '1242';
                    $sellerDefaultNational = '';
                    foreach ($sellerMatchRows as $row) {
                        $cc = (string) ($row['code'] ?? '');
                        if ($cc !== '' && $sellerPhoneDigits !== '' && str_starts_with($sellerPhoneDigits, $cc)) {
                            $sellerDefaultCountry = $cc;
                            $sellerDefaultNational = substr($sellerPhoneDigits, strlen($cc)) ?: '';
                            break;
                        }
                    }
                    if ($sellerPhoneDigits !== '' && $sellerDefaultNational === '' && strlen($sellerPhoneDigits) >= 10) {
                        if (str_starts_with($sellerPhoneDigits, '1242')) {
                            $sellerDefaultCountry = '1242';
                            $sellerDefaultNational = substr($sellerPhoneDigits, 4) ?: $sellerPhoneDigits;
                        } elseif (strlen($sellerPhoneDigits) === 11 && str_starts_with($sellerPhoneDigits, '1')) {
                            $sellerDefaultCountry = '1';
                            $sellerDefaultNational = substr($sellerPhoneDigits, 1);
                        } else {
                            $sellerDefaultCountry = '1242';
                            $sellerDefaultNational = $sellerPhoneDigits;
                        }
                    }
                @endphp

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
                                <span class="text-red-500 normal-case tracking-normal font-semibold text-[11px]">(required to list)</span>
                            </label>
                            <div class="rounded-xl bg-gray-50 border border-gray-200 px-4 py-4 space-y-3">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="text-sm">
                                        <p class="text-gray-500 text-xs mb-0.5">Current</p>
                                        <p class="text-gray-900 font-medium" id="seller_dashboard_phone_display">{{ ($user->phone && $sellerPhoneDigits !== '') ? '+'.$sellerPhoneDigits : 'Not set' }}</p>
                                    </div>
                                    <div id="seller-dash-phone-verified-badge" class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-semibold
                                        {{ $user->phone_verified_at ? 'bg-green-50 text-green-700 border border-green-200' : 'hidden bg-yellow-50 text-yellow-700 border border-yellow-200' }}">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>{{ $user->phone_verified_at ? 'Verified' : 'Not verified' }}</span>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-[minmax(10rem,14rem),minmax(0,1.5fr),auto] gap-3 items-end">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Country / area code</label>
                                        <select id="seller_dash_phone_country" class="w-full px-3 py-2.5 rounded-xl border-2 border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white text-sm">
                                            @foreach ($sellerDialRows as $row)
                                                <option value="{{ $row['code'] }}" @if((string)($row['code'] ?? '') === $sellerDefaultCountry) selected @endif>{{ $row['label'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="min-w-[180px]">
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Phone Number</label>
                                        <input type="text" id="seller_dash_phone_input" value="{{ old('seller_phone_local', $sellerDefaultNational) }}"
                                            placeholder="e.g. (242) 555-1234"
                                            class="js-digits-only js-phone-format w-full px-4 py-2.5 rounded-xl border-2 border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                                            data-phone-country-select="#seller_dash_phone_country"
                                            data-cm-validate="phone"
                                            inputmode="numeric" autocomplete="tel-national">
                                    </div>
                                    <div class="flex md:block">
                                        <button type="button" id="seller-dash-send-code-btn"
                                            class="w-full md:w-auto px-4 py-2.5 rounded-xl bg-gray-200 text-gray-800 text-sm font-semibold hover:bg-gray-300 transition whitespace-nowrap">
                                            Send code
                                        </button>
                                    </div>
                                </div>
                                <input type="hidden" id="seller_dash_phone_full" value="">
                                <div id="seller-dash-phone-verify-row" class="grid grid-cols-1 md:grid-cols-[minmax(0,1.5fr),auto] gap-3 items-end hidden">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Verification code</label>
                                        <input type="text" id="seller_dash_phone_code_input" placeholder="6-digit code" maxlength="6" inputmode="numeric" pattern="[0-9]*"
                                            class="js-digits-only w-full px-4 py-2.5 rounded-xl border-2 border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                                        <p class="text-[11px] text-gray-500 mt-1">Code expires in 5 minutes.</p>
                                    </div>
                                    <div class="flex md:block">
                                        <button type="button" id="seller-dash-verify-phone-btn"
                                            class="w-full md:w-auto px-4 py-2.5 rounded-xl bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 transition whitespace-nowrap">
                                            Verify &amp; Save
                                        </button>
                                    </div>
                                </div>
                                @if(!$user->phone || !$user->phone_verified_at)
                                    <p class="text-[11px] text-red-500">You must add and verify a phone number before you can submit listings.</p>
                                @else
                                    <p class="text-[11px] text-gray-400">Verified phone is used for security, payouts, and pickup coordination.</p>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>

                {{-- ══════════════════════════════════════════
                     SECTION 2 — ACCOUNT TYPE
                ══════════════════════════════════════════ --}}
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                        <span class="material-icons-round text-gray-400" style="font-size:18px">workspace_premium</span>
                        <h3 class="font-bold text-gray-900 text-sm">Account Type</h3>
                    </div>
                    <div class="p-6">
                        @if($user->business_license_path)
                            {{-- BUSINESS SELLER VIEW --}}
                            <div class="flex flex-wrap items-start justify-between gap-4">
                                <div>
                                    <p class="font-bold text-gray-900 text-base mb-1">Business Seller Plan</p>
                                    @if($planExpiry ?? null)
                                        <p class="text-sm text-gray-500">Plan expires: <span class="font-semibold text-gray-700">{{ $planExpiry->format('M d, Y') }}</span></p>
                                    @endif
                                </div>
                                <div class="flex flex-col items-end gap-2">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-bold">
                                        <span class="material-icons-round" style="font-size:13px">workspace_premium</span>
                                        Business Seller
                                    </span>
                                </div>
                            </div>
                        @else
                            {{-- CASUAL SELLER VIEW --}}
                            <div class="flex flex-wrap items-start justify-between gap-4">
                                <div>
                                    <p class="font-bold text-gray-900 text-base mb-2">Casual Seller Account</p>
                                    <a href="{{ route('upgrade.membership') }}"
                                       class="inline-flex items-center gap-1.5 px-4 py-2 text-white text-sm font-semibold rounded-xl transition-colors duration-200"
                                       style="background-color:#063466;"
                                       onmouseover="this.style.backgroundColor='#052a52'" onmouseout="this.style.backgroundColor='#063466'">
                                        <span class="material-icons-round" style="font-size:15px">upgrade</span>
                                        Upgrade to Business Seller
                                    </a>
                                </div>
                                <span class="inline-flex items-center px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-semibold">
                                    Casual Seller
                                </span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- ══════════════════════════════════════════
                     SECTION 3 — DOCUMENTS
                ══════════════════════════════════════════ --}}
                @if(isset($documents) && $documents->count() > 0)
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                        <span class="material-icons-round text-gray-400" style="font-size:18px">folder_open</span>
                        <h3 class="font-bold text-gray-900 text-sm">Documents</h3>
                    </div>
                    <div class="p-6 space-y-3">
                        @foreach($documents as $document)
                            <div class="flex items-center justify-between gap-2 rounded-xl bg-gray-50 border border-gray-200 px-4 py-3">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">
                                        @if($document->doc_type === 'business_license') Business License
                                        @else {{ ucfirst(str_replace('_', ' ', $document->doc_type ?? 'Document')) }}
                                        @endif
                                    </p>
                                    @if($user->relationship_to_business && $document->doc_type === 'business_license')
                                        <p class="text-xs text-gray-500 mt-0.5">Relationship: {{ ucfirst(str_replace('_', ' ', $user->relationship_to_business)) }}</p>
                                    @endif
                                </div>
                                @if($document->path ?? null)
                                    <a href="{{ route('user.document.view', $document->id) }}" target="_blank" rel="noopener"
                                       class="text-blue-600 hover:text-blue-800 text-sm font-semibold shrink-0">View</a>
                                @else
                                    <span class="text-gray-400 text-sm shrink-0">—</span>
                                @endif
                            </div>
                        @endforeach
                        <p class="text-xs text-gray-400">Documents uploaded during registration. To update, contact support.</p>
                    </div>
                </div>
                @endif

                {{-- ══════════════════════════════════════════
                     SECTION 4 — PAYOUT SETTINGS
                ══════════════════════════════════════════ --}}
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                        <span class="material-icons-round text-gray-400" style="font-size:18px">account_balance_wallet</span>
                        <h3 class="font-bold text-gray-900 text-sm">Payout Settings</h3>
                    </div>
                    <div class="p-6">
                        @if(!$payoutMethod)
                            <div class="flex items-start gap-3 rounded-xl bg-amber-50 border border-amber-200 px-4 py-3 mb-4">
                                <span class="material-icons-round text-amber-500 flex-shrink-0" style="font-size:18px">warning</span>
                                <p class="text-sm text-amber-800 font-medium">Payout details must be completed before earnings can be released.</p>
                            </div>
                        @else
                            <div class="rounded-xl bg-gray-50 border border-gray-200 px-4 py-4 mb-4 space-y-2 text-sm text-gray-700">
                                <p><span class="font-semibold text-gray-900">Bank:</span> {{ $payoutMethod->bank_name }}</p>
                                <p><span class="font-semibold text-gray-900">Account holder:</span> {{ $payoutMethod->account_holder_name }}</p>
                                <p><span class="font-semibold text-gray-900">Bank account:</span> ****{{ strlen($payoutMethod->account_number ?? '') >= 4 ? substr($payoutMethod->account_number, -4) : '****' }}</p>
                                @if($payoutMethod->routing_number)
                                    <p><span class="font-semibold text-gray-900">Routing / Branch:</span> ****{{ substr($payoutMethod->routing_number, -4) }}</p>
                                @endif
                            </div>
                        @endif
                        <button type="button" onclick="showPayoutModal()"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold transition-colors duration-200 shadow-sm">
                            <span class="material-icons-round" style="font-size:18px">{{ $payoutMethod ? 'edit' : 'add' }}</span>
                            {{ $payoutMethod ? 'Edit Payout Settings' : 'Add Payout Settings' }}
                        </button>
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

            <!-- SUBMISSION TAB -->
            <div id="content-submission" class="tab-content hidden p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Submit New Listing</h2>
                
                @if(!$payoutMethod)
                    <div class="bg-amber-50 border-l-4 border-amber-400 p-6 rounded-lg mb-6">
                        <h3 class="text-lg font-semibold text-amber-900 mb-2">Payout Settings Required</h3>
                        <p class="text-amber-800 mb-4">You must add payout settings before submitting a listing.</p>
                        <a href="{{ route('seller.account') }}" class="inline-block bg-amber-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-amber-700 transition duration-200">
                            Add Payout Settings
                        </a>
                    </div>
                @else
                    <div class="bg-white border border-gray-200 rounded-lg p-8 text-center">
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">Ready to List Your Vehicle?</h3>
                        <p class="text-gray-600 mb-6">Submit a new listing through our three-step submission process.</p>
                        <a href="{{ route('seller.listings.create') }}"
                           class="inline-block bg-blue-600 text-white px-8 py-4 rounded-lg font-semibold hover:bg-blue-700 transition duration-200 text-lg">
                            SUBMIT NEW LISTING
                        </a>
                        <p class="text-sm text-gray-500 mt-4">Three-step process: Vehicle Information → Photo Upload → Auction Settings & Payment</p>
                    </div>
                @endif
            </div>

            <!-- AUCTIONS TAB -->
            <div id="content-auctions" class="tab-content hidden p-6 dash-no-scrollbar" style="height:100%; overflow-y:auto;" data-tour-id="seller-auctions">

                {{-- Page header --}}
                <div class="flex items-center justify-between gap-4 mb-6 flex-wrap">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-lg shadow-blue-500/20">
                            <span class="material-icons-round text-white text-xl">gavel</span>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-gray-900 tracking-tight">My Auctions</h2>
                            <p class="text-sm text-gray-500">Live listings, completed sales, and outcomes.</p>
                        </div>
                    </div>
                    <a href="{{ route('seller.listings.create') }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold shadow-lg shadow-blue-600/25 transition-all duration-200">
                        <span class="material-icons-round text-lg">add_circle_outline</span>
                        New Listing
                    </a>
                </div>

                {{-- Stats --}}
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-5">
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                            <span class="material-icons-round text-blue-600 text-2xl">bolt</span>
                        </div>
                        <div>
                            <p class="text-3xl font-bold text-gray-900">{{ $auctionSummary['current_count'] }}</p>
                            <p class="text-sm text-gray-500 mt-0.5">Live Auctions</p>
                        </div>
                    </div>
                    <div class="bg-white rounded-2xl border border-amber-100 shadow-sm p-5 flex items-center gap-4 cursor-pointer hover:border-amber-300 transition-colors" onclick="showAuctionSection('pending')">
                        <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center flex-shrink-0">
                            <span class="material-icons-round text-amber-500 text-2xl">hourglass_top</span>
                        </div>
                        <div>
                            <p class="text-3xl font-bold text-amber-600">{{ $pendingListingsCount }}</p>
                            <p class="text-sm text-gray-500 mt-0.5">Pending Approval</p>
                        </div>
                    </div>
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center flex-shrink-0">
                            <span class="material-icons-round text-emerald-600 text-2xl">sell</span>
                        </div>
                        <div>
                            <p class="text-3xl font-bold text-gray-900">{{ $auctionSummary['total_items_sold'] }}</p>
                            <p class="text-sm text-gray-500 mt-0.5">Items Sold</p>
                        </div>
                    </div>
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center flex-shrink-0">
                            <span class="material-icons-round text-purple-600 text-2xl">payments</span>
                        </div>
                        <div>
                            <p class="text-3xl font-bold text-gray-900">${{ number_format($auctionSummary['total_sales_revenue'], 0) }}</p>
                            <p class="text-sm text-gray-500 mt-0.5">Total Revenue</p>
                        </div>
                    </div>
                </div>

                {{-- Main card with tabs --}}
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                    {{-- Card header: tabs + view toggle --}}
                    <div class="px-5 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center gap-3">
                        <nav class="flex gap-2 bg-gray-100 p-1 rounded-xl border border-gray-200 flex-1 flex-wrap">
                            <button type="button" onclick="showAuctionSection('current')" id="auction-current"
                                    class="auction-tab-button flex-1 px-4 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-indigo-600 rounded-lg shadow-sm transition-all duration-200">
                                <span class="material-icons-round text-sm mr-1 align-middle">bolt</span>
                                Current
                            </button>
                            <button type="button" onclick="showAuctionSection('pending')" id="auction-pending"
                                    class="auction-tab-button flex-1 px-4 py-2.5 text-sm font-semibold text-gray-600 hover:text-gray-900 rounded-lg transition-all duration-200 relative">
                                <span class="material-icons-round text-sm mr-1 align-middle">hourglass_top</span>
                                Pending
                                @if($pendingListingsCount > 0)
                                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-amber-500 text-white text-[10px] font-bold ml-1">{{ $pendingListingsCount }}</span>
                                @endif
                            </button>
                            <button type="button" onclick="showAuctionSection('past')" id="auction-past"
                                    class="auction-tab-button flex-1 px-4 py-2.5 text-sm font-semibold text-gray-600 hover:text-gray-900 rounded-lg transition-all duration-200">
                                <span class="material-icons-round text-sm mr-1 align-middle">task_alt</span>
                                Completed
                            </button>
                            <button type="button" onclick="showAuctionSection('rejected')" id="auction-rejected"
                                    class="auction-tab-button flex-1 px-4 py-2.5 text-sm font-semibold text-gray-600 hover:text-gray-900 rounded-lg transition-all duration-200">
                                <span class="material-icons-round text-sm mr-1 align-middle">block</span>
                                Rejected
                            </button>
                        </nav>
                        <div class="inline-flex rounded-xl bg-gray-100 p-1 shrink-0" role="group" aria-label="View layout">
                            <button type="button" id="seller-auctions-view-btn-grid"
                                    class="seller-auctions-view-toggle-btn is-active inline-flex items-center gap-1.5 rounded-lg px-3 py-2 text-sm font-medium"
                                    onclick="setSellerAuctionsViewMode('grid')">
                                <span class="material-icons-round" style="font-size:18px">grid_view</span>
                                <span>Grid</span>
                            </button>
                            <button type="button" id="seller-auctions-view-btn-list"
                                    class="seller-auctions-view-toggle-btn inline-flex items-center gap-1.5 rounded-lg px-3 py-2 text-sm font-medium"
                                    onclick="setSellerAuctionsViewMode('list')">
                                <span class="material-icons-round" style="font-size:18px">view_list</span>
                                <span>List</span>
                            </button>
                        </div>
                    </div>
                    <div class="p-5">

                {{-- CURRENT AUCTIONS --}}
                <div id="auction-section-current" class="auction-section">
                    @if($currentAuctions->count() > 0)
                        <div class="seller-auction-grid grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($currentAuctions as $listing)
                                @php
                                    $img = $listing->images->first();
                                    $imgUrl = $img ? (str_contains($img->image_path ?? '', '/') ? asset($img->image_path) : asset('uploads/listings/' . $img->image_path)) : null;
                                    $endTime = $listing->auction_end_time
                                        ? \Carbon\Carbon::parse($listing->auction_end_time)
                                        : ($listing->auction_start_time ? \Carbon\Carbon::parse($listing->auction_start_time)->addDays($listing->auction_duration ?? 7) : null);
                                @endphp
                                <div class="s-card flex flex-col bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden hover:border-blue-200 hover:shadow-md transition-all">
                                    <div class="s-card-img relative h-48 bg-gray-100 overflow-hidden flex-shrink-0">
                                        @if($imgUrl)
                                            <img src="{{ $imgUrl }}" alt="{{ $listing->year }} {{ $listing->make }} {{ $listing->model }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-gray-300">
                                                <span class="material-icons-round text-5xl">directions_car</span>
                                            </div>
                                        @endif
                                        <div class="absolute top-3 left-3 inline-flex items-center gap-1.5 bg-blue-600 text-white text-[11px] font-bold px-2 py-1 rounded-lg">
                                            <span class="material-icons-round" style="font-size:12px">bolt</span>
                                            LIVE
                                        </div>
                                        @if($endTime)
                                            <div class="absolute bottom-3 left-3 inline-flex items-center gap-1.5 bg-black/60 backdrop-blur-sm text-white text-[11px] font-bold px-2 py-1 rounded-lg">
                                                <span class="material-icons-round" style="font-size:12px">schedule</span>
                                                <span id="countdown-{{ $listing->id }}" data-end-time="{{ $endTime->toIso8601String() }}">—</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="p-4 flex flex-col flex-1">
                                        <h3 class="font-bold text-gray-900 text-sm leading-tight mb-0.5">{{ $listing->year }} {{ $listing->make }} {{ $listing->model }}</h3>
                                        <p class="text-xs text-gray-400 font-mono mb-2">{{ $listing->item_number ?? 'CM' . str_pad($listing->id, 6, '0', STR_PAD_LEFT) }}</p>
                                        <p class="text-sm font-bold text-blue-600 mb-3">Current Bid: ${{ number_format($listing->current_bid, 2) }}</p>
                                        <div class="flex gap-2 pt-3 border-t border-gray-100 mt-auto">
                                            <a href="{{ route('seller.listings.show', $listing->id) }}"
                                               class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold transition">
                                                <span class="material-icons-round" style="font-size:14px">visibility</span> View
                                            </a>
                                            <button type="button" onclick="openDeleteModal({{ $listing->id }}, '{{ addslashes($listing->year . ' ' . $listing->make . ' ' . $listing->model) }}')"
                                                    class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl border border-red-200 bg-white hover:bg-red-50 hover:border-red-300 text-red-600 text-xs font-semibold transition">
                                                <span class="material-icons-round" style="font-size:14px">delete_outline</span> Delete
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="rounded-xl border border-dashed border-gray-200 bg-gray-50 py-14 text-center">
                            <span class="material-icons-round text-gray-300 text-5xl block mb-3">inventory_2</span>
                            <p class="text-gray-700 text-base font-semibold">No live auctions</p>
                            <p class="text-gray-400 text-sm mt-1.5 max-w-sm mx-auto">When you have active listings, they will appear here.</p>
                            <a href="{{ route('seller.listings.create') }}" class="inline-flex items-center gap-2 mt-5 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold transition shadow-lg shadow-blue-600/25">
                                <span class="material-icons-round text-lg">add</span> Create a Listing
                            </a>
                        </div>
                    @endif
                </div>

                <!-- PENDING LISTINGS — Awaiting admin approval -->
                <div id="auction-section-pending" class="auction-section hidden">
                    @if(($pendingListings ?? collect())->count() > 0)
                        <div class="mb-4 flex items-center gap-3 px-1">
                            <span class="material-icons-round text-amber-500" style="font-size:18px">info</span>
                            <p class="text-sm text-amber-700 font-medium">
                                These listings are currently under review by our team. You will be notified once a decision is made.
                            </p>
                        </div>
                        <div class="seller-auction-grid grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($pendingListings as $listing)
                                @php
                                    $imgPnd   = $listing->images->first();
                                    $imgUrlPnd = $imgPnd
                                        ? (str_contains($imgPnd->image_path ?? '', '/')
                                            ? asset($imgPnd->image_path)
                                            : asset('uploads/listings/' . $imgPnd->image_path))
                                        : null;
                                    $submittedAt = $listing->created_at;
                                @endphp
                                <div class="s-card flex flex-col bg-white rounded-2xl border border-amber-200 shadow-sm overflow-hidden hover:shadow-md transition-all">
                                    <div class="s-card-img relative h-48 bg-amber-50/30 overflow-hidden flex-shrink-0">
                                        @if($imgUrlPnd)
                                            <img src="{{ $imgUrlPnd }}"
                                                 alt="{{ $listing->year }} {{ $listing->make }} {{ $listing->model }}"
                                                 class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-amber-200">
                                                <span class="material-icons-round text-5xl">directions_car</span>
                                            </div>
                                        @endif
                                        {{-- Pending badge --}}
                                        <div class="absolute top-3 left-3 inline-flex items-center gap-1.5 bg-amber-500 text-white text-[11px] font-bold px-2 py-1 rounded-lg">
                                            <span class="material-icons-round" style="font-size:12px">hourglass_top</span>
                                            Pending Review
                                        </div>
                                        {{-- Submitted date --}}
                                        <div class="absolute bottom-3 left-3 inline-flex items-center gap-1.5 bg-black/60 backdrop-blur-sm text-white text-[11px] font-bold px-2 py-1 rounded-lg">
                                            <span class="material-icons-round" style="font-size:12px">event</span>
                                            Submitted {{ $submittedAt->format('M d, Y') }}
                                        </div>
                                    </div>
                                    <div class="p-4 flex flex-col flex-1">
                                        <h3 class="font-bold text-gray-900 text-sm leading-tight mb-0.5">
                                            {{ $listing->year }} {{ $listing->make }} {{ $listing->model }}
                                        </h3>
                                        <p class="text-xs text-gray-400 font-mono mb-3">
                                            {{ $listing->item_number ?? 'CM' . str_pad($listing->id, 6, '0', STR_PAD_LEFT) }}
                                        </p>

                                        {{-- Status message --}}
                                        <div class="bg-amber-50 border border-amber-200 rounded-xl p-3 mb-3 flex items-start gap-2">
                                            <span class="material-icons-round text-amber-500 flex-shrink-0" style="font-size:16px">schedule</span>
                                            <div>
                                                <p class="text-xs font-semibold text-amber-800">Under Admin Review</p>
                                                <p class="text-xs text-amber-700 mt-0.5">Our team is reviewing your submission. This usually takes 1–2 business days.</p>
                                            </div>
                                        </div>

                                        {{-- Spec snippets --}}
                                        <div class="grid grid-cols-2 gap-x-4 gap-y-1 text-xs text-gray-500 mb-4">
                                            @if($listing->starting_price)
                                                <span class="flex items-center gap-1">
                                                    <span class="material-icons-round" style="font-size:12px">price_check</span>
                                                    Start: ${{ number_format($listing->starting_price, 0) }}
                                                </span>
                                            @endif
                                            @if($listing->auction_duration)
                                                <span class="flex items-center gap-1">
                                                    <span class="material-icons-round" style="font-size:12px">timer</span>
                                                    {{ $listing->auction_duration }}d auction
                                                </span>
                                            @endif
                                            @if($listing->odometer)
                                                <span class="flex items-center gap-1">
                                                    <span class="material-icons-round" style="font-size:12px">speed</span>
                                                    {{ number_format($listing->odometer) }} mi
                                                </span>
                                            @endif
                                            @if($listing->condition)
                                                <span class="flex items-center gap-1">
                                                    <span class="material-icons-round" style="font-size:12px">star_outline</span>
                                                    {{ ucfirst($listing->condition) }}
                                                </span>
                                            @endif
                                        </div>

                                        <a href="{{ route('seller.listings.show', $listing->id) }}"
                                           class="mt-auto inline-flex items-center justify-center gap-1.5 w-full px-3 py-2 rounded-xl border border-gray-200 bg-white hover:border-amber-300 hover:text-amber-700 text-gray-700 text-xs font-semibold transition">
                                            <span class="material-icons-round" style="font-size:14px">visibility</span>
                                            View Submission
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="rounded-xl border border-dashed border-amber-200 bg-amber-50/30 py-14 text-center">
                            <span class="material-icons-round text-amber-300 text-5xl block mb-3">hourglass_empty</span>
                            <p class="text-gray-700 text-base font-semibold">No pending listings</p>
                            <p class="text-gray-400 text-sm mt-1.5 max-w-sm mx-auto">Listings you submit for admin review will appear here until they are approved or rejected.</p>
                            <a href="{{ route('seller.listings.create') }}"
                               class="inline-flex items-center gap-2 mt-5 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold transition shadow-lg shadow-blue-600/25">
                                <span class="material-icons-round text-lg">add</span> Create a Listing
                            </a>
                        </div>
                    @endif
                </div>
                {{-- end auction-section-pending --}}

                <!-- COMPLETED / PAST AUCTIONS — Sold + Ended Not Sold -->
                <div id="auction-section-past" class="auction-section hidden">
                    @php
                        // Merge sold listings + ended-not-sold listings into one unified list
                        $allPastCards = collect();
                        foreach (($pastAuctions ?? collect()) as $l) {
                            $l->_past_type = 'sold';
                            $allPastCards->push($l);
                        }
                        foreach (($endedAuctions ?? collect()) as $l) {
                            $l->_past_type = 'not_sold';
                            $allPastCards->push($l);
                        }
                        $allPastCards = $allPastCards->sortByDesc(function($l) {
                            return $l->auction_end_time ?? $l->updated_at;
                        })->values();

                        $statusBadgeMap = [
                            'awaiting_payment'  => ['label' => 'Awaiting Payment',                  'classes' => 'bg-amber-50 text-amber-700 border-amber-200'],
                            'payment_received'  => ['label' => 'Payment Received — Action Required', 'classes' => 'bg-blue-50 text-blue-700 border-blue-200'],
                            'completed'         => ['label' => 'Completed',                          'classes' => 'bg-emerald-50 text-emerald-700 border-emerald-200'],
                            'awaiting_invoice'  => ['label' => 'Awaiting Invoice',                   'classes' => 'bg-slate-100 text-slate-600 border-slate-200'],
                        ];
                    @endphp

                    @if($allPastCards->count() > 0)
                        <div class="seller-auction-grid grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($allPastCards as $listing)
                                @php
                                    $isSold     = $listing->_past_type === 'sold';
                                    $imgP       = $listing->images->first();
                                    $imgUrlP    = $imgP ? (str_contains($imgP->image_path ?? '', '/') ? asset($imgP->image_path) : asset('uploads/listings/' . $imgP->image_path)) : null;
                                    $endTime    = $listing->auction_end_time
                                        ? \Carbon\Carbon::parse($listing->auction_end_time)
                                        : ($listing->auction_start_time ? \Carbon\Carbon::parse($listing->auction_start_time)->addDays($listing->auction_duration ?? 7) : null);
                                    $soldDate   = $endTime ? $endTime->format('M d, Y') : '—';
                                    $status     = $listing->completion_status ?? 'awaiting_invoice';
                                    $badge      = $statusBadgeMap[$status] ?? $statusBadgeMap['awaiting_invoice'];
                                    $highestBid = $listing->current_bid ?? $listing->getHighestBidAmount();
                                @endphp
                                <div class="s-card flex flex-col bg-white rounded-2xl border {{ $isSold ? 'border-emerald-200' : 'border-gray-200' }} shadow-sm overflow-hidden hover:shadow-md transition-all">
                                    <div class="s-card-img relative h-48 bg-gray-100 overflow-hidden flex-shrink-0">
                                        @if($imgUrlP)
                                            <img src="{{ $imgUrlP }}" alt="{{ $listing->year }} {{ $listing->make }} {{ $listing->model }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-gray-300">
                                                <span class="material-icons-round text-5xl">directions_car</span>
                                            </div>
                                        @endif
                                        <div class="absolute top-3 left-3 inline-flex items-center gap-1 bg-black/60 backdrop-blur-sm text-white text-[11px] font-bold px-2 py-1 rounded-lg">
                                            <span class="material-icons-round" style="font-size:12px">flag</span>
                                            Finished
                                        </div>
                                        @if($isSold)
                                            <div class="absolute bottom-0 left-0 right-0 px-3 py-2 bg-emerald-600/90 text-white text-xs font-semibold">
                                                Sold · {{ $soldDate }}
                                            </div>
                                        @else
                                            <div class="absolute bottom-0 left-0 right-0 px-3 py-2 bg-gray-700/80 text-white text-xs font-semibold">
                                                Not Sold
                                            </div>
                                        @endif
                                    </div>
                                    <div class="p-4 flex flex-col flex-1">
                                        <h3 class="font-bold text-gray-900 text-sm leading-tight mb-0.5">{{ $listing->year }} {{ $listing->make }} {{ $listing->model }}</h3>
                                        <p class="text-xs text-gray-400 font-mono mb-3">{{ $listing->item_number ?? 'CM' . str_pad($listing->id, 6, '0', STR_PAD_LEFT) }}</p>

                                        @if($isSold)
                                            <p class="text-sm font-bold text-emerald-700 mb-2">Sale Price: ${{ number_format($listing->sale_price ?? 0, 2) }}</p>

                                            @if($status === 'payment_received')
                                                <div class="bg-blue-50 border border-blue-200 rounded-xl p-3 mb-3">
                                                    <p class="text-xs font-semibold text-blue-900 mb-2">Enter the pick-up code from the buyer to complete this transaction.</p>
                                                    <form method="POST" action="{{ route('seller.dashboard.confirm-pickup', $listing->id) }}" class="seller-pin-form" data-pin-form>
                                                        @csrf
                                                        <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Pick-up Code</label>
                                                        <div class="flex gap-1.5 mb-2" data-pin-boxes>
                                                            @for($i = 0; $i < 6; $i++)
                                                                <input type="text" inputmode="numeric" pattern="[0-9]" maxlength="1" required
                                                                       class="seller-pin-box w-9 h-10 text-center text-base font-bold border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white"
                                                                       aria-label="Digit {{ $i + 1 }}">
                                                            @endfor
                                                        </div>
                                                        <input type="hidden" name="pickup_pin" data-pin-hidden>
                                                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold py-2 rounded-lg transition" data-pin-submit disabled>
                                                            Submit Code
                                                        </button>
                                                    </form>
                                                </div>
                                            @elseif($status === 'completed')
                                                <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-3 mb-3 flex items-center gap-2 text-sm text-emerald-800">
                                                    <span class="material-icons-round text-base">check_circle</span>
                                                    <span class="font-semibold">Pickup Confirmed</span>
                                                    <span class="ml-auto text-xs text-emerald-600">{{ ($listing->payout_status ?? null) === 'completed' ? 'Payout Complete' : 'Payout Processing' }}</span>
                                                </div>
                                            @elseif($status === 'awaiting_payment')
                                                <div class="bg-amber-50 border border-amber-200 rounded-xl p-3 mb-3">
                                                    <p class="text-xs text-amber-900 font-medium">Awaiting buyer payment.</p>
                                                </div>
                                            @endif

                                            <div class="grid grid-cols-3 gap-1 text-xs pt-3 border-t border-gray-100 mb-3">
                                                <div>
                                                    <p class="text-gray-400">Sale Price</p>
                                                    <p class="font-semibold text-gray-900">${{ number_format($listing->sale_price ?? 0, 0) }}</p>
                                                </div>
                                                <div>
                                                    <p class="text-gray-400">CayMark Fee</p>
                                                    <p class="font-semibold text-red-500">-${{ number_format($listing->seller_commission_amount ?? 0, 0) }}</p>
                                                </div>
                                                <div>
                                                    <p class="text-gray-400">You Receive</p>
                                                    <p class="font-semibold text-emerald-600">${{ number_format($listing->net_payout_amount ?? 0, 0) }}</p>
                                                </div>
                                            </div>
                                        @else
                                            <p class="text-sm font-bold text-gray-500 mb-3">Highest Bid: {{ $highestBid > 0 ? '$' . number_format($highestBid, 2) : '—' }}</p>
                                        @endif

                                        <a href="{{ route('seller.listings.show', $listing->id) }}"
                                           class="mt-auto inline-flex items-center justify-center gap-1.5 w-full px-3 py-2 rounded-xl border border-gray-200 bg-white hover:border-blue-300 hover:text-blue-600 text-gray-700 text-xs font-semibold transition">
                                            <span class="material-icons-round" style="font-size:14px">visibility</span> View Details
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="rounded-xl border border-dashed border-gray-200 bg-gray-50 py-14 text-center">
                            <span class="material-icons-round text-gray-300 text-5xl block mb-3">task_alt</span>
                            <p class="text-gray-700 text-base font-semibold">No past auctions yet</p>
                            <p class="text-gray-400 text-sm mt-1.5 max-w-sm mx-auto">Ended and sold auctions will appear here.</p>
                        </div>
                    @endif
                </div>

                <!-- REJECTED LISTINGS -->
                <div id="auction-section-rejected" class="auction-section hidden">
                    @if($rejectedListings->count() > 0)
                        <div class="seller-auction-grid grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($rejectedListings as $listing)
                                @php
                                    $imgR = $listing->images->first();
                                    $imgUrlR = $imgR ? (str_contains($imgR->image_path ?? '', '/') ? asset($imgR->image_path) : asset('uploads/listings/' . $imgR->image_path)) : null;
                                    $rejReason  = $listing->rejection_reason ?? '';
                                    $rejNotes   = $listing->rejection_notes  ?? '';
                                    $canEdit    = $listing->can_edit ?? false;
                                    $deadline   = $listing->edit_deadline;
                                    $listingName = addslashes($listing->year . ' ' . $listing->make . ' ' . $listing->model);
                                    $reasonJs   = addslashes($rejReason);
                                    $notesJs    = addslashes($rejNotes);
                                @endphp
                                <div class="s-card flex flex-col bg-white rounded-2xl border border-red-200 shadow-sm overflow-hidden hover:shadow-md transition-all">
                                    <div class="s-card-img relative h-48 bg-red-50/30 overflow-hidden flex-shrink-0">
                                        @if($imgUrlR)
                                            <img src="{{ $imgUrlR }}" alt="{{ $listing->year }} {{ $listing->make }} {{ $listing->model }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-red-200">
                                                <span class="material-icons-round text-5xl">directions_car</span>
                                            </div>
                                        @endif
                                        <div class="absolute top-3 left-3 inline-flex items-center gap-1.5 bg-red-600 text-white text-[11px] font-bold px-2 py-1 rounded-lg">
                                            <span class="material-icons-round" style="font-size:12px">block</span>
                                            Rejected
                                        </div>
                                    </div>
                                    <div class="p-4 flex flex-col flex-1">
                                        <h3 class="font-bold text-gray-900 text-sm leading-tight mb-0.5">{{ $listing->year }} {{ $listing->make }} {{ $listing->model }}</h3>
                                        <p class="text-xs text-gray-400 font-mono mb-3">{{ $listing->item_number ?? 'CM' . str_pad($listing->id, 6, '0', STR_PAD_LEFT) }}</p>

                                        @if($rejReason)
                                            <div class="bg-red-50 border border-red-200 rounded-xl p-3 mb-3">
                                                <p class="text-xs font-semibold text-red-700 mb-0.5">Reason for Rejection</p>
                                                <p class="text-sm text-red-800">{{ $rejReason }}</p>
                                            </div>
                                        @endif

                                        {{-- Edit window: 3 days (72 hrs) --}}
                                        @if($canEdit)
                                            @if($deadline)
                                                <p class="text-xs text-amber-600 font-medium mb-2">
                                                    Edit window closes: <span id="rejection-timer-{{ $listing->id }}" data-deadline="{{ $deadline->toIso8601String() }}">—</span>
                                                </p>
                                            @endif
                                            <button type="button"
                                                    onclick="openRejectionModal('{{ $listing->id }}', '{{ $listingName }}', '{{ $reasonJs }}', '{{ $notesJs }}', '{{ route('seller.listings.edit', $listing->id) }}')"
                                                    class="mt-auto w-full inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold transition">
                                                <span class="material-icons-round" style="font-size:14px">edit</span>
                                                Edit Submission
                                            </button>
                                        @else
                                            <div class="mt-auto flex items-center justify-center gap-1.5 py-2 rounded-xl border border-gray-200 bg-gray-50 text-gray-500 text-xs font-semibold">
                                                <span class="material-icons-round" style="font-size:14px">check</span>
                                                Finished
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="rounded-xl border border-dashed border-gray-200 bg-gray-50 py-14 text-center">
                            <span class="material-icons-round text-gray-300 text-5xl block mb-3">block</span>
                            <p class="text-gray-700 text-base font-semibold">No rejected listings</p>
                            <p class="text-gray-400 text-sm mt-1.5 max-w-sm mx-auto">If a submission is declined, you will see it here with the reason.</p>
                        </div>
                    @endif
                </div>

                    </div>{{-- end p-5 content wrapper --}}
                </div>{{-- end main auctions card --}}
            </div>{{-- end content-auctions --}}

            <!-- ══ Rejection Details Modal ══ -->
            <div id="rejectionModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
                <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
                    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-red-100 flex items-center justify-center">
                                <span class="material-icons-round text-red-600">block</span>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-900">Submission Rejected</h3>
                                <p id="rejModal_listingName" class="text-xs text-slate-500"></p>
                            </div>
                        </div>
                        <button onclick="_modalHide('rejectionModal')" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-slate-100 text-slate-400 hover:text-slate-700 transition">
                            <span class="material-icons-round text-lg">close</span>
                        </button>
                    </div>
                    <div class="px-6 py-5 space-y-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Reason for Rejection</p>
                            <p id="rejModal_reason" class="text-sm text-red-700 font-medium">—</p>
                        </div>
                        <div id="rejModal_notesWrap" class="hidden">
                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500 mb-1">Additional Notes</p>
                            <p id="rejModal_notes" class="text-sm text-slate-700"></p>
                        </div>
                        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                            <p class="text-sm text-amber-900 leading-relaxed">
                                Please make the necessary adjustments to your submission before resubmitting. Ensure all required information is accurate and all photos meet the minimum requirements.
                            </p>
                        </div>
                    </div>
                    <div class="px-6 pb-5 flex gap-3">
                        <button onclick="_modalHide('rejectionModal')" class="flex-1 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-semibold rounded-xl transition">
                            Close
                        </button>
                        <a id="rejModal_editBtn" href="#" class="flex-1 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition text-center">
                            Edit Submission
                        </a>
                    </div>
                </div>
            </div>
            {{-- End Rejection Modal --}}

            <!-- NOTIFICATIONS TAB (same UI as buyer: header, filters, grouped by month, cards with mark-read) -->
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
                                <p class="text-sm text-gray-500">Stay updated with your listing and auction activity</p>
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
                        $groupedNotifications = $notifications->sortByDesc('created_at')->groupBy(function($notification) {
                            return $notification->created_at->format('F Y');
                        });
                    @endphp
                    <div class="notifications-scroll-wrapper notifications-scrollbar flex-1 min-h-0" style="max-height: 65vh; overflow-y: scroll; overflow-x: hidden;">
                    <div class="notifications-container space-y-6 py-1 px-4">
                        @foreach($groupedNotifications as $month => $monthNotifications)
                            <div class="notification-month-group" data-month="{{ $month }}">
                                <div class="mb-4 flex items-center gap-3">
                                    <div class="h-px flex-1 bg-gradient-to-r from-transparent via-gray-300 to-transparent"></div>
                                    <h3 class="text-lg font-bold text-gray-700 px-4">{{ $month }}</h3>
                                    <div class="h-px flex-1 bg-gradient-to-r from-transparent via-gray-300 to-transparent"></div>
                                </div>
                                <div class="space-y-3">
                                    @foreach($monthNotifications->sortByDesc('created_at') as $notification)
                            @php
                                $d = is_array($notification->data) ? $notification->data : [];
                                $msg = $d['message'] ?? $d['title'] ?? 'Notification';
                                $type = $d['type'] ?? 'info';
                                $isUnread = !$notification->read_at;
                                $link = $d['link'] ?? null;
                                $actionLabel = $d['action_label'] ?? 'View details';
                                $iconMap = [
                                    'bid' => 'gavel', 'outbid' => 'trending_down', 'win' => 'celebration', 'sale' => 'celebration',
                                    'payment' => 'payment', 'auction' => 'schedule', 'listing' => 'description',
                                    'suspicious_login' => 'security', 'default' => 'notifications'
                                ];
                                $icon = $iconMap[$type] ?? $iconMap['default'];
                                $colorMap = [
                                    'bid' => ['bg' => 'bg-blue-50', 'border' => 'border-blue-200', 'icon' => 'text-blue-600', 'dot' => 'bg-blue-600'],
                                    'outbid' => ['bg' => 'bg-amber-50', 'border' => 'border-amber-200', 'icon' => 'text-amber-600', 'dot' => 'bg-amber-600'],
                                    'win' => ['bg' => 'bg-emerald-50', 'border' => 'border-emerald-200', 'icon' => 'text-emerald-600', 'dot' => 'bg-emerald-600'],
                                    'sale' => ['bg' => 'bg-emerald-50', 'border' => 'border-emerald-200', 'icon' => 'text-emerald-600', 'dot' => 'bg-emerald-600'],
                                    'payment' => ['bg' => 'bg-purple-50', 'border' => 'border-purple-200', 'icon' => 'text-purple-600', 'dot' => 'bg-purple-600'],
                                    'listing' => ['bg' => 'bg-indigo-50', 'border' => 'border-indigo-200', 'icon' => 'text-indigo-600', 'dot' => 'bg-indigo-600'],
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
                                    <div class="flex-shrink-0 w-12 h-12 rounded-xl {{ $isUnread ? $colors['bg'] : 'bg-gray-100' }} flex items-center justify-center border-2 {{ $isUnread ? $colors['border'] : 'border-gray-200' }}">
                                        <span class="material-icons-round {{ $isUnread ? $colors['icon'] : 'text-gray-400' }} text-xl">{{ $icon }}</span>
                                    </div>
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
                        <p class="text-gray-500 text-sm max-w-sm mx-auto">You'll receive notifications for listing approval, new bids, auction results, payouts, and more.</p>
                    </div>
                @endif
            </div>

            <!-- MESSAGING CENTER TAB -->
            <div id="content-messaging" class="tab-content hidden p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-2">Messaging Center</h2>
                <p class="text-gray-600 mb-6">Post-payment pickup coordination with buyers.</p>
                <div class="bg-gradient-to-br from-blue-50 via-white to-teal-50 border-2 border-teal-200 rounded-2xl p-8 text-center">
                    <div class="w-16 h-16 bg-teal-600 rounded-2xl flex items-center justify-center mx-auto mb-4" style="background-color:#0d9488;">
                        <span class="material-icons-round text-white" style="font-size: 2rem;">forum</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">All your sales in one place</h3>
                    <p class="text-sm text-gray-600 max-w-md mx-auto mb-6">
                        Send pickup schedules, respond to buyer requests, and confirm pickup with the buyer's PIN.
                        @if(($messagingThreads ?? collect())->count() > 0)
                            You currently have <strong class="text-gray-900">{{ $messagingThreads->count() }}</strong> active sale{{ $messagingThreads->count() === 1 ? '' : 's' }}.
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
                    <div id="seller-support-success-banner" class="flex items-center gap-3 rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 mb-6 text-emerald-800" role="status">
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
                            <form method="POST" action="{{ route('seller.support.submit') }}" class="p-6 space-y-5">
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
                        @php
                            $sellerTickets = \App\Models\SupportTicket::where('user_id', auth()->id())->latest()->get();
                        @endphp
                        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-2">
                                <span class="material-icons-round text-gray-400" style="font-size:18px">history</span>
                                <h3 class="font-bold text-gray-900 text-sm">Request History</h3>
                            </div>
                            <div class="p-6">
                                @if($sellerTickets->count() > 0)
                                    <div class="space-y-3">
                                        @foreach($sellerTickets as $ticket)
                                            @php
                                                $tOpen = $ticket->status === 'open';
                                                $statusMap = [
                                                    'open'        => ['bg-blue-100 text-blue-800',       'Open'],
                                                    'in_progress' => ['bg-amber-100 text-amber-800',     'In Progress'],
                                                    'resolved'    => ['bg-emerald-100 text-emerald-800', 'Resolved'],
                                                    'closed'      => ['bg-gray-100 text-gray-600',       'Closed'],
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
                                    ['icon' => 'help_outline',   'label' => 'View FAQ',          'href' => route('help-center')],
                                    ['icon' => 'gavel',          'label' => 'Auction Guide',      'href' => route('video-guide')],
                                    ['icon' => 'storefront',     'label' => 'Seller Guide',       'href' => route('sellers-guide')],
                                    ['icon' => 'info_outline',   'label' => 'How Auctions Work',  'href' => route('video-guide')],
                                ] as $help)
                                    <a href="{{ $help['href'] }}" class="flex items-center justify-between px-5 py-3 hover:bg-gray-50 transition group">
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
</div>

<!-- Delete Listing Confirmation Modal -->
<div id="deleteListingModal" class="hidden fixed inset-0 bg-black/50 overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6 border border-gray-200">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
                <span class="material-icons-round text-red-600 text-2xl">delete_outline</span>
            </div>
            <div>
                <h3 class="text-lg font-bold text-gray-900">Remove listing?</h3>
                <p class="text-sm text-gray-500">This cannot be undone.</p>
            </div>
        </div>
        <p class="text-gray-600 text-sm mb-6" id="deleteListingTitle">Are you sure you want to remove this listing?</p>
        <form id="deleteListingForm" method="POST" action="">
            @csrf
            @method('DELETE')
            <div class="flex gap-3">
                <button type="button" onclick="closeDeleteModal()" class="flex-1 px-4 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition">Cancel</button>
                <button type="submit" class="flex-1 px-4 py-2.5 bg-red-600 text-white font-medium rounded-xl hover:bg-red-700 transition">Remove</button>
            </div>
        </form>
    </div>
</div>
<script>
function openDeleteModal(listingId, title) {
    document.getElementById('deleteListingForm').action = '{{ url("seller/listings") }}/' + listingId;
    document.getElementById('deleteListingTitle').textContent = title ? 'Remove “‘ + title + '”?' : 'Remove this listing?';
    document.getElementById('deleteListingModal').classList.remove('hidden');
    document.getElementById('deleteListingModal').classList.add('flex');
}
function closeDeleteModal() {
    document.getElementById('deleteListingModal').classList.add('hidden');
    document.getElementById('deleteListingModal').classList.remove('flex');
}
document.getElementById('deleteListingModal') && document.getElementById('deleteListingModal').addEventListener('click', function(e) {
    if (e.target === this) closeDeleteModal();
});
</script>

<!-- ════════════════════════════════════════════════════
     CHANGE EMAIL MODAL (2-step)
════════════════════════════════════════════════════ -->
<div id="emailChangeModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="text-base font-bold text-gray-900">Change Email Address</h3>
            <button type="button" onclick="closeEmailChangeModal()" class="text-gray-400 hover:text-gray-600">
                <span class="material-icons-round">close</span>
            </button>
        </div>

        <!-- Step 1: Credentials -->
        <div id="emailStep1" class="p-6">
            <p class="text-sm text-gray-500 mb-5">Enter your new email and current password to initiate the change. A verification code will be sent to your <strong>new email</strong>.</p>
            <form method="POST" action="{{ route('seller.dashboard.initiate-email-change') }}">
                @csrf
                <!-- Current Email (read-only) -->
                <div class="mb-4">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Current Email</label>
                    <input type="email" value="{{ $user->email }}" readonly
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 text-gray-500 text-sm cursor-not-allowed">
                </div>
                <!-- New Email -->
                <div class="mb-4">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">New Email</label>
                    <input type="email" name="new_email" required placeholder="your@newemail.com"
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 text-sm transition">
                </div>
                <!-- Password -->
                <div class="mb-5">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Account Password</label>
                    <div class="relative">
                        <input type="password" name="password" id="ecm_password" required
                               class="w-full px-4 py-2.5 pr-10 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 text-sm transition"
                               placeholder="Your current password">
                        <button type="button" onclick="ecmTogglePwd()" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                            <span class="material-icons-round text-lg" id="ecm_eye">visibility</span>
                        </button>
                    </div>
                </div>
                <div class="flex items-center justify-between gap-3">
                    <a href="{{ route('password.request') }}" target="_blank"
                       class="text-xs text-blue-600 hover:text-blue-800">Forgot your password?</a>
                    <div class="flex gap-2">
                        <button type="button" onclick="closeEmailChangeModal()"
                                class="px-4 py-2 text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition">Cancel</button>
                        <button type="submit"
                                class="px-5 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-xl transition">Send Code</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Step 2: OTP Verification -->
        <div id="emailStep2" class="p-6 hidden">
            <div class="flex items-center gap-2 text-sm text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-xl px-4 py-3 mb-5">
                <span class="material-icons-round" style="font-size:18px">mail</span>
                <span>Verification code sent to <strong>{{ $emailPendingNew ?? 'your new email' }}</strong>. Expires in 10 minutes.</span>
            </div>
            <form method="POST" action="{{ route('seller.dashboard.update-email') }}">
                @csrf
                <input type="hidden" name="email" value="{{ $emailPendingNew ?? '' }}">
                <div class="mb-5">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Verification Code</label>
                    <input type="text" name="code" maxlength="6" pattern="[0-9]*" inputmode="numeric" required
                           placeholder="000000"
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 text-center text-2xl font-mono tracking-[0.3em] transition">
                    @error('code')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="flex items-center justify-between gap-3">
                    <form method="POST" action="{{ route('seller.dashboard.cancel-email-change') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-xs text-gray-500 hover:text-gray-700">Cancel change</button>
                    </form>
                    <button type="submit"
                            class="px-5 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-xl transition">Confirm Change</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ════════════════════════════════════════════════════
     CHANGE PASSWORD MODAL
════════════════════════════════════════════════════ -->
<div id="passwordModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h3 class="text-base font-bold text-gray-900">Change Password</h3>
            <button type="button" onclick="hidePasswordModal()" class="text-gray-400 hover:text-gray-600">
                <span class="material-icons-round">close</span>
            </button>
        </div>
        <div class="p-6">
            <form method="POST" action="{{ route('seller.dashboard.change-password') }}">
                @csrf
                <!-- Current Password -->
                <div class="mb-4">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Current Password</label>
                    <div class="relative">
                        <input type="password" id="modal_current_password" name="current_password" required
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 pr-10 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600"
                                onclick="togglePasswordModal('modal_current_password', this)" aria-label="Toggle password visibility">
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
                        <input type="password" id="modal_new_password" name="password" required
                               minlength="8" maxlength="15"
                               data-password-strength
                               data-cm-validate="password-register"
                               data-cm-label="New password"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 pr-10 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600"
                                onclick="togglePasswordModal('modal_new_password', this)" aria-label="Toggle password visibility">
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
                        <input type="password" id="modal_confirm_password" name="password_confirmation" required
                               minlength="8" maxlength="15"
                               data-cm-match="#modal_new_password"
                               data-cm-label="Confirm password"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 pr-10 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600"
                                onclick="togglePasswordModal('modal_confirm_password', this)" aria-label="Toggle password visibility">
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

<!-- ════════════════════════════════════════════════════
     PAYOUT SETTINGS MODAL
════════════════════════════════════════════════════ -->
<div id="payoutModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden max-h-[90vh] flex flex-col">
        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 flex-shrink-0">
            <div class="flex items-center gap-2">
                <span class="material-icons-round text-blue-600">account_balance</span>
                <h3 class="text-base font-bold text-gray-900">Payout Settings</h3>
            </div>
            <button type="button" onclick="hidePayoutModal()" class="text-gray-400 hover:text-gray-600">
                <span class="material-icons-round">close</span>
            </button>
        </div>

        <!-- Scrollable body -->
        <div class="overflow-y-auto flex-1">
            <!-- Earnings notice -->
            <div class="mx-6 mt-5 flex items-start gap-2.5 bg-amber-50 border border-amber-200 rounded-xl px-4 py-3">
                <span class="material-icons-round text-amber-500 flex-shrink-0" style="font-size:18px">warning</span>
                <p class="text-xs text-amber-800 leading-relaxed">Payout details must be completed before earnings can be released.</p>
            </div>

            <form id="payoutForm" method="POST" action="{{ route('seller.dashboard.update-payout') }}">
                @csrf
                <div class="px-6 pt-5 pb-4 space-y-4">
                    <!-- Bank Name -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Bank Name *</label>
                        <input type="text"
                               name="bank_name"
                               value="{{ old('bank_name', $payoutMethod->bank_name ?? '') }}"
                               required
                               placeholder="e.g. Commonwealth Bank"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    </div>

                    <!-- Account Name -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Account Name *</label>
                        <input type="text"
                               name="account_holder_name"
                               value="{{ old('account_holder_name', $payoutMethod->account_holder_name ?? '') }}"
                               required
                               autocomplete="name"
                               placeholder="Name as it appears on the account"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    </div>

                    <!-- Account Number -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Account Number {{ $payoutMethod ? '' : '*' }}</label>
                        @if($payoutMethod && $payoutMethod->account_number)
                            <p class="text-[11px] text-gray-400 mb-1.5">Current: ****{{ strlen($payoutMethod->account_number) >= 4 ? substr($payoutMethod->account_number, -4) : str_repeat('*', strlen($payoutMethod->account_number)) }}</p>
                        @endif
                        <div class="flex items-center border-2 border-gray-200 rounded-xl focus-within:border-blue-500 transition-colors overflow-hidden">
                            <span class="material-icons-round text-gray-300 flex-shrink-0 ml-3 select-none" style="font-size:20px">tag</span>
                            <input type="text"
                                   id="payoutAccountNumber"
                                   name="account_number"
                                   value=""
                                   inputmode="numeric"
                                   autocomplete="off"
                                   maxlength="27"
                                   placeholder="{{ $payoutMethod ? 'Leave blank to keep current' : '0000 0000 0000 0000' }}"
                                   {{ !$payoutMethod ? 'required' : '' }}
                                   class="flex-1 px-3 py-3 bg-transparent text-sm text-gray-900 placeholder-gray-300 focus:outline-none font-mono tracking-wider">
                        </div>
                        <p class="text-[11px] text-gray-400 mt-1">Enter digits only — spaces are added automatically.</p>
                    </div>

                    <!-- Routing / Branch Number -->
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Routing / Branch Number <span class="normal-case font-normal text-gray-400">(if applicable)</span></label>
                        @if($payoutMethod && $payoutMethod->routing_number)
                            <p class="text-[11px] text-gray-400 mb-1.5">Current: ****{{ substr($payoutMethod->routing_number, -4) }}</p>
                        @endif
                        <div class="flex items-center border-2 border-gray-200 rounded-xl focus-within:border-blue-500 transition-colors overflow-hidden">
                            <span class="material-icons-round text-gray-300 flex-shrink-0 ml-3 select-none" style="font-size:20px">account_tree</span>
                            <input type="text"
                                   id="payoutRoutingNumber"
                                   name="routing_number"
                                   value=""
                                   inputmode="numeric"
                                   autocomplete="off"
                                   maxlength="15"
                                   placeholder="{{ $payoutMethod ? 'Leave blank to keep current' : 'Optional' }}"
                                   class="flex-1 px-3 py-3 bg-transparent text-sm text-gray-900 placeholder-gray-300 focus:outline-none font-mono tracking-wider">
                        </div>
                    </div>

                    <!-- Truth Declaration -->
                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 mt-2">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" id="payoutTruthDeclaration" required
                                   class="mt-0.5 w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 flex-shrink-0">
                            <span class="text-xs text-gray-600 leading-relaxed">
                                I confirm that the information provided above is accurate and true. I understand that CayMark is not responsible for failed payouts due to incorrect or incomplete banking details provided by me.
                            </span>
                        </label>
                    </div>
                </div>

                <!-- Footer -->
                <div class="flex items-center justify-end gap-3 px-6 pb-6">
                    <button type="button" onclick="hidePayoutModal()"
                            class="px-4 py-2 text-sm font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition">Cancel</button>
                    <button type="submit"
                            class="px-5 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-xl transition">Save Payout Settings</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Tab Navigation - Controlled by sidebar, no horizontal tabs
function showTab(tabName) {
    document.querySelectorAll('.tab-content').forEach(content => {
        content.style.display = 'none';
    });
    
    const contentElement = document.getElementById('content-' + tabName);
    if (contentElement) {
        contentElement.style.display = 'block';
    }

    if (tabName === 'auctions' && typeof applyStoredSellerAuctionsViewMode === 'function') {
        applyStoredSellerAuctionsViewMode();
    }
    
    // Initialize charts if switching to dashboard tab
    if (tabName === 'dashboard') {
        setTimeout(initializeCharts, 50);
    }
}

// Show tab on page load (server route sets active tab)
document.addEventListener('DOMContentLoaded', function() {
    const tab = @json($activeTab ?? 'dashboard');

    showTab(tab);

    @if(session('open_payout_modal'))
    if (typeof showPayoutModal === 'function') {
        showPayoutModal();
    }
    @endif

    @php $emailPendingForJs = session('email_change_pending') || (isset($emailPending) && $emailPending); @endphp
    @if($emailPendingForJs)
    if (typeof openEmailChangeModal === 'function') {
        openEmailChangeModal(2);
    }
    @endif

    if (tab === 'support') {
        var banner = document.getElementById('seller-support-success-banner');
        if (banner) banner.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    if (tab === 'auctions') {
        const params = new URLSearchParams(window.location.search);
        const sec = params.get('section');
        if (sec && ['current', 'pending', 'past', 'rejected', 'won'].indexOf(sec) !== -1) {
            showAuctionSection(sec);
        }
    }
    
    // Initialize Charts (will work for dashboard tab, safe to call for others)
    if (tab === 'dashboard') {
        initializeCharts();
    } else {
        // Initialize charts with delay in case user switches to dashboard
        setTimeout(initializeCharts, 100);
    }

    // Seller account: phone SMS verification (country code + national number)
    (function() {
        var sendBtn = document.getElementById('seller-dash-send-code-btn');
        var verifyBtn = document.getElementById('seller-dash-verify-phone-btn');
        var phoneInput = document.getElementById('seller_dash_phone_input');
        var countrySelect = document.getElementById('seller_dash_phone_country');
        var phoneFull = document.getElementById('seller_dash_phone_full');
        var codeInput = document.getElementById('seller_dash_phone_code_input');
        var verifyRow = document.getElementById('seller-dash-phone-verify-row');
        var verifiedBadge = document.getElementById('seller-dash-phone-verified-badge');
        var phoneDisplay = document.getElementById('seller_dashboard_phone_display');
        if (!sendBtn || !verifyBtn || !phoneInput || !countrySelect) return;

        var csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
            document.querySelector('input[name="_token"]')?.value || '';
        var sendUrl = '{{ route("registration.phone.send-code") }}';
        var verifyUrl = '{{ route("registration.phone.verify") }}';

        function getFullPhone() {
            var code = (countrySelect && countrySelect.value) ? String(countrySelect.value).trim() : '';
            var num = (phoneInput && phoneInput.value) ? String(phoneInput.value).trim().replace(/^0+/, '') : '';
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
                    var digits = String(res.data.phone || '').replace(/\D/g, '');
                    if (phoneDisplay) phoneDisplay.textContent = digits ? ('+' + digits) : phone;
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

(function () {
    var STORAGE_KEY = 'sellerAuctionsViewMode';
    window.setSellerAuctionsViewMode = function (mode) {
        var root = document.getElementById('content-auctions');
        var btnGrid = document.getElementById('seller-auctions-view-btn-grid');
        var btnList = document.getElementById('seller-auctions-view-btn-list');
        if (!root) return;
        var isList = mode === 'list';
        root.classList.toggle('seller-auctions-view-list', isList);
        if (btnGrid && btnList) {
            btnGrid.classList.toggle('is-active', !isList);
            btnList.classList.toggle('is-active', isList);
        }
        try {
            localStorage.setItem(STORAGE_KEY, isList ? 'list' : 'grid');
        } catch (e) {}
    };
    window.applyStoredSellerAuctionsViewMode = function () {
        try {
            var v = localStorage.getItem(STORAGE_KEY);
            if (v === 'list') {
                setSellerAuctionsViewMode('list');
            } else {
                setSellerAuctionsViewMode('grid');
            }
        } catch (e) {
            setSellerAuctionsViewMode('grid');
        }
    };
})();

// Auction Section Navigation
function showAuctionSection(section) {
    document.querySelectorAll('.auction-section').forEach(function (el) {
        el.classList.add('hidden');
    });

    document.querySelectorAll('.auction-tab-button').forEach(function (btn) {
        btn.classList.remove('text-white', 'bg-gradient-to-r', 'from-blue-600', 'to-indigo-600', 'shadow-sm');
        btn.classList.add('text-gray-600', 'hover:text-gray-900');
    });

    const panel = document.getElementById('auction-section-' + section);
    if (panel) panel.classList.remove('hidden');

    const activeButton = document.getElementById('auction-' + section);
    if (activeButton) {
        activeButton.classList.remove('text-gray-600', 'hover:text-gray-900');
        activeButton.classList.add('text-white', 'bg-gradient-to-r', 'from-blue-600', 'to-indigo-600', 'shadow-sm');
    }
}

// Six-box pickup PIN inputs on the Completed tab.
// - auto-advances to next box on input
// - jumps back on Backspace when empty
// - accepts pasted 6-digit codes
// - mirrors the value into a single hidden field for submit
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-pin-form]').forEach(function (form) {
        var boxes = Array.from(form.querySelectorAll('[data-pin-boxes] .seller-pin-box'));
        var hidden = form.querySelector('[data-pin-hidden]');
        var submit = form.querySelector('[data-pin-submit]');
        if (!boxes.length || !hidden) return;

        function syncHidden() {
            var value = boxes.map(function (b) { return b.value || ''; }).join('');
            hidden.value = value;
            if (submit) submit.disabled = value.length !== 6;
        }

        boxes.forEach(function (box, idx) {
            box.addEventListener('input', function (e) {
                var v = (box.value || '').replace(/\D/g, '').slice(0, 1);
                box.value = v;
                if (v && idx < boxes.length - 1) boxes[idx + 1].focus();
                syncHidden();
            });
            box.addEventListener('keydown', function (e) {
                if (e.key === 'Backspace' && !box.value && idx > 0) {
                    boxes[idx - 1].focus();
                }
            });
            box.addEventListener('paste', function (e) {
                var text = (e.clipboardData || window.clipboardData).getData('text') || '';
                var digits = text.replace(/\D/g, '').slice(0, 6);
                if (!digits) return;
                e.preventDefault();
                digits.split('').forEach(function (d, i) {
                    if (boxes[i]) boxes[i].value = d;
                });
                var next = Math.min(digits.length, boxes.length - 1);
                boxes[next].focus();
                syncHidden();
            });
        });

        syncHidden();
    });
});

// ——— Modal helpers (shared pattern: hidden + flex classes) ———
function _modalShow(id) {
    var el = document.getElementById(id);
    if (el) el.classList.remove('hidden');
}
function _modalHide(id) {
    var el = document.getElementById(id);
    if (el) el.classList.add('hidden');
}

// Password Modal
function showPasswordModal() { _modalShow('passwordModal'); }
function hidePasswordModal()  { _modalHide('passwordModal'); }

function togglePasswordModal(inputId, btn) {
    var input = document.getElementById(inputId);
    if (!input) return;
    var showing = input.type === 'text';
    input.type = showing ? 'password' : 'text';
    btn.querySelectorAll('.eye-open').forEach(function(el) { el.classList.toggle('hidden', !showing); });
    btn.querySelectorAll('.eye-closed').forEach(function(el) { el.classList.toggle('hidden', showing); });
}

// Payout Modal
function showPayoutModal() { _modalShow('payoutModal'); }
function hidePayoutModal()  { _modalHide('payoutModal'); }

// Account number auto-formatter (groups digits into sets of 4, e.g. 1234 5678 9012 3456)
(function () {
    function formatBankNumber(input, groupSize, maxGroups) {
        input.addEventListener('input', function () {
            var digits = this.value.replace(/\D/g, '').slice(0, groupSize * maxGroups);
            var groups = [];
            for (var i = 0; i < digits.length; i += groupSize) {
                groups.push(digits.slice(i, i + groupSize));
            }
            this.value = groups.join(' ');
        });
        // Strip spaces before form submit so only raw digits reach the server
        var form = input.closest('form');
        if (form) {
            form.addEventListener('submit', function () {
                input.value = input.value.replace(/\s/g, '');
            }, { once: false });
        }
    }

    var acct    = document.getElementById('payoutAccountNumber');
    var routing = document.getElementById('payoutRoutingNumber');
    if (acct)    formatBankNumber(acct, 4, 5);   // up to 20 digits → 5 groups of 4
    if (routing) formatBankNumber(routing, 3, 4); // up to 12 digits → 4 groups of 3 (routing style)
})();

// Email Change Modal
function openEmailChangeModal(step) {
    _modalShow('emailChangeModal');
    showEmailStep(step || 1);
}
function closeEmailChangeModal() { _modalHide('emailChangeModal'); }

function showEmailStep(step) {
    var s1 = document.getElementById('emailStep1');
    var s2 = document.getElementById('emailStep2');
    if (!s1 || !s2) return;
    if (step === 2) {
        s1.classList.add('hidden');
        s2.classList.remove('hidden');
    } else {
        s1.classList.remove('hidden');
        s2.classList.add('hidden');
    }
}

function ecmTogglePwd() {
    var input = document.getElementById('ecm_password');
    var eye   = document.getElementById('ecm_eye');
    if (!input || !eye) return;
    if (input.type === 'password') {
        input.type = 'text';
        eye.textContent = 'visibility_off';
    } else {
        input.type = 'password';
        eye.textContent = 'visibility';
    }
}

// Open the rejection-details popup modal
function openRejectionModal(listingId, listingName, reason, notes, editUrl) {
    document.getElementById('rejModal_listingName').textContent = listingName || '';
    document.getElementById('rejModal_reason').textContent = reason || 'No reason provided.';
    var notesWrap = document.getElementById('rejModal_notesWrap');
    var notesEl   = document.getElementById('rejModal_notes');
    if (notes && notes.trim() !== '') {
        notesEl.textContent = notes;
        notesWrap.classList.remove('hidden');
    } else {
        notesWrap.classList.add('hidden');
    }
    var editBtn = document.getElementById('rejModal_editBtn');
    if (editBtn) editBtn.href = editUrl || '#';
    _modalShow('rejectionModal');
}

// Close modals when clicking the backdrop
window.onclick = function(event) {
    var ids = ['passwordModal', 'payoutModal', 'emailChangeModal', 'rejectionModal'];
    ids.forEach(function(id) {
        var el = document.getElementById(id);
        if (el && event.target === el) el.classList.add('hidden');
    });
}

// Countdown Timer
function updateCountdowns() {
    document.querySelectorAll('[id^="countdown-"]').forEach(element => {
        const endTime = new Date(element.getAttribute('data-end-time'));
        const now = new Date();
        const diff = endTime - now;

        if (diff <= 0) {
            element.textContent = 'Auction Ended';
            return;
        }

        const days = Math.floor(diff / (1000 * 60 * 60 * 24));
        const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((diff % (1000 * 60)) / 1000);

        let timeString = '';
        if (days > 0) {
            timeString = `${days}d ${hours}h ${minutes}m`;
        } else if (hours > 0) {
            timeString = `${hours}h ${minutes}m ${seconds}s`;
        } else {
            timeString = `${minutes}m ${seconds}s`;
        }

        element.textContent = timeString;
    });
}

// Rejection Timer
function updateRejectionTimers() {
    document.querySelectorAll('[id^="rejection-timer-"]').forEach(element => {
        const deadline = new Date(element.getAttribute('data-deadline'));
        const now = new Date();
        const diff = deadline - now;

        if (diff <= 0) {
            element.textContent = 'Time Expired';
            return;
        }

        const days = Math.floor(diff / (1000 * 60 * 60 * 24));
        const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));

        let timeString = '';
        if (days > 0) {
            timeString = `${days} days, ${hours} hours`;
        } else if (hours > 0) {
            timeString = `${hours} hours, ${minutes} minutes`;
        } else {
            timeString = `${minutes} minutes`;
        }

        element.textContent = timeString;
    });
}

// Update timers every second
setInterval(() => {
    updateCountdowns();
    updateRejectionTimers();
}, 1000);
updateCountdowns();
updateRejectionTimers();

// Initialize Chart.js charts
function initializeCharts() {
    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart');
    if (revenueCtx) {
        const revenueData = @json($revenueChartData ?? ['labels' => [], 'data' => []]);
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: revenueData.labels || [],
                datasets: [{
                    label: 'Revenue ($)',
                    data: revenueData.data || [],
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    fill: true,
                    borderWidth: 3,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    pointBackgroundColor: 'rgb(59, 130, 246)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleFont: { size: 14, weight: 'bold' },
                        bodyFont: { size: 13 },
                        callbacks: {
                            label: function(context) {
                                return '$' + context.parsed.y.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString();
                            },
                            font: { size: 11 }
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                            drawBorder: false
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: { size: 11 }
                        }
                    }
                }
            }
        });
    }

    // Listing Status Chart
    const statusCtx = document.getElementById('listingStatusChart');
    if (statusCtx) {
        const statusData = @json($listingStatusChartData ?? ['labels' => [], 'data' => [], 'colors' => []]);
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: statusData.labels || [],
                datasets: [{
                    data: statusData.data || [],
                    backgroundColor: statusData.colors || ['#3B82F6', '#10B981', '#EF4444', '#F59E0B'],
                    borderWidth: 0,
                    hoverOffset: 8,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            font: { size: 12, weight: '500' },
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                return label + ': ' + value + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
    }

    // Auction Performance Chart
    const performanceCtx = document.getElementById('auctionPerformanceChart');
    if (performanceCtx) {
        const performanceData = @json($auctionPerformanceData ?? ['labels' => [], 'listings' => [], 'bids' => []]);
        new Chart(performanceCtx, {
            type: 'bar',
            data: {
                labels: performanceData.labels || [],
                datasets: [
                    {
                        label: 'New Listings',
                        data: performanceData.listings || [],
                        backgroundColor: 'rgba(59, 130, 246, 0.7)',
                        borderColor: 'rgb(59, 130, 246)',
                        borderWidth: 2,
                        borderRadius: 6,
                    },
                    {
                        label: 'Bids Received',
                        data: performanceData.bids || [],
                        backgroundColor: 'rgba(16, 185, 129, 0.7)',
                        borderColor: 'rgb(16, 185, 129)',
                        borderWidth: 2,
                        borderRadius: 6,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            padding: 15,
                            font: { size: 12, weight: '500' },
                            usePointStyle: true
                        }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                            drawBorder: false
                        },
                        ticks: {
                            font: { size: 11 }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            maxRotation: 45,
                            minRotation: 45,
                            font: { size: 10 }
                        }
                    }
                }
            }
        });
    }

    // Bid Activity Chart
    const bidActivityCtx = document.getElementById('bidActivityChart');
    if (bidActivityCtx) {
        const bidActivityData = @json($bidActivityData ?? ['labels' => [], 'counts' => [], 'amounts' => []]);
        new Chart(bidActivityCtx, {
            type: 'line',
            data: {
                labels: bidActivityData.labels || [],
                datasets: [
                    {
                        label: 'Bid Count',
                        data: bidActivityData.counts || [],
                        borderColor: 'rgb(249, 115, 22)',
                        backgroundColor: 'rgba(249, 115, 22, 0.1)',
                        tension: 0.4,
                        fill: true,
                        borderWidth: 3,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        yAxisID: 'y',
                    },
                    {
                        label: 'Bid Amount ($)',
                        data: bidActivityData.amounts || [],
                        borderColor: 'rgb(236, 72, 153)',
                        backgroundColor: 'rgba(236, 72, 153, 0.1)',
                        tension: 0.4,
                        fill: true,
                        borderWidth: 3,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        yAxisID: 'y1',
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            padding: 15,
                            font: { size: 12, weight: '500' },
                            usePointStyle: true
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        callbacks: {
                            label: function(context) {
                                if (context.datasetIndex === 0) {
                                    return 'Bids: ' + context.parsed.y;
                                } else {
                                    return 'Amount: $' + context.parsed.y.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                                }
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Bid Count',
                            font: { size: 12, weight: 'bold' }
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                            drawBorder: false
                        },
                        ticks: {
                            font: { size: 11 }
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Bid Amount ($)',
                            font: { size: 12, weight: 'bold' }
                        },
                        grid: {
                            drawOnChartArea: false,
                        },
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString();
                            },
                            font: { size: 11 }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: { size: 11 }
                        }
                    }
                }
            }
        });
    }

    // Performance Insights chart (Business Seller dashboard only)
    if (typeof window._buildInsightsChart === 'function') {
        window._buildInsightsChart();
    }
}

// ——— Notifications (same behavior as buyer dashboard; API under /seller/) ———
var NOTIFICATION_BASE = '/seller/notifications';
function handleNotificationClick(element) {
    var card = element.closest ? element.closest('.notification-card') : element;
    if (!card) return;
    var notificationId = card.dataset.notificationId;
    var link = (card.dataset.link || '').trim();
    var isUnread = card.dataset.isUnread === 'true';
    if (link) {
        if (isUnread) {
            var csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || document.querySelector('input[name="_token"]')?.value || '';
            fetch(NOTIFICATION_BASE + '/' + notificationId + '/mark-read', {
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
function markNotificationAsRead(notificationId, element) {
    if (!element.dataset.isUnread || element.dataset.isUnread === 'false') return;
    var csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || document.querySelector('input[name="_token"]')?.value || '';
    fetch(NOTIFICATION_BASE + '/' + notificationId + '/mark-read', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        body: JSON.stringify({})
    }).then(function(r) { return r.json(); }).then(function(data) {
        if (data.success) {
            element.classList.remove('bg-blue-50', 'bg-amber-50', 'bg-emerald-50', 'bg-purple-50', 'bg-indigo-50', 'border-blue-200', 'border-amber-200', 'border-emerald-200', 'border-purple-200', 'border-indigo-200', 'shadow-sm', 'cursor-pointer');
            element.classList.add('border-gray-200');
            element.dataset.isUnread = 'false';
            element.setAttribute('data-read-status', 'read');
            element.removeAttribute('onclick');
            var unreadDot = element.querySelector('.unread-dot');
            if (unreadDot) unreadDot.remove();
            var iconContainer = element.querySelector('.flex-shrink-0.w-12');
            if (iconContainer) {
                iconContainer.classList.remove('bg-blue-50', 'bg-amber-50', 'bg-emerald-50', 'bg-purple-50', 'bg-indigo-50', 'border-blue-200', 'border-amber-200', 'border-emerald-200', 'border-purple-200', 'border-indigo-200');
                iconContainer.classList.add('bg-gray-100', 'border-gray-200');
            }
            var icon = element.querySelector('.material-icons-round');
            if (icon) { icon.classList.remove('text-blue-600', 'text-amber-600', 'text-emerald-600', 'text-purple-600', 'text-indigo-600'); icon.classList.add('text-gray-400'); }
            var text = element.querySelector('.font-semibold');
            if (text) { text.classList.remove('text-gray-900'); text.classList.add('text-gray-700'); }
            updateUnreadCount();
            var currentFilter = window.notificationFilter || 'all';
            if (currentFilter === 'unread') {
                element.style.display = 'none';
                var monthGroup = element.closest('.notification-month-group');
                if (monthGroup && monthGroup.querySelectorAll('.notification-card[style*="display: block"], .notification-card:not([style*="display: none"])').length === 0) {
                    monthGroup.style.display = 'none';
                }
            }
        }
    }).catch(function(e) { console.error('Error marking notification as read:', e); });
}
function markAllNotificationsAsRead() {
    var btn = document.getElementById('read-all-notifications-btn');
    if (btn) btn.disabled = true;
    var csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || document.querySelector('input[name="_token"]')?.value || '';
    fetch(NOTIFICATION_BASE + '/mark-all-read', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        body: JSON.stringify({})
    }).then(function(r) { return r.json(); }).then(function(data) {
        if (data.success) {
            document.querySelectorAll('.notification-card[data-is-unread="true"]').forEach(function(card) {
                card.classList.remove('bg-blue-50', 'bg-amber-50', 'bg-emerald-50', 'bg-purple-50', 'bg-indigo-50', 'border-blue-200', 'border-amber-200', 'border-emerald-200', 'border-purple-200', 'border-indigo-200', 'shadow-sm', 'cursor-pointer');
                card.classList.add('border-gray-200');
                card.dataset.isUnread = 'false';
                card.setAttribute('data-read-status', 'read');
                card.removeAttribute('onclick');
                var unreadDot = card.querySelector('.unread-dot'); if (unreadDot) unreadDot.remove();
                var iconContainer = card.querySelector('.flex-shrink-0.w-12');
                if (iconContainer) {
                    iconContainer.classList.remove('bg-blue-50', 'bg-amber-50', 'bg-emerald-50', 'bg-purple-50', 'bg-indigo-50', 'border-blue-200', 'border-amber-200', 'border-emerald-200', 'border-purple-200', 'border-indigo-200');
                    iconContainer.classList.add('bg-gray-100', 'border-gray-200');
                }
                var icon = card.querySelector('.material-icons-round');
                if (icon) { icon.classList.remove('text-blue-600', 'text-amber-600', 'text-emerald-600', 'text-purple-600', 'text-indigo-600'); icon.classList.add('text-gray-400'); }
                var text = card.querySelector('.font-semibold');
                if (text) { text.classList.remove('text-gray-900'); text.classList.add('text-gray-700'); }
            });
            var unreadDisplay = document.querySelector('.unread-count-display');
            if (unreadDisplay) unreadDisplay.style.display = 'none';
            if (btn) { btn.style.display = 'none'; btn.disabled = false; }
            updateUnreadCount();
        }
    }).catch(function(e) { if (btn) btn.disabled = false; });
}
function updateUnreadCount() {
    fetch(NOTIFICATION_BASE + '/unread-count').then(function(r) { return r.json(); }).then(function(data) {
        var count = data.count || 0;
        var headerBadge = document.querySelector('.unread-count-badge');
        if (headerBadge) { headerBadge.textContent = count; headerBadge.style.display = count > 0 ? 'flex' : 'none'; }
        var sidebarBadge = document.querySelector('.sidebar-notification-badge');
        if (sidebarBadge) { sidebarBadge.textContent = count; sidebarBadge.style.display = count > 0 ? 'flex' : 'none'; }
        var unreadDisplay = document.querySelector('.unread-count-display');
        if (unreadDisplay) {
            if (count > 0) {
                unreadDisplay.innerHTML = '<span class="w-2 h-2 bg-blue-600 rounded-full animate-pulse"></span><span class="text-sm font-semibold text-blue-700">' + count + ' unread</span>';
                unreadDisplay.style.display = 'flex';
            } else {
                unreadDisplay.style.display = 'none';
            }
        }
    }).catch(function(e) {});
}
function setNotificationFilter(filterType) {
    window.notificationFilter = filterType; // keep for read-marking logic
    // Update active button styles
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

function filterNotifications(filterType) {
    var allCards = document.querySelectorAll('.notification-card');
    var monthGroups = document.querySelectorAll('.notification-month-group');
    allCards.forEach(function(card) {
        var readStatus = card.getAttribute('data-read-status');
        if (filterType === 'all') card.style.display = '';
        else if (filterType === 'unread') card.style.display = readStatus === 'unread' ? '' : 'none';
        else if (filterType === 'read') card.style.display = readStatus === 'read' ? '' : 'none';
    });
    monthGroups.forEach(function(group) {
        var cards = group.querySelectorAll('.notification-card');
        var hasVisible = false;
        cards.forEach(function(c) { if (c.style.display !== 'none') hasVisible = true; });
        group.style.display = hasVisible ? '' : 'none';
    });
}
document.addEventListener('DOMContentLoaded', function() {
    if (document.querySelector('.notification-card')) updateUnreadCount();
});
</script>

@endsection
