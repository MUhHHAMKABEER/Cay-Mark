<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Services\Seller\SellerDashboardService;
use App\Repositories\Seller\SellerRepository;
use App\Models\Listing;
use App\Models\SellerPayoutMethod;
use Illuminate\Http\Request;
use App\Services\Seller\SellerDashboardOps;
use App\Http\Requests\SellerDashboardUpdatePayoutRequest;
use App\Http\Requests\SellerDashboardChangePasswordRequest;
use App\Http\Requests\SellerDashboardConfirmPickupRequest;
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
    public function updatePayout(SellerDashboardUpdatePayoutRequest $request)
    {
        return SellerDashboardOps::updatePayout($request, $this->repository);
    }

    /**
     * Change password
     */
    public function changePassword(SellerDashboardChangePasswordRequest $request)
    {
        return SellerDashboardOps::changePassword($request);
    }

    /**
     * Confirm pickup with PIN
     */
    public function confirmPickup(SellerDashboardConfirmPickupRequest $request, $listingId)
    {
        return SellerDashboardOps::confirmPickup($request, $listingId, $this->repository);
    }
}

