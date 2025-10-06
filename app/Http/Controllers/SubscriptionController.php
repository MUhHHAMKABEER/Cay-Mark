<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
   public function plans()
{
    $plans = [
        [
            'name' => 'Casual',
            'role' => 'seller',
            'price' => 65,
            'duration' => 'per listing',
            'features' => [
                '1 Active Listing Only (30-Day Marketplace Listing)',
                'No Auction Access',
                'Basic Listing Placement (First Tier)',
                'No Auction Features',
                'No Reserve Price Allowed',
                'Must Repurchase to Relist',
            ]
        ],
        [
            'name' => 'Standard',
            'role' => 'seller',
            'price' => 150,
            'duration' => 'per year',
            'features' => [
                '2 Active Listings per Month',
                'Marketplace + Auction Access',
                'Basic Listing Placement (First Tier) by Default',
                'Boosted Listing Feature Available (Second Tier Upgrade)',
                'Seller Dashboard Access',
                'Additional Listing Add-ons Available',
                'No Reserve Price Allowed',
            ]
        ],
        [
            'name' => 'Advanced',
            'role' => 'seller',
            'price' => 500,
            'duration' => 'per year',
            'features' => [
                '10 Active Listings per Month',
                'Marketplace + Auction Access',
                'Boosted Listing Placement (Second Tier) by Default',
                'Eligible for Premium Placement Upgrade (Third Tier)',
                'Seller Dashboard Access',
                'Additional Listing Add-ons Available',
                'Reserve Prices Allowed',
            ]
        ]
    ];

    return view('subscription.seller_plans', compact('plans'));
}
public function simulate(Request $request)
{
    $plan = $request->query('plan');

    $amounts = [
        'casual' => 65,
        'standard' => 150,
        'advanced' => 500,
    ];

    // Simulate payment by creating a record in payments table
    \App\Models\Payment::create([
        'user_id' => auth()->id(),
        'payment_type' => 'buyer_subscription',
        'reference_id' => null,
        'amount' => $amounts[$plan] ?? 0,
        'status' => 'paid',
        'meta' => json_encode(['plan' => $plan]),
    ]);

    // Redirect to welcome page
    return redirect()->route('welcome')->with('success', 'Subscription activated for ' . ucfirst($plan) . ' plan!');
}

}
