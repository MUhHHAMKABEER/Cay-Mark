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
        ];
    }
}

