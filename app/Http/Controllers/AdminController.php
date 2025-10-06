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
use App\Models\Membership;
use App\Models\Payment;
use App\Models\Dispute;
use App\Models\Notification;
use App\Models\Boost;
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
     * Admin Dashboard - Home Page
     */
    public function dashboard()
    {
        $recentActivities = $this->getRecentActivities();
        $alerts = $this->getSystemAlerts();
        
        $stats = [
            'total_users' => User::count(),
            'total_listings' => Listing::count(),
            'pending_listings' => Listing::where('status', 'pending')->count(),
            'active_memberships' => Membership::where('status', 'active')->count(),
            'pending_payments' => Payment::where('status', 'pending')->count(),
            'open_disputes' => Dispute::where('status', 'open')->count(),
        ];

        return view('admin.dashboard', compact('stats', 'alerts', 'recentActivities'));
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
    $listing->status = 'approved';
    $listing->save();

    return redirect()->back()->with('success', 'Listing approved successfully!');
}

public function disapprove($id)
{
    $listing = Listing::findOrFail($id);
    $listing->status = 'rejected';
    $listing->save();

    return redirect()->back()->with('error', 'Listing disapproved!');
}
    public function userManagement()
    {
        $users = User::with(['subscriptions'])
                    ->orderBy('created_at', 'desc')
                    ->paginate(20);
                    
        $userStats = [
            'total' => User::count(),
            'buyers' => User::where('role', 'buyer')->count(),
            'sellers' => User::where('role', 'seller')->count(),
            // 'suspended' => User::where('status', 'suspended')->count(),
            // 'banned' => User::where('status', 'banned')->count(),
        ];

        return view('admin.user', compact('users', 'userStats'));
    }

    /**
     * Membership Management Page
     */
    public function membershipManagement()
    {
        $memberships = Membership::with(['user', 'plan'])
                                ->orderBy('created_at', 'desc')
                                ->paginate(20);
                                
        $membershipStats = [
            'total' => Membership::count(),
            'active' => Membership::where('status', 'active')->count(),
            'expired' => Membership::where('status', 'expired')->count(),
            'pending_renewal' => Membership::where('status', 'pending_renewal')->count(),
            'expiring_soon' => Membership::where('expires_at', '<=', Carbon::now()->addDays(7))
                                        ->where('status', 'active')
                                        ->count(),
        ];

        return view('admin.memberships', compact('memberships', 'membershipStats'));
    }

    /**
     * Listing Review/Approval Page
     */
    public function listingReview()
    {
        $pendingListings = Listing::with(['user', 'category'])
                                ->where('status', 'pending')
                                ->orderBy('created_at', 'desc')
                                ->paginate(15);
                                
        $recentlyApproved = Listing::with(['user'])
                                ->where('status', 'approved')
                                ->where('approved_at', '>=', Carbon::now()->subDays(7))
                                ->orderBy('approved_at', 'desc')
                                ->limit(10)
                                ->get();

        return view('admin.listing-review', compact('pendingListings', 'recentlyApproved'));
    }

    /**
     * Active Listings Management Page
     */
    public function activeListings()
    {
        $activeListings = Listing::with(['user', 'category', 'boosts'])
                                ->where('status', 'active')
                                ->orderBy('created_at', 'desc')
                                ->paginate(20);
                                
        $listingStats = [
            'total_active' => Listing::where('status', 'active')->count(),
            'expired' => Listing::where('status', 'expired')->count(),
            'sold' => Listing::where('status', 'sold')->count(),
            'with_boosts' => Listing::has('boosts')->count(),
        ];

        return view('admin.ActiveListing', compact('activeListings', 'listingStats'));
    }

    /**
     * Boosts & Add-ons Management Page
     */
    public function boostsAddOns()
    {
        $boosts = Boost::with(['listing.user', 'boostPlan'])
                        ->orderBy('created_at', 'desc')
                        ->paginate(20);
                        
        $boostStats = [
            'total_purchases' => Boost::count(),
            'active' => Boost::where('status', 'active')->count(),
            'pending' => Boost::where('status', 'pending')->count(),
            'expired' => Boost::where('status', 'expired')->count(),
            'revenue' => Boost::where('status', 'active')->sum('amount'),
        ];

        return view('admin.boosts-addons', compact('boosts', 'boostStats'));
    }

    /**
     * Payments Management Page
     */
    public function payments()
    {
        $payments = Payment::with(['user', 'listing'])
                            ->orderBy('created_at', 'desc')
                            ->paginate(20);
                            
        $paymentStats = [
            'total_revenue' => Payment::where('status', 'completed')->sum('amount'),
            'pending_release' => Payment::where('status', 'pending_release')->sum('amount'),
            'held' => Payment::where('status', 'held')->sum('amount'),
            'completed' => Payment::where('status', 'completed')->count(),
            'failed' => Payment::where('status', 'failed')->count(),
        ];

        return view('admin.payments', compact('payments', 'paymentStats'));
    }

    /**
     * Disputes Center Page
     */
    public function disputes()
    {
        $disputes = Dispute::with(['buyer', 'seller', 'listing', 'messages'])
                            ->orderBy('created_at', 'desc')
                            ->paginate(15);
                            
        $disputeStats = [
            'open' => Dispute::where('status', 'open')->count(),
            'resolved' => Dispute::where('status', 'resolved')->count(),
            'in_progress' => Dispute::where('status', 'in_progress')->count(),
            'escalated' => Dispute::where('status', 'escalated')->count(),
        ];

        return view('admin.disputes', compact('disputes', 'disputeStats'));
    }

    /**
     * Notification Management Page
     */
    public function notifications()
    {
        $notifications = Notification::with(['user'])
                                    ->orderBy('created_at', 'desc')
                                    ->paginate(20);
                                    
        $systemNotifications = Notification::whereNull('user_id')
                                        ->where('type', 'system')
                                        ->orderBy('created_at', 'desc')
                                        ->limit(50)
                                        ->get();

        return view('admin.notifications', compact('notifications', 'systemNotifications'));
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
        $subscriptionData = Membership::selectRaw('plan_id, status, COUNT(*) as count')
                                    ->groupBy('plan_id', 'status')
                                    ->get();
                                    
        // Dispute analytics
        $disputeData = Dispute::selectRaw('DATE(created_at) as date, status, COUNT(*) as count')
                            ->where('created_at', '>=', Carbon::now()->subDays(30))
                            ->groupBy('date', 'status')
                            ->orderBy('date')
                            ->get();

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
        $user->update(['status' => 'suspended']);
        
        return back()->with('success', 'User suspended successfully.');
    }

    public function banUser(User $user)
    {
        $user->update(['status' => 'banned']);
        
        return back()->with('success', 'User banned successfully.');
    }

    public function approveListing(Listing $listing)
    {
        $listing->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => auth()->id()
        ]);
        
        return back()->with('success', 'Listing approved successfully.');
    }

    public function rejectListing(Listing $listing)
    {
        $listing->update([
            'status' => 'rejected',
            'rejected_at' => now(),
            'rejected_by' => auth()->id()
        ]);
        
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
        
        // Check for open disputes
        $disputeCount = Dispute::where('status', 'open')->count();
        if ($disputeCount > 0) {
            $alerts[] = [
                'type' => 'danger',
                'message' => "{$disputeCount} open disputes need attention",
                'link' => route('admin.disputes')
            ];
        }
        
        // Check for pending payments
        $paymentCount = Payment::where('status', 'pending_release')->count();
        if ($paymentCount > 0) {
            $alerts[] = [
                'type' => 'info',
                'message' => "{$paymentCount} payments pending release",
                'link' => route('admin.payments')
            ];
        }
        
        // Check for expiring memberships
        $expiringCount = Membership::where('expires_at', '<=', Carbon::now()->addDays(3))
                                ->where('status', 'active')
                                ->count();
        if ($expiringCount > 0) {
            $alerts[] = [
                'type' => 'warning',
                'message' => "{$expiringCount} memberships expiring soon",
                'link' => route('admin.memberships')
            ];
        }
        
        return $alerts;
    }
}