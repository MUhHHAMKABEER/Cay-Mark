<?php

namespace App\Services\Buyer;

use App\Models\Bid;
use App\Models\Invoice;
use App\Models\Listing;
use App\Models\PostAuctionThread;
use App\Models\User;
use App\Models\Watchlist;
use Illuminate\Support\Collection;

class BuyerDashboardService
{
    /**
     * Get current auctions where buyer has placed bids
     */
    public function getCurrentAuctions(User $user): Collection
    {
        return $user->getCurrentAuctionsAsBuyer();
    }

    /**
     * Get won auctions (payment completed)
     */
    public function getWonAuctions(User $user): Collection
    {
        return $user->getWonAuctions();
    }

    /**
     * Get lost auctions (ended but buyer didn't win)
     */
    public function getLostAuctions(User $user): Collection
    {
        return $user->getLostAuctions();
    }

    /**
     * Get saved items (watchlist)
     */
    public function getSavedItems(User $user): Collection
    {
        return $user->getSavedItems();
    }

    /**
     * Get notifications for buyer
     */
    public function getNotifications(User $user): Collection
    {
        return $user->getNotifications(20);
    }

    /**
     * Get messaging threads for buyer
     */
    public function getMessagingThreads(User $user): Collection
    {
        return PostAuctionThread::with(['seller', 'listing.images'])
            ->where('buyer_id', $user->id)
            ->latest('updated_at')
            ->take(10)
            ->get();
    }

    /**
     * Get bidding activity chart data (last 30 days)
     */
    public function getBiddingActivityData(User $user): array
    {
        $days = [];
        $bidCounts = [];
        $bidAmounts = [];
        
        for ($i = 29; $i >= 0; $i--) {
            $date = \Carbon\Carbon::now()->subDays($i);
            $dayStart = $date->copy()->startOfDay();
            $dayEnd = $date->copy()->endOfDay();
            
            $dayBids = Bid::where('user_id', $user->id)
                ->whereBetween('created_at', [$dayStart, $dayEnd])
                ->get();
            
            $days[] = $date->format('M d');
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
     * Get spending trends chart data (last 6 months)
     */
    public function getSpendingTrendsData(User $user): array
    {
        $months = [];
        $spending = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = \Carbon\Carbon::now()->subMonths($i);
            $monthStart = $date->copy()->startOfMonth();
            $monthEnd = $date->copy()->endOfMonth();
            
            $monthSpending = Invoice::where('buyer_id', $user->id)
                ->where('payment_status', 'paid')
                ->whereBetween('paid_at', [$monthStart, $monthEnd])
                ->sum('total_amount_due');
            
            $months[] = $date->format('M Y');
            $spending[] = round($monthSpending, 2);
        }
        
        return [
            'labels' => $months,
            'data' => $spending,
        ];
    }

    /**
     * Get win/loss ratio data
     */
    public function getWinLossRatioData(User $user): array
    {
        $won = $this->getWonAuctions($user)->count();
        $lost = $this->getLostAuctions($user)->count();
        $total = $won + $lost;
        
        return [
            'labels' => ['Won', 'Lost'],
            'data' => [$won, $lost],
            'colors' => ['#10B981', '#EF4444'],
            'won' => $won,
            'lost' => $lost,
            'total' => $total,
            'winRate' => $total > 0 ? round(($won / $total) * 100, 1) : 0,
        ];
    }

    /**
     * Get buyer summary statistics (for dashboard overview)
     */
    public function getBuyerSummary(User $user): array
    {
        $won = $this->getWonAuctions($user);
        $current = $this->getCurrentAuctions($user);
        $saved = $this->getSavedItems($user);

        $totalSpent = Invoice::where('buyer_id', $user->id)
            ->where('payment_status', 'paid')
            ->sum('total_amount_due');

        $pending = Invoice::where('buyer_id', $user->id)
            ->where('payment_status', 'pending')
            ->get();

        return [
            'total_spent' => round($totalSpent, 2),
            'items_won' => $won->count(),
            'active_bids_count' => $current->count(),
            'saved_items_count' => $saved->count(),
            'pending_payment_count' => $pending->count(),
            'pending_payment_amount' => round($pending->sum('total_amount_due'), 2),
        ];
    }

    /**
     * Get average purchase data (for dashboard overview)
     */
    public function getAveragePurchaseData(User $user): array
    {
        $invoices = Invoice::where('buyer_id', $user->id)
            ->where('payment_status', 'paid')
            ->get();

        $count = $invoices->count();
        $average = $count > 0 ? round($invoices->avg('total_amount_due'), 2) : 0;
        $highest = $count > 0 ? round($invoices->max('total_amount_due'), 2) : 0;

        return [
            'average' => $average,
            'highest' => $highest,
            'count' => $count,
        ];
    }

    /**
     * Get all dashboard data
     */
    public function getDashboardData(User $user): array
    {
        return [
            'currentAuctions' => $this->getCurrentAuctions($user),
            'wonAuctions' => $this->getWonAuctions($user),
            'lostAuctions' => $this->getLostAuctions($user),
            'savedItems' => $this->getSavedItems($user),
            'notifications' => $this->getNotifications($user),
            'messagingThreads' => $this->getMessagingThreads($user),
            'biddingActivityData' => $this->getBiddingActivityData($user),
            'spendingTrendsData' => $this->getSpendingTrendsData($user),
            'winLossRatioData' => $this->getWinLossRatioData($user),
            'buyerSummary' => $this->getBuyerSummary($user),
            'averagePurchaseData' => $this->getAveragePurchaseData($user),
        ];
    }
}

