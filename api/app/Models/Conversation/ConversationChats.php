<?php

namespace App\Models\Conversation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConversationChats extends Model
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
}
