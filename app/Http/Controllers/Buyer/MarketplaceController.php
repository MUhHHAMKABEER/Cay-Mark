<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MarketplaceController extends Controller
{
       public function index()
    {
        // Later we will fetch products from DB
        return view('Buyer.marketplace');
    }
}
