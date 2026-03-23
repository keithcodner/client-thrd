<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use App\Models\User;

class UserTyping implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets;

    public $conversationId;
    public $user;
    public $isTyping;

    /**
     * Create a new event instance.
     *
     * @param int $conversationId
     * @param User $user
     * @param bool $isTyping
     * @return void
     */
    public function __construct(int $conversationId, User $user, bool $isTyping)
    {
        $this->conversationId = $conversationId;
        $this->user = $user;
        $this->isTyping = $isTyping;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('typing.' . $this->conversationId);
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'typingStatus';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'is_typing' => $this->isTyping,
        ];
    }
}
