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
use App\Models\User;
use App\Models\Conversation\Conversation;

class NewChatMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $chat;
    public $sender;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(ConversationChat $chat)
    {
        $this->chat = $chat;
        $this->sender = User::find($chat->init_user_id);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('sitePrivateChat.' . $this->chat->conversation_id);
    }

    public function broadcastAs()
    {
        return 'newMessage';
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->chat->id,
            'conversation_id' => $this->chat->conversation_id,
            'content' => $this->chat->content,
            'type' => $this->chat->type ?? 'chat',
            'sender' => [
                'id' => $this->sender->id,
                'name' => $this->sender->name,
            ],
            'created_at' => $this->chat->created_at->toISOString(),
            'timestamp' => $this->chat->created_at->format('g:i A'),
        ];
    }
}
