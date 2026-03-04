<?php

namespace App\Models\Conversation;

use App\Models\Conversation\ConversationGroupTracker;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConversationCategory extends Model
{
    use HasFactory;

    /*
        - how this table works: The table is a way for conversations to be organized
        --> when a user is created we should create a default conversation category called 'archived' chats
        ---> if a conversation is not in a category, its displayed as normal (not categorized)
        --> user creates category, that belongs to this user(user can have multiple categories)
        --> this appears in their chat window (with no current chats added)...other than archived
        --> when a new conversation is created; it is uncategorized BUT can be added to the archived category disignated for this user
        -->when a trade is completed or aborted, its automatically added to the archived conversation category
        --> we need proper validation that a user is not managing another persons categories

        CORE RULES:
        --> each conversation cannot be added to  more than 2 conversation categories at a time; one for the initiator and one for the prospect (or initiator, accepter)
        --> conversations are added to the conversation_group_tracker table and reference this table for the name and user id
        -- 
    
    */

    // Define the table name if it doesn't follow Laravel's naming conventions
    protected $table = 'conversation_categories';

    // Specify the primary key if it's not 'id'
    protected $primaryKey = 'id';

    // Specify whether the primary key is auto-incrementing
    public $incrementing = true;

    // Specify the primary key type
    protected $keyType = 'int';

    // Define fillable attributes for mass assignment
    protected $fillable = [
        'owner_user_id',
        'category_an_id', //alpha numeric id
        'category_name',
        'category_description', //default
        'category_expand_state', //close (defeault), opened
        'category_status', //active, in-active
        'category_type', //default, custom
        'created_at',
        'updated_at',
    ];

    public function convoGroupTracker()
    {
        return $this->hasMany(ConversationGroupTracker::class, 'convo_cat_id');
    }
}
