<?php
namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Chat;
use Illuminate\Http\Request;
use App\Events\NewMessageSent;
use App\Http\Requests\MessageStoreRequest;
use App\Services\MessageOps;

class MessageController extends Controller
{


public function store(MessageStoreRequest $request)
{
    return MessageOps::store($request);
}


    public function fetch($chatId)
    {
        $chat = Chat::with('messages.user')->findOrFail($chatId);

        return response()->json($chat->messages);
    }
}
