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
     * Display the buyer dashboard with all tabs data
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

        return view('dashboard.buyer', $data);
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

