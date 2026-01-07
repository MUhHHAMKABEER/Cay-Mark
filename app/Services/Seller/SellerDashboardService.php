<?php

namespace App\Services\Seller;

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
        ];
    }
}

