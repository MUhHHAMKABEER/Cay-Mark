<?php

namespace App\Services;

class MessageOps
{
    public static function store($request)
    {
        $request->validated();

        $message = \App\Models\Message::create([
            'chat_id' => $request->chat_id,
            'sender_id' => auth()->id(),
            'body' => $request->message,
        ]);

        broadcast(new \App\Events\NewMessageSent($message))->toOthers();

        return response()->json($message);
    }
}

