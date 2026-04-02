<?php

namespace App\Services;

use App\Models\SupportTicket;
use Illuminate\Support\Facades\Auth;

class SupportOps
{
    public static function buyerStore($request)
    {
        $validated = $request->validated();

        SupportTicket::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'message' => $validated['message'],
            'status' => 'open',
        ]);

        return back()->with('success', 'Support ticket submitted successfully. We will respond soon.');
    }

    public static function sellerStore($request)
    {
        $validated = $request->validated();

        SupportTicket::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'message' => $validated['message'],
            'status' => 'open',
        ]);

        return back()->with('success', 'Support ticket submitted successfully. We will respond soon.');
    }
}
