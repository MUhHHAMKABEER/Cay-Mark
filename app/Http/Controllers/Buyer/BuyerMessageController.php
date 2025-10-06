<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Chat;
use App\Models\Message;
use App\Events\NewMessageSent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BuyerMessageController extends Controller
{
    /**
     * Show list of chats and optionally an active chat.
     * Expects optional ?chat_id= param to mark which chat should be open.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // load chats where user is buyer
        $chats = Chat::with(['buyer', 'seller', 'listing', 'messages'])
            ->where('buyer_id', $user->id)
            ->orWhere('seller_id', $user->id)
            ->get();

        // get active chat if provided
        $activeChat = null;
        if ($request->has('chat_id')) {
            $activeChat = Chat::with(['buyer', 'seller', 'listing', 'messages'])
                ->find($request->input('chat_id'));
        } else {
            // optional: pick the first chat as active
            $activeChat = $chats->first() ?? null;
        }

        // ensure messages collection is sorted ascending by created_at for consistent rendering
        if ($activeChat) {
            $activeChat->setRelation('messages', $activeChat->messages->sortBy('created_at')->values());
        }

        // pass data to view (adjust view path/name as you have it)
        return view('Buyer.messages', [
            'chats' => $chats,
            'activeChat' => $activeChat,
            'chatId' => $activeChat?->id,
        ]);
    }

}
