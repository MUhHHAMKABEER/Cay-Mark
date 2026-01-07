<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Services\Seller\SellerDashboardService;
use App\Repositories\Seller\SellerRepository;
use App\Models\Listing;
use App\Models\SellerPayoutMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SellerDashboardController extends Controller
{
    protected $dashboardService;
    protected $repository;

    public function __construct(SellerDashboardService $dashboardService, SellerRepository $repository)
    {
        $this->dashboardService = $dashboardService;
        $this->repository = $repository;
    }

    /**
     * Display the seller dashboard with all tabs data
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $activeTab = $request->get('tab', 'user');

        // Get all data using service
        $dashboardData = $this->dashboardService->getDashboardData($user);

        $data = array_merge([
            'user' => $user,
            'activeTab' => $activeTab,
        ], $dashboardData);

        return view('dashboard.seller', $data);
    }


    /**
     * Update payout settings
     */
    public function updatePayout(Request $request)
    {
        $request->validate([
            'bank_name' => 'required|string|max:255',
            'account_holder_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'routing_number' => 'nullable|string|max:255',
            'swift_number' => 'nullable|string|max:255',
            'country' => 'required|string|max:255',
        ]);

        $user = Auth::user();

        // Deactivate existing payout methods
        SellerPayoutMethod::where('user_id', $user->id)
            ->update(['is_active' => false]);

        // Use repository to save payout method
        $this->repository->savePayoutMethod($user, [
            'bank_name' => $request->bank_name,
            'account_holder_name' => $request->account_holder_name,
            'account_number' => $request->account_number,
            'routing_number' => $request->routing_number,
            'swift_number' => $request->swift_number,
            'is_active' => true,
        ]);

        return back()->with('success', 'Payout settings updated successfully.');
    }

    /**
     * Change password
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();
        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('success', 'Password changed successfully.');
    }

    /**
     * Confirm pickup with PIN
     */
    public function confirmPickup(Request $request, $listingId)
    {
        $request->validate([
            'pickup_pin' => 'required|string',
        ]);

        $user = Auth::user();
        $listing = $this->repository->getListingById($user, $listingId);

        if (!$listing) {
            return back()->withErrors(['pickup_pin' => 'Listing not found.']);
        }

        if ($listing->pickup_pin !== $request->pickup_pin) {
            return back()->withErrors(['pickup_pin' => 'Invalid pickup PIN.']);
        }

        $listing->pickup_confirmed = true;
        $listing->pickup_confirmed_at = now();
        $listing->pickup_confirmed_by = $user->id;
        $listing->save();

        // Trigger payout processing
        // This would typically be handled by a service or event

        return back()->with('success', 'Pickup confirmed successfully. Payment processing has begun.');
    }
}

