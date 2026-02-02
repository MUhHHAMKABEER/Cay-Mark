<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Message;
use Illuminate\Http\Request;
use App\Http\Requests\ChatMessageStoreRequest;
use App\Events\NewMessageSent;
use Illuminate\Support\Facades\Log;   // ✅ this is missing
use App\Services\ChatOps;


class ChatController extends Controller
{
    public function chat(Request $request, $chatId = null)
    {
        $userId = auth()->id();

        $chats = Chat::with(['buyer', 'seller', 'listing', 'messages.user'])
            ->where(function($q) use ($userId) {
                $q->where('buyer_id', $userId)
                  ->orWhere('seller_id', $userId);
            })
            ->orderByDesc('updated_at')
            ->get();

        $activeChat = null;
        if ($chatId) {
            $activeChat = $chats->firstWhere('id', $chatId);
        }
        $activeChat = $activeChat ?? $chats->first();

        return view('Seller.Sellerchat', [
            'chats' => $chats,
            'activeChat' => $activeChat,
            'chatId' => $activeChat?->id
        ]);
    }
  public function sendMessage(ChatMessageStoreRequest $request, $chatId)
    {
        return ChatOps::sendMessage($request, $chatId);
    }

}
