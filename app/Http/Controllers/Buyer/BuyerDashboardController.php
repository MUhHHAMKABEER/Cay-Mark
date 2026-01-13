<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Services\Buyer\BuyerDashboardService;
use App\Repositories\Buyer\BuyerRepository;
use Illuminate\Http\Request;
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
     * Display the buyer dashboard with all tabs data (legacy - redirects to user)
     */
    public function index(Request $request)
    {
        return redirect()->route('buyer.user');
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

        return view('buyer.auctions', compact('user', 'currentAuctions', 'wonAuctions', 'lostAuctions', 'section'));
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
    public function updateEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email,' . Auth::id(),
        ]);

        $user = Auth::user();
        $user->email = $request->email;
        $user->email_verified_at = null;
        $user->save();

        return back()->with('success', 'Email address updated successfully.');
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
}

