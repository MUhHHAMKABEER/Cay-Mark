<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
// app/Models/User.php
protected $fillable = [
    'name','email','password','username','nationality','island',
    'dob','gender','phone','marketing_opt_in','role',
    'id_type','business_license_path','relationship_to_business','registration_complete',
    'is_restricted','restriction_ends_at','restriction_reason'
];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
   protected $casts = [
        'email_verified_at' => 'datetime',
        'dob' => 'date',
        'marketing_opt_in' => 'boolean',
        'registration_complete' => 'boolean',
        'is_restricted' => 'boolean',
        'restriction_ends_at' => 'datetime',
    ];


    const ROLE_BUYER = 'buyer';
    const ROLE_SELLER = 'seller';
    const ROLE_GUEST = 'guest';

    // optional helper method
    public function isSeller()
    {
        return $this->role === self::ROLE_SELLER;
    }

    public function isBuyer()
    {
        return $this->role === self::ROLE_BUYER;
    }

public function hasActiveSubscription()
{
    return $this->payments()
        ->where('status', 'paid')
        ->exists();
}

public function payments()
{
    return $this->hasMany(Payment::class);
}
public function buyNows()
{
    return $this->hasMany(BuyNow::class);
}

public function watchlist()
{
    return $this->belongsToMany(Listing::class, 'watchlists')->withTimestamps();
}
    public function bids()
    {
        return $this->hasMany(Bid::class);
    }

    public function deposits()
    {
        return $this->hasMany(Deposit::class);
    }

    public function wallet()
    {
        return $this->hasOne(UserWallet::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'buyer_id');
    }

    public function sellerInvoices()
    {
        return $this->hasMany(Invoice::class, 'seller_id');
    }

    public function payouts()
    {
        return $this->hasMany(Payout::class, 'seller_id');
    }

    public function payoutMethod()
    {
        return $this->hasOne(SellerPayoutMethod::class);
    }

    public function documents()
    {
        return $this->hasMany(UserDocument::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function listings()
    {
        return $this->hasMany(Listing::class, 'seller_id');
    }

    public function activeSubscription()
    {
        return $this->hasOne(Subscription::class)->where('status', 'active');
    }

    /**
     * Check if user has completed registration
     */
    public function isRegistrationComplete()
    {
        return $this->registration_complete === true;
    }

    /**
     * Check if user has a role assigned
     */
    public function hasRole()
    {
        return !empty($this->role);
    }

    /**
     * Check if user can access buyer features
     */
    public function canAccessBuyerFeatures()
    {
        return $this->isRegistrationComplete() && $this->role === self::ROLE_BUYER;
    }

    /**
     * Check if user can access seller features
     */
    public function canAccessSellerFeatures()
    {
        return $this->isRegistrationComplete() && $this->role === self::ROLE_SELLER;
    }

    /**
     * Get current auctions for seller (active + awaiting PIN confirmation)
     */
    public function getCurrentAuctions()
    {
        return $this->listings()
            ->currentAuctionsForSeller($this->id)
            ->with(['images', 'bids', 'invoices' => function($query) {
                $query->where('payment_status', 'paid');
            }])
            ->get()
            ->map(function($listing) {
                $listing->current_bid = $listing->getCurrentBid();
                $listing->awaiting_pin = $listing->isAwaitingPickup();
                $listing->winning_invoice = $listing->getWinningInvoice();
                return $listing;
            });
    }

    /**
     * Get past auctions for seller (completed with pickup confirmed)
     */
    public function getPastAuctions()
    {
        return $this->listings()
            ->pastAuctionsForSeller($this->id)
            ->with(['images', 'invoices' => function($query) {
                $query->where('payment_status', 'paid');
            }])
            ->latest('pickup_confirmed_at')
            ->get()
            ->map(function($listing) {
                $listing->final_price = $listing->getFinalPrice();
                return $listing;
            });
    }

    /**
     * Get rejected listings with edit window info
     */
    public function getRejectedListings()
    {
        return $this->listings()
            ->rejectedForSeller($this->id)
            ->with('images')
            ->get()
            ->map(function($listing) {
                $listing->can_edit = $listing->canBeEdited();
                $listing->edit_deadline = $listing->getEditDeadline();
                $listing->hours_remaining = $listing->getEditHoursRemaining();
                return $listing;
            });
    }

    /**
     * Get auction summary statistics for seller
     */
    public function getAuctionSummary(): array
    {
        $totalListings = $this->listings()->count();
        $activeListings = $this->listings()
            ->whereIn('status', ['active', 'pending'])
            ->count();
        $soldListings = $this->listings()
            ->where('status', 'sold')
            ->count();
        
        $totalRevenue = $this->sellerInvoices()
            ->where('payment_status', 'paid')
            ->sum('winning_bid_amount');

        return [
            'total_listings' => $totalListings,
            'active_listings' => $activeListings,
            'sold_listings' => $soldListings,
            'total_revenue' => $totalRevenue,
            // For view compatibility
            'current_count' => $activeListings,
            'total_items_sold' => $soldListings,
            'total_sales_revenue' => $totalRevenue,
        ];
    }

    /**
     * Get current auctions where buyer has placed bids
     */
    public function getCurrentAuctionsAsBuyer()
    {
        $listingIds = $this->bids()->distinct()->pluck('listing_id');

        return Listing::with(['images', 'bids' => function($query) {
                $query->where('user_id', $this->id)->latest();
            }])
            ->whereIn('id', $listingIds)
            ->where(function($query) {
                $query->where(function($q) {
                    $q->where('status', 'active')
                      ->orWhere('status', 'pending');
                })
                ->orWhere(function($q) {
                    $q->where('status', 'sold')
                      ->whereHas('invoices', function($inv) {
                          $inv->where('buyer_id', $this->id)
                              ->where('payment_status', 'pending');
                      });
                });
            })
            ->get()
            ->map(function($listing) {
                $listing->highest_bid = $listing->getHighestBidAmount();
                $listing->user_highest_bid = $listing->getUserHighestBid($this->id);
                $listing->is_winning = $listing->isUserWinning($this->id);
                $listing->pending_invoice = $listing->getPendingInvoiceForUser($this->id);
                return $listing;
            });
    }

    /**
     * Get won auctions for buyer (payment completed)
     */
    public function getWonAuctions()
    {
        return $this->invoices()
            ->where('payment_status', 'paid')
            ->with(['listing.images', 'bid'])
            ->latest('paid_at')
            ->get()
            ->map(function($invoice) {
                $invoice->listing->final_price = $invoice->winning_bid_amount;
                return $invoice;
            });
    }

    /**
     * Get lost auctions for buyer (ended but didn't win)
     */
    public function getLostAuctions()
    {
        $listingIds = $this->bids()->distinct()->pluck('listing_id');

        return Listing::with(['images', 'bids' => function($query) {
                $query->where('user_id', $this->id)->latest();
            }, 'invoices' => function($query) {
                $query->where('payment_status', 'paid');
            }])
            ->whereIn('id', $listingIds)
            ->where('status', 'sold')
            ->whereDoesntHave('invoices', function($query) {
                $query->where('buyer_id', $this->id)
                      ->where('payment_status', 'paid');
            })
            ->get()
            ->map(function($listing) {
                $listing->user_highest_bid = $listing->getUserHighestBid($this->id);
                $winningInvoice = $listing->getWinningInvoice();
                $listing->winning_price = $winningInvoice ? $winningInvoice->winning_bid_amount : 0;
                return $listing;
            });
    }

    /**
     * Get saved items (watchlist) for buyer
     */
    public function getSavedItems()
    {
        return $this->watchlist()
            ->with(['images', 'bids' => function($query) {
                $query->latest()->limit(1);
            }])
            ->get()
            ->map(function($listing) {
                $listing->highest_bid = $listing->getHighestBidAmount();
                return $listing;
            });
    }

    /**
     * Get notifications (latest)
     */
    public function getNotifications($limit = 20)
    {
        return $this->notifications()
            ->latest()
            ->take($limit)
            ->get();
    }

    /**
     * Get active payout method
     */
    public function getActivePayoutMethod()
    {
        return $this->payoutMethod()
            ->where('is_active', true)
            ->first();
    }
}
