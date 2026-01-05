<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string|min:10',
        ]);

        // TODO: Create SupportTicket model and store ticket
        // For now, just return success message
        return back()->with('success', 'Support ticket submitted successfully. We will respond soon.');
    }
}

