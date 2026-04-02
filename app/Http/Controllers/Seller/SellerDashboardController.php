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
use App\Http\Requests\SellerDashboardUpdateEmailRequest;
use App\Http\Requests\SellerDashboardUpdatePhoneRequest;
use App\Services\EmailChangeVerificationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
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
     * Legacy entry (query ?tab=) — prefer named tab routes under /seller/*
     */
    public function index(Request $request)
    {
        return $this->renderSellerDashboard($request->query('tab', 'dashboard'));
    }

    public function dashboard()
    {
        return $this->renderSellerDashboard('dashboard');
    }

    public function account()
    {
        return $this->renderSellerDashboard('user');
    }

    public function auctions()
    {
        return $this->renderSellerDashboard('auctions');
    }

    public function submission()
    {
        return $this->renderSellerDashboard('submission');
    }

    public function notifications()
    {
        return $this->renderSellerDashboard('notifications');
    }

    public function support()
    {
        return $this->renderSellerDashboard('support');
    }

    protected function renderSellerDashboard(string $activeTab)
    {
        $user = Auth::user();
        $dashboardData = $this->dashboardService->getDashboardData($user);

        return view('dashboard.seller', array_merge([
            'user' => $user,
            'activeTab' => $activeTab,
        ], $dashboardData));
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
     * Update email (verification code sent to old email first)
     */
    public function updateEmail(SellerDashboardUpdateEmailRequest $request)
    {
        $request->validated();
        $user = $request->user();
        $service = new EmailChangeVerificationService();

        if ($request->filled('code')) {
            $ok = $service->verifyAndUpdateEmail($user, $request->input('code'));
            if (!$ok) {
                return back()->withErrors(['code' => 'Invalid or expired verification code. Please request a new code.'])->withInput();
            }
            return back()->with('success', 'Email address updated successfully. Please verify your new email when you receive the link.');
        }

        try {
            $service->sendCodeToOldEmail($user, $request->input('email'));
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
        return back()->with('success', 'A verification code has been sent to your current email address. Enter the code below to confirm the change.')
            ->with('email_change_pending', true)->with('email_change_new', $request->input('email'));
    }

    /**
     * Update phone number
     */
    public function updatePhone(SellerDashboardUpdatePhoneRequest $request)
    {
        $request->user()->update([
            'phone' => $request->input('phone') ?: null,
        ]);
        return back()->with('success', 'Phone number updated successfully.');
    }

    /**
     * Confirm pickup with PIN
     */
    public function confirmPickup(SellerDashboardConfirmPickupRequest $request, $listingId)
    {
        return SellerDashboardOps::confirmPickup($request, $listingId, $this->repository);
    }
}

