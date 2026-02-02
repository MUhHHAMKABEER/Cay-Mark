<?php

namespace App\Services;

class SupportOps
{
    public static function buyerStore($request)
    {
        $request->validated();
        return back()->with('success', 'Support ticket submitted successfully. We will respond soon.');
    }

    public static function sellerStore($request)
    {
        $request->validated();
        return back()->with('success', 'Support ticket submitted successfully. We will respond soon.');
    }
}

