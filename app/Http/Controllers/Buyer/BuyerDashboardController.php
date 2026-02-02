<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Services\Buyer\BuyerDashboardService;
use App\Services\InvoiceService;
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
     * Display the buyer dashboard with all tabs (Dashboard Overview, User, Auctions, etc.)
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $tab = $request->get('tab', 'dashboard');

        if ($tab === 'auctions') {
            $invoiceService = new InvoiceService();
            $invoices = $invoiceService->getBuyerInvoices($user);
            return view('Buyer.auctions-won', compact('invoices'));
        }

        $dashboardData = $this->dashboardService->getDashboardData($user);

        $data = array_merge(
            ['user' => $user, 'activeTab' => $tab],
            $dashboardData
        );

        return view('dashboard.buyer', $data);
    }

    /**
     * Display USER page - Account Information
     */
    public function user()
    {
        $user = Auth::user();
        return view('buyer.user', compact('user'));
    }

    /**
     * Display AUCTIONS page
     */
    public function auctions(Request $request)
    {
        $user = Auth::user();
        $section = $request->get('section', 'current'); // current, won, lost

        $currentAuctions = $this->dashboardService->getCurrentAuctions($user);
        $wonAuctions = $this->dashboardService->getWonAuctions($user);
        $lostAuctions = $this->dashboardService->getLostAuctions($user);
        
        // Get chart data
        $biddingActivityData = $this->dashboardService->getBiddingActivityData($user);
        $spendingTrendsData = $this->dashboardService->getSpendingTrendsData($user);
        $winLossRatioData = $this->dashboardService->getWinLossRatioData($user);

        return view('buyer.auctions', compact('user', 'currentAuctions', 'wonAuctions', 'lostAuctions', 'section', 
            'biddingActivityData', 'spendingTrendsData', 'winLossRatioData'));
    }

    /**
     * Display SAVED ITEMS page
     */
    public function savedItems()
    {
        $user = Auth::user();
        $savedItems = $this->dashboardService->getSavedItems($user);
        return view('buyer.saved-items', compact('user', 'savedItems'));
    }

    /**
     * Display NOTIFICATIONS page
     */
    public function notifications()
    {
        $user = Auth::user();
        $notifications = $this->dashboardService->getNotifications($user);
        return view('buyer.notifications', compact('user', 'notifications'));
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

