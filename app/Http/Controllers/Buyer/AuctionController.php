<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuctionController extends Controller
{
    public function index()
    {
        return response()->json(['message' => 'AuctionController index placeholder']);
    }

    // add other methods you need (show, store, etc.)
}
