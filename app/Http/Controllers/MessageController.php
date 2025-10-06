<?php
namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Chat;
use Illuminate\Http\Request;
 use App\Events\NewMessageSent;

class MessageController extends Controller
{


public function store(Request $request)
{
    $message = Message::create([
        'chat_id' => $request->chat_id,
        'sender_id' => auth()->id(),
        'body' => $request->message,
    ]);

    broadcast(new NewMessageSent($message))->toOthers();

    return response()->json($message);
}


    public function fetch($chatId)
    {
        $chat = Chat::with('messages.user')->findOrFail($chatId);

        return response()->json($chat->messages);
    }
}
