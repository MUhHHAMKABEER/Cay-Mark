<?php

namespace App\Http\Controllers\Seller;

use App\Models\Listing;
use Illuminate\Support\Str;
use App\Models\ListingImage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

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

        return view('Seller.submit-listing', compact('makes'));
    }

    public function store(Request $request)
    {
        // Validate
        $request->validate([
            'listing_method' => 'required|in:buy_now,auction',
            'auction_duration' => 'nullable|integer',
            'major_category' => 'required|string',
            'condition' => 'required|string',
            'make' => 'nullable|string',
            'model' => 'nullable|string',
            'year' => 'nullable|string',
            'color' => 'nullable|string',
            'fuel_type' => 'nullable|string',
            'transmission' => 'nullable|string',
            'title_status' => 'nullable|string',
            'primary_damage' => 'nullable|string',
            'secondary_damage' => 'nullable|string',
            'keys_available' => 'required|string',
            'price' => 'nullable|numeric|min:0',   // ✅ new validation
            'odometer' => 'nullable|integer|min:0', // ✅ new validation
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        // Create listing
        $listing = Listing::create([
            'seller_id' => Auth::id(),
            'listing_method' => $request->listing_method,
            'auction_duration' => $request->auction_duration,
            'major_category' => $request->major_category,
            'subcategory' => $request->subcategory,
            'other_make' => $request->other_make,
            'other_model' => $request->other_model,
            'condition' => $request->condition,
            'make' => $request->make,
            'model' => $request->model,
            'trim' => $request->trim,
            'year' => $request->year,
            'color' => $request->color,
            'fuel_type' => $request->fuel_type,
            'transmission' => $request->transmission,
            'title_status' => $request->title_status,
            'primary_damage' => $request->primary_damage,
            'secondary_damage' => $request->secondary_damage,
            'keys_available' => $request->keys_available === 'yes',
            'engine_type' => $request->engine_type,
            'hull_material' => $request->hull_material,
            'category_type' => $request->category_type,
            'price' => $request->price,          // ✅ storing price
            'odometer' => $request->odometer,    // ✅ storing odometer
            'expires_at' => now()->addDays(30),
            'listing_state' => 'active',
        ]);

        // dd($listing);6

        // Handle Images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                // Generate unique filename
                $newFileName = 'LISTING_IMG_'.microtime(true).'_'.uniqid().'.'.$image->getClientOriginalExtension();

                // Try moving the file to public/uploads/listings
                if ($image->move(public_path('uploads/listings'), $newFileName)) {
                    ListingImage::create([
                        'listing_id' => $listing->id,
                        'image_path' => $newFileName,
                    ]);
                } else {
                    // Rollback listing if any image fails
                    $listing->delete();

                    return back()->with('failure', 'Failed to upload one or more images.');
                }
            }
        }

        return redirect()->back()->with('success', 'Listing created successfully!');
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
