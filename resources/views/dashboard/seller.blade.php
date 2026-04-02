@extends('layouts.dashboard')

@section('title', 'Seller Dashboard - CayMark')

@section('content')
<style>
.notifications-scrollbar { max-height: 65vh; overflow-y: scroll !important; overflow-x: hidden; }
.notifications-scrollbar::-webkit-scrollbar { width: 12px; }
.notifications-scrollbar::-webkit-scrollbar-track { background: #e2e8f0; border-radius: 6px; }
.notifications-scrollbar::-webkit-scrollbar-thumb { background: #94a3b8; border-radius: 6px; }
.notifications-scrollbar::-webkit-scrollbar-thumb:hover { background: #64748b; }
/* Auctions tab: scroll inside fixed-height main (layouts/dashboard .main-content overflow:hidden) */
.seller-auctions-scrollbar {
    max-height: calc(100vh - 4rem);
    overflow-y: auto;
    overflow-x: hidden;
    -webkit-overflow-scrolling: touch;
}
.seller-auctions-scrollbar::-webkit-scrollbar { width: 10px; }
.seller-auctions-scrollbar::-webkit-scrollbar-track { background: #e2e8f0; border-radius: 6px; }
.seller-auctions-scrollbar::-webkit-scrollbar-thumb { background: #94a3b8; border-radius: 6px; }
.seller-auctions-scrollbar::-webkit-scrollbar-thumb:hover { background: #64748b; }

/* Seller — Auctions tab: professional polish */
.seller-auctions-wrap {
    background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
    border-radius: 1rem;
    padding: 1.25rem;
}
@media (min-width: 640px) {
    .seller-auctions-wrap { padding: 1.5rem 1.75rem; }
}
.seller-auctions-hero-icon {
    width: 3rem;
    height: 3rem;
    border-radius: 0.875rem;
    background: linear-gradient(135deg, #063466 0%, #1e40af 100%);
    box-shadow: 0 8px 24px rgba(6, 52, 102, 0.35);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.seller-auctions-stat {
    position: relative;
    overflow: hidden;
    border-radius: 1rem;
    padding: 1.25rem 1.25rem 1.125rem;
    color: #fff;
    box-shadow: 0 10px 40px -12px rgba(15, 23, 42, 0.25);
}
.seller-auctions-stat::after {
    content: '';
    position: absolute;
    right: -1rem;
    top: -1rem;
    width: 6rem;
    height: 6rem;
    border-radius: 50%;
    background: rgba(255,255,255,0.08);
    pointer-events: none;
}
.seller-auctions-stat-label {
    font-size: 0.6875rem;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    opacity: 0.92;
}
.seller-auctions-stat-value {
    font-size: 1.875rem;
    font-weight: 800;
    line-height: 1.15;
    letter-spacing: -0.02em;
}
.seller-auctions-stat-hint {
    font-size: 0.8125rem;
    opacity: 0.88;
    margin-top: 0.25rem;
}
#content-auctions .auction-tab-button {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.625rem 1rem;
    border-radius: 0.75rem;
    font-size: 0.8125rem;
    font-weight: 600;
    letter-spacing: 0.02em;
    color: #64748b;
    background: transparent;
    border: none;
    cursor: pointer;
    transition: color 0.2s, background 0.2s, box-shadow 0.2s;
}
#content-auctions .auction-tab-button:hover {
    color: #0f172a;
    background: rgba(255,255,255,0.7);
}
#content-auctions .auction-tab-button.active {
    color: #fff !important;
    background: linear-gradient(135deg, #063466 0%, #1e3a8a 100%) !important;
    box-shadow: 0 4px 16px rgba(6, 52, 102, 0.35);
}
.seller-auction-card {
    border-radius: 1rem;
    border: 1px solid rgba(226, 232, 240, 0.95);
    background: #fff;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.06);
    overflow: hidden;
    transition: box-shadow 0.25s ease, border-color 0.25s ease, transform 0.2s ease;
}
.seller-auction-card:hover {
    box-shadow: 0 12px 40px -12px rgba(15, 23, 42, 0.18);
    border-color: #cbd5e1;
}
.seller-auction-card-media {
    position: relative;
    height: 12rem;
    background: linear-gradient(135deg, #e2e8f0 0%, #f1f5f9 100%);
}
.seller-auction-card-media img {
    transition: transform 0.35s ease;
}
.seller-auction-card:hover .seller-auction-card-media img {
    transform: scale(1.04);
}
.seller-auctions-empty {
    border-radius: 1rem;
    border: 2px dashed #cbd5e1;
    background: linear-gradient(180deg, rgba(248,250,252,0.9) 0%, rgba(241,245,249,0.5) 100%);
}

/* Listing card: View / Delete — match professional auctions UI */
#content-auctions .seller-auction-card-actions {
    display: flex;
    align-items: stretch;
    gap: 0.5rem;
    margin-top: 0.75rem;
    padding-top: 0.75rem;
    border-top: 1px solid #f1f5f9;
}
#content-auctions .seller-auction-btn {
    flex: 1 1 0;
    min-width: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.375rem;
    min-height: 2.5rem;
    padding: 0.5rem 0.625rem;
    font-size: 0.8125rem;
    font-weight: 600;
    letter-spacing: 0.02em;
    border-radius: 0.625rem;
    line-height: 1.2;
    transition: background 0.2s ease, color 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease, transform 0.15s ease;
    border: 1px solid transparent;
    cursor: pointer;
    text-decoration: none;
}
#content-auctions .seller-auction-btn .material-icons-round {
    font-size: 1.125rem;
    opacity: 0.95;
}
#content-auctions .seller-auction-btn--view {
    color: #fff;
    background: linear-gradient(135deg, #063466 0%, #1e3a8a 100%);
    border-color: rgba(6, 52, 102, 0.35);
    box-shadow: 0 1px 2px rgba(15, 23, 42, 0.08);
}
#content-auctions .seller-auction-btn--view:hover {
    box-shadow: 0 4px 14px rgba(6, 52, 102, 0.28);
    filter: brightness(1.06);
}
#content-auctions .seller-auction-btn--view:active {
    transform: scale(0.98);
}
#content-auctions .seller-auction-btn--delete {
    color: #b91c1c;
    background: #fff;
    border-color: #fecaca;
}
#content-auctions .seller-auction-btn--delete:hover {
    background: #fef2f2;
    border-color: #f87171;
    color: #991b1b;
    box-shadow: 0 1px 3px rgba(185, 28, 28, 0.12);
}
#content-auctions .seller-auction-btn--delete:active {
    transform: scale(0.98);
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
                <!-- Header Section: Business vs Individual Seller -->
                <div class="mb-4">
                    <div class="flex flex-wrap items-center gap-3 mb-2">
                        <h2 class="text-3xl font-bold bg-gradient-to-r from-blue-600 via-purple-600 to-blue-600 bg-clip-text text-transparent">
                            {{ $user->business_license_path ? 'Business Seller' : 'Individual Seller' }} Dashboard
                        </h2>
                        @if($user->business_license_path)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-800">Business Account</span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">Individual Seller</span>
                        @endif
                    </div>
                    <p class="text-gray-600 text-sm">{{ $user->business_license_path ? 'Manage your business listings, payouts, and buyer coordination.' : 'Your sales summary and listing status at a glance.' }}</p>
                </div>

                @if(!$user->business_license_path)
                {{-- INDIVIDUAL SELLER: Only Total Revenue, Active Auction, Pending Payout, Items Sold, Status Overview --}}
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
                    <div class="bg-gradient-to-br from-blue-500 via-blue-600 to-indigo-600 rounded-xl shadow-xl p-4 text-white transform hover:scale-105 transition-all duration-300 relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-white/10 rounded-full -mr-12 -mt-12"></div>
                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-2">
                                <span class="material-icons-round text-3xl opacity-80">attach_money</span>
                                <span class="text-xs font-semibold bg-white/20 px-2 py-0.5 rounded-full">Revenue</span>
                            </div>
                            <p class="text-blue-100 text-xs font-medium mb-1">Total Revenue</p>
                            <p class="text-3xl font-bold mb-0.5">${{ number_format($auctionSummary['total_sales_revenue'] ?? 0, 0) }}</p>
                            <p class="text-xs text-blue-100 opacity-75">All time earnings</p>
                        </div>
                    </div>
                    <div class="bg-gradient-to-br from-purple-500 via-pink-500 to-rose-600 rounded-xl shadow-xl p-4 text-white transform hover:scale-105 transition-all duration-300 relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-white/10 rounded-full -mr-12 -mt-12"></div>
                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-2">
                                <span class="material-icons-round text-3xl opacity-80">gavel</span>
                                <span class="text-xs font-semibold bg-white/20 px-2 py-0.5 rounded-full">Active</span>
                            </div>
                            <p class="text-purple-100 text-xs font-medium mb-1">Active Auctions</p>
                            <p class="text-3xl font-bold mb-0.5">{{ $auctionSummary['current_count'] ?? 0 }}</p>
                            <p class="text-xs text-purple-100 opacity-75">Currently live</p>
                        </div>
                    </div>
                    <div class="bg-gradient-to-br from-amber-500 via-orange-500 to-red-600 rounded-xl shadow-xl p-4 text-white transform hover:scale-105 transition-all duration-300 relative overflow-hidden" style="background: linear-gradient(to bottom right, #f59e0b, #f97316, #dc2626);">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-white/10 rounded-full -mr-12 -mt-12"></div>
                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-2">
                                <span class="material-icons-round text-3xl opacity-80 text-white">account_balance_wallet</span>
                                <span class="text-xs font-semibold bg-white/20 px-2 py-0.5 rounded-full text-white">Payout</span>
                            </div>
                            <p class="text-white text-xs font-medium mb-1">Pending Payout</p>
                            <p class="text-3xl font-bold mb-0.5 text-white">${{ number_format(collect($pendingPayouts ?? [])->sum('net_payout'), 0) }}</p>
                            <p class="text-xs text-white opacity-90">{{ count($pendingPayouts ?? []) }} pending</p>
                        </div>
                    </div>
                    <div class="bg-gradient-to-br from-emerald-500 via-green-500 to-teal-600 rounded-xl shadow-xl p-4 text-white transform hover:scale-105 transition-all duration-300 relative overflow-hidden" style="background: linear-gradient(to bottom right, #10b981, #059669, #0d9488);">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-white/10 rounded-full -mr-12 -mt-12"></div>
                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-2">
                                <span class="material-icons-round text-3xl opacity-80 text-white">check_circle</span>
                                <span class="text-xs font-semibold bg-white/20 px-2 py-0.5 rounded-full text-white">Sold</span>
                            </div>
                            <p class="text-white text-xs font-medium mb-1">Items Sold</p>
                            <p class="text-3xl font-bold mb-0.5 text-white">{{ $auctionSummary['total_items_sold'] ?? 0 }}</p>
                            <p class="text-xs text-white opacity-90">Total sold</p>
                        </div>
                    </div>
                </div>
                <!-- Status Overview (Individual Seller only) – full width so area feels filled -->
                <div class="w-full">
                    <div class="bg-gradient-to-br from-white to-gray-50 rounded-xl shadow-lg border border-gray-200 p-6 hover:shadow-xl transition-all duration-300 flex flex-col lg:flex-row lg:items-center lg:gap-8">
                        <div class="flex items-center justify-between mb-3 lg:mb-0 lg:flex-shrink-0 lg:w-48">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">Status Overview</h3>
                                <p class="text-xs text-gray-500">Listing distribution</p>
                            </div>
                            <div class="bg-purple-100 rounded-lg p-1.5">
                                <span class="material-icons-round text-purple-600 text-lg">pie_chart</span>
                            </div>
                        </div>
                        <div class="flex-1 min-h-[280px] lg:min-h-[320px]">
                            <canvas id="listingStatusChart"></canvas>
                        </div>
                    </div>
                </div>
                @else
                {{-- BUSINESS SELLER: Full dashboard (existing) --}}
                <!-- Top Stats Cards Row (Horizontal) -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
                    <div class="bg-gradient-to-br from-blue-500 via-blue-600 to-indigo-600 rounded-xl shadow-xl p-4 text-white transform hover:scale-105 transition-all duration-300 relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-white/10 rounded-full -mr-12 -mt-12"></div>
                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-2">
                                <span class="material-icons-round text-3xl opacity-80">attach_money</span>
                                <span class="text-xs font-semibold bg-white/20 px-2 py-0.5 rounded-full">Revenue</span>
                            </div>
                            <p class="text-blue-100 text-xs font-medium mb-1">Total Revenue</p>
                            <p class="text-3xl font-bold mb-0.5">${{ number_format($auctionSummary['total_sales_revenue'] ?? 0, 0) }}</p>
                            <p class="text-xs text-blue-100 opacity-75">All time earnings</p>
                        </div>
                    </div>
                    <div class="bg-gradient-to-br from-emerald-500 via-green-500 to-teal-600 rounded-xl shadow-xl p-4 text-white transform hover:scale-105 transition-all duration-300 relative overflow-hidden" style="background: linear-gradient(to bottom right, #10b981, #059669, #0d9488);">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-white/10 rounded-full -mr-12 -mt-12"></div>
                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-2">
                                <span class="material-icons-round text-3xl opacity-80 text-white">check_circle</span>
                                <span class="text-xs font-semibold bg-white/20 px-2 py-0.5 rounded-full text-white">Sales</span>
                            </div>
                            <p class="text-white text-xs font-medium mb-1">Items Sold</p>
                            <p class="text-3xl font-bold mb-0.5 text-white">{{ $auctionSummary['total_items_sold'] ?? 0 }}</p>
                            <p class="text-xs text-white opacity-90">{{ $salesConversionData['conversion_rate'] ?? 0 }}% conversion</p>
                        </div>
                    </div>
                    <div class="bg-gradient-to-br from-purple-500 via-pink-500 to-rose-600 rounded-xl shadow-xl p-4 text-white transform hover:scale-105 transition-all duration-300 relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-white/10 rounded-full -mr-12 -mt-12"></div>
                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-2">
                                <span class="material-icons-round text-3xl opacity-80">gavel</span>
                                <span class="text-xs font-semibold bg-white/20 px-2 py-0.5 rounded-full">Active</span>
                            </div>
                            <p class="text-purple-100 text-xs font-medium mb-1">Active Auctions</p>
                            <p class="text-3xl font-bold mb-0.5">{{ $auctionSummary['current_count'] ?? 0 }}</p>
                            <p class="text-xs text-purple-100 opacity-75">Currently live</p>
                        </div>
                    </div>
                    <div class="bg-gradient-to-br from-amber-500 via-orange-500 to-red-600 rounded-xl shadow-xl p-4 text-white transform hover:scale-105 transition-all duration-300 relative overflow-hidden" style="background: linear-gradient(to bottom right, #f59e0b, #f97316, #dc2626);">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-white/10 rounded-full -mr-12 -mt-12"></div>
                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-2">
                                <span class="material-icons-round text-3xl opacity-80 text-white">inventory_2</span>
                                <span class="text-xs font-semibold bg-white/20 px-2 py-0.5 rounded-full text-white">Total</span>
                            </div>
                            <p class="text-white text-xs font-medium mb-1">Total Listings</p>
                            <p class="text-3xl font-bold mb-0.5 text-white">{{ $auctionSummary['total_listings'] ?? 0 }}</p>
                            <p class="text-xs text-white opacity-90">All listings</p>
                        </div>
                    </div>
                </div>

                <!-- Secondary Stats Row (Horizontal) -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg shadow-md border border-blue-200 p-4 hover:shadow-lg transition-all duration-300">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-blue-700 text-xs font-medium">Avg. Sale Price</span>
                            <span class="material-icons-round text-blue-600 text-lg">trending_up</span>
                        </div>
                        <p class="text-xl font-bold text-blue-900">${{ number_format($averageSalePriceData['average'] ?? 0, 0) }}</p>
                        <p class="text-xs text-blue-600 mt-0.5">Based on {{ $averageSalePriceData['count'] ?? 0 }} sales</p>
                    </div>
                    <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg shadow-md border border-green-200 p-4 hover:shadow-lg transition-all duration-300">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-green-700 text-xs font-medium">Conversion Rate</span>
                            <span class="material-icons-round text-green-600 text-lg">percent</span>
                        </div>
                        <p class="text-xl font-bold text-green-900">{{ $salesConversionData['conversion_rate'] ?? 0 }}%</p>
                        <p class="text-xs text-green-600 mt-0.5">{{ $salesConversionData['sold'] ?? 0 }} of {{ $salesConversionData['total'] ?? 0 }} sold</p>
                    </div>
                    <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg shadow-md border border-purple-200 p-4 hover:shadow-lg transition-all duration-300">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-purple-700 text-xs font-medium">Highest Sale</span>
                            <span class="material-icons-round text-purple-600 text-lg">arrow_upward</span>
                        </div>
                        <p class="text-xl font-bold text-purple-900">${{ number_format($averageSalePriceData['highest'] ?? 0, 0) }}</p>
                        <p class="text-xs text-purple-600 mt-0.5">Best performing listing</p>
                    </div>
                    <div class="bg-gradient-to-br from-amber-50 to-amber-100 rounded-lg shadow-md border border-amber-200 p-4 hover:shadow-lg transition-all duration-300">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-amber-700 text-xs font-medium">Pending Payout</span>
                            <span class="material-icons-round text-amber-600 text-lg">account_balance_wallet</span>
                        </div>
                        <p class="text-xl font-bold text-amber-900">${{ number_format(collect($pendingPayouts ?? [])->sum('net_payout'), 0) }}</p>
                        <p class="text-xs text-amber-600 mt-0.5">{{ count($pendingPayouts ?? []) }} payout(s) pending</p>
                    </div>
                </div>

                <!-- Main Charts Row (Horizontal Layout) -->
                <div class="grid grid-cols-1 xl:grid-cols-3 gap-4 mb-4">
                    <!-- Revenue Trend Chart -->
                    <div class="xl:col-span-2 bg-gradient-to-br from-white to-gray-50 rounded-xl shadow-lg border border-gray-200 p-4 hover:shadow-xl transition-all duration-300">
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">Revenue Trend</h3>
                                <p class="text-xs text-gray-500">Last 6 months performance</p>
                            </div>
                            <div class="bg-blue-100 rounded-lg p-1.5">
                                <span class="material-icons-round text-blue-600 text-lg">show_chart</span>
                            </div>
                        </div>
                        <div class="h-64">
                            <canvas id="revenueChart"></canvas>
                        </div>
                    </div>

                    <!-- Listing Status Chart -->
                    <div class="bg-gradient-to-br from-white to-gray-50 rounded-xl shadow-lg border border-gray-200 p-4 hover:shadow-xl transition-all duration-300">
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">Status Overview</h3>
                                <p class="text-xs text-gray-500">Listing distribution</p>
                            </div>
                            <div class="bg-purple-100 rounded-lg p-1.5">
                                <span class="material-icons-round text-purple-600 text-lg">pie_chart</span>
                            </div>
                        </div>
                        <div class="h-64">
                            <canvas id="listingStatusChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Pending vs Completed Payouts Section -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
                    <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-4">
                        <h3 class="text-lg font-bold text-gray-900 mb-3 flex items-center gap-2">
                            <span class="material-icons-round text-amber-600">schedule</span>
                            Pending Payouts
                        </h3>
                        @if(isset($pendingPayouts) && $pendingPayouts->count() > 0)
                            <div class="space-y-2 max-h-48 overflow-y-auto">
                                @foreach($pendingPayouts as $payout)
                                    <div class="flex justify-between items-center py-2 border-b border-gray-100 last:border-0">
                                        <div>
                                            <p class="font-medium text-gray-900 text-sm">{{ $payout->item_title ?? 'Sale #' . $payout->payout_number }}</p>
                                            <p class="text-xs text-gray-500">{{ $payout->payout_generated_at?->format('M j, Y') }}</p>
                                        </div>
                                        <span class="font-bold text-amber-700">${{ number_format($payout->net_payout, 2) }}</span>
                                    </div>
                                @endforeach
                            </div>
                            <p class="text-sm text-amber-700 mt-2 font-medium">Total: ${{ number_format($pendingPayouts->sum('net_payout'), 2) }}</p>
                        @else
                            <p class="text-gray-500 text-sm">No pending payouts.</p>
                        @endif
                    </div>
                    <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-4">
                        <h3 class="text-lg font-bold text-gray-900 mb-3 flex items-center gap-2">
                            <span class="material-icons-round text-green-600">check_circle</span>
                            Completed Payouts
                        </h3>
                        @if(isset($completedPayouts) && $completedPayouts->count() > 0)
                            <div class="space-y-2 max-h-48 overflow-y-auto">
                                @foreach($completedPayouts as $payout)
                                    <div class="flex justify-between items-center py-2 border-b border-gray-100 last:border-0">
                                        <div>
                                            <p class="font-medium text-gray-900 text-sm">{{ $payout->item_title ?? 'Sale #' . $payout->payout_number }}</p>
                                            <p class="text-xs text-gray-500">{{ $payout->payout_processed_at?->format('M j, Y') }}</p>
                                        </div>
                                        <span class="font-bold text-green-700">${{ number_format($payout->net_payout, 2) }}</span>
                                    </div>
                                @endforeach
                            </div>
                            <a href="{{ route('seller.payouts') }}" class="text-sm text-blue-600 hover:underline mt-2 inline-block">View all payouts →</a>
                        @else
                            <p class="text-gray-500 text-sm">No completed payouts yet.</p>
                        @endif
                    </div>
                </div>

                <!-- Second Charts Row (Horizontal) -->
                <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">
                    <!-- Auction Performance Chart -->
                    <div class="bg-gradient-to-br from-white to-gray-50 rounded-xl shadow-lg border border-gray-200 p-4 hover:shadow-xl transition-all duration-300">
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">Auction Activity</h3>
                                <p class="text-xs text-gray-500">Last 30 days performance</p>
                            </div>
                            <div class="bg-green-100 rounded-lg p-1.5">
                                <span class="material-icons-round text-green-600 text-lg">bar_chart</span>
                            </div>
                        </div>
                        <div class="h-72">
                            <canvas id="auctionPerformanceChart"></canvas>
                        </div>
                    </div>

                    <!-- Bid Activity Chart -->
                    <div class="bg-gradient-to-br from-white to-gray-50 rounded-xl shadow-lg border border-gray-200 p-4 hover:shadow-xl transition-all duration-300">
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">Bid Activity</h3>
                                <p class="text-xs text-gray-500">Last 7 days bid trends</p>
                            </div>
                            <div class="bg-orange-100 rounded-lg p-1.5">
                                <span class="material-icons-round text-orange-600 text-lg">timeline</span>
                            </div>
                        </div>
                        <div class="h-72">
                            <canvas id="bidActivityChart"></canvas>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- USER TAB -->
            <div id="content-user" class="tab-content p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Account Information</h2>

                <!-- Full Name / Business Name -->
                @if($user->business_license_path)
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Business Name</label>
                        <div class="bg-gray-50 border border-gray-300 rounded-lg px-4 py-3">
                            <span class="text-gray-900">{{ $user->name }}</span>
                        </div>
                        <p class="text-sm text-gray-500 mt-1">Cannot be changed</p>
                    </div>
                @else
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                        <div class="bg-gray-50 border border-gray-300 rounded-lg px-4 py-3">
                            <span class="text-gray-900">{{ $user->name }}</span>
                        </div>
                        <p class="text-sm text-gray-500 mt-1">Cannot be changed</p>
                    </div>
                @endif

                <!-- Email Address (editable with verification) -->
                <div class="mb-6">
                    @php $emailChangePending = session('email_change_pending') || (new \App\Services\EmailChangeVerificationService())->hasPendingChange($user); @endphp
                    @if($emailChangePending)
                        @php $pendingNew = session('email_change_new') ?? (new \App\Services\EmailChangeVerificationService())->getPendingNewEmail($user); @endphp
                        <form method="POST" action="{{ route('seller.dashboard.update-email') }}" class="space-y-3">
                            @csrf
                            <input type="hidden" name="email" value="{{ $pendingNew }}">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Verify email change</label>
                            <p class="text-sm text-gray-600">We sent a verification code to <strong>{{ $user->email }}</strong>. Enter it below to confirm the change to <strong>{{ $pendingNew }}</strong>.</p>
                            <div class="flex flex-wrap items-end gap-3">
                                <div class="flex-1 min-w-[140px] max-w-[200px]">
                                    <input type="text" name="code" value="{{ old('code') }}" placeholder="000000" maxlength="6" pattern="[0-9]*" inputmode="numeric" required
                                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 font-mono text-lg text-center tracking-widest focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    @error('code')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                                </div>
                                <button type="submit" class="px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200 font-medium">Confirm change</button>
                            </div>
                            <p class="text-xs text-gray-500">Code expires in 15 minutes.</p>
                        </form>
                    @else
                        <form method="POST" action="{{ route('seller.dashboard.update-email') }}" class="space-y-3">
                            @csrf
                            <label class="block text-sm font-medium text-gray-700 mb-2">Registered Email Address</label>
                            <div class="flex flex-wrap items-end gap-3">
                                <div class="flex-1 min-w-[200px]">
                                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror">
                                    @error('email')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                                </div>
                                <button type="submit" class="px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200 font-medium">Send verification code</button>
                            </div>
                            <p class="text-sm text-gray-500">A code will be sent to your current email to approve the change.</p>
                        </form>
                    @endif
                </div>

                <!-- Phone Number (editable) -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                    <form method="POST" action="{{ route('seller.dashboard.update-phone') }}" class="flex flex-wrap items-end gap-3">
                        @csrf
                        <div class="flex-1 min-w-[200px]">
                            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="Your phone number"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('phone') border-red-500 @enderror">
                            @error('phone')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <button type="submit" class="px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200 font-medium">
                            Save
                        </button>
                    </form>
                    <p class="text-sm text-gray-500 mt-1">Update your registered phone number</p>
                </div>

                <!-- Account Type -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Account Type</label>
                    <div class="bg-gray-50 border border-gray-300 rounded-lg px-4 py-3">
                        <span class="text-gray-900 font-semibold">
                            {{ $user->business_license_path ? 'Business Seller' : 'Individual Seller' }}
                        </span>
                    </div>
                </div>

                <!-- Password Management -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Password Management</label>
                    <button onclick="showPasswordModal()" 
                            class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-lg font-medium transition duration-200">
                        Change Password
                    </button>
                    <p class="text-sm text-gray-500 mt-2">Password is not displayed.</p>
                </div>

                <!-- Uploaded Documents -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-4">Uploaded Documents</label>
                    @if($documents->count() > 0)
                        <div class="space-y-4">
                            @foreach($documents as $document)
                                <div class="bg-gray-50 border border-gray-300 rounded-lg p-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="font-medium text-gray-900">
                                                @if($document->doc_type === 'business_license')
                                                    Business License
                                                @else
                                                    {{ ucfirst(str_replace('_', ' ', $document->doc_type)) }}
                                                @endif
                                            </p>
                                            @if($user->relationship_to_business && $document->doc_type === 'business_license')
                                                <p class="text-sm text-gray-600 mt-1">
                                                    Relationship: {{ ucfirst(str_replace('_', ' ', $user->relationship_to_business)) }}
                                                </p>
                                            @endif
                                        </div>
                                        @if($document->path)
                                            <a href="{{ asset('storage/' . $document->path) }}" 
                                               target="_blank" 
                                               class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                                View
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <p class="text-sm text-gray-500 mt-4">Documents are view-only.</p>
                    @else
                        <div class="bg-gray-50 border border-gray-300 rounded-lg p-6 text-center">
                            <p class="text-gray-500">No documents uploaded yet.</p>
                        </div>
                    @endif
                </div>

                <!-- Payout Settings -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-4">Payout Settings</label>
                    @if($payoutMethod)
                        <div class="bg-gray-50 border border-gray-300 rounded-lg p-4 mb-4">
                            <div class="space-y-2">
                                <p><span class="font-medium">Bank Name:</span> {{ $payoutMethod->bank_name }}</p>
                                <p><span class="font-medium">Account Holder:</span> {{ $payoutMethod->account_holder_name }}</p>
                                <p><span class="font-medium">Account Number:</span> ****{{ substr($payoutMethod->account_number, -4) }}</p>
                                @if($payoutMethod->routing_number)
                                    <p><span class="font-medium">Routing Number:</span> ****{{ substr($payoutMethod->routing_number, -4) }}</p>
                                @endif
                            </div>
                        </div>
                    @endif
                    <button onclick="showPayoutModal()" 
                            class="bg-blue-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-blue-700 transition duration-200">
                        {{ $payoutMethod ? 'Update Payout Settings' : 'Add Payout Settings' }}
                    </button>
                    <p class="text-sm text-gray-500 mt-2">Payout details must be entered before any earnings can be released.</p>
                </div>
            </div>

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
                        <a href="{{ route('listings.create') }}" 
                           class="inline-block bg-blue-600 text-white px-8 py-4 rounded-lg font-semibold hover:bg-blue-700 transition duration-200 text-lg">
                            SUBMIT NEW LISTING
                        </a>
                        <p class="text-sm text-gray-500 mt-4">Three-step process: Vehicle Information → Photo Upload → Auction Settings & Payment</p>
                    </div>
                @endif
            </div>

            <!-- AUCTIONS TAB -->
            <div id="content-auctions" class="tab-content hidden p-4 sm:p-6 lg:p-7 seller-auctions-scrollbar" data-tour-id="seller-auctions">
                <div class="seller-auctions-wrap">
                <div class="mb-8 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
                    <div class="flex items-start gap-4">
                        <div class="seller-auctions-hero-icon" aria-hidden="true">
                            <span class="material-icons-round text-white text-2xl">gavel</span>
                        </div>
                        <div>
                            <h2 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">My Auctions</h2>
                            <p class="text-slate-600 text-sm mt-1 max-w-xl leading-relaxed">Live listings, completed sales, and outcomes — all in one place.</p>
                        </div>
                    </div>
                    <a href="{{ route('seller.listings.create') }}"
                       class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold text-white shadow-lg shadow-slate-900/20 transition hover:opacity-95 active:scale-[0.99]"
                       style="background: linear-gradient(135deg, #063466 0%, #1e3a8a 100%);">
                        <span class="material-icons-round text-lg">add_circle_outline</span>
                        New listing
                    </a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
                    <div class="seller-auctions-stat" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 55%, #1e40af 100%);">
                        <p class="seller-auctions-stat-label">Live now</p>
                        <p class="seller-auctions-stat-value">{{ $auctionSummary['current_count'] }}</p>
                        <p class="seller-auctions-stat-hint">Active auctions</p>
                    </div>
                    <div class="seller-auctions-stat" style="background: linear-gradient(135deg, #059669 0%, #047857 55%, #0f766e 100%);">
                        <p class="seller-auctions-stat-label">All-time</p>
                        <p class="seller-auctions-stat-value">{{ $auctionSummary['total_items_sold'] }}</p>
                        <p class="seller-auctions-stat-hint">Items sold</p>
                    </div>
                    <div class="seller-auctions-stat" style="background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 55%, #5b21b6 100%);">
                        <p class="seller-auctions-stat-label">Revenue</p>
                        <p class="seller-auctions-stat-value">${{ number_format($auctionSummary['total_sales_revenue'], 0) }}</p>
                        <p class="seller-auctions-stat-hint">Total sales (lifetime)</p>
                    </div>
                </div>

                <div class="mb-6">
                    <nav class="inline-flex flex-wrap gap-1.5 p-1.5 rounded-2xl bg-white/90 border border-slate-200/90 shadow-sm w-full sm:w-auto">
                        <button type="button" onclick="showAuctionSection('current')"
                                id="auction-current"
                                class="auction-tab-button active">
                            <span class="material-icons-round text-lg opacity-90">bolt</span>
                            Current
                        </button>
                        <button type="button" onclick="showAuctionSection('past')"
                                id="auction-past"
                                class="auction-tab-button">
                            <span class="material-icons-round text-lg opacity-90">history</span>
                            Past
                        </button>
                        <button type="button" onclick="showAuctionSection('rejected')"
                                id="auction-rejected"
                                class="auction-tab-button">
                            <span class="material-icons-round text-lg opacity-90">block</span>
                            Rejected
                        </button>
                        <button type="button" onclick="showAuctionSection('won')"
                                id="auction-won"
                                class="auction-tab-button">
                            <span class="material-icons-round text-lg opacity-90">emoji_events</span>
                            Won
                        </button>
                    </nav>
                </div>

                <!-- CURRENT AUCTIONS -->
                <div id="auction-section-current" class="auction-section">
                    @if($currentAuctions->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($currentAuctions as $listing)
                                <div class="seller-auction-card">
                                    <div class="seller-auction-card-media overflow-hidden">
                                        @php
                                            $img = $listing->images->first();
                                            $imgUrl = $img ? (str_contains($img->image_path ?? '', '/') ? asset($img->image_path) : asset('uploads/listings/' . $img->image_path)) : null;
                                        @endphp
                                        @if($imgUrl)
                                            <img src="{{ $imgUrl }}"
                                                 alt="{{ $listing->make }} {{ $listing->model }}"
                                                 class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-gray-400">
                                                <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="p-4">
                                        <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                            {{ $listing->year }} {{ $listing->make }} {{ $listing->model }}
                                        </h3>
                                        <p class="text-sm text-gray-600 mb-2">
                                            <span class="font-medium">ITEM NUMBER:</span> {{ $listing->item_number ?? 'CM' . str_pad($listing->id, 6, '0', STR_PAD_LEFT) }}
                                        </p>

                                        @if($listing->awaiting_pin)
                                            <!-- Awaiting PIN Confirmation -->
                                            <p class="text-lg font-bold text-green-600 mb-3">
                                                Final Sale Price: ${{ number_format($listing->current_bid, 2) }}
                                            </p>
                                            <div class="bg-amber-50 border-l-4 border-amber-400 p-4 rounded mb-3">
                                                <p class="font-semibold text-amber-900 mb-2">Awaiting Pickup Confirmation</p>
                                                <form method="POST" action="{{ route('seller.dashboard.confirm-pickup', $listing->id) }}">
                                                    @csrf
                                                    <div class="mb-3">
                                                        <label class="block text-sm font-medium text-gray-700 mb-2">ENTER PICKUP PIN</label>
                                                        <input type="text" 
                                                               name="pickup_pin" 
                                                               maxlength="4"
                                                               pattern="[0-9]{4}"
                                                               required
                                                               class="w-full border border-gray-300 rounded-lg px-4 py-2 text-center text-2xl font-bold focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                               placeholder="____">
                                                    </div>
                                                    <button type="submit" 
                                                            class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-blue-700 transition duration-200">
                                                        CONFIRM PICKUP
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            <!-- Active Auction -->
                                            <p class="text-lg font-bold text-blue-600 mb-3">
                                                Current Bid: ${{ number_format($listing->current_bid, 2) }}
                                            </p>
                                            @php
                                                $endTime = $listing->auction_end_time ?? ($listing->auction_start_time ? \Carbon\Carbon::parse($listing->auction_start_time)->addDays($listing->auction_duration) : null);
                                            @endphp
                                            @if($endTime && $endTime->isFuture())
                                                <div class="mb-3">
                                                    <p class="text-sm text-gray-600 mb-1">Time Remaining:</p>
                                                    <p class="text-lg font-bold text-red-600" id="countdown-{{ $listing->id }}" 
                                                       data-end-time="{{ $endTime->toIso8601String() }}">
                                                        Calculating...
                                                    </p>
                                                </div>
                                            @endif
                                        @endif

                                        <div class="seller-auction-card-actions">
                                            @if(in_array($listing->status, ['approved', 'active']))
                                                <a href="{{ route('seller.listings.show', $listing->id) }}" class="seller-auction-btn seller-auction-btn--view">
                                                    <span class="material-icons-round" aria-hidden="true">visibility</span> View
                                                </a>
                                                <button type="button" onclick="openDeleteModal({{ $listing->id }}, '{{ addslashes($listing->year . ' ' . $listing->make . ' ' . $listing->model) }}')" class="seller-auction-btn seller-auction-btn--delete">
                                                    <span class="material-icons-round" aria-hidden="true">delete_outline</span> Delete
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="seller-auctions-empty text-center py-14 px-6">
                            <span class="material-icons-round text-5xl text-slate-300 mb-3 block" aria-hidden="true">inventory_2</span>
                            <p class="text-slate-800 text-lg font-semibold">No live auctions</p>
                            <p class="text-slate-500 text-sm mt-2 max-w-md mx-auto">When you have active listings, they will appear here.</p>
                            <a href="{{ route('seller.listings.create') }}" class="inline-flex items-center gap-2 mt-6 px-5 py-2.5 rounded-xl text-sm font-semibold text-white shadow-md hover:opacity-95 transition" style="background: linear-gradient(135deg, #063466 0%, #1e3a8a 100%);">
                                <span class="material-icons-round text-lg">add</span> Create a listing
                            </a>
                        </div>
                    @endif
                </div>

                <!-- PAST AUCTIONS -->
                <div id="auction-section-past" class="auction-section hidden">
                    @if($pastAuctions->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($pastAuctions as $listing)
                                <div class="seller-auction-card">
                                    <div class="seller-auction-card-media overflow-hidden">
                                        @php
                                            $imgP = $listing->images->first();
                                            $imgUrlP = $imgP ? (str_contains($imgP->image_path ?? '', '/') ? asset($imgP->image_path) : asset('uploads/listings/' . $imgP->image_path)) : null;
                                        @endphp
                                        @if($imgUrlP)
                                            <img src="{{ $imgUrlP }}" alt="{{ $listing->make }} {{ $listing->model }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-gray-400">
                                                <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="p-4">
                                        <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                            {{ $listing->year }} {{ $listing->make }} {{ $listing->model }}
                                        </h3>
                                        <p class="text-sm text-gray-600 mb-2">
                                            <span class="font-medium">ITEM NUMBER:</span> {{ $listing->item_number ?? 'CM' . str_pad($listing->id, 6, '0', STR_PAD_LEFT) }}
                                        </p>
                                        <p class="text-lg font-bold text-green-600 mb-2">
                                            Final Sale Price: ${{ number_format($listing->final_price, 2) }}
                                        </p>
                                        <span class="inline-block bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-sm font-semibold">
                                            ENDED
                                        </span>
                                        <a href="{{ route('seller.listings.show', $listing->id) }}" class="mt-3 block w-full text-center px-3 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition">View details</a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="seller-auctions-empty text-center py-14 px-6">
                            <span class="material-icons-round text-5xl text-slate-300 mb-3 block" aria-hidden="true">history</span>
                            <p class="text-slate-800 text-lg font-semibold">No completed sales yet</p>
                            <p class="text-slate-500 text-sm mt-2 max-w-md mx-auto">Completed auctions will appear here after they end.</p>
                        </div>
                    @endif
                </div>

                <!-- REJECTED LISTINGS -->
                <div id="auction-section-rejected" class="auction-section hidden">
                    @if($rejectedListings->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($rejectedListings as $listing)
                                <div class="seller-auction-card border-red-200/80 bg-red-50/10">
                                    <div class="seller-auction-card-media overflow-hidden">
                                        @php
                                            $imgR = $listing->images->first();
                                            $imgUrlR = $imgR ? (str_contains($imgR->image_path ?? '', '/') ? asset($imgR->image_path) : asset('uploads/listings/' . $imgR->image_path)) : null;
                                        @endphp
                                        @if($imgUrlR)
                                            <img src="{{ $imgUrlR }}" alt="{{ $listing->make }} {{ $listing->model }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-gray-400">
                                                <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="p-4">
                                        <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                            {{ $listing->year }} {{ $listing->make }} {{ $listing->model }}
                                        </h3>
                                        <p class="text-sm text-gray-600 mb-2">
                                            <span class="font-medium">ITEM NUMBER:</span> {{ $listing->item_number ?? 'CM' . str_pad($listing->id, 6, '0', STR_PAD_LEFT) }}
                                        </p>
                                        
                                        @if($listing->rejection_reason || $listing->rejection_notes)
                                            <div class="bg-red-50 border border-red-200 rounded-lg p-3 mb-3">
                                                @if($listing->rejection_reason)
                                                    <p class="text-sm text-red-800 mb-1">
                                                        <span class="font-medium">Rejection Reason:</span> {{ $listing->rejection_reason }}
                                                    </p>
                                                @endif
                                                @if($listing->rejection_notes)
                                                    <p class="text-sm text-red-800">
                                                        <span class="font-medium">Rejection Notes:</span> {{ $listing->rejection_notes }}
                                                    </p>
                                                @endif
                                            </div>
                                        @endif

                                        @if($listing->can_edit)
                                            <div class="mb-3">
                                                <p class="text-sm text-amber-600 font-medium mb-1">
                                                    Time Remaining to Edit: <span id="rejection-timer-{{ $listing->id }}" 
                                                                                  data-deadline="{{ $listing->edit_deadline->toIso8601String() }}">
                                                        {{ $listing->time_remaining }}
                                                    </span>
                                                </p>
                                            </div>
                                            <a href="{{ route('seller.listings.edit', $listing->id) }}" 
                                               class="block w-full bg-blue-600 text-white text-center px-4 py-2 rounded-lg font-medium hover:bg-blue-700 transition duration-200">
                                                EDIT LISTING
                                            </a>
                                        @else
                                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-3 mb-3">
                                                <p class="text-sm text-gray-600">Editing is permanently locked. Submit a new listing to relist.</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="seller-auctions-empty text-center py-14 px-6">
                            <span class="material-icons-round text-5xl text-slate-300 mb-3 block" aria-hidden="true">task_alt</span>
                            <p class="text-slate-800 text-lg font-semibold">No rejected listings</p>
                            <p class="text-slate-500 text-sm mt-2 max-w-md mx-auto">If a submission is declined, you will see it here with the reason.</p>
                        </div>
                    @endif
                </div>

                <!-- WON AUCTIONS (ended with a winner) -->
                <div id="auction-section-won" class="auction-section hidden">
                    @if(isset($wonAuctions) && $wonAuctions->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($wonAuctions as $listing)
                                <div class="seller-auction-card border-2 border-emerald-300/90 shadow-md">
                                    <div class="seller-auction-card-media overflow-hidden">
                                        @php
                                            $imgW = $listing->images->first();
                                            $imgUrlW = $imgW ? (str_contains($imgW->image_path ?? '', '/') ? asset($imgW->image_path) : asset('uploads/listings/' . $imgW->image_path)) : null;
                                        @endphp
                                        @if($imgUrlW)
                                            <img src="{{ $imgUrlW }}" alt="{{ $listing->make }} {{ $listing->model }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-gray-400">
                                                <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="p-4">
                                        <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                            {{ $listing->year }} {{ $listing->make }} {{ $listing->model }}
                                        </h3>
                                        <p class="text-sm text-gray-600 mb-2">
                                            <span class="font-medium">ITEM NUMBER:</span> {{ $listing->item_number ?? 'CM' . str_pad($listing->id, 6, '0', STR_PAD_LEFT) }}
                                        </p>
                                        <p class="text-lg font-bold text-emerald-600 mb-2">
                                            Sale Price: ${{ number_format($listing->final_price ?? 0, 2) }}
                                        </p>
                                        <span class="inline-block bg-emerald-100 text-emerald-800 px-3 py-1 rounded-full text-sm font-semibold">
                                            WON
                                        </span>
                                        <a href="{{ route('seller.listings.show', $listing->id) }}" class="mt-3 block w-full text-center px-3 py-2 bg-emerald-50 text-emerald-700 text-sm font-medium rounded-lg hover:bg-emerald-100 transition">View details</a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="seller-auctions-empty text-center py-14 px-6">
                            <span class="material-icons-round text-5xl text-slate-300 mb-3 block" aria-hidden="true">emoji_events</span>
                            <p class="text-slate-800 text-lg font-semibold">No won auctions yet</p>
                            <p class="text-slate-500 text-sm mt-2 max-w-md mx-auto">When an auction ends with a winning bid, it will appear here.</p>
                        </div>
                    @endif
                </div>
                </div>
            </div>

            <!-- NOTIFICATIONS TAB (same UI as buyer: header, filters, grouped by month, cards with mark-read) -->
            <div id="content-notifications" class="tab-content hidden p-6 flex flex-col" style="min-height: 0;">
                <!-- Header -->
                <div class="mb-6 flex-shrink-0">
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
                <div class="mb-4 flex items-center gap-3 flex-shrink-0" x-data="{ currentFilter: 'all' }" x-init="window.notificationFilter = currentFilter">
                    <button @click="currentFilter = 'all'; window.notificationFilter = 'all'; filterNotifications('all')"
                            :class="currentFilter === 'all' ? 'bg-blue-600 text-white shadow-md' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                            class="px-5 py-2.5 rounded-lg font-semibold text-sm transition-all">
                        All
                    </button>
                    <button @click="currentFilter = 'unread'; window.notificationFilter = 'unread'; filterNotifications('unread')"
                            :class="currentFilter === 'unread' ? 'bg-blue-600 text-white shadow-md' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                            class="px-5 py-2.5 rounded-lg font-semibold text-sm transition-all">
                        Unread
                    </button>
                    <button @click="currentFilter = 'read'; window.notificationFilter = 'read'; filterNotifications('read')"
                            :class="currentFilter === 'read' ? 'bg-blue-600 text-white shadow-md' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                            class="px-5 py-2.5 rounded-lg font-semibold text-sm transition-all">
                        Read
                    </button>
                </div>

                @if($notifications->count() > 0)
                    @php
                        $groupedNotifications = $notifications->sortByDesc('created_at')->groupBy(function($notification) {
                            return $notification->created_at->format('F Y');
                        });
                    @endphp
                    <div class="notifications-scroll-wrapper notifications-scrollbar pr-2 border border-gray-200 rounded-xl flex-1 min-h-0" style="max-height: 55vh; overflow-y: scroll; overflow-x: hidden;">
                    <div class="notifications-container space-y-6 py-1">
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
                <h2 class="text-xl font-bold text-gray-900 mb-6">Messaging Center</h2>
                <p class="text-gray-600 mb-4">Post-payment pickup coordination threads with buyers.</p>

                @if($messagingThreads->count() > 0)
                    <div class="space-y-4">
                        @foreach($messagingThreads as $thread)
                            <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition duration-200">
                                <div class="flex items-center space-x-4">
                                    <div class="h-20 w-20 bg-gray-200 rounded-lg overflow-hidden flex-shrink-0">
                                        @if($thread->listing->images->first())
                                            <img src="{{ asset('storage/' . $thread->listing->images->first()->image_path) }}" 
                                                 alt="{{ $thread->listing->make }} {{ $thread->listing->model }}" 
                                                 class="w-full h-full object-cover">
                                        @endif
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="text-lg font-semibold text-gray-900">
                                            {{ $thread->listing->year }} {{ $thread->listing->make }} {{ $thread->listing->model }}
                                        </h3>
                                        <p class="text-sm text-gray-600">Buyer: {{ $thread->buyer->name }}</p>
                                    </div>
                                    <div>
                                        <a href="{{ route('post-auction.thread', $thread->invoice->id) }}" 
                                           class="bg-blue-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-blue-700 transition duration-200">
                                            View Thread
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12 bg-gray-50 rounded-lg">
                        <p class="text-gray-500 text-lg">No messaging threads available. Messaging Center unlocks after payment is completed.</p>
                    </div>
                @endif
            </div>

            <!-- CUSTOMER SUPPORT TAB -->
            <div id="content-support" class="tab-content hidden p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Customer Support</h2>

                <div class="bg-white border border-gray-200 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Submit Support Ticket</h3>
                    <form method="POST" action="{{ route('seller.support.submit') }}">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Ticket Title</label>
                            <select name="title" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select issue type...</option>
                                @foreach(\App\Models\SupportTicket::TITLE_OPTIONS as $option)
                                    <option value="{{ $option }}">{{ $option }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Message</label>
                            <textarea name="message" 
                                      rows="6" 
                                      required
                                      class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                        </div>
                        <button type="submit" 
                                class="bg-blue-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-blue-700 transition duration-200">
                            Submit Ticket
                        </button>
                    </form>
                </div>

                <!-- Ticket History -->
                <div class="mt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Ticket History</h3>
                    @php
                        $sellerTickets = \App\Models\SupportTicket::where('user_id', auth()->id())->latest()->get();
                    @endphp
                    @if($sellerTickets->count() > 0)
                        <div class="space-y-4">
                            @foreach($sellerTickets as $ticket)
                            <div class="border border-gray-200 rounded-lg p-4 {{ $ticket->status === 'open' ? 'bg-blue-50 border-blue-200' : '' }}">
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="font-semibold text-gray-900">{{ $ticket->title }}</h4>
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full
                                        {{ $ticket->status === 'open' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $ticket->status === 'in_progress' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $ticket->status === 'resolved' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $ticket->status === 'closed' ? 'bg-gray-100 text-gray-800' : '' }}">
                                        {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                    </span>
                                </div>
                                <p class="text-gray-600 text-sm mb-2">{{ $ticket->message }}</p>
                                <p class="text-xs text-gray-400">{{ $ticket->created_at->diffForHumans() }}</p>
                                @if($ticket->admin_reply)
                                <div class="mt-3 p-3 bg-gray-50 border border-gray-200 rounded-lg">
                                    <p class="text-xs font-semibold text-gray-500 mb-1">Admin Reply:</p>
                                    <p class="text-sm text-gray-700">{{ $ticket->admin_reply }}</p>
                                </div>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="bg-gray-50 rounded-lg p-6 text-center">
                            <p class="text-gray-500">No tickets submitted yet.</p>
                        </div>
                    @endif
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

<!-- Password Change Modal -->
<div id="passwordModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Change Password</h3>
            <form method="POST" action="{{ route('seller.dashboard.change-password') }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                    <div class="relative">
                        <input type="password" id="modal_current_password" name="current_password" required
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 pr-10 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600" aria-label="Toggle visibility" onclick="togglePasswordModal('modal_current_password', 'modal_current_eye')">
                            <svg id="modal_current_eye" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg id="modal_current_eye_slash" class="h-5 w-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                        </button>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                    <div class="relative">
                        <input type="password" id="modal_new_password" name="password" required minlength="8"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 pr-10 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600" aria-label="Toggle visibility" onclick="togglePasswordModal('modal_new_password', 'modal_new_eye')">
                            <svg id="modal_new_eye" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg id="modal_new_eye_slash" class="h-5 w-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                        </button>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                    <div class="relative">
                        <input type="password" id="modal_confirm_password" name="password_confirmation" required minlength="8"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 pr-10 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600" aria-label="Toggle visibility" onclick="togglePasswordModal('modal_confirm_password', 'modal_confirm_eye')">
                            <svg id="modal_confirm_eye" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg id="modal_confirm_eye_slash" class="h-5 w-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                        </button>
                    </div>
                </div>
                <div class="flex items-center justify-end space-x-3">
                    <button type="button" 
                            onclick="hidePasswordModal()" 
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition duration-200">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
                        Change Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Payout Settings Modal -->
<div id="payoutModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white m-4">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Payout Settings</h3>
            <form method="POST" action="{{ route('seller.dashboard.update-payout') }}">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Bank Name *</label>
                        <input type="text" 
                               name="bank_name" 
                               value="{{ $payoutMethod->bank_name ?? '' }}"
                               required
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Account Holder Name *</label>
                        <input type="text" 
                               name="account_holder_name" 
                               value="{{ $payoutMethod->account_holder_name ?? '' }}"
                               required
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Account Number {{ $payoutMethod ? '' : '*' }}</label>
                        @if($payoutMethod)
                            <p class="text-xs text-gray-500 mb-1">Currently: ****{{ substr($payoutMethod->account_number, -4) }}</p>
                        @endif
                        <input type="text" 
                               name="account_number" 
                               value=""
                               placeholder="{{ $payoutMethod ? 'Leave blank to keep current' : 'Required' }}"
                               {{ !$payoutMethod ? 'required' : '' }}
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Routing / Transfer Number</label>
                        @if($payoutMethod && $payoutMethod->routing_number)
                            <p class="text-xs text-gray-500 mb-1">Currently: ****{{ substr($payoutMethod->routing_number, -4) }}</p>
                        @endif
                        <input type="text" 
                               name="routing_number" 
                               value=""
                               placeholder="{{ $payoutMethod ? 'Leave blank to keep current' : 'Optional' }}"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">SWIFT Number</label>
                        @if($payoutMethod && $payoutMethod->swift_number)
                            <p class="text-xs text-gray-500 mb-1">Currently: ****{{ substr($payoutMethod->swift_number, -4) }}</p>
                        @endif
                        <input type="text" 
                               name="swift_number" 
                               value=""
                               placeholder="{{ $payoutMethod ? 'Leave blank to keep current' : 'Optional' }}"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Country / Payout Region *</label>
                        <input type="text" 
                               name="country" 
                               value="Bahamas"
                               required
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
                <div class="flex items-center justify-end space-x-3">
                    <button type="button" 
                            onclick="hidePayoutModal()" 
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition duration-200">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
                        Save Payout Settings
                    </button>
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
    
    // Initialize charts if switching to dashboard tab
    if (tabName === 'dashboard') {
        setTimeout(initializeCharts, 50);
    }
}

// Show tab on page load (server route sets active tab)
document.addEventListener('DOMContentLoaded', function() {
    const tab = @json($activeTab ?? 'dashboard');

    showTab(tab);

    if (tab === 'auctions') {
        const params = new URLSearchParams(window.location.search);
        const sec = params.get('section');
        if (sec && ['current', 'past', 'rejected', 'won'].indexOf(sec) !== -1) {
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
});

// Auction Section Navigation
function showAuctionSection(section) {
    document.querySelectorAll('.auction-section').forEach(function (el) {
        el.classList.add('hidden');
    });

    document.querySelectorAll('.auction-tab-button').forEach(function (btn) {
        btn.classList.remove('active');
    });

    const panel = document.getElementById('auction-section-' + section);
    if (panel) panel.classList.remove('hidden');

    const activeButton = document.getElementById('auction-' + section);
    if (activeButton) activeButton.classList.add('active');
}

// Password Modal
function showPasswordModal() {
    document.getElementById('passwordModal').classList.remove('hidden');
}

function hidePasswordModal() {
    document.getElementById('passwordModal').classList.add('hidden');
}

function togglePasswordModal(inputId, eyeIconId) {
    var input = document.getElementById(inputId);
    var eye = document.getElementById(eyeIconId);
    var slash = document.getElementById(eyeIconId + '_slash');
    if (!input || !eye || !slash) return;
    if (input.type === 'password') {
        input.type = 'text';
        eye.classList.add('hidden');
        slash.classList.remove('hidden');
    } else {
        input.type = 'password';
        eye.classList.remove('hidden');
        slash.classList.add('hidden');
    }
}

// Payout Modal
function showPayoutModal() {
    document.getElementById('payoutModal').classList.remove('hidden');
}

function hidePayoutModal() {
    document.getElementById('payoutModal').classList.add('hidden');
}

// Close modals when clicking outside
window.onclick = function(event) {
    const passwordModal = document.getElementById('passwordModal');
    const payoutModal = document.getElementById('payoutModal');
    if (event.target == passwordModal) {
        hidePasswordModal();
    }
    if (event.target == payoutModal) {
        hidePayoutModal();
    }
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

@if(session('success'))
    <div class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
        {{ session('success') }}
    </div>
    <script>
        setTimeout(() => {
            const el = document.querySelector('.fixed.top-4');
            if (el) el.remove();
        }, 3000);
    </script>
@endif

@if($errors->any())
    <div class="fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    <script>
        setTimeout(() => {
            const el = document.querySelector('.fixed.top-4');
            if (el) el.remove();
        }, 5000);
    </script>
@endif

@endsection
