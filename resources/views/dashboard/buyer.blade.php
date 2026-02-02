@extends('layouts.dashboard')

@section('title', 'Buyer Dashboard - CayMark')

@section('content')
<style>
.notifications-scrollbar { max-height: 65vh; overflow-y: scroll !important; overflow-x: hidden; }
.notifications-scrollbar::-webkit-scrollbar { width: 12px; }
.notifications-scrollbar::-webkit-scrollbar-track { background: #e2e8f0; border-radius: 6px; }
.notifications-scrollbar::-webkit-scrollbar-thumb { background: #94a3b8; border-radius: 6px; }
.notifications-scrollbar::-webkit-scrollbar-thumb:hover { background: #64748b; }
</style>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<div class="w-full h-full bg-gray-50" style="min-height: calc(100vh - 0px); padding: 0;">
    <div class="w-full h-full px-3 sm:px-4 lg:px-6 py-3">
        @php $summary = $buyerSummary ?? []; $avgPurchase = $averagePurchaseData ?? []; @endphp

        <div class="bg-white rounded-xl shadow-sm h-full" style="min-height: calc(100vh - 60px);">
            <!-- DASHBOARD TAB (Overview with Stats & Charts) -->
            <div id="content-dashboard" class="tab-content p-4" style="display: none; height: 100%; overflow-y: auto;">
                <div class="mb-4">
                    <h2 class="text-3xl font-bold bg-gradient-to-r from-blue-600 via-purple-600 to-blue-600 bg-clip-text text-transparent mb-1">Dashboard Overview</h2>
                    <p class="text-gray-600 text-sm">Real-time insights into your bidding activity and purchase analytics</p>
        </div>

                <!-- Top Stats Cards -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
                    <div class="bg-gradient-to-br from-blue-500 via-blue-600 to-indigo-600 rounded-xl shadow-xl p-4 text-white transform hover:scale-105 transition-all duration-300 relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-white/10 rounded-full -mr-12 -mt-12"></div>
                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-2">
                                <span class="material-icons-round text-3xl opacity-80">payments</span>
                                <span class="text-xs font-semibold bg-white/20 px-2 py-0.5 rounded-full">Spent</span>
                            </div>
                            <p class="text-blue-100 text-xs font-medium mb-1">Total Spent</p>
                            <p class="text-3xl font-bold mb-0.5">${{ number_format($summary['total_spent'] ?? 0, 0) }}</p>
                            <p class="text-xs text-blue-100 opacity-75">All paid purchases</p>
                        </div>
                    </div>
                    <div class="bg-gradient-to-br from-emerald-600 via-green-600 to-teal-700 rounded-xl shadow-xl p-4 text-white transform hover:scale-105 transition-all duration-300 relative overflow-hidden" style="background: linear-gradient(to bottom right, #059669, #16a34a, #0f766e);">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-white/10 rounded-full -mr-12 -mt-12"></div>
                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-2">
                                <span class="material-icons-round text-3xl text-white" style="opacity: 0.95;">check_circle</span>
                                <span class="text-xs font-semibold bg-white/25 px-2 py-0.5 rounded-full text-white">Won</span>
                            </div>
                            <p class="text-xs font-medium mb-1 text-white">Items Won</p>
                            <p class="text-3xl font-bold mb-0.5 text-white">{{ $summary['items_won'] ?? 0 }}</p>
                            <p class="text-xs text-white" style="opacity: 0.95;">{{ $winLossRatioData['winRate'] ?? 0 }}% win rate</p>
                        </div>
                    </div>
                    <div class="bg-gradient-to-br from-purple-500 via-pink-500 to-rose-600 rounded-xl shadow-xl p-4 text-white transform hover:scale-105 transition-all duration-300 relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-white/10 rounded-full -mr-12 -mt-12"></div>
                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-2">
                                <span class="material-icons-round text-3xl opacity-80">gavel</span>
                                <span class="text-xs font-semibold bg-white/20 px-2 py-0.5 rounded-full">Active</span>
                            </div>
                            <p class="text-purple-100 text-xs font-medium mb-1">Active Bids</p>
                            <p class="text-3xl font-bold mb-0.5">{{ $summary['active_bids_count'] ?? 0 }}</p>
                            <p class="text-xs text-purple-100 opacity-75">Currently bidding</p>
                        </div>
                    </div>
                    <div class="bg-gradient-to-br from-amber-600 via-orange-600 to-red-700 rounded-xl shadow-xl p-4 text-white transform hover:scale-105 transition-all duration-300 relative overflow-hidden" style="background: linear-gradient(to bottom right, #d97706, #ea580c, #b91c1c);">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-white/10 rounded-full -mr-12 -mt-12"></div>
                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-2">
                                <span class="material-icons-round text-3xl text-white" style="opacity: 0.95;">bookmark</span>
                                <span class="text-xs font-semibold bg-white/25 px-2 py-0.5 rounded-full text-white">Saved</span>
                            </div>
                            <p class="text-xs font-medium mb-1 text-white">Saved Items</p>
                            <p class="text-3xl font-bold mb-0.5 text-white">{{ $summary['saved_items_count'] ?? 0 }}</p>
                            <p class="text-xs text-white" style="opacity: 0.95;">Watchlist</p>
            </div>
                    </div>
                </div>

                <!-- Secondary Stats -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg shadow-md border border-blue-200 p-4 hover:shadow-lg transition-all duration-300">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-blue-700 text-xs font-medium">Avg. Purchase</span>
                            <span class="material-icons-round text-blue-600 text-lg">trending_up</span>
                        </div>
                        <p class="text-xl font-bold text-blue-900">${{ number_format($avgPurchase['average'] ?? 0, 0) }}</p>
                        <p class="text-xs text-blue-600 mt-0.5">Based on {{ $avgPurchase['count'] ?? 0 }} purchases</p>
                    </div>
                    <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg shadow-md border border-green-200 p-4 hover:shadow-lg transition-all duration-300">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-green-700 text-xs font-medium">Win Rate</span>
                            <span class="material-icons-round text-green-600 text-lg">percent</span>
                        </div>
                        <p class="text-xl font-bold text-green-900">{{ $winLossRatioData['winRate'] ?? 0 }}%</p>
                        <p class="text-xs text-green-600 mt-0.5">{{ $winLossRatioData['won'] ?? 0 }} won / {{ $winLossRatioData['total'] ?? 0 }} total</p>
                    </div>
                    <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg shadow-md border border-purple-200 p-4 hover:shadow-lg transition-all duration-300">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-purple-700 text-xs font-medium">Highest Purchase</span>
                            <span class="material-icons-round text-purple-600 text-lg">arrow_upward</span>
                        </div>
                        <p class="text-xl font-bold text-purple-900">${{ number_format($avgPurchase['highest'] ?? 0, 0) }}</p>
                        <p class="text-xs text-purple-600 mt-0.5">Best deal</p>
                    </div>
                    <div class="bg-gradient-to-br from-amber-50 to-amber-100 rounded-lg shadow-md border border-amber-200 p-4 hover:shadow-lg transition-all duration-300">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-amber-700 text-xs font-medium">Pending Payment</span>
                            <span class="material-icons-round text-amber-600 text-lg">schedule</span>
                        </div>
                        <p class="text-xl font-bold text-amber-900">${{ number_format($summary['pending_payment_amount'] ?? 0, 0) }}</p>
                        <p class="text-xs text-amber-600 mt-0.5">{{ $summary['pending_payment_count'] ?? 0 }} invoice(s)</p>
                    </div>
        </div>

                <!-- Charts Row -->
                <div class="grid grid-cols-1 xl:grid-cols-3 gap-4 mb-4">
                    <div class="xl:col-span-2 bg-gradient-to-br from-white to-gray-50 rounded-xl shadow-lg border border-gray-200 p-4">
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">Spending Trend</h3>
                                <p class="text-xs text-gray-500">Last 6 months</p>
                            </div>
                            <div class="bg-blue-100 rounded-lg p-1.5"><span class="material-icons-round text-blue-600 text-lg">show_chart</span></div>
                        </div>
                        <div class="h-64"><canvas id="spendingTrendsChart"></canvas></div>
                    </div>
                    <div class="bg-gradient-to-br from-white to-gray-50 rounded-xl shadow-lg border border-gray-200 p-4">
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">Win / Loss</h3>
                                <p class="text-xs text-gray-500">Auction results</p>
                            </div>
                            <div class="bg-purple-100 rounded-lg p-1.5"><span class="material-icons-round text-purple-600 text-lg">pie_chart</span></div>
                        </div>
                        <div class="h-64"><canvas id="winLossChart"></canvas></div>
                    </div>
                </div>
                <div class="grid grid-cols-1">
                    <div class="bg-gradient-to-br from-white to-gray-50 rounded-xl shadow-lg border border-gray-200 p-4">
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">Bidding Activity</h3>
                                <p class="text-xs text-gray-500">Last 30 days</p>
                            </div>
                            <div class="bg-green-100 rounded-lg p-1.5"><span class="material-icons-round text-green-600 text-lg">bar_chart</span></div>
                        </div>
                        <div class="h-72"><canvas id="biddingActivityChart"></canvas></div>
                    </div>
                    </div>
                </div>

            <!-- USER TAB -->
            <div id="content-user" class="tab-content hidden p-6">
                <!-- Header -->
                <div class="mb-8">
                    <div class="flex items-center gap-3 mb-1">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-lg shadow-blue-500/20">
                            <span class="material-icons-round text-white text-xl">person</span>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Account Information</h2>
                            <p class="text-sm text-gray-500">Manage your profile and security settings</p>
                        </div>
                    </div>
                </div>

                @if(session('success'))
                    <div class="flex items-center gap-3 rounded-xl bg-emerald-50 border border-emerald-200/80 px-4 py-3 mb-6 text-emerald-800 shadow-sm">
                        <span class="material-icons-round text-emerald-600 text-xl">check_circle</span>
                        <span class="font-medium">{{ session('success') }}</span>
                    </div>
                @endif
                @if($errors->any())
                    <div class="flex items-start gap-3 rounded-xl bg-red-50 border border-red-200/80 px-4 py-3 mb-6 text-red-800 shadow-sm">
                        <span class="material-icons-round text-red-600 text-xl flex-shrink-0">error</span>
                        <ul class="list-disc list-inside text-sm">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    </div>
                @endif

                <!-- Main Card -->
                <div class="bg-white rounded-2xl border border-gray-200/80 shadow-sm overflow-hidden">
                    <div class="p-6 md:p-8 space-y-6">
                        <!-- Profile section -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Full Name -->
                            <div class="group">
                                <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 mb-2">
                                    <span class="material-icons-round text-gray-400 text-lg group-focus-within:text-blue-600 transition-colors">badge</span>
                                    Full Name
                                </label>
                                <div class="flex items-center gap-3 rounded-xl bg-slate-50/80 border border-gray-200 px-4 py-3.5">
                                    <span class="material-icons-round text-gray-400 text-xl">person_outline</span>
                                    <span class="text-gray-900 font-medium">{{ $user->name }}</span>
                                </div>
                                <p class="text-xs text-gray-400 mt-1.5">Display name on your account</p>
                            </div>

                            <!-- Account Type -->
                            <div class="group">
                                <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 mb-2">
                                    <span class="material-icons-round text-gray-400 text-lg">workspace_premium</span>
                                    Account Type
                                </label>
                                <div class="flex items-center gap-3 rounded-xl bg-slate-50/80 border border-gray-200 px-4 py-3.5">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-semibold bg-blue-100 text-blue-800">Buyer</span>
                                </div>
                            </div>
                        </div>

                        <!-- Email (editable) -->
                        <div class="pt-2 border-t border-gray-100">
                            <form method="POST" action="{{ route('buyer.user.update-email') }}" class="group">
                                @csrf
                                <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 mb-2">
                                    <span class="material-icons-round text-gray-400 text-lg group-focus-within:text-blue-600 transition-colors">mail</span>
                                    Email Address
                                </label>
                                <div class="flex flex-col sm:flex-row gap-3">
                                    <div class="flex-1">
                                        <input type="email" name="email" value="{{ $user->email }}" required
                                            class="w-full px-4 py-3.5 rounded-xl border-2 border-gray-200 bg-gray-50/50 text-gray-900 font-medium placeholder-gray-400 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all">
                                    </div>
                                    <button type="submit" class="inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-6 py-3.5 rounded-xl font-semibold shadow-lg shadow-blue-600/20 hover:shadow-blue-600/30 transition-all duration-200">
                                        <span class="material-icons-round text-lg">save</span>
                                        Update Email
                                    </button>
                                </div>
                                <p class="text-xs text-gray-400 mt-1.5">Visible only to you. You can change it anytime.</p>
                            </form>
                        </div>

                        <!-- ID + Password row -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2 border-t border-gray-100">
                            <!-- ID -->
                            <div class="group">
                                <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 mb-2">
                                    <span class="material-icons-round text-gray-400 text-lg">tag</span>
                                    Account ID
                                </label>
                                <div class="flex items-center gap-3 rounded-xl bg-slate-50/80 border border-gray-200 px-4 py-3.5">
                                    <span class="text-gray-500 font-mono text-sm">#</span>
                                    <span class="text-gray-900 font-semibold">{{ $user->id }}</span>
                                </div>
                                <p class="text-xs text-gray-400 mt-1.5">Your unique account identifier</p>
                            </div>

                            <!-- Password -->
                            <div class="group">
                                <label class="flex items-center gap-2 text-sm font-semibold text-gray-600 mb-2">
                                    <span class="material-icons-round text-gray-400 text-lg">lock</span>
                                    Password
                                </label>
                                <div class="rounded-xl border-2 border-dashed border-gray-200 bg-gray-50/50 p-4">
                                    <p class="text-sm text-gray-500 mb-3">Your password is encrypted and never displayed.</p>
                                    <button type="button" onclick="showPasswordModal()"
                                        class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl border-2 border-gray-300 bg-white text-gray-700 font-semibold hover:border-blue-500 hover:text-blue-600 hover:bg-blue-50/50 transition-all duration-200">
                                        <span class="material-icons-round text-lg">key</span>
                        Change Password
                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

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
                        <div class="hidden md:flex items-center gap-4">
                            <div class="text-right">
                                <p class="text-xs text-gray-500 mb-0.5">Current</p>
                                <p class="text-lg font-bold text-blue-600">{{ $currentAuctions->count() }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-gray-500 mb-0.5">Won</p>
                                <p class="text-lg font-bold text-emerald-600">{{ $wonAuctions->count() }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-gray-500 mb-0.5">Lost</p>
                                <p class="text-lg font-bold text-gray-600">{{ $lostAuctions->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab Navigation -->
                <div class="mb-6">
                    <nav class="flex gap-2 bg-gray-100 p-1 rounded-xl border border-gray-200">
                        <button onclick="showAuctionSection('current')" id="auction-current" class="auction-tab-button active flex-1 px-4 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-indigo-600 rounded-lg shadow-sm transition-all duration-200">
                            <span class="material-icons-round text-sm mr-1.5 align-middle">schedule</span>
                            CURRENT
                        </button>
                        <button onclick="showAuctionSection('won')" id="auction-won" class="auction-tab-button flex-1 px-4 py-2.5 text-sm font-semibold text-gray-600 hover:text-gray-900 rounded-lg transition-all duration-200">
                            <span class="material-icons-round text-sm mr-1.5 align-middle">check_circle</span>
                            WON
                        </button>
                        <button onclick="showAuctionSection('lost')" id="auction-lost" class="auction-tab-button flex-1 px-4 py-2.5 text-sm font-semibold text-gray-600 hover:text-gray-900 rounded-lg transition-all duration-200">
                            <span class="material-icons-round text-sm mr-1.5 align-middle">cancel</span>
                            LOST
                        </button>
                    </nav>
                </div>

                <div id="auction-section-current" class="auction-section">
                    @if($currentAuctions->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($currentAuctions as $listing)
                                @php $pendingInvoice = $listing->pending_invoice ?? $listing->getPendingInvoiceForUser($user->id); @endphp
                                <div class="group bg-white rounded-2xl border-2 border-gray-200 overflow-hidden shadow-sm hover:shadow-xl hover:border-blue-300 transition-all duration-300">
                                    <div class="relative h-52 bg-gradient-to-br from-gray-100 to-gray-200 overflow-hidden">
                                        @if($listing->images->first())
                                            <img src="{{ str_contains($listing->images->first()->image_path, '/') ? asset($listing->images->first()->image_path) : asset('uploads/listings/' . $listing->images->first()->image_path) }}" alt="{{ $listing->make }} {{ $listing->model }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-gray-400">
                                                <span class="material-icons-round text-6xl">directions_car</span>
                                            </div>
                                        @endif
                                        <div class="absolute top-3 right-3 bg-white/90 backdrop-blur-sm px-2.5 py-1 rounded-lg shadow-sm">
                                            <span class="text-xs font-bold text-blue-600">LIVE</span>
                                        </div>
                                    </div>
                                    <div class="p-5">
                                        <h3 class="text-lg font-bold text-gray-900 mb-1.5 line-clamp-1">{{ $listing->year }} {{ $listing->make }} {{ $listing->model }}</h3>
                                        <p class="text-xs text-gray-500 mb-3 font-mono">ITEM #{{ $listing->item_number ?? 'CM' . str_pad($listing->id, 6, '0', STR_PAD_LEFT) }}</p>
                                        <div class="flex items-baseline gap-2 mb-4">
                                            <span class="text-xs text-gray-500 font-medium">Current Bid</span>
                                            <span class="text-2xl font-bold text-blue-600">${{ number_format($listing->highest_bid ?? $listing->starting_price ?? 0, 0) }}</span>
                                        </div>
                                        @if($pendingInvoice)
                                            <div class="bg-gradient-to-r from-amber-50 to-orange-50 border-2 border-amber-300 rounded-xl p-4 mb-4">
                                                <div class="flex items-center gap-2 mb-2">
                                                    <span class="material-icons-round text-amber-600 text-lg">warning</span>
                                                    <p class="font-bold text-amber-900 text-sm">PAYMENT REQUIRED</p>
                                                </div>
                                                <a href="{{ route('buyer.payment.checkout-single', ['invoiceId' => $pendingInvoice->id]) }}" class="block w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-center px-4 py-2.5 rounded-lg font-semibold hover:shadow-lg transition-all duration-200">
                                                    Complete Payment
                                                </a>
                                            </div>
                                        @else
                                            @php $endTime = $listing->auction_end_time ?? ($listing->auction_start_time ? \Carbon\Carbon::parse($listing->auction_start_time)->addDays($listing->auction_duration ?? 7) : null); @endphp
                                            @if($endTime && $endTime->isFuture())
                                                <div class="bg-red-50 border border-red-200 rounded-xl p-3 mb-4">
                                                    <p class="text-xs text-red-600 font-medium mb-1">Time Remaining</p>
                                                    <p class="text-lg font-bold text-red-600" id="countdown-{{ $listing->id }}" data-end-time="{{ $endTime->toIso8601String() }}">—</p>
                                                </div>
                                            @endif
                                            <a href="{{ route('auction.show', $listing->getSlugOrGenerate()) }}" class="block w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-center px-4 py-2.5 rounded-lg font-semibold hover:shadow-lg transition-all duration-200">
                                                Place Bid
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="bg-white rounded-2xl border-2 border-dashed border-gray-200 p-12 text-center">
                            <div class="w-20 h-20 mx-auto mb-4 rounded-2xl bg-gradient-to-br from-blue-100 to-indigo-100 flex items-center justify-center">
                                <span class="material-icons-round text-blue-600 text-4xl">gavel</span>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 mb-2">No Active Bids</h3>
                            <p class="text-gray-500 text-sm max-w-sm mx-auto mb-4">You don't have any active bids at the moment. Start bidding on auctions to see them here.</p>
                            <a href="{{ route('buyer.auctions') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white rounded-xl font-semibold hover:bg-blue-700 transition-all">
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
                                    $statusText = ($listing->payment_status ?? null) === 'paid' ? 'Purchase Complete' : 'Payment Pending';
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
                                        <p class="text-xs text-gray-500 mb-3 font-mono">ITEM #{{ $listing->item_number ?? '—' }}</p>
                                        <div class="flex items-baseline gap-2 mb-4">
                                            <span class="text-xs text-gray-500 font-medium">Final Price</span>
                                            <span class="text-2xl font-bold text-emerald-600">${{ number_format($listing->final_price ?? 0, 0) }}</span>
                                        </div>
                                        <div class="flex items-center gap-2 text-xs text-emerald-600 font-semibold">
                                            <span class="material-icons-round text-sm">check_circle</span>
                                            <span>{{ $statusText }}</span>
                                        </div>
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

                <div id="auction-section-lost" class="auction-section hidden">
                    @if($lostAuctions->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($lostAuctions as $listing)
                                <div class="group bg-white rounded-2xl border-2 border-gray-200 overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 opacity-75">
                                    <div class="relative h-52 bg-gradient-to-br from-gray-100 to-gray-200 overflow-hidden">
                                        @if($listing->images->first())
                                            <img src="{{ str_contains($listing->images->first()->image_path, '/') ? asset($listing->images->first()->image_path) : asset('uploads/listings/' . $listing->images->first()->image_path) }}" alt="{{ $listing->make }} {{ $listing->model }}" class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-300">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-gray-400">
                                                <span class="material-icons-round text-6xl">directions_car</span>
                                            </div>
                                        @endif
                                        <div class="absolute top-3 right-3 bg-gray-600 px-3 py-1 rounded-lg shadow-sm">
                                            <span class="text-xs font-bold text-white">ENDED</span>
                                        </div>
                                    </div>
                                    <div class="p-5">
                                        <h3 class="text-lg font-bold text-gray-900 mb-1.5 line-clamp-1">{{ $listing->year }} {{ $listing->make }} {{ $listing->model }}</h3>
                                        <p class="text-xs text-gray-500 mb-3 font-mono">ITEM #{{ $listing->item_number ?? 'CM' . str_pad($listing->id, 6, '0', STR_PAD_LEFT) }}</p>
                                        <div class="flex items-baseline gap-2 mb-4">
                                            <span class="text-xs text-gray-500 font-medium">Winning Bid</span>
                                            <span class="text-2xl font-bold text-gray-600">${{ number_format($listing->getHighestBidAmount() ?? $listing->starting_price ?? 0, 0) }}</span>
                                        </div>
                                        <div class="flex items-center gap-2 text-xs text-red-600 font-semibold">
                                            <span class="material-icons-round text-sm">cancel</span>
                                            <span>Outbid</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="bg-white rounded-2xl border-2 border-dashed border-gray-200 p-12 text-center">
                            <div class="w-20 h-20 mx-auto mb-4 rounded-2xl bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                                <span class="material-icons-round text-gray-400 text-4xl">trending_up</span>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 mb-2">No Lost Auctions</h3>
                            <p class="text-gray-500 text-sm max-w-sm mx-auto">Great! You haven't lost any auctions yet. Keep bidding to increase your chances of winning.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- SAVED ITEMS TAB -->
            <div id="content-saved" class="tab-content hidden p-6">
                <!-- Header -->
                <div class="mb-8">
                    <div class="flex items-center justify-between mb-1">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center shadow-lg shadow-amber-500/20">
                                <span class="material-icons-round text-white text-xl">bookmark</span>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Saved Items</h2>
                                <p class="text-sm text-gray-500">Your watchlist of favorite auctions</p>
                            </div>
                        </div>
                        @if($savedItems->count() > 0)
                            <div class="hidden md:flex items-center gap-2 px-4 py-2 rounded-xl bg-amber-50 border border-amber-200">
                                <span class="material-icons-round text-amber-600 text-lg">bookmark</span>
                                <span class="text-sm font-semibold text-amber-700">{{ $savedItems->count() }} saved</span>
                            </div>
                        @endif
                    </div>
                </div>

                @if($savedItems->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($savedItems as $listing)
                            @php 
                                $endTime = $listing->auction_end_time ?? ($listing->auction_start_time ? \Carbon\Carbon::parse($listing->auction_start_time)->addDays($listing->auction_duration ?? 7) : null);
                                $isActive = $listing->status === 'active' && $endTime && $endTime->isFuture();
                            @endphp
                            <div class="group bg-white rounded-2xl border-2 border-amber-200 overflow-hidden shadow-sm hover:shadow-xl hover:border-amber-300 transition-all duration-300">
                                <div class="relative h-52 bg-gradient-to-br from-amber-50 to-orange-100 overflow-hidden">
                                    @if($listing->images->first())
                                        <img src="{{ str_contains($listing->images->first()->image_path, '/') ? asset($listing->images->first()->image_path) : asset('uploads/listings/' . $listing->images->first()->image_path) }}" alt="{{ $listing->make }} {{ $listing->model }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-400">
                                            <span class="material-icons-round text-6xl">directions_car</span>
                                        </div>
                                    @endif
                                    <div class="absolute top-3 right-3 bg-amber-500 px-3 py-1 rounded-lg shadow-sm">
                                        <span class="text-xs font-bold text-white">SAVED</span>
                                    </div>
                                    @if($isActive)
                                        <div class="absolute top-3 left-3 bg-blue-600 px-2.5 py-1 rounded-lg shadow-sm">
                                            <span class="text-xs font-bold text-white">LIVE</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="p-5">
                                    <h3 class="text-lg font-bold text-gray-900 mb-1.5 line-clamp-1">{{ $listing->year }} {{ $listing->make }} {{ $listing->model }}</h3>
                                    <p class="text-xs text-gray-500 mb-3 font-mono">ITEM #{{ $listing->item_number ?? 'CM' . str_pad($listing->id, 6, '0', STR_PAD_LEFT) }}</p>
                                    <div class="flex items-baseline gap-2 mb-4">
                                        <span class="text-xs text-gray-500 font-medium">Current Bid</span>
                                        <span class="text-2xl font-bold text-blue-600">${{ number_format($listing->highest_bid ?? $listing->starting_price ?? 0, 0) }}</span>
                                    </div>
                                    @if($isActive && $endTime)
                                        <div class="bg-red-50 border border-red-200 rounded-xl p-3 mb-4">
                                            <p class="text-xs text-red-600 font-medium mb-1">Time Remaining</p>
                                            <p class="text-sm font-bold text-red-600" id="countdown-saved-{{ $listing->id }}" data-end-time="{{ $endTime->toIso8601String() }}">—</p>
                                        </div>
                                    @endif
                                    <div class="flex flex-col gap-2">
                                        <a href="{{ route('auction.show', $listing->getSlugOrGenerate()) }}" class="block w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-center px-4 py-2.5 rounded-lg font-semibold hover:shadow-lg transition-all duration-200">
                                            Place Bid
                                        </a>
                                        <form method="POST" action="{{ route('listing.watchlist', $listing) }}">
                                            @csrf
                                            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 border-2 border-gray-300 bg-white text-gray-700 px-4 py-2.5 rounded-lg font-semibold hover:border-red-400 hover:text-red-600 hover:bg-red-50 transition-all duration-200">
                                                <span class="material-icons-round text-lg">bookmark_remove</span>
                                                Remove from Saved
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="bg-white rounded-2xl border-2 border-dashed border-gray-200 p-12 text-center">
                        <div class="w-20 h-20 mx-auto mb-4 rounded-2xl bg-gradient-to-br from-amber-100 to-orange-100 flex items-center justify-center">
                            <span class="material-icons-round text-amber-600 text-4xl">bookmark_border</span>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">No Saved Items Yet</h3>
                        <p class="text-gray-500 text-sm max-w-sm mx-auto mb-4">Save auctions you're interested in to track them easily. Click the bookmark icon on any auction to add it here.</p>
                        <a href="{{ route('buyer.auctions') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white rounded-xl font-semibold hover:bg-blue-700 transition-all">
                            <span class="material-icons-round text-lg">search</span>
                            Browse Auctions
                        </a>
                    </div>
                @endif
            </div>

            <!-- NOTIFICATIONS TAB -->
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
                        // Group notifications by month and sort by date (newest first)
                        $groupedNotifications = $notifications->sortByDesc('created_at')->groupBy(function($notification) {
                            return $notification->created_at->format('F Y');
                        });
                    @endphp
                    
                    <div class="notifications-scroll-wrapper notifications-scrollbar pr-2 border border-gray-200 rounded-xl flex-1 min-h-0" style="max-height: 55vh; overflow-y: scroll; overflow-x: hidden;">
                    <div class="notifications-container space-y-6 py-1">
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
                                
                                // Icon mapping based on type
                                $iconMap = [
                                    'bid' => 'gavel',
                                    'outbid' => 'trending_down',
                                    'win' => 'celebration',
                                    'payment' => 'payment',
                                    'auction' => 'schedule',
                                    'default' => 'notifications'
                                ];
                                $icon = $iconMap[$type] ?? $iconMap['default'];
                                
                                // Color mapping
                                $colorMap = [
                                    'bid' => ['bg' => 'bg-blue-50', 'border' => 'border-blue-200', 'icon' => 'text-blue-600', 'dot' => 'bg-blue-600'],
                                    'outbid' => ['bg' => 'bg-amber-50', 'border' => 'border-amber-200', 'icon' => 'text-amber-600', 'dot' => 'bg-amber-600'],
                                    'win' => ['bg' => 'bg-emerald-50', 'border' => 'border-emerald-200', 'icon' => 'text-emerald-600', 'dot' => 'bg-emerald-600'],
                                    'payment' => ['bg' => 'bg-purple-50', 'border' => 'border-purple-200', 'icon' => 'text-purple-600', 'dot' => 'bg-purple-600'],
                                    'default' => ['bg' => 'bg-gray-50', 'border' => 'border-gray-200', 'icon' => 'text-gray-600', 'dot' => 'bg-gray-600']
                                ];
                                $colors = $colorMap[$type] ?? $colorMap['default'];
                            @endphp
                            <div class="notification-card group relative bg-white rounded-xl border-2 {{ $isUnread ? $colors['border'] . ' ' . $colors['bg'] : 'border-gray-200' }} p-4 hover:shadow-lg transition-all duration-200 {{ $isUnread ? 'shadow-sm cursor-pointer' : '' }}" 
                                 data-notification-id="{{ $notification->id }}"
                                 data-is-unread="{{ $isUnread ? 'true' : 'false' }}"
                                 data-read-status="{{ $isUnread ? 'unread' : 'read' }}"
                                 @if($isUnread) onclick="markNotificationAsRead('{{ $notification->id }}', this)" @endif>
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
                                                <div class="flex items-center gap-2 mt-2">
                                                    <span class="text-xs text-gray-500 font-medium">{{ $notification->created_at->format('M d, Y') }}</span>
                                                    <span class="text-gray-300">•</span>
                                                    <span class="text-xs text-gray-500">{{ $notification->created_at->diffForHumans() }}</span>
                                                </div>
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
                <h2 class="text-xl font-bold text-gray-900 mb-6">Messaging Center</h2>
                <p class="text-gray-600 mb-4">Post-payment pickup coordination threads with sellers.</p>
                @if($messagingThreads->count() > 0)
                    <div class="space-y-4">
                        @foreach($messagingThreads as $thread)
                            <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition duration-200">
                                <div class="flex items-center space-x-4">
                                    <div class="h-20 w-20 bg-gray-200 rounded-lg overflow-hidden flex-shrink-0">
                                        @if($thread->listing && $thread->listing->images->first())
                                            <img src="{{ str_contains($thread->listing->images->first()->image_path, '/') ? asset($thread->listing->images->first()->image_path) : asset('uploads/listings/' . $thread->listing->images->first()->image_path) }}" alt="" class="w-full h-full object-cover">
                                        @endif
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="text-lg font-semibold text-gray-900">{{ $thread->listing->year ?? '' }} {{ $thread->listing->make ?? '' }} {{ $thread->listing->model ?? 'Item' }}</h3>
                                        <p class="text-sm text-gray-600">Seller: {{ $thread->seller->name ?? '—' }}</p>
                                    </div>
                                    @if($thread->invoice)
                                        <a href="{{ route('post-auction.thread', $thread->invoice->id) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-blue-700">View Thread</a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12 bg-gray-50 rounded-lg"><p class="text-gray-500 text-lg">No messaging threads. Messaging unlocks after payment.</p></div>
                @endif
            </div>

            <!-- CUSTOMER SUPPORT TAB -->
            <div id="content-support" class="tab-content hidden p-6">
                <!-- Header -->
                <div class="mb-8">
                    <div class="flex items-center gap-3 mb-1">
                        <div class="w-10 h-10 rounded-xl bg-teal-600 flex items-center justify-center shadow-lg" style="background-color: #0d9488;">
                            <span class="material-icons-round text-white text-xl">support_agent</span>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center gap-3">
                                <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Customer Support</h2>
                                <div class="hidden md:flex items-center gap-2 px-3 py-1 rounded-lg bg-teal-50 border border-teal-200">
                                    <span class="text-xs font-semibold text-teal-700">CayMark</span>
                                </div>
                            </div>
                            <p class="text-sm text-gray-500">Get help with your account and auctions</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Support Form -->
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-2xl border-2 border-gray-200 shadow-sm overflow-hidden">
                            <div class="bg-teal-50 border-b-2 border-teal-200 px-6 py-4" style="background-color: #f0fdfa;">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-teal-600 flex items-center justify-center" style="background-color: #0d9488;">
                                        <span class="material-icons-round text-white text-lg">help_outline</span>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-900">Submit Support Ticket</h3>
                                        <p class="text-xs text-gray-600">We'll respond within 24 hours</p>
                                    </div>
                                </div>
                            </div>
                            <form method="POST" action="{{ route('buyer.customer-support.submit') }}" class="p-6 space-y-5">
                                @csrf
                                <div class="group">
                                    <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                                        <span class="material-icons-round text-gray-400 text-lg group-focus-within:text-teal-600 transition-colors">title</span>
                                        Ticket Title
                                    </label>
                                    <input type="text" name="title" required placeholder="Brief description of your issue"
                                        class="w-full px-4 py-3.5 rounded-xl border-2 border-gray-200 bg-gray-50/50 text-gray-900 font-medium placeholder-gray-400 focus:bg-white focus:border-teal-500 focus:ring-4 focus:ring-teal-500/10 transition-all">
                                    <p class="text-xs text-gray-400 mt-1.5">Be specific to help us assist you faster</p>
                                </div>
                                <div class="group">
                                    <label class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-2">
                                        <span class="material-icons-round text-gray-400 text-lg group-focus-within:text-teal-600 transition-colors">description</span>
                                        Message
                                    </label>
                                    <textarea name="message" rows="8" required placeholder="Describe your issue in detail..."
                                        class="w-full px-4 py-3.5 rounded-xl border-2 border-gray-200 bg-gray-50/50 text-gray-900 font-medium placeholder-gray-400 focus:bg-white focus:border-teal-500 focus:ring-4 focus:ring-teal-500/10 transition-all resize-none"></textarea>
                                    <p class="text-xs text-gray-400 mt-1.5">Include any relevant details, auction numbers, or error messages</p>
                                </div>
                                <div class="pt-2">
                                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 bg-teal-600 hover:bg-teal-700 text-white px-6 py-3.5 rounded-xl font-semibold shadow-lg shadow-teal-600/30 hover:shadow-teal-600/40 transition-all duration-200" style="background-color: #0d9488; color: #ffffff;">
                                        <span class="material-icons-round text-lg text-white">send</span>
                                        <span class="text-white font-semibold">Submit Ticket</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Help Sidebar -->
                    <div class="space-y-6">
                        <!-- Quick Help -->
                        <div class="bg-white rounded-2xl border-2 border-gray-200 shadow-sm p-6">
                            <div class="flex items-center gap-2 mb-4">
                                <span class="material-icons-round text-xl" style="color: #0d9488;">lightbulb</span>
                                <h4 class="font-bold text-gray-900">Quick Help</h4>
                            </div>
                            <div class="space-y-3 text-sm">
                                <div class="flex items-start gap-3">
                                    <span class="material-icons-round text-lg flex-shrink-0" style="color: #14b8a6;">check_circle</span>
                                    <div>
                                        <p class="font-semibold text-gray-900">Payment Issues</p>
                                        <p class="text-gray-600 text-xs">Include invoice number and payment method</p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3">
                                    <span class="material-icons-round text-lg flex-shrink-0" style="color: #14b8a6;">check_circle</span>
                                    <div>
                                        <p class="font-semibold text-gray-900">Auction Questions</p>
                                        <p class="text-gray-600 text-xs">Mention item number and auction details</p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3">
                                    <span class="material-icons-round text-lg flex-shrink-0" style="color: #14b8a6;">check_circle</span>
                                    <div>
                                        <p class="font-semibold text-gray-900">Account Help</p>
                                        <p class="text-gray-600 text-xs">Describe the issue you're experiencing</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Info -->
                        <div class="bg-teal-50 rounded-2xl border-2 border-teal-200 p-6" style="background-color: #f0fdfa;">
                            <div class="flex items-center gap-2 mb-4">
                                <span class="material-icons-round text-teal-600 text-xl" style="color: #0d9488;">contact_support</span>
                                <h4 class="font-bold text-gray-900">Need Immediate Help?</h4>
                            </div>
                            <p class="text-sm text-gray-700 mb-4">For urgent matters, our support team is available 24/7.</p>
                            <div class="space-y-2 text-sm">
                                <div class="flex items-center gap-2 text-gray-700">
                                    <span class="material-icons-round text-lg" style="color: #0d9488;">schedule</span>
                                    <span class="font-medium text-gray-700">Response Time: 24 hours</span>
                                </div>
                                <div class="flex items-center gap-2 text-gray-700">
                                    <span class="material-icons-round text-lg" style="color: #0d9488;">email</span>
                                    <span class="font-medium text-gray-700">support@caymark.com</span>
                                </div>
                            </div>
                        </div>

                        <!-- FAQ Link -->
                        <div class="bg-white rounded-2xl border-2 border-dashed border-gray-200 p-6 text-center">
                            <span class="material-icons-round text-gray-400 text-4xl mb-3 block">quiz</span>
                            <p class="text-sm font-semibold text-gray-900 mb-1">Check Our FAQ</p>
                            <p class="text-xs text-gray-500 mb-3">Find answers to common questions</p>
                            <a href="#" class="inline-flex items-center gap-2 text-sm font-semibold hover:underline" style="color: #0d9488;">
                                <span style="color: #0d9488;">View FAQ</span>
                                <span class="material-icons-round text-sm" style="color: #0d9488;">arrow_forward</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </div>

<!-- Password Modal -->
<div id="passwordModal" class="hidden fixed inset-0 bg-black/40 backdrop-blur-sm overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4" onclick="if(event.target===this)hidePasswordModal()">
    <div class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl border border-gray-200/80 overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                    <span class="material-icons-round text-white text-xl">key</span>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-white">Change Password</h3>
                    <p class="text-blue-100 text-sm">Create a strong, unique password</p>
                </div>
            </div>
        </div>
        <form method="POST" action="{{ route('buyer.user.change-password') }}" class="p-6 space-y-4">@csrf
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Current Password</label>
                <input type="password" name="current_password" required placeholder="Enter current password"
                    class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 bg-gray-50/50 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">New Password</label>
                <input type="password" name="password" required minlength="8" placeholder="Min. 8 characters"
                    class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 bg-gray-50/50 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Confirm New Password</label>
                <input type="password" name="password_confirmation" required minlength="8" placeholder="Re-enter new password"
                    class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 bg-gray-50/50 focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all">
                </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="hidePasswordModal()"
                    class="flex-1 px-4 py-3 rounded-xl border-2 border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 transition-all">Cancel</button>
                    <button type="submit" 
                    class="flex-1 px-4 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold shadow-lg shadow-blue-600/25 transition-all">Update Password</button>
                </div>
            </form>
    </div>
</div>

        <script>
function showTab(tabName) {
    document.querySelectorAll('.tab-content').forEach(c => { c.style.display = 'none'; });
    var el = document.getElementById('content-' + tabName);
    if (el) el.style.display = 'block';
    var url = new URL(window.location); url.searchParams.set('tab', tabName); window.history.pushState({}, '', url);
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
function showPasswordModal() { document.getElementById('passwordModal').classList.remove('hidden'); }
function hidePasswordModal() { document.getElementById('passwordModal').classList.add('hidden'); }
window.onclick = function(e) { if (e.target === document.getElementById('passwordModal')) hidePasswordModal(); };

document.addEventListener('DOMContentLoaded', function() {
    var tab = new URLSearchParams(window.location.search).get('tab') || 'dashboard';
        showTab(tab);
    if (tab === 'auctions') showAuctionSection(new URLSearchParams(window.location.search).get('section') || 'current');
    setInterval(updateCountdowns, 1000);
    updateCountdowns();
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
