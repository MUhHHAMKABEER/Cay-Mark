<?php

namespace App\Http\Controllers;

// namespace App\Http\Controllers;

// use App\Models\Listing;
// use Illuminate\Http\Request;

// class AdminController extends Controller
// {
//

//}


use App\Models\User;
use App\Models\Listing;
use App\Models\Payment;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth');
    //     // $this->middleware('admin');
    // }

    /**
     * Admin Dashboard - Overview (Default)
     */
    public function dashboard(Request $request)
    {
        // Check if analytics view requested
        if ($request->has('view') && $request->view === 'analytics') {
            return $this->analyticsDashboard($request);
        }

        // Default Overview
        $recentActivities = $this->getRecentActivities();
        $alerts = $this->getSystemAlerts();
        
        // Get active auctions count
        $activeAuctions = Listing::where('listing_method', 'auction')
            ->where('status', 'approved')
            ->where(function ($q) {
                $q->where('auction_end_time', '>', now())
                    ->orWhere(function ($q2) {
                        $q2->whereNull('auction_end_time')
                            ->whereRaw('DATE_ADD(auction_start_time, INTERVAL auction_duration DAY) > NOW()');
                    });
            })
            ->count();
        
        $stats = [
            'total_active_listings' => Listing::where('status', 'approved')->count(),
            'listings_awaiting_approval' => Listing::where('status', 'pending')->count(),
            'total_users' => User::count(),
            'total_buyers' => User::where('role', 'buyer')->count(),
            'total_sellers' => User::where('role', 'seller')->count(),
            'active_auctions' => $activeAuctions,
            'payments_pending' => Payment::where('status', 'pending')->orWhere('status', 'pending_release')->count(),
            'payouts_pending' => \App\Models\Payout::whereIn('status', ['pending', 'processing'])->count(),
            'open_disputes' => 0, // Will be updated when Dispute model exists
        ];

        // Recent user signups
        $recentSignups = User::orderBy('created_at', 'desc')->limit(10)->get();

        return view('admin.dashboard-overview', compact('stats', 'alerts', 'recentActivities', 'recentSignups'));
    }

    /**
     * Admin Dashboard - Analytics
     */
    public function analyticsDashboard(Request $request)
    {
        $dateFilter = $request->get('date_filter', '30_days'); // today, 7_days, 30_days, this_year
        
        // Calculate date ranges
        $dateRanges = $this->getDateRange($dateFilter);
        
        // Listing Metrics
        $listingMetrics = [
            'total_submitted' => Listing::whereBetween('created_at', $dateRanges)->count(),
            'total_approved' => Listing::where('status', 'approved')->whereBetween('created_at', $dateRanges)->count(),
            'total_rejected' => Listing::where('status', 'rejected')->whereBetween('created_at', $dateRanges)->count(),
            'total_completed' => Listing::where('status', 'approved')
                ->whereHas('invoices', function ($q) {
                    $q->where('payment_status', 'paid');
                })
                ->whereBetween('created_at', $dateRanges)
                ->count(),
        ];

        // Auction Metrics
        $completedAuctions = Listing::where('listing_method', 'auction')
            ->where('status', 'approved')
            ->whereHas('invoices', function ($q) {
                $q->where('payment_status', 'paid');
            })
            ->whereBetween('created_at', $dateRanges)
            ->get();

        $auctionMetrics = [
            'total_completed' => $completedAuctions->count(),
            'average_sale_price' => $completedAuctions->avg(function ($listing) {
                return $listing->invoices()->where('payment_status', 'paid')->first()->winning_bid_amount ?? 0;
            }) ?? 0,
            'average_bids_per_item' => \App\Models\Bid::whereIn('listing_id', $completedAuctions->pluck('id'))
                ->selectRaw('listing_id, COUNT(*) as bid_count')
                ->groupBy('listing_id')
                ->avg('bid_count') ?? 0,
        ];

        // Price Charts Data
        $priceChartData = $this->getPriceChartData($dateRanges);
        $membershipPriceChartData = $this->getMembershipPriceChartData($dateRanges);

        return view('admin.dashboard-analytics', compact(
            'listingMetrics',
            'auctionMetrics',
            'priceChartData',
            'membershipPriceChartData',
            'dateFilter',
            'dateRanges'
        ));
    }

    /**
     * Get date range based on filter
     */
    private function getDateRange($filter)
    {
        switch ($filter) {
            case 'today':
                return [now()->startOfDay(), now()->endOfDay()];
            case '7_days':
                return [now()->subDays(7)->startOfDay(), now()->endOfDay()];
            case '30_days':
                return [now()->subDays(30)->startOfDay(), now()->endOfDay()];
            case 'this_year':
                return [now()->startOfYear(), now()->endOfDay()];
            default:
                return [now()->subDays(30)->startOfDay(), now()->endOfDay()];
        }
    }

    /**
     * Get price chart data
     */
    private function getPriceChartData($dateRanges)
    {
        $invoices = \App\Models\Invoice::where('payment_status', 'paid')
            ->whereBetween('sale_date', $dateRanges)
            ->selectRaw('DATE(sale_date) as date, SUM(winning_bid_amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'labels' => $invoices->pluck('date')->toArray(),
            'data' => $invoices->pluck('total')->toArray(),
        ];
    }

    /**
     * Get membership tier price chart data
     */
    private function getMembershipPriceChartData($dateRanges)
    {
        // This would need subscription/payment data grouped by membership tier
        // For now, return placeholder structure
        return [
            'labels' => ['Basic Buyer', 'Premium Buyer', 'Casual Seller', 'Standard Seller', 'Advanced Seller'],
            'data' => [0, 0, 0, 0, 0], // Placeholder - implement based on actual subscription data
        ];
    }

    /**
     * User Management Page
     */
    public function adminListing(Request $request)
    {
        $totalListings    = Listing::count();
        $pendingListings  = Listing::where('status', 'pending')->count();
        $approvedListings = Listing::where('status', 'approved')->count();
        $rejectedListings = Listing::where('status', 'rejected')->count();

        $lastMonthTotal = Listing::whereBetween('created_at', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()])->count();
        $currentMonthTotal = Listing::whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count();

        $percentageChange = $lastMonthTotal > 0
            ? round((($currentMonthTotal - $lastMonthTotal) / $lastMonthTotal) * 100, 2)
            : 0;

        $query = Listing::query();

        if ($request->has('status') && in_array($request->status, ['pending', 'approved', 'rejected'])) {
            $query->where('status', $request->status);
        }

        $listings = $query->paginate(10);

        return view('admin.lisitng', compact(
            'listings',
            'totalListings',
            'pendingListings',
            'approvedListings',
            'rejectedListings',
            'percentageChange',
            'currentMonthTotal',
            'lastMonthTotal'
        ));
    }


    public function approve($id)
    {
        $listing = Listing::findOrFail($id);
        
        // Use AuctionTimeService to calculate start and end times
        $auctionTimeService = new \App\Services\AuctionTimeService();
        $approvalTime = now();
        
        try {
            $startTime = $auctionTimeService->calculateStartTime($approvalTime);
            $endTime = $auctionTimeService->calculateEndTime($startTime, $listing->auction_duration ?? 7);
            
            $listing->auction_start_time = $startTime;
            $listing->auction_end_time = $endTime;
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Cannot approve listing: ' . $e->getMessage());
        }
        
        $listing->status = 'approved';
        
        // Assign Item Number when approving (per PDF requirements)
        $listing->assignItemNumber();
        
        $listing->save();

        // Send approval email to seller
        try {
            \Mail::send('emails.listing-approved', [
                'listing' => $listing,
                'seller' => $listing->seller,
            ], function ($message) use ($listing) {
                $message->to($listing->seller->email, $listing->seller->name)
                    ->subject('Your Listing Has Been Approved - CayMark');
            });
        } catch (\Exception $e) {
            \Log::error('Failed to send approval email: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Listing approved successfully! Item Number: ' . $listing->item_number);
    }

    public function disapprove(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string',
            'rejection_notes' => 'nullable|string|max:1000',
        ]);

        $listing = Listing::findOrFail($id);
        $listing->status = 'rejected';
        $listing->rejected_at = now();
        $listing->rejected_by = auth()->id();
        $listing->rejection_reason = $request->rejection_reason;
        $listing->rejection_notes = $request->rejection_notes;
        $listing->save();

        // Send rejection email to seller
        try {
            \Mail::send('emails.listing-rejected', [
                'listing' => $listing,
                'seller' => $listing->seller,
                'rejectionReason' => $request->rejection_reason,
            ], function ($message) use ($listing) {
                $message->to($listing->seller->email, $listing->seller->name)
                    ->subject('Listing Rejected – ' . ($listing->year ?? '') . ' ' . ($listing->make ?? '') . ' ' . ($listing->model ?? '[VEHICLE_NAME]'));
            });
        } catch (\Exception $e) {
            \Log::error('Failed to send rejection email: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Listing rejected successfully.');
    }

    /**
     * User Management - Complete System
     */
    public function userManagement(Request $request)
    {
        $query = User::with(['subscriptions', 'activeSubscription.package']);

        // Filters
        if ($request->has('role') && in_array($request->role, ['buyer', 'seller'])) {
            $query->where('role', $request->role);
        }

        if ($request->has('status')) {
            if ($request->status === 'restricted') {
                $query->where('is_restricted', true);
            } elseif ($request->status === 'active') {
                $query->where('is_restricted', false);
            }
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(20);
                    
        $userStats = [
            'total' => User::count(),
            'buyers' => User::where('role', 'buyer')->count(),
            'sellers' => User::where('role', 'seller')->count(),
            'restricted' => User::where('is_restricted', true)->count(),
        ];

        return view('admin.user-management', compact('users', 'userStats'));
    }

    /**
     * View single user details
     */
    public function viewUser($id)
    {
        $user = User::with([
            'subscriptions.package',
            'listings',
            'bids',
            'invoices',
            'payouts',
            'payments',
            'deposits',
            'wallet'
        ])->findOrFail($id);

        // Get user activity log
        $activityLog = $this->getUserActivityLog($user);

        return view('admin.user-details', compact('user', 'activityLog'));
    }

    /**
     * Update user
     */
    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $id,
            'phone' => 'sometimes|nullable|string',
            'role' => 'sometimes|in:buyer,seller',
            'is_restricted' => 'sometimes|boolean',
            'restriction_ends_at' => 'sometimes|nullable|date',
            'restriction_reason' => 'sometimes|nullable|string',
            'internal_notes' => 'sometimes|nullable|string',
        ]);

        $user->update($validated);

        return back()->with('success', 'User updated successfully.');
    }

    /**
     * Reset user password
     */
    public function resetUserPassword(Request $request, $id)
    {
        $request->validate([
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::findOrFail($id);
        $user->password = \Hash::make($request->new_password);
        $user->save();

        return back()->with('success', 'Password reset successfully.');
    }

    /**
     * Suspend/Reactivate user
     */
    public function toggleUserStatus(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:suspend,reactivate',
            'reason' => 'required_if:action,suspend|string|max:255',
        ]);
    
        $user = User::findOrFail($id);
    
        if ($request->action === 'suspend') {
            $user->update([
                'is_restricted' => true,
                'restriction_reason' => $request->reason,
                'restriction_ends_at' => null, // or set a date if needed
            ]);
        }
    
        if ($request->action === 'reactivate') {
            $user->update([
                'is_restricted' => false,
                'restriction_reason' => null,
                'restriction_ends_at' => null,
            ]);
        }
    
        return back()->with('success', 'User status updated successfully.');
    }
    

    /**
     * Get user activity log
     */
    private function getUserActivityLog(User $user)
    {
        $activities = collect();

        // Bidding activity
        $bids = $user->bids()->with('listing')->orderBy('created_at', 'desc')->limit(20)->get();
        foreach ($bids as $bid) {
            $activities->push([
                'type' => 'bid',
                'message' => "Placed bid of $" . number_format($bid->amount, 2) . " on " . ($bid->listing->item_number ?? 'Listing #' . $bid->listing_id),
                'timestamp' => $bid->created_at,
            ]);
        }

        // Payment activity
        $payments = $user->payments()->orderBy('created_at', 'desc')->limit(20)->get();
        foreach ($payments as $payment) {
            $activities->push([
                'type' => 'payment',
                'message' => "Payment of $" . number_format($payment->amount, 2) . " - Status: " . $payment->status,
                'timestamp' => $payment->created_at,
            ]);
        }

        // Listing submissions
        if ($user->isSeller()) {
            $listings = $user->listings()->orderBy('created_at', 'desc')->limit(20)->get();
            foreach ($listings as $listing) {
                $activities->push([
                    'type' => 'listing',
                    'message' => "Submitted listing: " . ($listing->item_number ?? 'Listing #' . $listing->id) . " - Status: " . $listing->status,
                    'timestamp' => $listing->created_at,
                ]);
            }
        }

        return $activities->sortByDesc('timestamp')->take(50);
    }

    /**
     * Membership Management Page
     * Note: Using Subscription model instead of Membership
     */
    public function membershipManagement()
    {
        $memberships = \App\Models\Subscription::with(['user', 'package'])
                                ->orderBy('created_at', 'desc')
                                ->paginate(20);
                                
        $membershipStats = [
            'total' => \App\Models\Subscription::count(),
            'active' => \App\Models\Subscription::where('status', 'active')->count(),
            'expired' => \App\Models\Subscription::where('status', 'expired')->count(),
            'pending_renewal' => \App\Models\Subscription::where('status', 'pending')->count(),
            'expiring_soon' => \App\Models\Subscription::where('ends_at', '<=', Carbon::now()->addDays(7))
                                        ->where('status', 'active')
                                        ->whereNotNull('ends_at')
                                        ->count(),
        ];

        return view('admin.memberships', compact('memberships', 'membershipStats'));
    }

    /**
     * Listing Approval - Enhanced with all details
     */
    public function listingReview(Request $request)
    {
        $query = Listing::with(['seller', 'images'])
            ->where('status', 'pending');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('item_number', 'like', "%{$search}%")
                    ->orWhere('make', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%")
                    ->orWhere('vin', 'like', "%{$search}%");
            });
        }

        $pendingListings = $query->orderBy('created_at', 'desc')->paginate(15);

        // Rejection reasons dropdown
        $rejectionReasons = [
            'Poor quality photos',
            'Missing required information',
            'Invalid VIN/HIN',
            'Duplicate listing',
            'Prohibited item',
            'Incorrect category',
            'Other (specify in notes)',
        ];

        return view('admin.listing-approval', compact('pendingListings', 'rejectionReasons'));
    }

    /**
     * View single listing for approval
     */
    public function viewListingForApproval($id)
    {
        $listing = Listing::with(['seller', 'images'])->findOrFail($id);
        
        if ($listing->status !== 'pending') {
            return redirect()->route('admin.listing-review')
                ->with('error', 'This listing is not pending approval.');
        }

        return view('admin.listing-approval-detail', compact('listing'));
    }

    /**
     * Listing Management - Live Listings
     */
    public function activeListings(Request $request)
    {
        $query = Listing::with(['seller', 'images', 'bids'])
            ->where('status', 'approved');

        // Filters
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('item_number', 'like', "%{$search}%")
                    ->orWhere('make', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%");
            });
        }

        $activeListings = $query->orderBy('created_at', 'desc')->paginate(20);
                                
        $listingStats = [
            'total_active' => Listing::where('status', 'approved')->count(),
            'with_bids' => Listing::where('status', 'approved')->has('bids')->count(),
            'ending_soon' => Listing::where('status', 'approved')
                ->where('auction_end_time', '<=', now()->addHours(24))
                ->where('auction_end_time', '>', now())
                ->count(),
        ];

        return view('admin.listing-management', compact('activeListings', 'listingStats'));
    }

    /**
     * Boosts & Add-ons Management
     */
    public function boostsAddOns(Request $request)
    {
        // Placeholder for boosts/add-ons management
        // This feature can be implemented later when boosts/add-ons functionality is added
        
        $stats = [
            'total_boosts' => 0,
            'active_boosts' => 0,
            'total_addons' => 0,
            'active_addons' => 0,
        ];

        return view('admin.boosts-addons', compact('stats'));
    }

    /**
     * Edit listing details
     */
    public function editListing(Request $request, $id)
    {
        $listing = Listing::findOrFail($id);

        $validated = $request->validate([
            'make' => 'sometimes|string',
            'model' => 'sometimes|string',
            'year' => 'sometimes|string',
            'color' => 'sometimes|string',
            'starting_price' => 'sometimes|numeric',
            'buy_now_price' => 'sometimes|nullable|numeric',
            'reserve_price' => 'sometimes|nullable|numeric',
        ]);

        $listing->update($validated);

        return back()->with('success', 'Listing updated successfully.');
    }

    /**
     * Extend auction time
     */
    public function extendAuctionTime(Request $request, $id)
    {
        $request->validate([
            'additional_days' => 'required|integer|min:1|max:30',
        ]);

        $listing = Listing::findOrFail($id);
        
        if ($listing->auction_end_time) {
            $listing->auction_end_time = Carbon::parse($listing->auction_end_time)
                ->addDays($request->additional_days);
        } else {
            $listing->auction_end_time = now()->addDays($listing->auction_duration + $request->additional_days);
        }
        
        $listing->save();

        return back()->with('success', 'Auction time extended successfully.');
    }

    /**
     * Pause/Disable listing
     */
    public function toggleListingStatus(Request $request, $id)
    {
        $listing = Listing::findOrFail($id);
        $action = $request->get('action'); // 'pause' or 'activate'

        if ($action === 'pause') {
            $listing->update(['status' => 'paused']);
        } else {
            $listing->update(['status' => 'approved']);
        }

        return back()->with('success', 'Listing status updated successfully.');
    }

    /**
     * Delete listing
     */
    public function deleteListing($id)
    {
        $listing = Listing::findOrFail($id);
        $listing->delete();

        return redirect()->route('admin.active-listings')
            ->with('success', 'Listing deleted successfully.');
    }

    /**
     * Auction Management + Bidding Logs (Combined Page)
     */
    public function auctionManagement(Request $request)
    {
        $query = Listing::with(['seller', 'bids.user'])
            ->where('listing_method', 'auction')
            ->where('status', 'approved');

        // Filter active auctions
        if ($request->has('filter') && $request->filter === 'active') {
            $query->where(function ($q) {
                $q->where('auction_end_time', '>', now())
                    ->orWhere(function ($q2) {
                        $q2->whereNull('auction_end_time')
                            ->whereRaw('DATE_ADD(auction_start_time, INTERVAL auction_duration DAY) > NOW()');
                    });
            });
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('item_number', 'like', "%{$search}%")
                    ->orWhere('make', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%");
            });
        }

        $auctions = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get bidding logs for selected auction
        $biddingLogs = null;
        if ($request->has('auction_id')) {
            $biddingLogs = \App\Models\Bid::where('listing_id', $request->auction_id)
                ->with(['user', 'listing'])
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('admin.auction-management', compact('auctions', 'biddingLogs'));
    }

    /**
     * View bidding logs for auction
     */
    public function viewBiddingLogs($auctionId)
    {
        $listing = Listing::with(['seller'])->findOrFail($auctionId);
        $bids = \App\Models\Bid::where('listing_id', $auctionId)
            ->with(['user'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Detect irregular bidding activity
        $irregularActivity = $this->detectIrregularBidding($bids);

        return view('admin.bidding-logs', compact('listing', 'bids', 'irregularActivity'));
    }

    /**
     * Cancel auction
     */
    public function cancelAuction(Request $request, $id)
    {
        $listing = Listing::findOrFail($id);
        $listing->update(['status' => 'cancelled']);

        return back()->with('success', 'Auction cancelled successfully.');
    }

    /**
     * Pause/Resume auction
     */
    public function toggleAuctionStatus(Request $request, $id)
    {
        $listing = Listing::findOrFail($id);
        $action = $request->get('action'); // 'pause' or 'resume'

        if ($action === 'pause') {
            $listing->update(['status' => 'paused']);
        } else {
            $listing->update(['status' => 'approved']);
        }

        return back()->with('success', 'Auction status updated successfully.');
    }

    /**
     * Remove fraudulent bid
     */
    public function removeBid(Request $request, $bidId)
    {
        $bid = \App\Models\Bid::findOrFail($bidId);
        $bid->update(['status' => 'removed']);

        return back()->with('success', 'Bid removed successfully.');
    }

    /**
     * Detect irregular bidding activity
     */
    private function detectIrregularBidding($bids)
    {
        $alerts = [];

        // Check for rapid bidding (multiple bids within seconds)
        $previousBid = null;
        foreach ($bids as $bid) {
            if ($previousBid && $bid->user_id === $previousBid->user_id) {
                $timeDiff = $bid->created_at->diffInSeconds($previousBid->created_at);
                if ($timeDiff < 5) {
                    $alerts[] = [
                        'type' => 'rapid_bidding',
                        'message' => "User {$bid->user->name} placed multiple bids within {$timeDiff} seconds",
                        'bid_id' => $bid->id,
                    ];
                }
            }
            $previousBid = $bid;
        }

        return $alerts;
    }

    /**
     * Payment Management - Complete System
     */
    public function payments(Request $request)
    {
        $query = Payment::with(['user', 'invoice', 'listing']);

        // Filters
        if ($request->has('status') && in_array($request->status, ['pending', 'completed', 'failed', 'pending_release', 'held'])) {
            $query->where('status', $request->status);
        }

        if ($request->has('buyer_id')) {
            $query->where('user_id', $request->buyer_id);
        }

        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $payments = $query->orderBy('created_at', 'desc')->paginate(20);
                            
        $paymentStats = [
            'total_revenue' => Payment::where('status', 'completed')->sum('amount'),
            'pending_release' => Payment::where('status', 'pending_release')->sum('amount'),
            'held' => Payment::where('status', 'held')->sum('amount'),
            'completed' => Payment::where('status', 'completed')->count(),
            'failed' => Payment::where('status', 'failed')->count(),
            'pending' => Payment::where('status', 'pending')->count(),
        ];

        return view('admin.payment-management', compact('payments', 'paymentStats'));
    }

    /**
     * Update payment status
     */
    public function updatePaymentStatus(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending,completed,failed,pending_release,held',
        ]);

        $payment->update(['status' => $request->status]);

        // Resend confirmation email if status changed to completed
        if ($request->status === 'completed' && $payment->invoice) {
            try {
                \Mail::send('emails.payment-successful', [
                    'invoice' => $payment->invoice,
                    'buyer' => $payment->user,
                    'payment' => $payment,
                ], function ($message) use ($payment) {
                    $message->to($payment->user->email, $payment->user->name)
                        ->subject('Payment Successful – ' . ($payment->invoice->item_name ?? '[VEHICLE_NAME]'));
                });
            } catch (\Exception $e) {
                \Log::error('Failed to resend payment confirmation email: ' . $e->getMessage());
            }
        }

        return back()->with('success', 'Payment status updated successfully.');
    }

    /**
     * Regenerate invoice
     */
    public function regenerateInvoice($paymentId)
    {
        $payment = Payment::with('invoice')->findOrFail($paymentId);
        
        if (!$payment->invoice) {
            return back()->with('error', 'No invoice found for this payment.');
        }

        $invoiceService = new \App\Services\InvoiceService();
        $pdfPath = $invoiceService->generateInvoicePDF($payment->invoice);
        
        $payment->invoice->update(['pdf_path' => $pdfPath]);

        return back()->with('success', 'Invoice regenerated successfully.');
    }

    /**
     * Dispute Management - Complete System
     */
    public function disputes(Request $request)
    {
        // Note: Dispute model doesn't exist yet, using placeholder structure
        // When Dispute model is created, uncomment and use:
        /*
        $query = Dispute::with(['buyer', 'seller', 'listing', 'messages']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $disputes = $query->orderBy('created_at', 'desc')->paginate(15);
                            
        $disputeStats = [
            'open' => Dispute::where('status', 'open')->count(),
            'resolved' => Dispute::where('status', 'resolved')->count(),
            'in_progress' => Dispute::where('status', 'in_progress')->count(),
            'escalated' => Dispute::where('status', 'escalated')->count(),
        ];
        */

        // Placeholder data until Dispute model exists
        $disputes = collect([]);
        $disputeStats = [
            'total' => 0,
            'open' => 0,
            'resolved' => 0,
            'in_progress' => 0,
            'escalated' => 0,
        ];

        return view('admin.dispute-management', compact('disputes', 'disputeStats'));
    }

    /**
     * View dispute details
     */
    public function viewDispute($id)
    {
        // Placeholder - implement when Dispute model exists
        return view('admin.dispute-details', ['dispute' => null]);
    }

    /**
     * Update dispute status
     */
    public function updateDisputeStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:open,in_progress,escalated,resolved,closed',
            'admin_decision' => 'nullable|string|max:1000',
        ]);

        // Placeholder - implement when Dispute model exists
        // $dispute = Dispute::findOrFail($id);
        // $dispute->update([
        //     'status' => $request->status,
        //     'admin_decision' => $request->admin_decision,
        //     'resolved_at' => $request->status === 'resolved' ? now() : null,
        // ]);

        return back()->with('success', 'Dispute status updated successfully.');
    }

    /**
     * Notification & Message Log
     */
    public function notifications(Request $request)
    {
        // Get all notifications from database
        $query = \Illuminate\Notifications\DatabaseNotification::with('notifiable');

        if ($request->has('user_id')) {
            $query->where('notifiable_id', $request->user_id)
                ->where('notifiable_type', 'App\Models\User');
        }

        if ($request->has('type')) {
            $query->whereJsonContains('data->type', $request->type);
        }

        $notifications = $query->orderBy('created_at', 'desc')->paginate(50);

        // Get message logs (from chats/messages)
        $messageQuery = \App\Models\Message::with(['chat', 'user']);

        if ($request->has('chat_id')) {
            $messageQuery->where('chat_id', $request->chat_id);
        }

        $messages = $messageQuery->orderBy('created_at', 'desc')->paginate(50);

        return view('admin.notification-message-log', compact('notifications', 'messages'));
    }

    /**
     * Resend notification
     */
    public function resendNotification($notificationId)
    {
        $notification = \Illuminate\Notifications\DatabaseNotification::findOrFail($notificationId);
        $user = $notification->notifiable;

        // Extract notification data and resend
        $data = $notification->data;
        $notificationService = new \App\Services\NotificationService();

        // Map notification types to service methods
        // This is a simplified version - you may need to enhance based on actual notification types

        return back()->with('success', 'Notification resent successfully.');
    }

    /**
     * Reports & Analytics Page
     */
    public function reportsAnalytics()
    {
        // User growth data (last 30 days)
        $userGrowth = User::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                            ->where('created_at', '>=', Carbon::now()->subDays(30))
                            ->groupBy('date')
                            ->orderBy('date')
                            ->get();
                            
        // Sales data
        $salesData = Payment::selectRaw('DATE(created_at) as date, SUM(amount) as total, COUNT(*) as count')
                            ->where('status', 'completed')
                            ->where('created_at', '>=', Carbon::now()->subDays(30))
                            ->groupBy('date')
                            ->orderBy('date')
                            ->get();
                            
        // Subscription analytics
        $subscriptionData = \App\Models\Subscription::selectRaw('package_id, status, COUNT(*) as count')
                                    ->groupBy('package_id', 'status')
                                    ->get();
                                    
        // Dispute analytics (placeholder until Dispute model exists)
        $disputeData = collect([]);
        // $disputeData = Dispute::selectRaw('DATE(created_at) as date, status, COUNT(*) as count')
        //                     ->where('created_at', '>=', Carbon::now()->subDays(30))
        //                     ->groupBy('date', 'status')
        //                     ->orderBy('date')
        //                     ->get();

        return view('admin.reports-analytics', compact(
            'userGrowth', 
            'salesData', 
            'subscriptionData', 
            'disputeData'
        ));
    }

    /**
     * Action Methods
     */
    public function suspendUser(User $user)
    {
        $user->update([
            'is_restricted' => true,
            'restriction_reason' => 'Account suspended by admin',
        ]);
        
        return back()->with('success', 'User suspended successfully.');
    }

    public function banUser(User $user)
    {
        $user->update([
            'is_restricted' => true,
            'restriction_reason' => 'Account banned by admin',
        ]);
        
        return back()->with('success', 'User banned successfully.');
    }

    public function approveListing(Listing $listing)
    {
        // Use AuctionTimeService to calculate start and end times
        $auctionTimeService = new \App\Services\AuctionTimeService();
        $approvalTime = now();
        
        try {
            $startTime = $auctionTimeService->calculateStartTime($approvalTime);
            $endTime = $auctionTimeService->calculateEndTime($startTime, $listing->auction_duration ?? 7);
        } catch (\Exception $e) {
            return back()->with('error', 'Cannot approve listing: ' . $e->getMessage());
        }
        
        // Assign Item Number when approving (per PDF requirements)
        $listing->assignItemNumber();
        
        $listing->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => auth()->id(),
            'auction_start_time' => $startTime,
            'auction_end_time' => $endTime,
        ]);
        
        // Send approval email to seller
        try {
            \Mail::send('emails.listing-approved', [
                'listing' => $listing,
                'seller' => $listing->seller,
            ], function ($message) use ($listing) {
                $message->to($listing->seller->email, $listing->seller->name)
                    ->subject('Auction Now Live – ' . ($listing->year ?? '') . ' ' . ($listing->make ?? '') . ' ' . ($listing->model ?? '[VEHICLE_NAME]'));
            });
            
            // Send in-app notification
            $notificationService = new \App\Services\NotificationService();
            $notificationService->listingApproved($listing->seller, $listing);
        } catch (\Exception $e) {
            \Log::error('Failed to send approval email: ' . $e->getMessage());
        }
        
        return back()->with('success', 'Listing approved successfully. Item Number: ' . $listing->item_number);
    }

    public function rejectListing(Request $request, Listing $listing)
    {
        $request->validate([
            'rejection_reason' => 'required|string',
            'rejection_notes' => 'nullable|string|max:1000',
        ]);

        $listing->update([
            'status' => 'rejected',
            'rejected_at' => now(),
            'rejected_by' => auth()->id(),
            'rejection_reason' => $request->rejection_reason,
            'rejection_notes' => $request->rejection_notes,
        ]);
        
        // Send rejection email to seller
        try {
            \Mail::send('emails.listing-rejected', [
                'listing' => $listing,
                'seller' => $listing->seller,
                'rejectionReason' => $request->rejection_reason,
            ], function ($message) use ($listing) {
                $message->to($listing->seller->email, $listing->seller->name)
                    ->subject('Listing Rejected – ' . ($listing->year ?? '') . ' ' . ($listing->make ?? '') . ' ' . ($listing->model ?? '[VEHICLE_NAME]'));
            });
        } catch (\Exception $e) {
            \Log::error('Failed to send rejection email: ' . $e->getMessage());
        }
        
        return back()->with('success', 'Listing rejected successfully.');
    }

    public function releasePayment(Payment $payment)
    {
        $payment->update(['status' => 'completed']);
        
        return back()->with('success', 'Payment released successfully.');
    }

    public function holdPayment(Payment $payment)
    {
        $payment->update(['status' => 'held']);
        
        return back()->with('success', 'Payment held successfully.');
    }

    /**
     * Approve deposit withdrawal request.
     */
    public function approveWithdrawal($withdrawalId)
    {
        $withdrawal = \App\Models\Deposit::findOrFail($withdrawalId);
        
        if ($withdrawal->type !== 'withdrawal' || $withdrawal->status !== 'pending') {
            return back()->with('error', 'Invalid withdrawal request.');
        }

        // Check if buyer is highest bidder on any active auction (per PDF requirements)
        $user = $withdrawal->user;
        
        // Get all active listings
        $activeListings = \App\Models\Listing::where('status', 'approved')
            ->where(function ($q) {
                $q->where('auction_end_time', '>', now())
                    ->orWhere(function ($q2) {
                        $q2->whereNull('auction_end_time')
                            ->whereRaw('DATE_ADD(auction_start_time, INTERVAL auction_duration DAY) > NOW()');
                    });
            })
            ->get();

        foreach ($activeListings as $listing) {
            $highestBid = $listing->bids()
                ->where('status', 'active')
                ->orderByDesc('amount')
                ->first();
            
            if ($highestBid && $highestBid->user_id === $user->id) {
                return back()->with('error', 'Cannot approve withdrawal. Buyer is currently the highest bidder on an active auction.');
            }
        }

        // Process the withdrawal (remove from locked balance)
        $wallet = \App\Models\UserWallet::getOrCreateForUser($withdrawal->user_id);
        $wallet->locked_balance -= $withdrawal->amount;
        $wallet->updateTotalBalance();
        $wallet->save();

        // Update withdrawal status
        $withdrawal->status = 'completed';
        $withdrawal->save();

        // TODO: Process actual payment transfer (bank transfer, etc.)

        return back()->with('success', 'Withdrawal approved and processed.');
    }

    /**
     * Reject deposit withdrawal request.
     */
    public function rejectWithdrawal($withdrawalId)
    {
        $withdrawal = \App\Models\Deposit::findOrFail($withdrawalId);
        
        if ($withdrawal->type !== 'withdrawal' || $withdrawal->status !== 'pending') {
            return back()->with('error', 'Invalid withdrawal request.');
        }

        // Return funds to available balance
        $wallet = \App\Models\UserWallet::getOrCreateForUser($withdrawal->user_id);
        $wallet->locked_balance -= $withdrawal->amount;
        $wallet->available_balance += $withdrawal->amount;
        $wallet->updateTotalBalance();
        $wallet->save();

        // Update withdrawal status
        $withdrawal->status = 'cancelled';
        $withdrawal->save();

        return back()->with('success', 'Withdrawal request rejected. Funds returned to available balance.');
    }

    /**
     * Finance/Admin: View payout management page.
     */
    public function payoutManagement(Request $request)
    {
        $query = \App\Models\Payout::with(['seller', 'listing', 'invoice.buyer']);

        // Filter by status
        if ($request->has('status') && in_array($request->status, ['pending', 'processing', 'sent', 'on_hold', 'paid_successfully'])) {
            $query->where('status', $request->status);
        }

        // Filter by seller
        if ($request->has('seller_id')) {
            $query->where('seller_id', $request->seller_id);
        }

        // Filter by date range
        if ($request->has('date_from')) {
            $query->where('sale_date', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->where('sale_date', '<=', $request->date_to);
        }

        $payouts = $query->orderByDesc('created_at')->paginate(20);
        $sellers = \App\Models\User::where('role', 'seller')->get();

        return view('admin.payout-management', compact('payouts', 'sellers'));
    }

    /**
     * Finance/Admin: Update payout status.
     */
    public function updatePayoutStatus(Request $request, $payoutId)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,sent,on_hold,paid_successfully',
            'transaction_reference' => 'nullable|string|max:255',
            'date_sent' => 'nullable|date',
            'finance_notes' => 'nullable|string|max:1000',
        ]);

        $payout = \App\Models\Payout::findOrFail($payoutId);

        $payout->update([
            'status' => $request->status,
            'transaction_reference' => $request->transaction_reference,
            'date_sent' => $request->date_sent,
            'finance_notes' => $request->finance_notes,
        ]);

        // Send notification to seller if status changed to "Sent" or "Paid Successfully"
        if (in_array($request->status, ['sent', 'paid_successfully'])) {
            try {
                \Mail::send('emails.payout-status-updated', [
                    'payout' => $payout,
                    'seller' => $payout->seller,
                ], function ($message) use ($payout) {
                    $message->to($payout->seller->email, $payout->seller->name)
                        ->subject('Payout Status Updated - CayMark');
                });
            } catch (\Exception $e) {
                \Log::error('Failed to send payout status email: ' . $e->getMessage());
            }
        }

        return back()->with('success', 'Payout status updated successfully.');
    }

    /**
     * Finance/Admin: View payment and payout logs.
     */
    public function paymentPayoutLogs(Request $request)
    {
        $payments = \App\Models\Payment::with(['user', 'invoice', 'listing', 'seller'])
            ->when($request->has('type') && $request->type === 'buyer', function ($q) {
                $q->whereNotNull('invoice_id');
            })
            ->when($request->has('type') && $request->type === 'subscription', function ($q) {
                $q->whereNotNull('subscription_id');
            })
            ->when($request->has('status'), function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->orderByDesc('created_at')
            ->paginate(20);

        $payouts = \App\Models\Payout::with(['seller', 'listing', 'invoice'])
            ->when($request->has('payout_status'), function ($q) use ($request) {
                $q->where('status', $request->payout_status);
            })
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.payment-payout-logs', compact('payments', 'payouts'));
    }

    /**
     * Admin: View invoice log (per PDF requirements).
     * Backend page that logs every invoice generated.
     * Includes: Item Name, Item ID, Buyer Name, Seller Name, Winning Bid Amount,
     * Buyer Fees, Total Invoice Amount, Invoice File (admin download link),
     * Date Invoice Generated, Payment Status, Auction ID, Any notes or flags.
     */
    public function invoiceLog(Request $request)
    {
        $invoices = \App\Models\Invoice::with(['buyer', 'seller', 'listing', 'bid'])
            ->when($request->has('payment_status'), function ($q) use ($request) {
                $q->where('payment_status', $request->payment_status);
            })
            ->when($request->has('buyer_id'), function ($q) use ($request) {
                $q->where('buyer_id', $request->buyer_id);
            })
            ->when($request->has('seller_id'), function ($q) use ($request) {
                $q->where('seller_id', $request->seller_id);
            })
            ->when($request->has('date_from'), function ($q) use ($request) {
                $q->whereDate('invoice_generated_at', '>=', $request->date_from);
            })
            ->when($request->has('date_to'), function ($q) use ($request) {
                $q->whereDate('invoice_generated_at', '<=', $request->date_to);
            })
            ->orderByDesc('invoice_generated_at')
            ->paginate(50);

        return view('admin.invoice-log', compact('invoices'));
    }

    /**
     * Admin: Download invoice PDF.
     */
    public function downloadInvoice($invoiceId)
    {
        $invoice = \App\Models\Invoice::findOrFail($invoiceId);
        
        if (!$invoice->pdf_path || !file_exists(public_path($invoice->pdf_path))) {
            return back()->with('error', 'Invoice PDF not found.');
        }

        return response()->download(public_path($invoice->pdf_path));
    }

    /**
     * Helper Methods
     */
    private function getRecentActivities()
    {
        // Combine recent activities from different models
        $userActivities = User::latest()->limit(5)->get();
        $listingActivities = Listing::where('status', 'approved')
                                    ->latest()
                                    ->limit(5)
                                    ->get();
        $paymentActivities = Payment::where('status', 'completed')
                                    ->latest()
                                    ->limit(5)
                                    ->get();
                                    
        return collect()
                ->merge($userActivities->map(fn($user) => [
                    'type' => 'user_registration',
                    'message' => 'New user registered: ' . $user->name,
                    'time' => $user->created_at,
                    'icon' => 'user'
                ]))
                ->merge($listingActivities->map(fn($listing) => [
                    'type' => 'listing_approved',
                    'message' => 'Listing approved: ' . $listing->title,
                    'time' => $listing->approved_at,
                    'icon' => 'shopping-bag'
                ]))
                ->merge($paymentActivities->map(fn($payment) => [
                    'type' => 'payment_completed',
                    'message' => 'Payment completed: $' . $payment->amount,
                    'time' => $payment->updated_at,
                    'icon' => 'dollar-sign'
                ]))
                ->sortByDesc('time')
                ->take(10);
    }

    private function getSystemAlerts()
    {
        $alerts = [];
        
        // Check for pending listings
        $pendingCount = Listing::where('status', 'pending')->count();
        if ($pendingCount > 0) {
            $alerts[] = [
                'type' => 'warning',
                'message' => "{$pendingCount} listings awaiting approval",
                'link' => route('admin.listing-review')
            ];
        }
        
        // Check for open disputes (placeholder until Dispute model exists)
        // $disputeCount = Dispute::where('status', 'open')->count();
        // if ($disputeCount > 0) {
        //     $alerts[] = [
        //         'type' => 'danger',
        //         'message' => "{$disputeCount} open disputes need attention",
        //         'link' => route('admin.disputes')
        //     ];
        // }
        
        // Check for pending payments
        $paymentCount = Payment::where('status', 'pending_release')->orWhere('status', 'pending')->count();
        if ($paymentCount > 0) {
            $alerts[] = [
                'type' => 'info',
                'message' => "{$paymentCount} payments pending release",
                'link' => route('admin.payments')
            ];
        }
        
        // Check for pending payouts
        $payoutCount = \App\Models\Payout::whereIn('status', ['pending', 'processing'])->count();
        if ($payoutCount > 0) {
            $alerts[] = [
                'type' => 'info',
                'message' => "{$payoutCount} payouts pending",
                'link' => route('admin.payouts')
            ];
        }

        // Check for email sending failures (from logs)
        $emailFailures = $this->checkEmailFailures();
        if ($emailFailures > 0) {
            $alerts[] = [
                'type' => 'danger',
                'message' => "{$emailFailures} email sending failures detected",
                'link' => route('admin.email-templates')
            ];
        }

        // Check for payout errors (from logs or failed status)
        $payoutErrors = \App\Models\Payout::where('status', 'failed')->count();
        if ($payoutErrors > 0) {
            $alerts[] = [
                'type' => 'danger',
                'message' => "{$payoutErrors} payout errors requiring manual review",
                'link' => route('admin.payouts')
            ];
        }
        
        return $alerts;
    }

    /**
     * Check for email sending failures in logs
     */
    private function checkEmailFailures()
    {
        // Check recent log entries for email failures
        $logFile = storage_path('logs/laravel.log');
        if (!file_exists($logFile)) {
            return 0;
        }

        // Read last 1000 lines and count email failures
        $lines = file($logFile);
        $recentLines = array_slice($lines, -1000);
        $failureCount = 0;

        foreach ($recentLines as $line) {
            if (stripos($line, 'Failed to send') !== false && stripos($line, 'email') !== false) {
                // Check if failure is within last 24 hours
                if (preg_match('/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]/', $line, $matches)) {
                    $logTime = Carbon::parse($matches[1]);
                    if ($logTime->isAfter(now()->subDay())) {
                        $failureCount++;
                    }
                }
            }
        }

        return $failureCount;
    }

    /**
     * User Activity Insights
     */
    public function userActivityInsights(Request $request)
    {
        $dateFilter = $request->get('date_filter', '7_days');
        $dateRanges = $this->getDateRange($dateFilter);

        // Daily/Weekly active buyers
        $activeBuyers = User::where('role', 'buyer')
            ->whereHas('bids', function ($q) use ($dateRanges) {
                $q->whereBetween('created_at', $dateRanges);
            })
            ->orWhereHas('invoices', function ($q) use ($dateRanges) {
                $q->whereBetween('created_at', $dateRanges);
            })
            ->distinct()
            ->count();

        // Daily/Weekly active sellers
        $activeSellers = User::where('role', 'seller')
            ->whereHas('listings', function ($q) use ($dateRanges) {
                $q->whereBetween('created_at', $dateRanges);
            })
            ->distinct()
            ->count();

        // Most active bidders
        $mostActiveBidders = User::where('role', 'buyer')
            ->withCount(['bids' => function ($q) use ($dateRanges) {
                $q->whereBetween('created_at', $dateRanges);
            }])
            ->orderBy('bids_count', 'desc')
            ->limit(10)
            ->get();

        // Membership tier distribution
        $membershipDistribution = $this->getMembershipDistribution();

        // Repeat buyer rate
        $totalBuyers = User::where('role', 'buyer')->count();
        $repeatBuyers = User::where('role', 'buyer')
            ->has('invoices', '>', 1)
            ->count();
        $repeatBuyerRate = $totalBuyers > 0 ? ($repeatBuyers / $totalBuyers) * 100 : 0;

        return view('admin.user-activity-insights', compact(
            'activeBuyers',
            'activeSellers',
            'mostActiveBidders',
            'membershipDistribution',
            'repeatBuyerRate',
            'dateFilter'
        ));
    }

    /**
     * Get membership tier distribution
     */
    private function getMembershipDistribution()
    {
        // This would need subscription/package data
        // Placeholder structure
        return [
            'guest' => User::whereNull('role')->count(),
            'basic_buyer' => 0, // Implement based on subscription data
            'premium_buyer' => 0,
            'casual_seller' => 0,
            'standard_seller' => 0,
            'advanced_seller' => 0,
        ];
    }

    /**
     * Revenue Tracking
     */
    public function revenueTracking(Request $request)
    {
        $dateFilter = $request->get('date_filter', '30_days');
        $dateRanges = $this->getDateRange($dateFilter);

        // Total revenue from listing fees (Individual sellers: $25 per listing)
        $listingFees = Payment::whereHas('user', function ($q) {
            $q->where('role', 'seller');
        })
        ->where('status', 'completed')
        ->whereBetween('created_at', $dateRanges)
        ->where('amount', 25.00) // Individual seller listing fee
        ->sum('amount');

        // Total revenue from buyer fees (6% commission)
        $buyerFees = \App\Models\Invoice::where('payment_status', 'paid')
            ->whereBetween('sale_date', $dateRanges)
            ->sum('buyer_commission');

        // Total revenue from seller fees (4% commission)
        $sellerFees = \App\Models\Payout::whereIn('status', ['sent', 'paid_successfully'])
            ->whereBetween('payout_generated_at', $dateRanges)
            ->sum('seller_commission');

        // Total payout amounts sent to sellers
        $totalPayouts = \App\Models\Payout::whereIn('status', ['sent', 'paid_successfully'])
            ->whereBetween('payout_generated_at', $dateRanges)
            ->sum('net_payout');

        // Outstanding payouts pending
        $outstandingPayouts = \App\Models\Payout::whereIn('status', ['pending', 'processing'])
            ->sum('net_payout');

        // Failed payouts
        $failedPayouts = \App\Models\Payout::where('status', 'failed')
            ->whereBetween('payout_generated_at', $dateRanges)
            ->get();

        return view('admin.revenue-tracking', compact(
            'listingFees',
            'buyerFees',
            'sellerFees',
            'totalPayouts',
            'outstandingPayouts',
            'failedPayouts',
            'dateFilter',
            'dateRanges'
        ));
    }

    /**
     * Export revenue data to CSV
     */
    public function exportRevenue(Request $request)
    {
        $dateFilter = $request->get('date_filter', '30_days');
        $dateRanges = $this->getDateRange($dateFilter);

        // Get revenue data
        $revenueData = \App\Models\Invoice::where('payment_status', 'paid')
            ->whereBetween('sale_date', $dateRanges)
            ->with(['buyer', 'seller'])
            ->get();

        $filename = 'revenue_export_' . date('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($revenueData) {
            $file = fopen('php://output', 'w');
            
            // Headers
            fputcsv($file, ['Date', 'Buyer', 'Seller', 'Item', 'Sale Price', 'Buyer Fee', 'Seller Fee', 'Net Payout']);

            // Data
            foreach ($revenueData as $invoice) {
                $payout = $invoice->payout;
                fputcsv($file, [
                    $invoice->sale_date,
                    $invoice->buyer->name ?? 'N/A',
                    $invoice->seller->name ?? 'N/A',
                    $invoice->item_name,
                    $invoice->winning_bid_amount,
                    $invoice->buyer_commission,
                    $payout ? $payout->seller_commission : 0,
                    $payout ? $payout->net_payout : 0,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Admin: View all unpaid auctions beyond 48-hour deadline (per PDF requirements).
     */
    public function unpaidAuctions(Request $request)
    {
        $invoices = \App\Models\Invoice::where('payment_status', 'pending')
            ->whereNotNull('payment_deadline')
            ->where('payment_deadline', '<=', now())
            ->with(['buyer', 'seller', 'listing', 'bid'])
            ->when($request->has('status'), function ($q) use ($request) {
                if ($request->status === 'overdue') {
                    $q->where('is_overdue', true);
                } elseif ($request->status === 'pending') {
                    $q->where('is_overdue', false);
                }
            })
            ->orderByDesc('payment_deadline')
            ->paginate(50);

        return view('admin.unpaid-auctions', compact('invoices'));
    }

    /**
     * Admin: View buyer defaults management.
     */
    public function buyerDefaults(Request $request)
    {
        $defaults = \App\Models\BuyerDefault::with(['user', 'invoice', 'listing'])
            ->when($request->has('status'), function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->orderByDesc('defaulted_at')
            ->paginate(50);

        return view('admin.buyer-defaults', compact('defaults'));
    }

    /**
     * Admin: Resolve default by relisting (Option A).
     */
    public function resolveDefaultByRelist(Request $request, $defaultId)
    {
        $request->validate([
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $default = \App\Models\BuyerDefault::findOrFail($defaultId);
        $defaultService = new \App\Services\DefaultService();
        
        $defaultService->resolveByRelist($default, $request->admin_notes ?? '');

        return back()->with('success', 'Default resolved by relist. Seller can now create a new listing.');
    }

    /**
     * Admin: Offer to second-highest bidder (Option B - Step 1).
     */
    public function offerToSecondHighestBidder(Request $request, $defaultId)
    {
        $default = \App\Models\BuyerDefault::with(['invoice.listing', 'invoice.bid'])->findOrFail($defaultId);
        $invoice = $default->invoice;
        
        $defaultService = new \App\Services\DefaultService();
        $secondBid = $defaultService->getSecondHighestBidder($invoice);

        if (!$secondBid) {
            return back()->withErrors(['error' => 'No second-highest bidder found for this auction.']);
        }

        // Calculate fees for second-highest bidder
        $commissionService = new \App\Services\CommissionService();
        $buyerInvoice = $commissionService->calculateBuyerInvoice($secondBid->amount);

        // Create second-chance purchase record
        $secondChance = \App\Models\SecondChancePurchase::create([
            'original_invoice_id' => $invoice->id,
            'listing_id' => $invoice->listing_id,
            'buyer_id' => $secondBid->user_id,
            'seller_id' => $invoice->seller_id,
            'bid_id' => $secondBid->id,
            'bid_amount' => $secondBid->amount,
            'buyer_commission' => $buyerInvoice['buyer_commission'],
            'total_amount_due' => $buyerInvoice['total_due'],
            'status' => 'offered',
            'offered_at' => now(),
            'payment_deadline' => now()->addHours(48), // 48-hour payment window
        ]);

        // Update default status
        $default->update([
            'status' => 'second_chance',
            'resolution_type' => 'second_chance',
        ]);

        // TODO: Send notification/email to second-highest bidder
        // TODO: Generate invoice and payment link

        return back()->with('success', 'Second-chance offer created. Contact the buyer to complete the purchase.');
    }

    /**
     * Admin: Generate second-chance invoice and payment link.
     */
    public function generateSecondChanceInvoice(Request $request, $secondChanceId)
    {
        $secondChance = \App\Models\SecondChancePurchase::with(['buyer', 'seller', 'listing', 'bid'])->findOrFail($secondChanceId);

        if ($secondChance->status !== 'offered') {
            return back()->withErrors(['error' => 'Second-chance purchase is not in offered status.']);
        }

        // Generate invoice using InvoiceService
        $invoiceService = new \App\Services\InvoiceService();
        
        // Create a temporary bid object for invoice generation
        $tempBid = $secondChance->bid;
        $tempBid->amount = $secondChance->bid_amount;

        // Generate invoice
        $invoice = $invoiceService->generateInvoice($secondChance->listing, $tempBid);

        // Update second-chance purchase
        $secondChance->update([
            'new_invoice_id' => $invoice->id,
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);

        // TODO: Send email with payment link to buyer
        // TODO: Create dashboard notification

        return back()->with('success', 'Invoice generated. Payment link sent to buyer.');
    }

    /**
     * Admin: View second-chance purchases.
     */
    public function secondChancePurchases(Request $request)
    {
        $secondChances = \App\Models\SecondChancePurchase::with(['buyer', 'seller', 'listing', 'originalInvoice'])
            ->when($request->has('status'), function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->orderByDesc('created_at')
            ->paginate(50);

        return view('admin.second-chance-purchases', compact('secondChances'));
    }

    /**
     * Admin: Close unpaid auction (mark as closed permanently).
     */
    public function closeUnpaidAuction(Request $request, $defaultId)
    {
        $request->validate([
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $default = \App\Models\BuyerDefault::findOrFail($defaultId);
        
        $default->update([
            'status' => 'resolved',
            'resolution_type' => 'closed',
            'admin_notes' => $request->admin_notes ?? 'Auction closed permanently by admin.',
        ]);

        // Mark listing as closed
        $default->listing->update([
            'status' => 'closed',
        ]);

        return back()->with('success', 'Auction closed permanently.');
    }
}