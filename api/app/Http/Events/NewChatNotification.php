<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Conversation\ConversationChats;
use App\Models\Item;
use App\Models\User;
use App\Models\Conversation\Conversation;

class NewChatNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $content;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(ConversationChats $chat)
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
        /*DO NOT REMOVE ABC for now, its working when its there now....sighs*/
        return new PresenceChannel('sitePresenceChat'.$this->content->conversation_id);
    }

    public function broadcastAs() {
        return 'sitePresenceBroadCast';
    }

    public function broadcastWith()
    {
        return [
            'message' => $this->content->content,
            
        ];
    }
}
