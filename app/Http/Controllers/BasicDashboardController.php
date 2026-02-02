<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\BasicDashboardUpdateEmailRequest;
use App\Http\Requests\BasicDashboardChangePasswordRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Services\BasicDashboardOps;

class BasicDashboardController extends Controller
{
    /**
     * Basic (Default) Dashboard - Shown before membership selection
     */
    public function index(): View
    {
        $user = Auth::user();
        
        // Get user documents
        $documents = $user->documents()->orderBy('created_at', 'desc')->get();
        
        // Get notifications
        $notifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return view('dashboard.default', compact('user', 'documents', 'notifications'));
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

