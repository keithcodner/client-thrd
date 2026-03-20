<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Conversation\ConversationChat;
use App\Models\Item;
use App\Models\User;
use App\Models\Conversation\Conversation;

class NewChatMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $content;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(ConversationChat $chat)
    {
        $this->content  = $chat;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('sitePrivateChat.'.$this->content->conversation_id);
    }

    public function broadcastAs() {
        return 'siteBroadCast';
    }

    public function broadcastWith()
    {
        return [
            'message' => $this->content->content,
            /*
                 'user' => $this->user->only(['name', 'email']),
                'sender' => $this->sender,
                'receiver' => $this->receiver,
            */
        ];
    }
}
