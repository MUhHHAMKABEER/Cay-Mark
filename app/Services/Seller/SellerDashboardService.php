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
use Illuminate\Support\Collection;

class SellerDashboardService
{
    /**
     * Get current auctions (active + awaiting PIN confirmation)
     */
    public function getCurrentAuctions(User $user): Collection
    {
        return $user->getCurrentAuctions();
    }

    /**
     * Get past auctions (completed with pickup confirmed)
     */
    public function getPastAuctions(User $user): Collection
    {
        return $user->getPastAuctions();
    }

    /**
     * Get rejected listings with 72-hour edit window
     */
    public function getRejectedListings(User $user): Collection
    {
        return $user->getRejectedListings();
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
     * Get all dashboard data
     */
    public function getDashboardData(User $user): array
    {
        return [
            'currentAuctions' => $this->getCurrentAuctions($user),
            'pastAuctions' => $this->getPastAuctions($user),
            'rejectedListings' => $this->getRejectedListings($user),
            'auctionSummary' => $this->getAuctionSummary($user),
            'notifications' => $this->getNotifications($user),
            'messagingThreads' => $this->getMessagingThreads($user),
            'payoutMethod' => $this->getPayoutMethod($user),
            'documents' => $this->getDocuments($user),
            'revenueChartData' => $this->getRevenueChartData($user),
            'listingStatusChartData' => $this->getListingStatusChartData($user),
            'auctionPerformanceData' => $this->getAuctionPerformanceData($user),
            'salesConversionData' => $this->getSalesConversionData($user),
            'averageSalePriceData' => $this->getAverageSalePriceData($user),
            'bidActivityData' => $this->getBidActivityData($user),
        ];
    }
}

