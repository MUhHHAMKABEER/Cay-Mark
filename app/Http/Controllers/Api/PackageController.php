<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Package;

class PackageController extends Controller
{
   public function byRole($role)
{
    $packages = Package::where('role', strtolower($role))
        ->select(['id','name','title','price','role','description'])
        ->get();

    return response()->json($packages);
}

}
