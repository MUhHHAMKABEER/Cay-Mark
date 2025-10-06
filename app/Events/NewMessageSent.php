<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewMessageSent implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public $message;

    public function __construct(Message $message)
    {
        // load the sender relationship (message->user uses sender_id now)
        $this->message = $message->load('user');
    }

    public function broadcastOn()
    {
        return new PrivateChannel('chat.' . $this->message->chat_id);
    }

    public function broadcastWith()
    {
        return [
            'message' => [
                'id'         => $this->message->id,
                'chat_id'    => $this->message->chat_id,
                'sender_id'  => $this->message->sender_id,
                'body'       => $this->message->message,
                'created_at' => $this->message->created_at->toDateTimeString(),
                'user'       => $this->message->user ? [
                    'id'   => $this->message->user->id,
                    'name' => $this->message->user->name,
                ] : null,
            ],
        ];
    }
}
