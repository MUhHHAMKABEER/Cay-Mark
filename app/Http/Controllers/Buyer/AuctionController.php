<?php

namespace App\Http\Controllers\Buyer;

use App\Models\Bid;
use Carbon\Carbon;
use App\Models\Listing;
use Illuminate\Http\Request;
use App\Http\Requests\BuyerBidStoreRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Services\DepositService;
use App\Services\BiddingIncrementService;
use App\Services\Buyer\AuctionBidOrchestrator;


class AuctionController extends Controller
{
   public function index(Request $request)
{
    // Base query: only auction listings
    $query = Listing::with('images')
        ->withCount(['watchlistedBy as likes_count'])
        ->where('listing_method', 'auction');

    // If you have a 'status' column, filter approved ones
    if (Schema::hasColumn('listings', 'status')) {
        $query->where('status', 'approved');
    }

    // Helper to normalize incoming filter values
    $toArray = function ($val) {
        if (is_null($val)) {
            return [];
        }
        if (is_array($val)) {
            return array_values(array_filter($val, fn($v) => $v !== '' && $v !== null));
        }
        return trim($val) === '' ? [] : [trim($val)];
    };

    // Collect all filters from request
    $filters = [
        'location' => $toArray($request->input('location')),
        'vehicle_type' => $toArray($request->input('vehicle_type')),
        'makes' => $toArray($request->input('makes')),
        'models' => $toArray($request->input('models')),
        'damage_type' => $toArray($request->input('damage_type')),
        'body_style' => $toArray($request->input('body_style')),
        'engine_type' => $toArray($request->input('engine_type')),
        'cylinders' => $toArray($request->input('cylinders')),
        'transmission' => $toArray($request->input('transmission')),
        'drive_train' => $toArray($request->input('drive_train')),
        'fuel_type' => $toArray($request->input('fuel_type')),
    ];

    // Text search (header search bar)
    $search = trim((string) $request->input('search', ''));

    // Numeric / range filters
    $yearFrom = $request->input('year_from');
    $yearTo   = $request->input('year_to');
    $odoMin   = $request->input('odometer_min');
    $odoMax   = $request->input('odometer_max');

    if ($search !== '') {
        $query->where(function ($q) use ($search) {
            $q->where('make', 'like', '%' . $search . '%')
              ->orWhere('model', 'like', '%' . $search . '%')
              ->orWhere('year', 'like', '%' . $search . '%')
              ->orWhere('vin', 'like', '%' . $search . '%')
              ->orWhere('item_number', 'like', '%' . $search . '%');
        });
    }

    if ($request->filled('condition') && in_array($request->input('condition'), ['new', 'used', 'salvaged'])) {
        $query->where('condition', $request->input('condition'));
    }

    // Apply filters dynamically
    if (! empty($filters['location'])) {
        $query->whereIn('island', $filters['location']);
    }
    if (! empty($filters['vehicle_type'])) {
        $query->whereIn('major_category', $filters['vehicle_type']);
    }
    if (! empty($filters['makes'])) {
        $query->whereIn('make', $filters['makes']);
    }
    if (! empty($filters['models'])) {
        $query->whereIn('model', $filters['models']);
    }
    if (! empty($filters['damage_type'])) {
        $query->where(function($q) use ($filters) {
            $q->whereIn('primary_damage', $filters['damage_type'])
              ->orWhereIn('secondary_damage', $filters['damage_type']);
        });
    }
    if (! empty($filters['body_style'])) {
        if (Schema::hasColumn('listings', 'body_style')) {
            $query->whereIn('body_style', $filters['body_style']);
        } elseif (Schema::hasColumn('listings', 'subcategory')) {
            $query->whereIn('subcategory', $filters['body_style']);
        }
    }
    if (! empty($filters['engine_type'])) {
        if (Schema::hasColumn('listings', 'engine_type')) {
            $query->whereIn('engine_type', $filters['engine_type']);
        }
    }
    if (! empty($filters['cylinders'])) {
        if (Schema::hasColumn('listings', 'cylinders')) {
            $query->whereIn('cylinders', $filters['cylinders']);
        }
    }
    if (! empty($filters['transmission'])) {
        $query->whereIn('transmission', $filters['transmission']);
    }
    if (! empty($filters['drive_train'])) {
        // Check for both drive_train and drive_type columns for backward compatibility
        if (Schema::hasColumn('listings', 'drive_type')) {
            $query->whereIn('drive_type', $filters['drive_train']);
        } elseif (Schema::hasColumn('listings', 'drive_train')) {
            $query->whereIn('drive_train', $filters['drive_train']);
        }
    }
    if (! empty($filters['fuel_type'])) {
        if (Schema::hasColumn('listings', 'fuel_type')) {
            $query->whereIn('fuel_type', $filters['fuel_type']);
        }
    }

    // Year range
    if ($yearFrom !== null && $yearFrom !== '') {
        $query->where('year', '>=', (int) $yearFrom);
    }
    if ($yearTo !== null && $yearTo !== '') {
        $query->where('year', '<=', (int) $yearTo);
    }

    // Odometer range
    if (Schema::hasColumn('listings', 'odometer')) {
        if ($odoMin !== null && $odoMin !== '') {
            $query->where('odometer', '>=', (int) $odoMin);
        }
        if ($odoMax !== null && $odoMax !== '') {
            $query->where('odometer', '<=', (int) $odoMax);
        }
    }

    // Only show ACTIVE auctions
    $query->where('listing_state', 'active');

    // Sorting
    $sortBy = $request->input('sort', 'newest');
    switch ($sortBy) {
        case 'price_low':
            $query->orderBy('starting_price', 'asc');
            break;
        case 'price_high':
            $query->orderBy('starting_price', 'desc');
            break;
        case 'ending_soon':
            $query->orderBy('auction_end_time', 'asc');
            break;
        case 'newest':
        default:
            $query->orderBy('created_at', 'desc');
            break;
    }

    $likedListingIds = collect();
    if (Auth::check()) {
        $likedListingIds = Auth::user()->watchlist()->pluck('listing_id');
    }

    // Check if AJAX request
    if ($request->ajax()) {
        $auctions = $query->paginate(20);
        return response()->json([
            'success' => true,
            'html' => view('partials.auction-listings', compact('auctions', 'likedListingIds'))->render(),
            'pagination' => view('partials.auction-pagination', compact('auctions'))->render(),
            'count' => $auctions->total(),
        ]);
    }

    // Paginate results (20 per page)
    $auctions = $query
        ->paginate(20)
        ->appends($request->query());

    // Build filter option lists for UI (only from active auctions)
    $baseQuery = Listing::where('listing_method', 'auction')
        ->where('listing_state', 'active')
        ->where('status', 'approved');

    $filterOptions = [
        'locations' => $baseQuery->select('island')->distinct()->pluck('island')->filter()->sort()->values(),
        'vehicle_types' => $baseQuery->select('major_category')->distinct()->pluck('major_category')->filter()->sort()->values(),
        'makes' => $baseQuery->select('make')->distinct()->pluck('make')->filter()->sort()->values(),
        'models' => $baseQuery->select('model')->distinct()->pluck('model')->filter()->sort()->values(),
        'damage_types' => collect([
            'All Over', 'Front End', 'Rear End', 'Side', 'Mechanical', 
            'Minor Dents/Scratches', 'Flood', 'Fire', 'Vandalism', 
            'Interior', 'Undercarriage', 'Normal Wear', 'Engine', 
            'Transmission', 'None (No Reported Damage)'
        ]),
        'body_styles' => collect([
            'Sedan', 'SUV', 'Truck', 'Coupe', 'Hatchback', 
            'Van', 'Crossover', 'Convertible', 'Wagon', 'Other'
        ]),
        'engine_types' => $baseQuery->select('engine_type')->distinct()->pluck('engine_type')->filter()->sort()->values(),
        'cylinders' => collect(['2', '3', '4', '6', '8', '10', '12']),
        'transmissions' => $baseQuery->select('transmission')->distinct()->pluck('transmission')->filter()->sort()->values(),
        'drive_trains' => collect(['FWD', 'RWD', 'AWD', '4WD']),
        'fuel_types' => $baseQuery->select('fuel_type')->distinct()->pluck('fuel_type')->filter()->sort()->values(),
        'years' => $baseQuery->select('year')->distinct()->pluck('year')->filter()->sortDesc()->values(),
        'odometer_max' => $baseQuery->max('odometer') ?? 250000,
    ];

    return view('auction', compact('auctions', 'filterOptions', 'likedListingIds'));
}






public function show(Listing $listing)
{
    // Check if listing is an auction
    if ($listing->listing_method !== 'auction') {
        abort(404, "Listing '{$listing->slug}' exists but is not an auction listing (method: '{$listing->listing_method}').");
    }

    // Eager-load bids (with user) and images relation
    $auctionListing = $listing->load('bids.user', 'images');

    // ✅ Auction timing check - Use auction_end_time if set, otherwise calculate
    if ($auctionListing->auction_end_time) {
        $endDate = Carbon::parse($auctionListing->auction_end_time);
    } else {
        $startDate = $auctionListing->auction_start_time ?? $auctionListing->auction_start ?? $auctionListing->created_at;
        $endDate = Carbon::parse($startDate)->addDays($auctionListing->auction_duration ?? 7);
    }

    $isExpired = Carbon::now()->greaterThanOrEqualTo($endDate);
    
    // Don't abort on expired - just show the page with expired status
    // This allows users to view ended auctions

    // Determine current bid from bids table (preferred)
    // Show starting price as "Current Bid" if no bids yet (per PDF requirements)
    $highest = $auctionListing->bids()->where('status', 'active')->orderByDesc('amount')->first();
    if ($highest) {
        $currentBid = (float) $highest->amount;
    } else {
        // No bids yet - show starting price as "Current Bid" (not "Starting Price")
        $currentBid = (float) ($auctionListing->starting_price ?? $auctionListing->price ?? $auctionListing->current_bid ?? 0);
    }

    // Calculate time remaining
    $timeRemaining = !$isExpired ? now()->diff($endDate) : null;
    
    // Get increment service
    $incrementService = new \App\Services\BiddingIncrementService();
    $nextValidIncrement = $incrementService->calculateMinimumNextBid($currentBid);
    $incrementAmount = $incrementService->getIncrementForBid($currentBid);
    
    // Normalize images for the new view format
    $images = collect($auctionListing->images ?? [])->map(function($img) {
        $path = is_object($img) ? ($img->image_path ?? $img->path ?? null) : $img;
        if (!$path) return null;
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) return $path;
        return asset('uploads/listings/' . ltrim($path, '/'));
    })->filter()->values();
    
    $mainImage = $images->first() ?? asset('images/placeholder.png');

    // Use the new professional AuctionDetail view
    return view('Buyer.AuctionDetail', compact(
        'auctionListing',
        'endDate',
        'isExpired',
        'currentBid',
        'timeRemaining',
        'nextValidIncrement',
        'incrementAmount',
        'images',
        'mainImage'
    ));
}

// Helper method to calculate time remaining (add this to your controller)
private function calculateTimeRemaining($endDate)
{
    $now = Carbon::now();
    $end = Carbon::parse($endDate);

    if ($now->greaterThan($end)) {
        return 'Auction ended';
    }

    $diff = $now->diff($end);

    if ($diff->days > 0) {
        return $diff->days . 'd ' . $diff->h . 'h ' . $diff->i . 'm';
    }

    return $diff->h . 'h ' . $diff->i . 'm ' . $diff->s . 's';
}

// Helper method to get bid status for current user
private function getBidStatus($listing, $userId = null)
{
    if (!$userId) {
        return "You Haven't Bid";
    }

    $userBid = $listing->bids()
        ->where('user_id', $userId)
        ->where('status', 'active')
        ->orderByDesc('amount')
        ->first();

    if (!$userBid) {
        return "You Haven't Bid";
    }

    $highestBid = $listing->bids()
        ->where('status', 'active')
        ->orderByDesc('amount')
        ->first();

    if ($highestBid && $highestBid->user_id == $userId) {
        return 'You are Winning';
    }

    return 'You are Outbid';
}



    public function storeBid(BuyerBidStoreRequest $request, Listing $listing)
    {
        return AuctionBidOrchestrator::placeBid($request, $listing);
    }



public function auctionDetailBuyer($id, $slug = null)
{
    $auctionListing = Listing::with(['bids.user', 'images'])
        ->where('id', $id)
        ->where('listing_method', 'auction')
        ->firstOrFail();

    // Build SEO slug
    $expectedSlug = Str::slug("{$auctionListing->year} {$auctionListing->make} {$auctionListing->model}");
    if ($slug !== $expectedSlug) {
        return redirect()->route('auction.dashboard', [
            'id'   => $auctionListing->id,
            'slug' => $expectedSlug,
        ]);
    }

    // Auction End Date (use auction_end_time if set, otherwise calculate)
    $endDate = null;
    if ($auctionListing->auction_end_time) {
        $endDate = Carbon::parse($auctionListing->auction_end_time);
    } elseif (!empty($auctionListing->auction_duration)) {
        $startTime = $auctionListing->auction_start_time 
            ? Carbon::parse($auctionListing->auction_start_time)
            : Carbon::parse($auctionListing->created_at);
        $endDate = $startTime->copy()->addDays((int) $auctionListing->auction_duration);
    }

    // Check if expired
    $isExpired = $endDate ? $endDate->isPast() : true;
    
    // Calculate time remaining
    $timeRemaining = $endDate && !$isExpired ? now()->diff($endDate) : null;
    
    // Get next valid increment
    $incrementService = new \App\Services\BiddingIncrementService();
    $highestBid = $auctionListing->bids()->where('status', 'active')->orderByDesc('amount')->first();
    $currentBid = $highestBid ? (float) $highestBid->amount : (float) ($auctionListing->starting_price ?? $auctionListing->price ?? 0);
    $nextValidIncrement = $incrementService->calculateMinimumNextBid($currentBid);
    $incrementAmount = $incrementService->getIncrementForBid($currentBid);

    // Current bid - Show starting price as "Current Bid" if no bids yet (per PDF requirements)
    $highest = $auctionListing->bids()->where('status', 'active')->orderByDesc('amount')->first();
    if ($highest) {
        $currentBid = (float) $highest->amount;
    } else {
        // No bids yet - show starting price as "Current Bid" (not "Starting Price")
        $currentBid = (float) ($auctionListing->starting_price ?? $auctionListing->price ?? $auctionListing->current_bid ?? 0);
    }

    // Normalize images
    $images = collect($auctionListing->images)->map(fn($img) => asset('uploads/listings/' . $img->image_path));
    $mainImage = $images->first() ?? asset('images/placeholder.png');

    return view('Buyer.AuctionDetail', compact(
        'auctionListing',
        'endDate',
        'isExpired',
        'currentBid',
        'timeRemaining',
        'nextValidIncrement',
        'incrementAmount',
        'images',
        'mainImage'
    ));
}



}
