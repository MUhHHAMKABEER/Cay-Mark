<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;


class MarketplaceController extends Controller
{


public function index(Request $request)
{
    // Base query: only buy_now listings
    $query = Listing::with('images')->where('listing_method', 'buy_now');

    // If you added a status column and want to show only approved
    if (Schema::hasColumn('listings', 'status')) {
        $query->where('status', 'approved');
    }

    // Helper to normalize incoming filter values to non-empty array
    $toArray = function($val) {
        if (is_null($val)) return [];
        if (is_array($val)) return array_values(array_filter($val, function($v){ return $v !== '' && $v !== null; }));
        // single value (string)
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

    // Apply only provided (non-empty) filters
    if (!empty($filters['types'])) {
        $query->whereIn('major_category', $filters['types']);
    }

    // IMPORTANT: Only apply make filter if makes were actually selected.
    if (!empty($filters['makes'])) {
        $query->whereIn('make', $filters['makes']);
    }

    // Apply model filter if provided — this will work even if make is not selected.
    if (!empty($filters['models'])) {
        $query->whereIn('model', $filters['models']);
    }

    if (!empty($filters['locations']) && Schema::hasColumn('listings', 'location')) {
        $query->whereIn('location', $filters['locations']);
    }

    if (!empty($filters['colors'])) {
        $query->whereIn('color', $filters['colors']);
    }

    if (!empty($filters['primary_damage'])) {
        $query->whereIn('primary_damage', $filters['primary_damage']);
    }

    if (!empty($filters['secondary_damage'])) {
        $query->whereIn('secondary_damage', $filters['secondary_damage']);
    }

    if (!empty($filters['transmission'])) {
        $query->whereIn('transmission', $filters['transmission']);
    }

    if (!empty($filters['title_condition'])) {
        $query->whereIn('title_status', $filters['title_condition']);
    }

    // Year range
    if ($yearFrom !== null && $yearFrom !== '') {
        $query->where('year', '>=', $yearFrom);
    }
    if ($yearTo !== null && $yearTo !== '') {
        $query->where('year', '<=', $yearTo);
    }

    // Odometer range (only if column exists)
    if (Schema::hasColumn('listings', 'odometer')) {
        if ($odoMin !== null && $odoMin !== '') {
            $query->where('odometer', '>=', (int)$odoMin);
        }
        if ($odoMax !== null && $odoMax !== '') {
            $query->where('odometer', '<=', (int)$odoMax);
        }
    }

    // Paginate & keep querystring
    // $listings = $query->orderBy('created_at', 'desc')->paginate(12)->appends($request->query());
    $listings = $query
    ->where('listing_state', 'active')   // ✅ add condition here
    ->orderBy('created_at', 'desc')
    ->paginate(12)
    ->appends($request->query());


    // Build filter option lists defensively (only if column exists)
    $filterOptions = [
        'types' => Listing::select('major_category')->distinct()->pluck('major_category')->filter()->values(),
        'makes' => Listing::select('make')->distinct()->pluck('make')->filter()->values(),
        'models' => Listing::select('model')->distinct()->pluck('model')->filter()->values(),
        'colors' => Listing::select('color')->distinct()->pluck('color')->filter()->values(),
        'primary_damage' => Listing::select('primary_damage')->distinct()->pluck('primary_damage')->filter()->values(),
        'secondary_damage' => Listing::select('secondary_damage')->distinct()->pluck('secondary_damage')->filter()->values(),
        'transmission' => Listing::select('transmission')->distinct()->pluck('transmission')->filter()->values(),
        'title_status' => Listing::select('title_status')->distinct()->pluck('title_status')->filter()->values(),
        'locations' => Schema::hasColumn('listings', 'location') ? Listing::select('location')->distinct()->pluck('location')->filter()->values() : collect(),
        'years' => Listing::select('year')->distinct()->pluck('year')->filter()->sortDesc()->values(),
    ];

    return view('marketplace', compact('listings', 'filterOptions'));
}

}
