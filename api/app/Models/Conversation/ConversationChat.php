<?php

namespace App\Models\Conversation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class ConversationChat extends Model
{
    use HasFactory;

    protected $table = 'conversation_chats';

    protected $fillable = [
        'init_user_id',
        'end_user_id',
        'conversation_id',
        'chat_an_id',
        'title',
        'content',
        'attachment',
        'op1',
        'op2',
        'seen_by_other_user',
        'seen_by_received_user',
        'type',
        'created_at',
        'updated_at',
    ];

    /**
     * Get the user who sent the message
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'init_user_id');
    }

    /**
     * Get the conversation this message belongs to
     */
    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }
}
