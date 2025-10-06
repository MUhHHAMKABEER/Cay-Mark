<?php
// app/Models/Package.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'role',
        'price',
        'duration_days',
        'features',
        'max_listings',
        'max_listings_per_month',
        'auction_bid_limit',
        'auction_access',
        'marketplace_access',
        'seller_dashboard',
        'buy_now_feature',
        'reserve_pricing',
        'account_manager',
    ];

    protected $casts = [
        'features' => 'array',
        'price' => 'decimal:2',
        'auction_bid_limit' => 'decimal:2',
    ];

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function scopeForRole($query, $role)
    {
        return $query->where('role', $role);
    }
}
