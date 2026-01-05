<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Package;

class PackageController extends Controller
{
   public function byRole($role)
{
    $packages = Package::where('role', strtolower($role))
        ->get()
        ->map(function ($package) {
            // Create description from features array or use title
            $features = is_array($package->features) ? $package->features : json_decode($package->features, true);
            $description = '';
            if (is_array($features) && !empty($features)) {
                $description = implode(', ', array_slice($features, 0, 3)); // First 3 features as description
            } else {
                $description = $package->title; // Fallback to title
            }
            
            return [
                'id' => $package->id,
                'title' => $package->title,
                'description' => $description,
                'price' => (float) $package->price,
                'role' => $package->role,
                'features' => $features,
                'duration_days' => $package->duration_days,
            ];
        });

    return response()->json($packages);
}

}
