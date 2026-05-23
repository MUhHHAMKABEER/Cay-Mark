<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\BasicDashboardUpdateEmailRequest;
use App\Http\Requests\BasicDashboardChangePasswordRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Services\BasicDashboardOps;

class BasicDashboardController extends Controller
{
    private const VALID_TABS = ['dashboard', 'account', 'notifications', 'support'];

    /**
     * Basic (Default) Dashboard - Shown before membership selection.
     * Supports ?tab=dashboard|account|notifications|support query param.
     */
    public function index(Request $request): View|RedirectResponse
    {
        $user = Auth::user();

        // If registration is already complete, redirect to appropriate dashboard
        if ($user->isRegistrationComplete()) {
            $role = strtolower(trim($user->role ?? ''));
            if ($role === 'seller') return redirect()->route('seller.dashboard');
            if ($role === 'buyer')  return redirect()->route('welcome');
            if ($role === 'admin')  return redirect()->route('admin.dashboard');
        }

        $activeTab = in_array($request->query('tab'), self::VALID_TABS)
            ? $request->query('tab')
            : 'dashboard';

        // Get user documents
        $documents = $user->documents()->orderBy('created_at', 'desc')->get();

        // Get notifications (newest first; welcome system notifications included)
        $notifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->limit(30)
            ->get();

        return view('dashboard.default', compact('user', 'documents', 'notifications', 'activeTab'));
    }

    /**
     * Update email address (allowed in basic dashboard)
     */
    public function updateEmail(BasicDashboardUpdateEmailRequest $request)
    {
        return BasicDashboardOps::updateEmail($request);
    }

    /**
     * Change password (allowed in basic dashboard)
     */
    public function changePassword(BasicDashboardChangePasswordRequest $request)
    {
        return BasicDashboardOps::changePassword($request);
    }
}

