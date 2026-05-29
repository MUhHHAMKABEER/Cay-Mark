<?php

namespace App\Services\Seller;

use App\Models\Bid;
use App\Models\Invoice;
use App\Models\Listing;
use App\Models\PostAuctionThread;
use App\Models\SellerPayoutMethod;
use App\Models\User;
use App\Models\UserDocument;
use Carbon\Carbon;
use App\Services\UserActivityTimelineService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SellerDashboardService
{
    public function __construct(
        protected UserActivityTimelineService $activityTimelineService
    ) {
    }
    /**
     * Get current auctions (active + awaiting PIN confirmation)
     */
    public function getCurrentAuctions(User $user): Collection
    {
        return $user->getCurrentAuctions();
    }

    /**
     * Get completed-tab cards (every sold listing with stage-specific status).
     */
    public function getPastAuctions(User $user): Collection
    {
        return $user->getCompletedAuctions();
    }

    /**
     * Get rejected listings with 72-hour edit window
     */
    public function getRejectedListings(User $user): Collection
    {
        return $user->getRejectedListings();
    }

    /**
     * Get won auctions for seller (ended with a winner – status sold)
     */
    public function getWonAuctions(User $user): Collection
    {
        return $user->listings()
            ->where('status', 'sold')
            ->with(['images', 'invoices' => function ($q) {
                $q->where('payment_status', 'paid');
            }])
            ->latest('updated_at')
            ->get()
            ->map(function ($listing) {
                $listing->final_price = $listing->getFinalPrice();
                return $listing;
            });
    }

    /**
     * Get auction summary statistics
     */
    public function getAuctionSummary(User $user): array
    {
        return $user->getAuctionSummary();
    }

    /**
     * Get notifications for seller
     */
    public function getNotifications(User $user): Collection
    {
        return $user->getNotifications(20);
    }

    /**
     * Get messaging threads for seller
     */
    public function getMessagingThreads(User $user): Collection
    {
        return PostAuctionThread::with(['buyer', 'listing.images'])
            ->where('seller_id', $user->id)
            ->latest('updated_at')
            ->take(10)
            ->get();
    }

    /**
     * Get payout method for seller
     */
    public function getPayoutMethod(User $user): ?SellerPayoutMethod
    {
        return $user->getActivePayoutMethod();
    }

    /**
     * Get documents for seller
     */
    public function getDocuments(User $user): Collection
    {
        return UserDocument::where('user_id', $user->id)->get();
    }

    /**
     * Get revenue chart data (last 6 months)
     */
    public function getRevenueChartData(User $user): array
    {
        $months = [];
        $revenues = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthStart = $date->copy()->startOfMonth();
            $monthEnd = $date->copy()->endOfMonth();
            
            $revenue = Invoice::where('seller_id', $user->id)
                ->where('payment_status', 'paid')
                ->whereBetween('paid_at', [$monthStart, $monthEnd])
                ->sum('winning_bid_amount');
            
            $months[] = $date->format('M Y');
            $revenues[] = round($revenue, 2);
        }
        
        return [
            'labels' => $months,
            'data' => $revenues,
        ];
    }

    /**
     * Get listing status chart data
     */
    public function getListingStatusChartData(User $user): array
    {
        $active = $user->listings()->whereIn('status', ['active', 'pending'])->count();
        $sold = $user->listings()->where('status', 'sold')->count();
        $rejected = $user->listings()->where('status', 'rejected')->count();
        $ended = $user->listings()->where('status', 'ended_no_sale')->count();
        
        return [
            'labels' => ['Active', 'Sold', 'Rejected', 'Ended No Sale'],
            'data' => [$active, $sold, $rejected, $ended],
            'colors' => ['#3B82F6', '#10B981', '#EF4444', '#F59E0B'],
        ];
    }

    /**
     * Get auction performance data (last 30 days)
     */
    public function getAuctionPerformanceData(User $user): array
    {
        $days = [];
        $listings = [];
        $bids = [];
        
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dayStart = $date->copy()->startOfDay();
            $dayEnd = $date->copy()->endOfDay();
            
            // Count listings created on this day
            $dayListings = Listing::where('seller_id', $user->id)
                ->whereBetween('created_at', [$dayStart, $dayEnd])
                ->count();
            
            // Count bids on seller's listings on this day
            $dayBids = \App\Models\Bid::whereHas('listing', function($q) use ($user) {
                    $q->where('seller_id', $user->id);
                })
                ->whereBetween('created_at', [$dayStart, $dayEnd])
                ->count();
            
            $days[] = $date->format('M d');
            $listings[] = $dayListings;
            $bids[] = $dayBids;
        }
        
        return [
            'labels' => $days,
            'listings' => $listings,
            'bids' => $bids,
        ];
    }

    /**
     * Get sales conversion rate data
     */
    public function getSalesConversionData(User $user): array
    {
        $totalListings = $user->listings()->count();
        $soldListings = $user->listings()->where('status', 'sold')->count();
        $conversionRate = $totalListings > 0 ? round(($soldListings / $totalListings) * 100, 1) : 0;
        
        return [
            'total' => $totalListings,
            'sold' => $soldListings,
            'conversion_rate' => $conversionRate,
        ];
    }

    /**
     * Get average sale price data
     */
    public function getAverageSalePriceData(User $user): array
    {
        $invoices = Invoice::where('seller_id', $user->id)
            ->where('payment_status', 'paid')
            ->get();
        
        $averagePrice = $invoices->count() > 0 
            ? round($invoices->avg('winning_bid_amount'), 2) 
            : 0;
        
        $highestPrice = $invoices->max('winning_bid_amount') ?? 0;
        $lowestPrice = $invoices->min('winning_bid_amount') ?? 0;
        
        return [
            'average' => $averagePrice,
            'highest' => round($highestPrice, 2),
            'lowest' => round($lowestPrice, 2),
            'count' => $invoices->count(),
        ];
    }

    /**
     * Get bid activity over time (last 7 days)
     */
    public function getBidActivityData(User $user): array
    {
        $days = [];
        $bidCounts = [];
        $bidAmounts = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dayStart = $date->copy()->startOfDay();
            $dayEnd = $date->copy()->endOfDay();
            
            $dayBids = Bid::whereHas('listing', function($q) use ($user) {
                    $q->where('seller_id', $user->id);
                })
                ->whereBetween('created_at', [$dayStart, $dayEnd])
                ->get();
            
            $days[] = $date->format('D');
            $bidCounts[] = $dayBids->count();
            $bidAmounts[] = round($dayBids->sum('amount'), 2);
        }
        
        return [
            'labels' => $days,
            'counts' => $bidCounts,
            'amounts' => $bidAmounts,
        ];
    }

    /**
     * Get pending payouts for seller (pending, processing)
     */
    public function getPendingPayouts(User $user): Collection
    {
        return \App\Models\Payout::with(['listing', 'invoice'])
            ->where('seller_id', $user->id)
            ->whereIn('status', ['pending', 'processing'])
            ->orderByDesc('payout_generated_at')
            ->get();
    }

    /**
     * Get completed payouts for seller
     */
    public function getCompletedPayouts(User $user): Collection
    {
        return \App\Models\Payout::with(['listing', 'invoice'])
            ->where('seller_id', $user->id)
            ->where('status', 'completed')
            ->orderByDesc('payout_processed_at')
            ->take(20)
            ->get();
    }

    /**
     * Get the seller's active subscription with package info.
     */
    public function getActiveSubscription(User $user): ?object
    {
        return $user->subscriptions()
            ->with('package')
            ->where('status', 'active')
            ->where(function ($q) {
                // Include subscriptions with no end date (unlimited)
                // and those that haven't expired yet
                $q->whereNull('ends_at')
                  ->orWhere('ends_at', '>=', now());
            })
            ->latest('ends_at')
            ->first();
    }

    /**
     * Get listings awaiting admin approval.
     */
    public function getPendingListings(User $user): Collection
    {
        return $user->listings()
            ->where('status', 'pending')
            ->with(['images', 'bids'])
            ->latest('created_at')
            ->get();
    }

    /**
     * Count listings awaiting admin approval.
     */
    public function getPendingListingsCount(User $user): int
    {
        return $user->listings()->where('status', 'pending')->count();
    }

    /**
     * Count listings with status 'sold' as completed sales.
     */
    public function getCompletedSalesCount(User $user): int
    {
        return $user->listings()->where('status', 'sold')->count();
    }

    /**
     * Total lifetime payouts that have been successfully sent/processed.
     */
    public function getTotalEarnings(User $user): float
    {
        return (float) \App\Models\Payout::where('seller_id', $user->id)
            ->whereIn('status', ['sent', 'paid_successfully'])
            ->sum('net_payout');
    }

    /**
     * Sum of paid invoices not yet backed by a completed payout (still to be received).
     */
    public function getToBeReceived(User $user): float
    {
        return (float) Invoice::where('seller_id', $user->id)
            ->where('payment_status', 'paid')
            ->whereDoesntHave('payout', fn ($q) => $q->whereIn('status', ['sent', 'paid_successfully']))
            ->sum('winning_bid_amount');
    }

    /**
     * Auction-only activity feed (bids, approvals, endings, rejections).
     * Excludes messaging, pickup scheduling, and support updates.
     */
    public function getRecentAuctionActivity(User $user): Collection
    {
        $entries = collect();

        Bid::whereHas('listing', fn ($q) => $q->where('seller_id', $user->id))
            ->with('listing')
            ->latest()
            ->take(10)
            ->get()
            ->each(function ($bid) use ($entries) {
                $l = $bid->listing;
                if (!$l) return;
                $entries->push([
                    'type'        => 'bid',
                    'icon'        => 'gavel',
                    'color'       => 'blue',
                    'description' => 'New bid of $' . number_format((float) $bid->amount, 0) . ' on ' . $l->year . ' ' . $l->make . ' ' . $l->model,
                    'timestamp'   => $bid->created_at,
                ]);
            });

        $user->listings()
            ->whereIn('status', ['approved', 'active'])
            ->latest('updated_at')
            ->take(5)
            ->get()
            ->each(function ($l) use ($entries) {
                $entries->push([
                    'type'        => 'approved',
                    'icon'        => 'check_circle',
                    'color'       => 'green',
                    'description' => 'Auction approved: ' . $l->year . ' ' . $l->make . ' ' . $l->model,
                    'timestamp'   => $l->updated_at,
                ]);
            });

        $user->listings()
            ->whereIn('status', ['sold', 'ended_no_sale'])
            ->latest('updated_at')
            ->take(5)
            ->get()
            ->each(function ($l) use ($entries) {
                $isBuyNow = $l->buy_now_price && $l->status === 'sold';
                $entries->push([
                    'type'        => $isBuyNow ? 'buynow' : 'ended',
                    'icon'        => $isBuyNow ? 'shopping_cart' : 'timer_off',
                    'color'       => $isBuyNow ? 'purple' : 'orange',
                    'description' => ($isBuyNow ? 'Buy Now purchase completed: ' : 'Auction ended: ') . $l->year . ' ' . $l->make . ' ' . $l->model,
                    'timestamp'   => $l->updated_at,
                ]);
            });

        $user->listings()
            ->where('status', 'rejected')
            ->latest('updated_at')
            ->take(3)
            ->get()
            ->each(function ($l) use ($entries) {
                $entries->push([
                    'type'        => 'rejected',
                    'icon'        => 'cancel',
                    'color'       => 'red',
                    'description' => 'Listing rejected: ' . $l->year . ' ' . $l->make . ' ' . $l->model,
                    'timestamp'   => $l->updated_at,
                ]);
            });

        return $entries->sortByDesc('timestamp')->take(8)->values();
    }

    /**
     * Top 4 active listings by view count then bid count (for dashboard preview).
     */
    public function getTopActiveListings(User $user): Collection
    {
        return $user->listings()
            ->whereIn('status', ['approved', 'active'])
            ->with(['images'])
            ->withCount('bids')
            ->orderByDesc('view_count')
            ->orderByDesc('bids_count')
            ->take(4)
            ->get();
    }

    /**
     * Time-series data for the Performance Insights chart (week / month / year).
     */
    public function getPerformanceInsightsData(User $user): array
    {
        $result = [];

        foreach (['week' => 7, 'month' => 30, 'year' => 12] as $period => $count) {
            $isYear = ($period === 'year');
            $labels = $bids = $watchlistAdds = $sales = [];

            for ($i = $count - 1; $i >= 0; $i--) {
                if ($isYear) {
                    $date  = Carbon::now()->subMonths($i);
                    $start = $date->copy()->startOfMonth();
                    $end   = $date->copy()->endOfMonth();
                    $labels[] = $date->format('M Y');
                } else {
                    $date  = Carbon::now()->subDays($i);
                    $start = $date->copy()->startOfDay();
                    $end   = $date->copy()->endOfDay();
                    $labels[] = $date->format($period === 'week' ? 'D' : 'M d');
                }

                $bids[] = Bid::whereHas('listing', fn ($q) => $q->where('seller_id', $user->id))
                    ->whereBetween('created_at', [$start, $end])
                    ->count();

                $watchlistAdds[] = DB::table('watchlists')
                    ->join('listings', 'listings.id', '=', 'watchlists.listing_id')
                    ->where('listings.seller_id', $user->id)
                    ->whereBetween('watchlists.created_at', [$start, $end])
                    ->count();

                $sales[] = Invoice::where('seller_id', $user->id)
                    ->where('payment_status', 'paid')
                    ->whereBetween('paid_at', [$start, $end])
                    ->count();
            }

            $result[$period] = compact('labels', 'bids', 'watchlistAdds', 'sales');
        }

        $result['total_views'] = (int) $user->listings()->sum('view_count');

        return $result;
    }

    /**
     * Get all dashboard data
     */
    public function getDashboardData(User $user): array
    {
        return [
            'currentAuctions' => $this->getCurrentAuctions($user),
            'endedAuctions' => $user->listings()->endedNotSoldForSeller($user->id)->with(['images', 'bids'])->get()->map(function($l) { $l->current_bid = $l->getCurrentBid(); return $l; }),
            'pastAuctions' => $this->getPastAuctions($user),
            'wonAuctions' => $this->getWonAuctions($user),
            'rejectedListings' => $this->getRejectedListings($user),
            'auctionSummary' => $this->getAuctionSummary($user),
            'notifications' => $this->getNotifications($user),
            'messagingThreads' => $this->getMessagingThreads($user),
            'payoutMethod' => $this->getPayoutMethod($user),
            'documents' => $this->getDocuments($user),
            'pendingListings'  => $this->getPendingListings($user),
            'pendingPayouts' => $this->getPendingPayouts($user),
            'completedPayouts' => $this->getCompletedPayouts($user),
            'revenueChartData' => $this->getRevenueChartData($user),
            'listingStatusChartData' => $this->getListingStatusChartData($user),
            'auctionPerformanceData' => $this->getAuctionPerformanceData($user),
            'salesConversionData' => $this->getSalesConversionData($user),
            'averageSalePriceData' => $this->getAverageSalePriceData($user),
            'bidActivityData' => $this->getBidActivityData($user),
            'activityTimeline' => $this->activityTimelineService->buildFor($user),
            // Business Seller dashboard extras
            'activeSubscription'      => $this->getActiveSubscription($user),
            'pendingListingsCount'    => $this->getPendingListingsCount($user),
            'completedSalesCount'     => $this->getCompletedSalesCount($user),
            'totalEarnings'           => $this->getTotalEarnings($user),
            'toBeReceived'            => $this->getToBeReceived($user),
            'recentAuctionActivity'   => $this->getRecentAuctionActivity($user),
            'topActiveListings'       => $this->getTopActiveListings($user),
            'performanceInsightsData' => $this->getPerformanceInsightsData($user),
        ];
    }
}

