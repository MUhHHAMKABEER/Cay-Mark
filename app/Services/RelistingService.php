<?php

namespace App\Services;

use App\Models\Listing;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RelistingService
{
    /**
     * Relisting window for Business Sellers: 48 hours
     */
    const RELISTING_WINDOW_HOURS = 48;

    /**
     * Check if a listing is eligible for relisting.
     * 
     * Rules:
     * - Business Seller only
     * - Within 48 hours of original listing end/expiry
     * - No sales (no bids for auctions, no buy now purchases)
     * 
     * @param Listing $listing
     * @param User $user
     * @return array ['eligible' => bool, 'reason' => string, 'hours_remaining' => float|null]
     */
    public function checkRelistingEligibility(Listing $listing, User $user): array
    {
        // Individual Seller: No relisting feature
        $userPackage = $user->activeSubscription?->package;
        if (!$userPackage || $userPackage->price == 25.00) {
            return [
                'eligible' => false,
                'reason' => 'Individual sellers cannot relist. Please create a new listing.',
                'hours_remaining' => null,
            ];
        }

        // Business Seller only (price = $599.99)
        if ($userPackage->price != 599.99) {
            return [
                'eligible' => false,
                'reason' => 'Relisting is only available for Business Seller memberships.',
                'hours_remaining' => null,
            ];
        }

        // Check if listing has sales
        $hasBids = $listing->bids()->where('status', 'active')->exists();
        $hasBuyNow = $listing->bought || $listing->listing_state === 'sold';

        if ($hasBids || $hasBuyNow) {
            return [
                'eligible' => false,
                'reason' => 'This listing has sales/bids and cannot be relisted.',
                'hours_remaining' => null,
            ];
        }

        // Check if within 48-hour window
        $listingEndTime = $listing->expires_at ?? Carbon::parse($listing->created_at)->addDays($listing->auction_duration ?? 30);
        $hoursSinceEnd = now()->diffInHours($listingEndTime, false);

        if ($hoursSinceEnd < 0) {
            // Listing ended more than 48 hours ago
            $hoursAgo = abs($hoursSinceEnd);
            if ($hoursAgo > self::RELISTING_WINDOW_HOURS) {
                return [
                    'eligible' => false,
                    'reason' => 'Relisting window has expired. Listing ended ' . round($hoursAgo) . ' hours ago. Free relisting is only available within 48 hours.',
                    'hours_remaining' => null,
                ];
            }
        }

        $hoursRemaining = self::RELISTING_WINDOW_HOURS - abs($hoursSinceEnd);

        return [
            'eligible' => true,
            'reason' => 'Listing is eligible for free relisting.',
            'hours_remaining' => max(0, $hoursRemaining),
        ];
    }

    /**
     * Create a relist of an existing listing (Business Seller only).
     * 
     * @param Listing $originalListing
     * @param User $user
     * @return Listing
     * @throws \Exception
     */
    public function relistListing(Listing $originalListing, User $user): Listing
    {
        $eligibility = $this->checkRelistingEligibility($originalListing, $user);

        if (!$eligibility['eligible']) {
            throw new \Exception($eligibility['reason']);
        }

        return DB::transaction(function () use ($originalListing, $user) {
            // Create new listing as a relist
            $relist = Listing::create([
                'seller_id' => $user->id,
                'original_listing_id' => $originalListing->id,
                'relisted_at' => now(),
                'is_relist' => true,
                'listing_method' => $originalListing->listing_method,
                'auction_duration' => $originalListing->auction_duration,
                'major_category' => $originalListing->major_category,
                'subcategory' => $originalListing->subcategory,
                'other_make' => $originalListing->other_make,
                'other_model' => $originalListing->other_model,
                'condition' => $originalListing->condition,
                'make' => $originalListing->make,
                'model' => $originalListing->model,
                'trim' => $originalListing->trim,
                'year' => $originalListing->year,
                'vin' => $originalListing->vin,
                'color' => $originalListing->color,
                'fuel_type' => $originalListing->fuel_type,
                'transmission' => $originalListing->transmission,
                'title_status' => $originalListing->title_status,
                'primary_damage' => $originalListing->primary_damage,
                'secondary_damage' => $originalListing->secondary_damage,
                'keys_available' => $originalListing->keys_available,
                'engine_type' => $originalListing->engine_type,
                'hull_material' => $originalListing->hull_material,
                'category_type' => $originalListing->category_type,
                'price' => $originalListing->price,
                'odometer' => $originalListing->odometer,
                'status' => 'pending', // Needs admin approval again
                'expires_at' => now()->addDays($originalListing->auction_duration ?? 30),
                'listing_state' => 'active',
            ]);

            // Copy images from original listing
            foreach ($originalListing->images as $image) {
                // You may want to copy the image files or reference them
                // For now, we'll just reference the same images
                // In production, you might want to duplicate the files
            }

            return $relist;
        });
    }

    /**
     * Get listings eligible for relisting for a user.
     * 
     * @param User $user
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getEligibleListingsForRelist(User $user)
    {
        $userPackage = $user->activeSubscription?->package;

        // Individual Seller: No relisting
        if (!$userPackage || $userPackage->price == 25.00) {
            return collect([]);
        }

        // Business Seller only
        if ($userPackage->price != 599.99) {
            return collect([]);
        }

        $cutoffTime = now()->subHours(self::RELISTING_WINDOW_HOURS);

        return Listing::where('seller_id', $user->id)
            ->where('is_relist', false) // Not already a relist
            ->where(function ($query) {
                $query->where('expires_at', '<=', now())
                    ->orWhere(function ($q) {
                        $q->whereNull('expires_at')
                            ->whereRaw('DATE_ADD(created_at, INTERVAL COALESCE(auction_duration, 30) DAY) <= NOW()');
                    });
            })
            ->where('expires_at', '>=', $cutoffTime) // Within 48 hours
            ->where('bought', false) // No buy now sales
            ->whereDoesntHave('bids', function ($query) {
                $query->where('status', 'active');
            })
            ->get()
            ->filter(function ($listing) use ($user) {
                $eligibility = $this->checkRelistingEligibility($listing, $user);
                return $eligibility['eligible'];
            });
    }
}
