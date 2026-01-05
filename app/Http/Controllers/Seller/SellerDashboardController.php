<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Listing;
use App\Models\PostAuctionThread;
use App\Models\SellerPayoutMethod;
use App\Models\UserDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class SellerDashboardController extends Controller
{
    /**
     * Display the seller dashboard with all tabs data
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
            'pastAuctions' => $this->getPastAuctions($user),
            'rejectedListings' => $this->getRejectedListings($user),
            'auctionSummary' => $this->getAuctionSummary($user),
            'notifications' => $this->getNotifications($user),
            'messagingThreads' => $this->getMessagingThreads($user),
            'payoutMethod' => $this->getPayoutMethod($user),
            'documents' => $this->getDocuments($user),
        ];

        return view('dashboard.seller', $data);
    }

    /**
     * Get current auctions (active + awaiting PIN confirmation)
     */
    private function getCurrentAuctions($user)
    {
        $listings = Listing::with(['images', 'bids', 'invoices' => function($query) {
                $query->where('payment_status', 'paid');
            }])
            ->where('seller_id', $user->id)
            ->where(function($query) {
                $query->where('status', 'active')
                    ->orWhere('status', 'pending')
                    ->orWhere(function($q) {
                        $q->where('status', 'sold')
                          ->whereHas('invoices', function($inv) {
                              $inv->where('payment_status', 'paid');
                          })
                          ->where('pickup_confirmed', false);
                    });
            })
            ->get()
            ->map(function($listing) {
                // Get highest bid or final sale price
                $highestBid = $listing->bids()->max('amount');
                $winningInvoice = $listing->invoices()->where('payment_status', 'paid')->first();
                
                $listing->current_bid = $winningInvoice ? $winningInvoice->winning_bid_amount : ($highestBid ?? $listing->starting_price ?? 0);
                $listing->awaiting_pin = $winningInvoice && !$listing->pickup_confirmed;
                $listing->winning_invoice = $winningInvoice;
                
                return $listing;
            });

        return $listings;
    }

    /**
     * Get past auctions (completed with pickup confirmed)
     */
    private function getPastAuctions($user)
    {
        $listings = Listing::with(['images', 'invoices' => function($query) {
                $query->where('payment_status', 'paid');
            }])
            ->where('seller_id', $user->id)
            ->where('status', 'sold')
            ->where('pickup_confirmed', true)
            ->latest('pickup_confirmed_at')
            ->get()
            ->map(function($listing) {
                $winningInvoice = $listing->invoices()->where('payment_status', 'paid')->first();
                $listing->final_price = $winningInvoice ? $winningInvoice->winning_bid_amount : 0;
                return $listing;
            });

        return $listings;
    }

    /**
     * Get rejected listings
     */
    private function getRejectedListings($user)
    {
        $listings = Listing::with('images')
            ->where('seller_id', $user->id)
            ->where('status', 'rejected')
            ->latest('updated_at')
            ->get()
            ->map(function($listing) {
                // Calculate time remaining for editing (72 hours from rejection)
                $rejectedAt = $listing->updated_at;
                $deadline = $rejectedAt->copy()->addHours(72);
                $now = Carbon::now();
                
                $listing->can_edit = $now->lessThan($deadline);
                $listing->edit_deadline = $deadline;
                $listing->time_remaining = $now->lessThan($deadline) ? $now->diffForHumans($deadline, true) : null;
                
                return $listing;
            });

        return $listings;
    }

    /**
     * Get auction summary statistics
     */
    private function getAuctionSummary($user)
    {
        // Current: Active auctions + awaiting pickup confirmation
        $currentCount = Listing::where('seller_id', $user->id)
            ->where(function($query) {
                $query->where('status', 'active')
                    ->orWhere('status', 'pending')
                    ->orWhere(function($q) {
                        $q->where('status', 'sold')
                          ->whereHas('invoices', function($inv) {
                              $inv->where('payment_status', 'paid');
                          })
                          ->where('pickup_confirmed', false);
                    });
            })
            ->count();

        // Past: Completed sales
        $pastListings = Listing::where('seller_id', $user->id)
            ->where('status', 'sold')
            ->where('pickup_confirmed', true)
            ->with(['invoices' => function($query) {
                $query->where('payment_status', 'paid');
            }])
            ->get();

        $totalItemsSold = $pastListings->count();
        $totalSalesRevenue = $pastListings->sum(function($listing) {
            $invoice = $listing->invoices()->where('payment_status', 'paid')->first();
            return $invoice ? $invoice->winning_bid_amount : 0;
        });

        return [
            'current_count' => $currentCount,
            'total_items_sold' => $totalItemsSold,
            'total_sales_revenue' => $totalSalesRevenue,
        ];
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
        return PostAuctionThread::with(['listing.images', 'invoice', 'buyer'])
            ->where('seller_id', $user->id)
            ->where('is_unlocked', true)
            ->latest('unlocked_at')
            ->get()
            ->filter(function($thread) {
                return $thread->invoice && $thread->invoice->payment_status === 'paid';
            });
    }

    /**
     * Get payout method
     */
    private function getPayoutMethod($user)
    {
        return SellerPayoutMethod::where('user_id', $user->id)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Get user documents
     */
    private function getDocuments($user)
    {
        return UserDocument::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Update payout settings
     */
    public function updatePayout(Request $request)
    {
        $request->validate([
            'bank_name' => 'required|string|max:255',
            'account_holder_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'routing_number' => 'nullable|string|max:255',
            'swift_number' => 'nullable|string|max:255',
            'country' => 'required|string|max:255',
        ]);

        $user = Auth::user();

        // Deactivate existing payout methods
        SellerPayoutMethod::where('user_id', $user->id)
            ->update(['is_active' => false]);

        // Create or update payout method
        SellerPayoutMethod::updateOrCreate(
            ['user_id' => $user->id, 'is_active' => true],
            [
                'bank_name' => $request->bank_name,
                'account_holder_name' => $request->account_holder_name,
                'account_number' => $request->account_number,
                'routing_number' => $request->routing_number,
                'swift_number' => $request->swift_number,
                'is_active' => true,
            ]
        );

        return back()->with('success', 'Payout settings updated successfully.');
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

    /**
     * Confirm pickup with PIN
     */
    public function confirmPickup(Request $request, $listingId)
    {
        $request->validate([
            'pickup_pin' => 'required|string',
        ]);

        $listing = Listing::where('id', $listingId)
            ->where('seller_id', Auth::id())
            ->firstOrFail();

        if ($listing->pickup_pin !== $request->pickup_pin) {
            return back()->withErrors(['pickup_pin' => 'Invalid pickup PIN.']);
        }

        $listing->pickup_confirmed = true;
        $listing->pickup_confirmed_at = now();
        $listing->pickup_confirmed_by = Auth::id();
        $listing->save();

        // Trigger payout processing
        // This would typically be handled by a service or event

        return back()->with('success', 'Pickup confirmed successfully. Payment processing has begun.');
    }
}

