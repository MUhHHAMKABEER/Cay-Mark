<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\User;
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
            'supportCategories' => SupportTicket::categoryOptionsForRole(User::ROLE_SELLER),
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
     * Step 1 of email change: validate password + send OTP to the NEW email address.
     */
    public function requestEmailChange(Request $request)
    {
        $request->validate([
            'new_email' => 'required|email|max:255',
            'password'  => 'required|string',
        ]);

        $user    = $request->user();
        $service = new EmailChangeVerificationService();

        try {
            $service->sendCodeToNewEmail($user, $request->input('new_email'), $request->input('password'));
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }

        return back()
            ->with('success', 'A 6-digit verification code has been sent to ' . $request->input('new_email') . '. Enter it below to confirm.')
            ->with('email_change_pending', true)
            ->with('email_change_new', $request->input('new_email'));
    }

    /**
     * Step 2 of email change: verify OTP and complete the update.
     */
    public function updateEmail(SellerDashboardUpdateEmailRequest $request)
    {
        $request->validated();
        $user    = $request->user();
        $service = new EmailChangeVerificationService();

        if ($request->filled('code')) {
            $ok = $service->verifyAndUpdateEmail($user, $request->input('code'));
            if (!$ok) {
                return back()
                    ->withErrors(['code' => 'Invalid or expired verification code. Please request a new one.'])
                    ->with('email_change_pending', true)
                    ->with('email_change_new', $service->getPendingNewEmail($user))
                    ->withInput();
            }
            try {
                (new \App\Services\NotificationService())->emailUpdated($user);
            } catch (\Throwable) {}

            return back()->with('success', 'Your email address has been updated successfully.');
        }

        // Legacy inline-form path (kept for backwards compatibility)
        try {
            $service->sendCodeToOldEmail($user, $request->input('email'));
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }

        return back()
            ->with('success', 'A verification code has been sent to your current email address.')
            ->with('email_change_pending', true)
            ->with('email_change_new', $request->input('email'));
    }

    /**
     * Cancel a pending email change.
     */
    public function cancelEmailChange(Request $request)
    {
        (new EmailChangeVerificationService())->cancelPendingChange($request->user());
        return back()->with('success', 'Email change cancelled.');
    }

    /**
     * Confirm pickup with PIN
     */
    public function confirmPickup(SellerDashboardConfirmPickupRequest $request, $listingId)
    {
        return SellerDashboardOps::confirmPickup($request, $listingId, $this->repository);
    }
}

