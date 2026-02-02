<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\SellerSupportStoreRequest;
use App\Services\SupportOps;

class SupportController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(SellerSupportStoreRequest $request)
    {
        return SupportOps::sellerStore($request);
    }
}

