<?php

namespace App\Http\Controllers\Seller;

use App\Models\Listing;
use App\Models\ListingImage;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\Package;
use App\Services\VinHinDecoderService;
use App\Helpers\TextFormatter;
use App\Http\Requests\SellerListingStoreRequest;
use App\Http\Requests\SellerListingUpdateRequest;
use App\Http\Requests\SellerDecodeVinHinRequest;
use App\Services\Seller\ListingVinDecodeOps;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ListingController extends Controller
{
    public function create()
    {
        $makes = [
            'Acura', 'Alfa Romeo', 'Arctic Cat', 'Audi', 'Azimut', 'Beneteau',
            'Bentley', 'Bobcat', 'BRP / Can-Am', 'BMW', 'BYD', 'Buick',
            'Cadillac', 'Carver', 'Case Construction', 'Case IH', 'Caterpillar',
            'Changan', 'Chaparral', 'Chris-Craft', 'Chrysler', 'Citroën', 'Club Car',
            'Cobia', 'Columbia ParCar', 'Komatsu', 'Cushman', 'Daihatsu', 'Dodge',
            'Doosan', 'EdgeWater', 'E-Z-GO', 'Fendt', 'Fiat', 'Formula', 'Ford',
            'Four Winns', 'Galeon', 'Geely', 'Genesis', 'GMC', 'Grady-White', 'Honda',
            'Hyundai', 'Hyundai Heavy Industries', 'Hitachi', 'Infiniti', 'Isuzu',
            'Jeanneau', 'JCB', 'Jaguar', 'John Deere', 'Kawasaki', 'Kia', 'Komatsu',
            'Kubota', 'Kymco', 'Landini', 'Land Rover', 'Lexus', 'Lincoln', 'Lotus',
            'Malibu', 'Manitou', 'Marlow-Hunter', 'Maserati', 'MasterCraft', 'Mazda',
            'McLaren', 'Mercedes-Benz', 'Mini', 'Mitsubishi', 'Monterey', 'New Holland',
            'Nissan', 'Polaris', 'Pontiac', 'Porsche', 'RAM', 'Ranger', 'Regal',
            'Renault', 'Rolls-Royce', 'Scion', 'Sea Hunt', 'Sea Ray', 'Silverton',
            'Star EV', 'Stingray', 'Terex', 'Tiara', 'Tomberlin', 'Tracker',
            'Tracker Marine', 'Toyota', 'Valtra', 'Volkswagen', 'Volvo',
            'Volvo Construction Equipment', 'Wacker Neuson', 'Yamaha',
        ];

        sort($makes); // Sort alphabetically

        $user = Auth::user();
        $maxYear = (int) date('Y') + 1;

        // Casual seller = no business licence on file → pays $25 per listing.
        // Business seller = has uploaded a business licence → subscription covers listings.
        $isIndividualSeller = empty($user->business_license_path);

        $missingRequirements = $user->getMissingListingRequirements();

        return view('Seller.submit-listing-new', compact('user', 'maxYear', 'isIndividualSeller', 'missingRequirements'));
    }

    public function store(SellerListingStoreRequest $request)
    {
        try {
            $user = Auth::user();

            $missingRequirements = $user->getMissingListingRequirements();
            if (!empty($missingRequirements)) {
                return back()->withErrors([
                    'requirements' => 'Please complete the following in your profile before submitting a listing: ' . implode(', ', $missingRequirements) . '.',
                ])->withInput();
            }

            // Casual seller = no business licence → $25 per listing fee applies.
            $isIndividualSeller = empty($user->business_license_path);

            $validated = $request->validated();

            // Validate pricing rules
            if ($request->starting_price && $request->starting_price <= 0) {
                return back()->withErrors(['starting_price' => 'Starting Bid must be greater than $0 if entered. Please enter a valid starting price.'])->withInput();
            }
            if ($request->reserve_price && $request->starting_price && $request->reserve_price < $request->starting_price) {
                return back()->withErrors(['reserve_price' => 'Reserve Price must be greater than or equal to Starting Bid. Please adjust your pricing.'])->withInput();
            }

            $photos      = $request->file('photos', []);
            $totalPhotos = count($photos) + ($request->hasFile('cover_photo') ? 1 : 0);

            if (count($photos) < 5) {
                return back()->withErrors(['photos' => 'You need at least 6 photos total (1 cover + 5 additional). Currently you have ' . $totalPhotos . ' photo(s).'])->withInput()->with('error_section', 'section2');
            }
            if ($totalPhotos > 15) {
                return back()->withErrors(['photos' => 'Maximum 15 photos allowed (1 cover + 14 additional). You have uploaded ' . $totalPhotos . '.'])->withInput()->with('error_section', 'section2');
            }

            if (!$request->boolean('vin_decode_success')) {
                $validated['vin'] = null;
            }

            // Duplicate VIN check
            $duplicateVinFlag = false;
            if (!empty($validated['vin'])) {
                $vin             = TextFormatter::toAllCaps($validated['vin']);
                $existingListing = Listing::where('vin', $vin)
                    ->where('id', '!=', $request->id ?? 0)
                    ->where('status', '!=', 'rejected')
                    ->first();
                if ($existingListing) {
                    $duplicateVinFlag = true;
                }
            }

            // ── Step 1: Pre-move uploaded files to persistent temp storage ──
            // PHP deletes temp files at request end, so we move them NOW
            // before dispatching the queue job.
            $queueKey = (string) \Illuminate\Support\Str::uuid();
            $tempDir  = storage_path("app/listing-queue/{$queueKey}");
            mkdir($tempDir, 0755, true);

            $staged = ['cover' => null, 'photos' => [], 'video' => null];

            if ($request->hasFile('cover_photo')) {
                $f    = $request->file('cover_photo');
                $name = 'cover.' . $f->getClientOriginalExtension();
                $f->move($tempDir, $name);
                $staged['cover'] = $name;
            }

            foreach ($request->file('photos', []) as $i => $photo) {
                $name = "photo_{$i}." . $photo->getClientOriginalExtension();
                $photo->move($tempDir, $name);
                $staged['photos'][] = $name;
            }

            if ($request->hasFile('engine_video')) {
                $v    = $request->file('engine_video');
                $name = 'video.' . $v->getClientOriginalExtension();
                $v->move($tempDir, $name);
                $staged['video'] = $name;
            }

            // ── Step 2: Create listing DB record + payment (no file I/O) ───
            $listing = Listing::createFromPayloadOnly(
                $user,
                $request,
                $validated,
                $isIndividualSeller,
                $duplicateVinFlag
            );

            // ── Step 3: Dispatch media job — seller is redirected instantly ──
            \App\Jobs\ProcessListingMedia::dispatch($listing->id, $tempDir, $staged);

            return redirect()
                ->route('seller.listings.success', ['id' => $listing->id])
                ->with('listing_submitted', true);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput()->with('error_section', $this->detectErrorSection($e->errors()));
        } catch (\Illuminate\Http\Exceptions\PostTooLargeException $e) {
            return back()->withErrors(['photos' => 'The total size of your uploaded files exceeds the server limit. Please reduce image sizes or upload fewer photos. Maximum total size: 25MB.'])->withInput()->with('error_section', 'photos');
        } catch (\Exception $e) {
            \Log::error('Listing submission error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
            ]);
            return back()->withErrors(['general' => 'An unexpected error occurred while submitting your listing. Please check all fields and try again. If the problem persists, contact support.'])->withInput();
        }
    }

    /**
     * Show listing submission success page.
     */
    public function success($id)
    {
        $user = Auth::user();
        $listing = Listing::where('id', $id)
            ->where('seller_id', $user->id)
            ->with('images')
            ->firstOrFail();
        
        return view('Seller.listing-success', compact('listing'));
    }

    /**
     * Spec: pending or rejected-within-3-days are editable. Active/approved/sold are not.
     */
    private function sellerCanEditListing(Listing $listing): bool
    {
        $status = strtolower((string) $listing->status);
        if ($status === 'pending') {
            return true;
        }
        if ($status === 'rejected') {
            return $listing->canBeEdited();
        }

        return false;
    }

    private function editBlockedMessage(Listing $listing): string
    {
        $status = strtolower((string) $listing->status);
        if ($status === 'sold') {
            return 'Sold listings cannot be edited.';
        }
        if ($status === 'rejected') {
            return 'The 3-day window to edit this rejected submission has closed. Please submit a new listing.';
        }

        return 'Active listings cannot be edited. You can view or delete them, or contact support.';
    }

    /**
     * Detect which section has errors for better UX.
     */
    private function detectErrorSection($errors)
    {
        $section1Fields = ['make', 'model', 'year', 'vehicle_type', 'island', 'color', 'interior_color', 'vin', 'identifier_kind'];
        $section2Fields = ['title_status', 'is_salvaged', 'run_and_drive', 'engine_starts', 'keys_available', 'primary_damage', 'secondary_damage', 'cover_photo', 'photos', 'engine_video', 'additional_notes'];
        $section3Fields = ['auction_duration', 'starting_price', 'reserve_price', 'buy_now_price', 'cardholder_name', 'card_number', 'card_expiry', 'card_cvc', 'terms_accepted'];
        
        // Handle both MessageBag object and array
        $errorKeys = [];
        if (is_array($errors)) {
            $errorKeys = array_keys($errors);
        } elseif (method_exists($errors, 'keys')) {
            $errorKeys = $errors->keys();
        } elseif (method_exists($errors, 'toArray')) {
            $errorKeys = array_keys($errors->toArray());
        } else {
            // Fallback: try to get keys from the object
            $errorKeys = array_keys((array) $errors);
        }
        
        foreach ($errorKeys as $key) {
            // Handle array keys like 'photos.0', 'photos.1', etc.
            $baseKey = explode('.', $key)[0];
            
            if (in_array($baseKey, $section1Fields)) return 'section1';
            if (in_array($baseKey, $section2Fields)) return 'section2';
            if (in_array($baseKey, $section3Fields)) return 'section3';
        }
        
        return 'section1'; // Default
    }

    /**
     * Decode VIN/HIN via AJAX.
     */
    public function decodeVinHin(SellerDecodeVinHinRequest $request)
    {
        return ListingVinDecodeOps::decode($request);
    }

    public function getModels($make)
    {
        // Map of car makes to their models
        $modelsByMake = [
            'Acura' => ['MDX', 'RDX', 'TLX', 'ILX', 'NSX', 'ZDX'],
            'Audi' => ['A1', 'A3', 'A4', 'A5', 'A6', 'A7', 'A8', 'Q3', 'Q5', 'Q7', 'Q8', 'TT', 'R8'],
            'BMW' => ['1 Series', '2 Series', '3 Series', '4 Series', '5 Series', '6 Series', '7 Series', 'X1', 'X3', 'X4', 'X5', 'X6', 'X7', 'Z4'],
            'Ford' => ['Fiesta', 'Focus', 'Fusion', 'Mustang', 'Escape', 'Edge', 'Explorer', 'Expedition', 'Ranger', 'F-150', 'F-250'],
            'Honda' => ['Fit', 'Jazz', 'Civic', 'Accord', 'CR-V', 'HR-V', 'BR-V', 'Freed', 'Brio', 'N-One', 'N-Box'],
            'Toyota' => ['Vitz', 'Yaris', 'Passo', 'Porte', 'Corolla', 'Camry', 'Aqua', 'Prius', 'RAV4', 'Hilux', 'Land Cruiser', 'HiAce', '86'],
        ];

        // Normalize make input (case-insensitive)
        $formattedMake = ucfirst(strtolower(trim($make)));

        // Get models or return empty array if make not found
        $models = $modelsByMake[$formattedMake] ?? [];

        // Return JSON response
        return response()->json([
            'success' => true,
            'make' => $formattedMake,
            'models' => $models
        ]);
    }


    public function showListing(Request $request)
    {
        $user = $request->user();
        $query = Listing::where('seller_id', $user->id)->with('images');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('make', 'like', '%' . $request->search . '%')
                    ->orWhere('model', 'like', '%' . $request->search . '%')
                    ->orWhere('year', 'like', '%' . $request->search . '%')
                    ->orWhere('item_number', 'like', '%' . $request->search . '%');
            });
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('listing_method')) {
            $query->where('listing_method', $request->listing_method);
        }

        $listings = $query->orderByDesc('created_at')->paginate(12);
        $counts = [
            'total' => Listing::where('seller_id', $user->id)->count(),
            'active' => Listing::where('seller_id', $user->id)->whereIn('status', ['active', 'pending'])->count(),
            'sold' => Listing::where('seller_id', $user->id)->where('status', 'sold')->count(),
        ];
        return view('Seller.Listing.index', compact('listings', 'counts'));
    }

    /**
     * Seller view: single listing preview with full vehicle details and analytics.
     */
    public function show($id)
    {
        $user = Auth::user();
        $listing = Listing::with('images')->where('seller_id', $user->id)->findOrFail($id);

        $totalBids = $listing->bids()->count();
        $viewCount = $listing->view_count ?? 0;

        $endDate = $listing->auction_end_time
            ? \Carbon\Carbon::parse($listing->auction_end_time)
            : \Carbon\Carbon::parse($listing->auction_start_time ?? $listing->created_at)->addDays($listing->auction_duration ?? 7);
        $isExpired = $endDate->isPast();
        $timeRemaining = !$isExpired ? now()->diff($endDate) : null;

        $highestBid = $listing->bids()->where('status', 'active')->orderByDesc('amount')->first();
        $currentBid = $highestBid ? (float) $highestBid->amount : (float) ($listing->starting_price ?? $listing->price ?? 0);

        return view('Seller.Listing.show', compact(
            'listing',
            'totalBids',
            'viewCount',
            'endDate',
            'isExpired',
            'timeRemaining',
            'currentBid'
        ));
    }

    /**
     * Edit listing – same form as add listing, pre-filled (submit-listing-new in edit mode).
     * Per "Notes for System Issues":
     *  - Active / approved / sold listings are NOT editable (View + Delete only).
     *  - Rejected listings are editable only within 3 days (72h) of rejection.
     *  - Pending listings (awaiting admin approval) remain editable.
     */
    public function edit($id)
    {
        $user = Auth::user();
        $listing = Listing::where('seller_id', $user->id)->with('images')->findOrFail($id);

        if (! $this->sellerCanEditListing($listing)) {
            return redirect()
                ->route('seller.listings.show', $listing->id)
                ->with('error', $this->editBlockedMessage($listing));
        }

        $maxYear = (int) date('Y') + 1;
        // Casual (Individual) seller = no business license on file.
        $isIndividualSeller = empty($user->business_license_path);

        return view('Seller.submit-listing-new', compact('listing', 'user', 'maxYear', 'isIndividualSeller'));
    }

    /**
     * Update listing from edit form.
     */
    public function update(SellerListingUpdateRequest $request, $id)
    {
        $user = Auth::user();
        $listing = Listing::where('seller_id', $user->id)->findOrFail($id);

        if (! $this->sellerCanEditListing($listing)) {
            return redirect()
                ->route('seller.listings.show', $listing->id)
                ->with('error', $this->editBlockedMessage($listing));
        }

        $validated = $request->validated();
        if ($request->starting_price && $request->starting_price <= 0) {
            return back()->withErrors(['starting_price' => 'Starting Bid must be greater than $0 if entered.'])->withInput();
        }
        if ($request->reserve_price && $request->starting_price && $request->reserve_price < $request->starting_price) {
            return back()->withErrors(['reserve_price' => 'Reserve Price must be greater than or equal to Starting Bid.'])->withInput();
        }
        if (!$request->boolean('vin_decode_success')) {
            $validated['vin'] = null;
        }
        $listing->updateFromSellerInput($request, $validated);
        return redirect()->route('seller.listings.show', $listing->id)->with('success', 'Listing updated successfully.');
    }

    /**
     * Delete a listing (only allowed for own listing and when not sold).
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $listing = Listing::where('seller_id', $user->id)->findOrFail($id);
        if ($listing->status === 'sold') {
            return redirect()->route('seller.auctions')->with('error', 'Cannot delete a sold listing.');
        }
        if ($listing->listing_method === 'auction' && $listing->status === 'approved') {
            try {
                (new \App\Services\NotificationService())->auctionClosedBySeller($user, $listing);
            } catch (\Throwable $e) {
            }
        }
        $listing->delete();
        return redirect()->route('seller.auctions')->with('success', 'Listing removed.');
    }

  public function showAuctionLisitng(Request $request)
{
    $query = Listing::where('listing_method', 'auction')
        ->with('images');

    // Apply search
    if ($request->has('search') && $request->search != '') {
        $query->where(function($q) use ($request) {
            $q->where('make', 'like', '%' . $request->search . '%')
              ->orWhere('model', 'like', '%' . $request->search . '%')
              ->orWhere('year', 'like', '%' . $request->search . '%');
        });
    }

    // Paginate results
    $listings = $query->paginate(9); // 9 per page (3 per row)

    return view('Seller.Auction.index', compact('listings'));
}


    /**
     * Dedicated Buy Now page: show listing with fixed price and purchase CTA.
     * Guests see "Log in / Register to purchase"; buyers see "Purchase now" button.
     */
    public function buyNowGuest(Request $request, $listingId)
    {
        $listing = Listing::with('images')->findOrFail($listingId);
        if ($listing->listing_method !== 'buy_now') {
            return redirect()->route('listing.show', $listing)->with('info', 'This listing is not a Buy Now item.');
        }
        return view('buy-now', compact('listing'));
    }

    public function showBuyerDashboard(Request $request)
    {
        // Get the selected listing from session if it exists
        $selectedListing = $request->session()->get('selected_listing');

        // Pass it to view
        // $view = view('buyer.dashboard', compact('selectedListing'));

        // Remove the session after showing it once
        $request->session()->forget('selected_listing');

        return $view;
    }

    public function addToWatchlist(Request $request, $listingId)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Please log in to use watchlist.');
        }

        $plan = $user->activePlan(); // standard or casual
        $limit = $plan === 'standard' ? 50 : 5;

        // Assume you have watchlist relation
        $watchlistCount = $user->watchlist()->count();

        if ($watchlistCount >= $limit) {
            return back()->with('error', "You have reached the watchlist limit of $limit items for your plan.");
        }

        // Add listing to watchlist
        $user->watchlist()->attach($listingId);

        return back()->with('success', 'Listing added to watchlist.');
    }

    public function listingDetailBuyer($id, $slug = null)
{
    $listing = Listing::with('images')->findOrFail($id);

    $expectedSlug = Str::slug("{$listing->year} {$listing->make} {$listing->model}");

    // Redirect to correct SEO-friendly URL if slug mismatch
    if ($slug !== $expectedSlug) {
        return redirect()->route('listing.show', [
            'id'   => $listing->id,
            'slug' => $expectedSlug,
        ]);
    }

    return view('Buyer.ListingDetail', compact('listing'));
}

}
