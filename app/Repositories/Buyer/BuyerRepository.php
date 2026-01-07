<?php

namespace App\Repositories\Buyer;

use App\Models\Bid;
use App\Models\Invoice;
use App\Models\Listing;
use App\Models\User;
use App\Models\Watchlist;
use Illuminate\Support\Collection;

class BuyerRepository
{
    /**
     * Get listings where buyer has placed bids
     */
    public function getListingsWithBids(User $user, array $filters = []): Collection
    {
        $listingIds = Bid::where('user_id', $user->id)
            ->distinct()
            ->pluck('listing_id');

        $query = Listing::whereIn('id', $listingIds);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['statuses'])) {
            $query->whereIn('status', $filters['statuses']);
        }

        if (isset($filters['with_relations'])) {
            $query->with($filters['with_relations']);
        }

        return $query->get();
    }

    /**
     * Get buyer bids for a listing
     */
    public function getBidsByListing(User $user, int $listingId): Collection
    {
        return Bid::where('user_id', $user->id)
            ->where('listing_id', $listingId)
            ->latest()
            ->get();
    }

    /**
     * Get buyer invoices
     */
    public function getInvoices(User $user, array $filters = []): Collection
    {
        $query = Invoice::where('buyer_id', $user->id);

        if (isset($filters['payment_status'])) {
            $query->where('payment_status', $filters['payment_status']);
        }

        if (isset($filters['with_relations'])) {
            $query->with($filters['with_relations']);
        }

        return $query->latest()->get();
    }

    /**
     * Get watchlist items
     */
    public function getWatchlist(User $user): Collection
    {
        return Watchlist::where('user_id', $user->id)
            ->with('listing')
            ->latest()
            ->get();
    }

    /**
     * Add to watchlist
     */
    public function addToWatchlist(User $user, int $listingId): Watchlist
    {
        return Watchlist::firstOrCreate([
            'user_id' => $user->id,
            'listing_id' => $listingId,
        ]);
    }

    /**
     * Remove from watchlist
     */
    public function removeFromWatchlist(User $user, int $listingId): bool
    {
        return Watchlist::where('user_id', $user->id)
            ->where('listing_id', $listingId)
            ->delete();
    }

    /**
     * Get highest bid for listing by buyer
     */
    public function getHighestBidByBuyer(User $user, int $listingId): ?Bid
    {
        return Bid::where('user_id', $user->id)
            ->where('listing_id', $listingId)
            ->orderBy('amount', 'desc')
            ->first();
    }

    /**
     * Get won auctions count
     */
    public function getWonAuctionsCount(User $user): int
    {
        return Invoice::where('buyer_id', $user->id)
            ->where('payment_status', 'paid')
            ->count();
    }
}


