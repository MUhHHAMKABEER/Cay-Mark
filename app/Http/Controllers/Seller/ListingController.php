<?php

namespace App\Http\Controllers\Seller;

use App\Models\Listing;
use App\Models\ListingImage;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\Package;
use App\Services\VinHinDecoderService;
use App\Helpers\TextFormatter;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

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
        return view('Seller.submit-listing-new', compact('user'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $userPackage = $user->activeSubscription?->package;
        $isIndividualSeller = $userPackage && $userPackage->price == 25.00;

        // SECTION 1 VALIDATION - Vehicle Information
        $validated = $request->validate([
            // VIN/HIN (optional if manual entry)
            'vin' => 'nullable|string|max:17',
            
            // Manual fields (required if VIN decode fails)
            'make' => 'nullable|string',
            'model' => 'nullable|string',
            'year' => 'nullable|string',
            'trim' => 'nullable|string',
            'engine_size' => 'nullable|string',
            'cylinders' => 'nullable|string',
            'drive_type' => 'nullable|string',
            'fuel_type' => 'nullable|string',
            'transmission' => 'nullable|string',
            'vehicle_type' => 'nullable|string',
            
            // Required condition fields
            'title_status' => 'required|in:yes,no',
            'island' => 'required|string',
            'color' => 'required|string',
            'interior_color' => 'required|string',
            'primary_damage' => 'required|string',
            'keys_available' => 'required|in:yes,no',
            'secondary_damage' => 'nullable|string',
            'additional_notes' => 'nullable|string',
            
            // SECTION 2 - Photos
            'cover_photo' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'photos.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            
            // SECTION 3 - Auction Settings
            'auction_duration' => 'required|in:5,7,14,21,28',
            'starting_price' => 'nullable|numeric|min:0',
            'reserve_price' => 'nullable|numeric|min:0',
            'buy_now_price' => 'nullable|numeric|min:0',
            
            // Payment (Individual Sellers only)
            'payment_method' => $isIndividualSeller ? 'required|string' : 'nullable',
        ]);

        // Validate pricing rules
        if ($request->starting_price && $request->starting_price <= 0) {
            return back()->withErrors(['starting_price' => 'Starting Bid must be greater than $0 if entered.']);
        }
        if ($request->reserve_price && $request->starting_price && $request->reserve_price < $request->starting_price) {
            return back()->withErrors(['reserve_price' => 'Reserve Price must be greater than or equal to Starting Bid.']);
        }

        // Validate photos (cover + 5-10 additional)
        $photos = $request->file('photos', []);
        $totalPhotos = count($photos) + 1; // +1 for cover photo
        
        if ($totalPhotos < 6) {
            return back()->withErrors(['photos' => 'Minimum 5 additional photos required (plus cover photo).']);
        }
        if ($totalPhotos > 11) {
            return back()->withErrors(['photos' => 'Maximum 10 additional photos allowed (plus cover photo).']);
        }

        // Check for duplicate VIN if provided
        $duplicateVinFlag = false;
        if (!empty($validated['vin'])) {
            $vin = TextFormatter::toAllCaps($validated['vin']);
            $existingListing = Listing::where('vin', $vin)
                ->where('id', '!=', $request->id ?? 0)
                ->where('status', '!=', 'rejected')
                ->first();

            if ($existingListing) {
                $duplicateVinFlag = true;
            }
        }

        return DB::transaction(function () use ($request, $validated, $user, $isIndividualSeller, $duplicateVinFlag, $totalPhotos) {
            // Process payment for Individual Sellers ($25)
            if ($isIndividualSeller) {
                // TODO: Integrate with payment gateway (Stripe)
                // For now, create a payment record
                $payment = Payment::create([
                    'user_id' => $user->id,
                    'amount' => 25.00,
                    'method' => $request->payment_method ?? 'credit_card',
                    'status' => 'completed', // Will be updated after payment gateway confirmation
                ]);
            }

            // Calculate auction end date
            $auctionDuration = (int) $validated['auction_duration'];
            $expiresAt = now()->addDays($auctionDuration);

            // Create listing with status PENDING (per PDF requirements)
            $listing = Listing::create([
                'seller_id' => $user->id,
                'listing_method' => 'auction', // All listings are auctions per PDF
                'auction_duration' => $auctionDuration,
                'make' => $validated['make'] ?? null,
                'model' => $validated['model'] ?? null,
                'trim' => $validated['trim'] ?? null,
                'year' => $validated['year'] ?? null,
                'vin' => !empty($validated['vin']) ? TextFormatter::toAllCaps($validated['vin']) : null,
                'duplicate_vin_flag' => $duplicateVinFlag,
                'color' => $validated['color'],
                'interior_color' => $validated['interior_color'],
                'island' => $validated['island'],
                'fuel_type' => $validated['fuel_type'] ?? null,
                'transmission' => $validated['transmission'] ?? null,
                'title_status' => $validated['title_status'] === 'yes' ? 'CLEAN' : 'SALVAGE',
                'primary_damage' => $validated['primary_damage'],
                'secondary_damage' => $validated['secondary_damage'] ?? null,
                'keys_available' => $validated['keys_available'] === 'yes',
                'engine_type' => $validated['engine_size'] ?? null,
                'starting_price' => $validated['starting_price'] ?? null,
                'reserve_price' => $validated['reserve_price'] ?? null,
                'buy_now_price' => $validated['buy_now_price'] ?? null,
                'status' => 'pending', // PENDING APPROVAL per PDF
                'expires_at' => $expiresAt,
                'listing_state' => 'active',
            ]);

            // Handle Cover Photo (first image)
            $coverPhotoId = null;
            if ($request->hasFile('cover_photo')) {
                $coverPhoto = $request->file('cover_photo');
                $newFileName = 'COVER_' . microtime(true) . '_' . uniqid() . '.' . $coverPhoto->getClientOriginalExtension();
                
                if ($coverPhoto->move(public_path('uploads/listings'), $newFileName)) {
                    $coverImage = ListingImage::create([
                        'listing_id' => $listing->id,
                        'image_path' => $newFileName,
                    ]);
                    $coverPhotoId = $coverImage->id;
                }
            }

            // Handle Additional Photos (preserve order)
            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $index => $photo) {
                    $newFileName = 'LISTING_IMG_' . ($index + 1) . '_' . microtime(true) . '_' . uniqid() . '.' . $photo->getClientOriginalExtension();
                    
                    if (!$photo->move(public_path('uploads/listings'), $newFileName)) {
                        $listing->delete();
                        return back()->with('failure', 'Failed to upload one or more photos.');
                    }
                    
                    ListingImage::create([
                        'listing_id' => $listing->id,
                        'image_path' => $newFileName,
                    ]);
                }
            }

            // Update listing with cover photo ID
            if ($coverPhotoId) {
                $listing->cover_photo_id = $coverPhotoId;
                $listing->save();
            }

            // Send confirmation email
            try {
                Mail::send('emails.listing-submitted', [
                    'listing' => $listing,
                    'user' => $user,
                ], function ($message) use ($user, $listing) {
                    $message->to($user->email, $user->name)
                        ->subject('Listing Submitted for Review – ' . ($listing->year ?? '') . ' ' . ($listing->make ?? '') . ' ' . ($listing->model ?? '[VEHICLE_NAME]'));
                });
                
                // Send in-app notification
                $notificationService = new \App\Services\NotificationService();
                $notificationService->listingSubmitted($user, $listing);
            } catch (\Exception $e) {
                // Log error but don't fail submission
                \Log::error('Failed to send listing submission email: ' . $e->getMessage());
            }

            return redirect()->route('seller.listings')
                ->with('success', 'YOUR LISTING HAS BEEN SUBMITTED FOR REVIEW.');
        });
    }

    /**
     * Decode VIN/HIN via AJAX.
     */
    public function decodeVinHin(Request $request)
    {
        $request->validate([
            'vin_hin' => 'required|string|max:17',
        ]);

        $decoder = new VinHinDecoderService();
        $result = $decoder->decode($request->vin_hin);

        if ($result['success']) {
            $formatted = $decoder->formatDecodedData($result['data']);
            return response()->json([
                'success' => true,
                'data' => $formatted,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message'],
        ]);
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


    public function showListing()
    {
        // Fetch only listings for the logged-in seller
        $products = Listing::where('listing_method', 'buy_now')->with('images')->get();
        // dd($products);
        return view('Seller.Listing.index', compact('products'));
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


    public function buyNowGuest(Request $request, $listingId)
    {
        // Store the listing ID or object in session
        $listing = Listing::find($listingId);
        $request->session()->put('selected_listing', [
            'id' => $listing->id,
            'make' => $listing->make,
            'model' => $listing->model,
            'price' => $listing->price,
            'location' => $listing->location,
            'image' => $listing->image ?? 'https://via.placeholder.com/80',
        ]);

        // Debug session right here
        // dd($request->session()->get('selected_listing')); // <- This will show session and stop execution

        // Normally, redirect to registration page
        return redirect()->route('register', ['role' => 'buyer']);
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

    public function show($id)
    {

        $listing = Listing::with('images')->findOrFail($id);

        return view('showDetail', compact('listing'));
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
