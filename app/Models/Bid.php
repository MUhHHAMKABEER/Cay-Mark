<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bid extends Model
{
    protected $fillable = ['listing_id', 'user_id', 'amount', 'status'];

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get highest bid for a listing
     */
    public static function getHighestBidForListing($listingId): ?float
    {
        return static::where('listing_id', $listingId)
            ->max('amount');
    }

    /**
     * Get user's highest bid on a listing
     */
    public static function getUserHighestBid($listingId, $userId): ?float
    {
        return static::where('listing_id', $listingId)
            ->where('user_id', $userId)
            ->max('amount');
    }

    /**
     * Get all bids for a listing
     */
    public static function getBidsForListing($listingId)
    {
        return static::where('listing_id', $listingId)
            ->with('user')
            ->orderBy('amount', 'desc')
            ->get();
    }

    /**
     * Get all listings where user has placed bids
     */
    public static function getListingIdsForUser($userId)
    {
        return static::where('user_id', $userId)
            ->distinct()
            ->pluck('listing_id');
    }

    /**
     * Check if user has bid on listing
     */
    public static function userHasBidOnListing($userId, $listingId): bool
    {
        return static::where('user_id', $userId)
            ->where('listing_id', $listingId)
            ->exists();
    }
}
