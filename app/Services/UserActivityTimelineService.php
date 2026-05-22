<?php

namespace App\Services;

use App\Models\Bid;
use App\Models\Listing;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class UserActivityTimelineService
{
    public const LIMIT = 15;

    /**
     * @return array<int, array{type: string, icon: string, description: string, timestamp: Carbon, url?: string}>
     */
    public function buildFor(User $user, int $limit = self::LIMIT): array
    {
        $entries = collect();

        if ($user->role === User::ROLE_BUYER || $user->role === null) {
            $entries = $entries->merge($this->buyerEntries($user));
        }

        if ($user->role === User::ROLE_SELLER) {
            $entries = $entries->merge($this->sellerEntries($user));
        }

        $entries = $entries->merge($this->watchlistEntries($user));

        return $entries
            ->sortByDesc(fn (array $item) => $item['timestamp']->timestamp)
            ->take($limit)
            ->values()
            ->all();
    }

    protected function buyerEntries(User $user): Collection
    {
        $entries = collect();

        Bid::query()
            ->where('user_id', $user->id)
            ->with('listing')
            ->latest()
            ->take(self::LIMIT)
            ->get()
            ->each(function (Bid $bid) use ($entries) {
                $listing = $bid->listing;
                $title = $listing ? $this->listingLabel($listing) : 'an auction';

                $entries->push([
                    'type' => 'bid_placed',
                    'icon' => 'gavel',
                    'description' => 'Placed a bid of $' . number_format((float) $bid->amount, 0) . ' on ' . $title,
                    'timestamp' => $bid->created_at,
                    'url' => $listing ? $this->listingUrl($listing) : null,
                ]);
            });

        $user->getWonAuctions()
            ->take(self::LIMIT)
            ->each(function (Listing $listing) use ($entries) {
                $entries->push([
                    'type' => 'auction_won',
                    'icon' => 'emoji_events',
                    'description' => 'Won auction: ' . $this->listingLabel($listing),
                    'timestamp' => $listing->updated_at ?? $listing->created_at,
                    'url' => $this->listingUrl($listing),
                ]);
            });

        return $entries;
    }

    protected function sellerEntries(User $user): Collection
    {
        return Listing::query()
            ->where('seller_id', $user->id)
            ->latest()
            ->take(self::LIMIT)
            ->get()
            ->map(fn (Listing $listing) => [
                'type' => 'listing_posted',
                'icon' => 'add_circle',
                'description' => 'Posted listing: ' . $this->listingLabel($listing),
                'timestamp' => $listing->created_at,
                'url' => route('seller.listings.show', $listing->id),
            ]);
    }

    protected function watchlistEntries(User $user): Collection
    {
        return DB::table('watchlists')
            ->where('watchlists.user_id', $user->id)
            ->join('listings', 'listings.id', '=', 'watchlists.listing_id')
            ->select('watchlists.created_at', 'listings.id', 'listings.year', 'listings.make', 'listings.model', 'listings.slug')
            ->orderByDesc('watchlists.created_at')
            ->limit(self::LIMIT)
            ->get()
            ->map(function ($row) {
                $listing = new Listing([
                    'id' => $row->id,
                    'year' => $row->year,
                    'make' => $row->make,
                    'model' => $row->model,
                    'slug' => $row->slug,
                ]);

                return [
                    'type' => 'watchlist_save',
                    'icon' => 'favorite',
                    'description' => 'Saved to watchlist: ' . $this->listingLabel($listing),
                    'timestamp' => Carbon::parse($row->created_at),
                    'url' => $this->listingUrl($listing),
                ];
            });
    }

    protected function listingLabel(Listing $listing): string
    {
        $parts = array_filter([
            $listing->year,
            $listing->make,
            $listing->model,
        ]);

        $label = trim(implode(' ', $parts));

        return $label !== '' ? $label : 'Listing #' . $listing->id;
    }

    protected function listingUrl(Listing $listing): ?string
    {
        if (filled($listing->slug) && Route::has('auction.show')) {
            return route('auction.show', $listing->slug);
        }

        if (Route::has('listing.show')) {
            return route('listing.show', $listing->id);
        }

        return null;
    }
}
