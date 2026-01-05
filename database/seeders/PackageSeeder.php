<?php
// database/seeders/PackageSeeder.php
namespace Database\Seeders;

use App\Models\Package;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    public function run()
    {
        // Clear existing packages (optional - comment out if you want to keep old data)
        Package::truncate();

        // BUYER MEMBERSHIP - Only one option
        Package::create([
            'title' => 'Buyer Membership',
            'role' => 'buyer',
            'price' => 64.99,
            'duration_days' => 365,
            'auction_access' => true,
            'marketplace_access' => true,
            'buy_now_feature' => true,
            'features' => json_encode([
                'Full vehicle details access',
                'Bidding in all auctions',
                'Able to purchase vehicles with Buy Now prices',
                'Seller messaging AFTER winning an item',
                'Full buyer dashboard and notifications',
                'Platform access fee: $64.99 per year'
            ])
        ]);

        // INDIVIDUAL SELLER - Free (no payment required at registration)
        Package::create([
            'title' => 'Individual Seller',
            'role' => 'seller',
            'price' => 0.00, // Free - no payment required at registration
            'duration_days' => null, // No annual membership
            'max_listings' => null, // Unlimited submissions
            'auction_access' => true,
            'marketplace_access' => true,
            'seller_dashboard' => true,
            'buy_now_feature' => true,
            'reserve_pricing' => true,
            'features' => json_encode([
                'Unlimited listing submissions',
                'No registration fee',
                'May set Buy Now Price',
                'May set Reserve Price',
                'May set Starting Bid Price',
                'No relisting feature (create new listing)',
                'Rejected listings can be edited and resubmitted',
                '4% seller commission (minimum $150)'
            ])
        ]);

        // BUSINESS SELLER - Annual membership
        Package::create([
            'title' => 'Business Seller',
            'role' => 'seller',
            'price' => 599.99,
            'duration_days' => 365,
            'max_listings' => null, // Unlimited listings
            'auction_access' => true,
            'marketplace_access' => true,
            'seller_dashboard' => true,
            'buy_now_feature' => true,
            'reserve_pricing' => true,
            'account_manager' => true,
            'features' => json_encode([
                'Unlimited listing submissions',
                'No per-listing fee',
                'Free relisting within 48 hours (for items with no sales)',
                'May set Buy Now Price',
                'May set Reserve Price',
                'May set Starting Bid Price',
                'Advanced listing management tools',
                '4% seller commission (minimum $150)',
                'Annual membership: $599.99 per year'
            ])
        ]);
    }
}
