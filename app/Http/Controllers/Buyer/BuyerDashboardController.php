<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Services\Buyer\BuyerDashboardService;
use App\Services\Buyer\BuyerDashboardOps;
use App\Repositories\Buyer\BuyerRepository;
use Illuminate\Http\Request;
use App\Http\Requests\BuyerDashboardUpdateEmailRequest;
use App\Http\Requests\BuyerDashboardChangePasswordRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class BuyerDashboardController extends Controller
{
    protected $dashboardService;
    protected $repository;

    public function __construct(BuyerDashboardService $dashboardService, BuyerRepository $repository)
    {
        $this->dashboardService = $dashboardService;
        $this->repository = $repository;
    }

    /**
     * Legacy /dashboard/buyer?tab= → use named routes under /buyer/*
     */
    public function index(Request $request)
    {
        return $this->renderUnifiedBuyerDashboard($request, $request->get('tab', 'dashboard'));
    }

    protected function renderUnifiedBuyerDashboard(Request $request, string $activeTab)
    {
        $user = Auth::user();
        $dashboardData = $this->dashboardService->getDashboardData($user);

        return view('dashboard.buyer', array_merge(
            ['user' => $user, 'activeTab' => $activeTab],
            $dashboardData
        ));
    }

    /**
     * Dashboard overview (main tab)
     */
    public function dashboardOverview(Request $request)
    {
        return $this->renderUnifiedBuyerDashboard($request, 'dashboard');
    }

    /**
     * Account settings tab
     */
    public function user(Request $request)
    {
        return $this->renderUnifiedBuyerDashboard($request, 'user');
    }

    /**
     * Auctions tab (current / won / lost — ?section=)
     */
    public function auctions(Request $request)
    {
        return $this->renderUnifiedBuyerDashboard($request, 'auctions');
    }

    /**
     * Saved items tab
     */
    public function savedItems(Request $request)
    {
        return $this->renderUnifiedBuyerDashboard($request, 'saved');
    }

    /**
     * Notifications tab
     */
    public function notifications(Request $request)
    {
        return $this->renderUnifiedBuyerDashboard($request, 'notifications');
    }

    /**
     * Messaging center tab
     */
    public function messagingCenter(Request $request)
    {
        return $this->renderUnifiedBuyerDashboard($request, 'messaging');
    }

    /**
     * Customer support tab
     */
    public function customerSupport(Request $request)
    {
        return $this->renderUnifiedBuyerDashboard($request, 'support');
    }


    /**
     * Update email address
     */
    public function updateEmail(BuyerDashboardUpdateEmailRequest $request)
    {
        return BuyerDashboardOps::updateEmail($request);
    }

    /**
     * Change password
     */
    public function changePassword(BuyerDashboardChangePasswordRequest $request)
    {
        return BuyerDashboardOps::changePassword($request);
    }
}

