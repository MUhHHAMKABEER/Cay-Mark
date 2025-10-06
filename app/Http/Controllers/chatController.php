<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Message;
use Illuminate\Http\Request;
use App\Events\NewMessageSent;
use Illuminate\Support\Facades\Log;   // ✅ this is missing


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
  public function sendMessage(Request $request, $chatId)
    {
        try {
            Log::info("sendMessage called", [
                'chatId'      => $chatId,
                'request_data'=> $request->all(),
                'auth_user'   => $request->user()?->id,
            ]);

            $request->validate([
                'message' => 'required|string|max:2000',
            ]);

            $user = $request->user();
            if (!$user) {
                return response()->json(['success' => false, 'error' => 'Not authenticated'], 401);
            }

            $chat = Chat::find($chatId);
            if (!$chat) {
                return response()->json(['success' => false, 'error' => 'Chat not found'], 404);
            }

            if (!in_array($user->id, [$chat->buyer_id, $chat->seller_id])) {
                return response()->json(['success' => false, 'error' => 'Unauthorized'], 403);
            }

            // create message using sender_id (do NOT rely on user_id column)
            $message = Message::create([
                'chat_id'   => $chat->id,
                'sender_id' => $user->id,
                'message'   => $request->input('message'),
            ]);

            // eager load sender for broadcasting
            $message->load('user');

            // touch chat so it sorts by updated_at
            $chat->touch();

            // broadcast the event (Your NewMessageSent expects Message->user loaded)
            event(new NewMessageSent($message));

            return response()->json([
                'success' => true,
                'message' => $message,
            ], 201);

        } catch (\Throwable $e) {
            Log::error("sendMessage: Exception", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'error'   => 'Server Error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

}
