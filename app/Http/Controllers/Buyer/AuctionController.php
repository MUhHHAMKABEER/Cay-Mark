<?php

namespace App\Http\Controllers\Buyer;

use App\Models\Bid;
use Carbon\Carbon;
use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Services\DepositService;
use App\Services\BiddingIncrementService;


class AuctionController extends Controller
{
   public function index(Request $request)
{
    // Base query: only auction listings
    $query = Listing::with('images')->where('listing_method', 'auction');

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

    // Collect filters from request
    $filters = [
        'types' => $toArray($request->input('type')),
        'makes' => $toArray($request->input('makes')),
        'models' => $toArray($request->input('models')),
        'locations' => $toArray($request->input('locations')),
        'colors' => $toArray($request->input('colors')),
        'primary_damage' => $toArray($request->input('primary_damage')),
        'secondary_damage' => $toArray($request->input('secondary_damage')),
        'transmission' => $toArray($request->input('transmission')),
        'title_condition' => $toArray($request->input('title_condition')),
    ];

    // Numeric / range filters
    $yearFrom = $request->input('year_from');
    $yearTo   = $request->input('year_to');
    $odoMin   = $request->input('odo_min');
    $odoMax   = $request->input('odo_max');

    // Apply filters dynamically
    if (! empty($filters['types'])) {
        $query->whereIn('major_category', $filters['types']);
    }
    if (! empty($filters['makes'])) {
        $query->whereIn('make', $filters['makes']);
    }
    if (! empty($filters['models'])) {
        $query->whereIn('model', $filters['models']);
    }
    if (! empty($filters['locations']) && Schema::hasColumn('listings', 'location')) {
        $query->whereIn('location', $filters['locations']);
    }
    if (! empty($filters['colors'])) {
        $query->whereIn('color', $filters['colors']);
    }
    if (! empty($filters['primary_damage'])) {
        $query->whereIn('primary_damage', $filters['primary_damage']);
    }
    if (! empty($filters['secondary_damage'])) {
        $query->whereIn('secondary_damage', $filters['secondary_damage']);
    }
    if (! empty($filters['transmission'])) {
        $query->whereIn('transmission', $filters['transmission']);
    }
    if (! empty($filters['title_condition'])) {
        $query->whereIn('title_status', $filters['title_condition']);
    }

    // Year range
    if ($yearFrom !== null && $yearFrom !== '') {
        $query->where('year', '>=', $yearFrom);
    }
    if ($yearTo !== null && $yearTo !== '') {
        $query->where('year', '<=', $yearTo);
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

    // Paginate results (20 per page)
    $auctions = $query
        ->orderBy('created_at', 'desc')
        ->paginate(20)
        ->appends($request->query());

    // Build filter option lists for UI
    $filterOptions = [
        'types' => Listing::select('major_category')->distinct()->pluck('major_category')->filter()->values(),
        'makes' => Listing::select('make')->distinct()->pluck('make')->filter()->values(),
        'models' => Listing::select('model')->distinct()->pluck('model')->filter()->values(),
        'colors' => Listing::select('color')->distinct()->pluck('color')->filter()->values(),
        'primary_damage' => Listing::select('primary_damage')->distinct()->pluck('primary_damage')->filter()->values(),
        'secondary_damage' => Listing::select('secondary_damage')->distinct()->pluck('secondary_damage')->filter()->values(),
        'transmission' => Listing::select('transmission')->distinct()->pluck('transmission')->filter()->values(),
        'title_status' => Listing::select('title_status')->distinct()->pluck('title_status')->filter()->values(),
        'locations' => Schema::hasColumn('listings', 'location')
            ? Listing::select('location')->distinct()->pluck('location')->filter()->values()
            : collect(),
        'years' => Listing::select('year')->distinct()->pluck('year')->filter()->sortDesc()->values(),
    ];

    return view('auction', compact('auctions', 'filterOptions'));
}






public function show($id)
{
    // eager-load bids (with user) and images relation (named 'images' in your model)
    $auctionListing = Listing::with('bids.user', 'images')
        ->where('id', $id)
        ->where('listing_method', 'auction')
        ->firstOrFail();

    if (!$auctionListing) {
        dd("No auction listing found for ID: $id", \App\Models\Listing::find($id));
    }

    // ✅ Auction timing check
    $startDate = $auctionListing->auction_start ?? $auctionListing->created_at;
    $endDate = Carbon::parse($startDate)->addDays($auctionListing->auction_duration);

    if (Carbon::now()->greaterThanOrEqualTo($endDate)) {
        abort(404, 'This auction has ended.');
    }

    // determine current bid from bids table (preferred)
    // Show starting price as "Current Bid" if no bids yet (per PDF requirements)
    $highest = $auctionListing->bids()->where('status', 'active')->orderByDesc('amount')->first();
    if ($highest) {
        $currentBid = (float) $highest->amount;
    } else {
        // No bids yet - show starting price as "Current Bid" (not "Starting Price")
        $currentBid = (float) ($auctionListing->starting_price ?? $auctionListing->price ?? $auctionListing->current_bid ?? 0);
    }

    // Map images relation -> URLs
    $images = collect($auctionListing->images ?? [])
        ->map(function($img) {
            $path = null;
            if (is_object($img)) {
                $path = $img->image_path ?? $img->path ?? $img->url ?? $img->image ?? null;
            } else {
                $path = $img;
            }

            if (! $path) return null;

            if (Str::startsWith($path, ['http://', 'https://'])) {
                return $path;
            }

            return asset('uploads/listings/' . ltrim($path, '/'));
        })
        ->filter()
        ->values();

    $mainImage = $images->first() ?? ($auctionListing->main_image_url ?? asset('images/placeholder.png'));

    // Prepare data for the new dynamic view
    $vehicle = (object) [
        // Basic vehicle info
        'id' => $auctionListing->id,
        'year' => $auctionListing->year,
        'make' => $auctionListing->make,
        'model' => $auctionListing->model,
        'location' => $auctionListing->location ?? $auctionListing->city,
        'city' => $auctionListing->city,

        // Pricing and bidding
        'price' => $auctionListing->price ?? $auctionListing->est_retail_value,
        'current_bid' => $currentBid,
        'buy_now_price' => $auctionListing->buy_now_price ?? $auctionListing->price,
        'reserve_met' => $auctionListing->reserve_met ?? false,
        'bid_status' => $auctionListing->bid_status_label ?? "You Haven't Bid",
        'sale_status' => $auctionListing->sale_status_label ?? 'On Minimum Bid',

        // Vehicle details
        'vin' => $auctionListing->vin ?? $auctionListing->vehicle_vin,
        'lot_number' => $auctionListing->lot_number ?? $auctionListing->lot ?? $auctionListing->id,
        'title_code' => $auctionListing->title_code ?? $auctionListing->title,
        'mileage' => $auctionListing->odometer ?? $auctionListing->mileage,
        'primary_damage' => $auctionListing->primary_damage ?? $auctionListing->damage,
        'secondary_damage' => $auctionListing->secondary_damage,
        'keys' => $auctionListing->keys ?? ($auctionListing->has_keys ? 'Yes' : 'No'),

        // Vehicle specifications
        'vehicle_type' => $auctionListing->vehicle_type ?? $auctionListing->type,
        'body_style' => $auctionListing->body_style ?? $auctionListing->body,
        'color' => $auctionListing->color,
        'engine' => $auctionListing->engine ?? $auctionListing->engine_size,
        'cylinders' => $auctionListing->cylinders ?? $auctionListing->cyl,
        'transmission' => $auctionListing->transmission,
        'drive' => $auctionListing->drive ?? $auctionListing->drive_type,
        'fuel' => $auctionListing->fuel,

        // Auction timing
        'time_remaining' => $this->calculateTimeRemaining($endDate),
        'sale_date' => $endDate->format('l, F j, Y'),
        'auction_time' => $endDate->format('g:i A T'),
        'sale_name' => $auctionListing->sale_name ?? 'Online Auction',

        // History and additional info
        'sales_records' => $auctionListing->sales_records ?? '12 records found',
        'previous_sales' => $auctionListing->previous_sales ?? '12 sales found',
        'ownership_history' => $auctionListing->ownership_history ?? '1 owner found',
        'safety_recalls' => $auctionListing->safety_recalls ?? '2 records found',
        'accidents' => $auctionListing->accidents ?? '1 record found',

        // Images
        'main_image_url' => $mainImage,
        'images' => $images->toArray(),

        // Timestamps
        'updated_at' => $auctionListing->updated_at,
        'created_at' => $auctionListing->created_at,
    ];

    // dd($vehicle); // Uncomment to debug the vehicle data

    return view('showAuctionDetail', compact('vehicle'));
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



    public function storeBid(Request $request, $id)
    {
        $user = Auth::user();
        
        // SELLER RESTRICTION: Sellers cannot bid (per PDF requirements)
        if ($user->role === 'seller') {
            throw ValidationException::withMessages([
                'amount' => 'Sellers are not allowed to bid on auctions.',
            ]);
        }
        
        // BUYER MEMBERSHIP REQUIRED
        if ($user->role !== 'buyer') {
            throw ValidationException::withMessages([
                'amount' => 'Buyer membership required to place bids.',
            ]);
        }

        // ACCOUNT RESTRICTION: Check if user is restricted from auction participation (per PDF requirements)
        if ($user->is_restricted) {
            // Check if restriction has expired
            if ($user->restriction_ends_at && now()->greaterThan($user->restriction_ends_at)) {
                // Auto-remove expired restriction
                $user->update([
                    'is_restricted' => false,
                    'restriction_ends_at' => null,
                    'restriction_reason' => null,
                ]);
            } else {
                throw ValidationException::withMessages([
                    'amount' => 'Your account is currently restricted from placing bids due to a non-payment default. This restriction will be lifted on ' . $user->restriction_ends_at->format('F d, Y') . '. You can still browse listings, view your account, and contact support.',
                ]);
            }
        }
        
        $depositService = new DepositService();
        $incrementService = new BiddingIncrementService();

        // simple validation
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
        ]);

        $amount = (float) $data['amount'];

        // Use DB transaction to avoid race conditions
        return DB::transaction(function () use ($id, $user, $amount, $depositService, $incrementService) {
            $listing = Listing::lockForUpdate()->where('id', $id)
                ->where('listing_method', 'auction')
                ->where('status', 'approved')
                ->firstOrFail();

            // Check if auction is still active (use auction_end_time if set, otherwise calculate)
            $auctionEndDate = $listing->auction_end_time 
                ? Carbon::parse($listing->auction_end_time)
                : Carbon::parse($listing->auction_start_time ?? $listing->created_at)->addDays($listing->auction_duration);
                
            if (now()->greaterThanOrEqualTo($auctionEndDate)) {
                throw ValidationException::withMessages([
                    'amount' => 'This auction has ended.',
                ]);
            }

            // fetch current highest active bid
            $highestBid = $listing->bids()->where('status', 'active')->orderByDesc('amount')->first();
            $current = $highestBid ? (float) $highestBid->amount : (float) ($listing->current_bid ?? 0);
            $startingPrice = (float) ($listing->starting_price ?? $listing->price ?? 0);

            // Use the higher of starting price or current bid for increment validation
            $bidBase = max($startingPrice, $current);

            // Validate bid increment using CayMark Increment Table
            $incrementValidation = $incrementService->validateBidIncrement($bidBase, $amount);
            if (!$incrementValidation['valid']) {
                throw ValidationException::withMessages([
                    'amount' => $incrementValidation['message'],
                ]);
            }

            // Ensure bid is at least starting price
            if ($amount < $startingPrice) {
                throw ValidationException::withMessages([
                    'amount' => 'Your bid must be at least the starting price of $' . number_format($startingPrice, 2) . '.',
                ]);
            }

            // Check deposit requirement (10% for bids >= $2,000)
            $depositCheck = $depositService->checkDepositForBid($user, $amount);
            if (!$depositCheck['has_deposit']) {
                throw ValidationException::withMessages([
                    'amount' => 'Insufficient deposit. Required: $' . number_format($depositCheck['required'], 2) . '. Available: $' . number_format($depositCheck['available'], 2) . '. Please add funds to your deposit wallet.',
                ]);
            }

            // AUTO-SNIPING PROTECTION: If bid placed < 60 seconds remaining, reset timer to 60 seconds
            $secondsRemaining = now()->diffInSeconds($auctionEndDate, false);
            $timerReset = false;
            if ($secondsRemaining > 0 && $secondsRemaining < 60) {
                // Reset auction end time to 60 seconds from now (per PDF requirements)
                $newEndTime = now()->addSeconds(60);
                $listing->auction_end_time = $newEndTime;
                $timerReset = true;
            }

            // create bid
            $bid = Bid::create([
                'listing_id' => $listing->id,
                'user_id' => $user->id,
                'amount' => $amount,
                'status' => 'active',
            ]);

            // Lock deposit for this bid (if required)
            $requiredDeposit = $depositService->calculateRequiredDeposit($amount);
            if ($requiredDeposit > 0) {
                $depositService->lockDepositForBid($user, $bid, $requiredDeposit);
            }

            // Update listing
            $listing->current_bid = $amount;
            $listing->save();

            // Send bid placed notification
            $notificationService = new \App\Services\NotificationService();
            $notificationService->bidPlaced($user, $listing);

            // Calculate new end time for response
            $newEndDate = $timerReset ? $listing->auction_end_time : $auctionEndDate;

            // return JSON for AJAX
            return response()->json([
                'success' => true,
                'bid' => [
                    'id' => $bid->id,
                    'amount' => number_format($bid->amount, 2),
                    'created_at' => $bid->created_at->toDateTimeString(),
                ],
                'currentBid' => number_format($amount, 2),
                'timerReset' => $timerReset,
                'newEndTime' => $timerReset ? $newEndDate->toDateTimeString() : null,
                'message' => $timerReset ? 'Bid placed! Timer extended by 60 seconds.' : 'Bid placed successfully!',
            ]);
        });
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
