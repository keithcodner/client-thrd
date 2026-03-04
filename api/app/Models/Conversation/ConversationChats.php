<?php

namespace App\Models\Conversation;

use App\Models\TradeTransactions\TradeTransaction;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConversationChats extends Model
{
    use HasFactory;

    protected $table = 'conversation_chats';
    protected $primaryKey  = 'id';

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
        'created_at',
        'updated_at'
    ];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class, 'id');
    }
}
