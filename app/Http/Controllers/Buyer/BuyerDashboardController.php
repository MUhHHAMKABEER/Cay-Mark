<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Bid;
use App\Models\Invoice;
use App\Models\Listing;
use App\Models\PostAuctionThread;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BuyerDashboardController extends Controller
{
    /**
     * Display the buyer dashboard with all tabs data
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $activeTab = $request->get('tab', 'user');

        // Get all data for different tabs
        $data = [
            'user' => $user,
            'activeTab' => $activeTab,
            'currentAuctions' => $this->getCurrentAuctions($user),
            'wonAuctions' => $this->getWonAuctions($user),
            'lostAuctions' => $this->getLostAuctions($user),
            'savedItems' => $this->getSavedItems($user),
            'notifications' => $this->getNotifications($user),
            'messagingThreads' => $this->getMessagingThreads($user),
        ];

        return view('dashboard.buyer', $data);
    }

    /**
     * Get current auctions where buyer has placed bids
     */
    private function getCurrentAuctions($user)
    {
        // Get all listings where user has placed bids
        $listingIds = Bid::where('user_id', $user->id)
            ->distinct()
            ->pluck('listing_id');

        $listings = Listing::with(['images', 'bids' => function($query) use ($user) {
                $query->where('user_id', $user->id)->latest();
            }])
            ->whereIn('id', $listingIds)
            ->where(function($query) use ($user) {
                $query->where(function($q) {
                    $q->where('status', 'active')
                      ->orWhere('status', 'pending');
                })
                ->orWhere(function($q) use ($user) {
                    $q->where('status', 'sold')
                      ->whereHas('invoices', function($inv) use ($user) {
                          $inv->where('buyer_id', $user->id)
                              ->where('payment_status', 'pending');
                      });
                });
            })
            ->get()
            ->map(function($listing) use ($user) {
                // Get highest bid on this listing
                $highestBid = $listing->bids()->max('amount');
                $userHighestBid = $listing->bids()->where('user_id', $user->id)->max('amount');
                
                // Check if user won but payment pending
                $invoice = Invoice::where('listing_id', $listing->id)
                    ->where('buyer_id', $user->id)
                    ->where('payment_status', 'pending')
                    ->first();

                $listing->highest_bid = $highestBid ?? $listing->starting_price ?? 0;
                $listing->user_highest_bid = $userHighestBid;
                $listing->is_winning = $invoice ? true : false;
                $listing->pending_invoice = $invoice;
                
                return $listing;
            });

        return $listings;
    }

    /**
     * Get won auctions (payment completed)
     */
    private function getWonAuctions($user)
    {
        $invoices = Invoice::with(['listing.images', 'bid'])
            ->where('buyer_id', $user->id)
            ->where('payment_status', 'paid')
            ->latest('paid_at')
            ->get()
            ->map(function($invoice) {
                $invoice->listing->final_price = $invoice->winning_bid_amount;
                return $invoice;
            });

        return $invoices;
    }

    /**
     * Get lost auctions
     */
    private function getLostAuctions($user)
    {
        // Get listings where user bid but didn't win
        $userBidListingIds = Bid::where('user_id', $user->id)
            ->distinct()
            ->pluck('listing_id');

        // Get listings that are sold but user didn't win
        $lostListings = Listing::with(['images', 'invoices' => function($query) {
                $query->where('payment_status', 'paid');
            }])
            ->whereIn('id', $userBidListingIds)
            ->where('status', 'sold')
            ->whereDoesntHave('invoices', function($query) use ($user) {
                $query->where('buyer_id', $user->id)
                      ->where('payment_status', 'paid');
            })
            ->get()
            ->map(function($listing) {
                $winningInvoice = $listing->invoices()->where('payment_status', 'paid')->first();
                $listing->final_price = $winningInvoice ? $winningInvoice->winning_bid_amount : null;
                return $listing;
            })
            ->filter(function($listing) {
                return $listing->final_price !== null;
            });

        return $lostListings;
    }

    /**
     * Get saved items (watchlist)
     */
    private function getSavedItems($user)
    {
        return $user->watchlist()
            ->with(['images', 'bids' => function($query) {
                $query->latest()->limit(1);
            }])
            ->get()
            ->map(function($listing) {
                $highestBid = $listing->bids()->max('amount');
                $listing->highest_bid = $highestBid ?? $listing->starting_price ?? 0;
                return $listing;
            });
    }

    /**
     * Get notifications
     */
    private function getNotifications($user)
    {
        return $user->notifications()
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();
    }

    /**
     * Get messaging threads (post-payment)
     */
    private function getMessagingThreads($user)
    {
        return PostAuctionThread::with(['listing.images', 'invoice', 'seller'])
            ->where('buyer_id', $user->id)
            ->where('is_unlocked', true)
            ->latest('unlocked_at')
            ->get();
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
        $user->password = \Hash::make($request->password);
        $user->save();

        return back()->with('success', 'Password changed successfully.');
    }
}

