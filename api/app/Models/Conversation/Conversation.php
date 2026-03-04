<?php

namespace App\Models\Conversation;

use App\Models\Conversation\ConversationChats;
use App\Models\TradeTransactions\TradeTransaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    /*
        This model is in desparate need of documentation ***
        - type_second:
        const ChatStatus = Object.freeze({
            SOCIAL: { status: 'social', label: 'Social', color: 'bg-blue-600' },
            TRADE: { status: 'trade', label: 'Trade', color: 'bg-orange-500' },
            EVENT: { status: 'event', label: 'Event', color: 'bg-green-500' },
            SERVICE: { status: 'service', label: 'Service', color: 'bg-purple-500' },
            PRIVATE: { status: 'private', label: 'Private', color: 'bg-purple-900' },
            GROUP: { status: 'group', label: 'Group', color: 'bg-green-700' },
            SYSTEM: { status: 'system', label: 'System', color: 'bg-blue-800' },
            ADMIN: { status: 'admin', label: 'Admin', color: 'bg-red-600' }
        });

        types:
        - couple, group (more often than no, it will be couple for now)
    
        status:
        - active, marked_for_deletion, old_chat
        - when 'deleted_by_user_id' and 'deleted_by_from_id' are true, status = marked_for_deletion
        - if deleted_by_user_id = true for 1 out of 2 users, status = old_chat 
        - if status_second = completed for 1 out of 2 users, status = old_chat 
        - new chats , status = active

    */

    protected $table = 'conversation';
    protected $primaryKey  = 'id';

    protected $fillable = [
        'id',
        'user_id',
        'from_id',
        'item_id',
        'conv_an_id',
        'group_ids',
        'title',
        'content',
        'deleted_by_user_id',
        'deleted_by_from_id',
        'deleted_by_group_ids',
        'status',// active, marked_for_deletion, old_chat
        'status_second', //complete, 
        'type',
        'type_second', // check chat enums
        'created_at',
        'updated_at',
    ];

    public function chat()
    {
        return $this->hasMany(ConversationChats::class, 'conversation_id')
        ->orderBy('created_at', 'DESC');
    }

    public function tradeTransaction()
    {
        return $this->hasOne(TradeTransaction::class, 'trade_conversation_id');
    }

    public function user_id_data()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function from_id_data()
    {
        return $this->belongsTo(User::class, 'from_id');
    }

    
}
