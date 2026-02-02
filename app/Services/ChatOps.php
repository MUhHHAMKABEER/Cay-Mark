<?php

namespace App\Services;

class ChatOps
{
    public static function sendMessage($request, $chatId)
    {
        try {
            \Log::info("sendMessage called", [
                'chatId'      => $chatId,
                'request_data'=> $request->all(),
                'auth_user'   => $request->user()?->id,
            ]);

            $request->validated();

            $user = $request->user();
            if (!$user) {
                return response()->json(['success' => false, 'error' => 'Not authenticated'], 401);
            }

            $chat = \App\Models\Chat::find($chatId);
            if (!$chat) {
                return response()->json(['success' => false, 'error' => 'Chat not found'], 404);
            }

            if (!in_array($user->id, [$chat->buyer_id, $chat->seller_id])) {
                return response()->json(['success' => false, 'error' => 'Unauthorized'], 403);
            }

            $message = \App\Models\Message::create([
                'chat_id'   => $chat->id,
                'sender_id' => $user->id,
                'message'   => $request->input('message'),
            ]);

            $message->load('user');
            $chat->touch();

            event(new \App\Events\NewMessageSent($message));

            return response()->json([
                'success' => true,
                'message' => $message,
            ], 201);
        } catch (\Throwable $e) {
            \Log::error("sendMessage: Exception", [
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

