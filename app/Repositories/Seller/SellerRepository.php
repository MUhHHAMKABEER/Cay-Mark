<?php

namespace App\Repositories\Seller;

use App\Models\Listing;
use App\Models\SellerPayoutMethod;
use App\Models\User;
use App\Models\UserDocument;
use Illuminate\Support\Collection;

class SellerRepository
{
    /**
     * Get listings by seller with filters
     */
    public function getListingsBySeller(User $user, array $filters = []): Collection
    {
        $query = Listing::where('seller_id', $user->id);

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
     * Get seller payout method
     */
    public function getPayoutMethod(User $user): ?SellerPayoutMethod
    {
        return SellerPayoutMethod::where('user_id', $user->id)->first();
    }

    /**
     * Save or update payout method
     */
    public function savePayoutMethod(User $user, array $data): SellerPayoutMethod
    {
        return SellerPayoutMethod::updateOrCreate(
            ['user_id' => $user->id],
            $data
        );
    }

    /**
     * Get seller documents
     */
    public function getDocuments(User $user): Collection
    {
        return UserDocument::where('user_id', $user->id)->get();
    }

    /**
     * Get listing by ID for seller
     */
    public function getListingById(User $user, int $listingId): ?Listing
    {
        return Listing::where('id', $listingId)
            ->where('seller_id', $user->id)
            ->first();
    }

    /**
     * Get active listings count
     */
    public function getActiveListingsCount(User $user): int
    {
        return Listing::where('seller_id', $user->id)
            ->whereIn('status', ['active', 'pending'])
            ->count();
    }

    /**
     * Get sold listings count
     */
    public function getSoldListingsCount(User $user): int
    {
        return Listing::where('seller_id', $user->id)
            ->where('status', 'sold')
            ->count();
    }
}


