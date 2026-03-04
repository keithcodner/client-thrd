<?php

namespace App\Models\Conversation;

use App\Models\Conversation\ConversationCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConversationGroupTracker extends Model
{
    use HasFactory;

    /*
        - this tables tracks the organization of conversations for a specific user
        - each trackee record (this record), can only be assigned a conversation category id at a time
        - for example, this conversation cannot be part of multiple conversation categories (we'll need proper logic for this)
        - we shouldn't be too concerned about user_id (who ever created the convo catetgory, is assigned the user_id here)
    
    */

    // Define the table name explicitly (optional if the table name follows Laravel's naming conventions)
    protected $table = 'conversation_group_tracker';

    // Indicate that the table does not use auto-incrementing IDs
    public $incrementing = false;

    // Define the primary key (if applicable)
    protected $primaryKey = 'id';

    // Specify the primary key type
    protected $keyType = 'int';

    // Specify if the model should use timestamps (created_at, updated_at)
    public $timestamps = true;

    // Define fillable attributes for mass assignment
    protected $fillable = [
        'id',
        'convo_cat_id', //tracks conversation category id
        'convo_id', // tracks conversation id
        'user_id', //tracks user id of the who the conversation cateogry belongs to (not to be confused with inititor/propsect)
        'an_id', //random string
        'tracker_type', //default, custom
        'tracker_status', //active
        'tracker_order', //determined by server logic?
        'created_at',
        'updated_at',
    ];

    // If needed, specify any hidden attributes
    protected $hidden = [];

    // If needed, specify any casts
    protected $casts = [
        'id' => 'integer',
        'convo_cat_id' => 'integer',
        'convo_id' => 'integer',
        'user_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // public function convoGroupTracker()
    // {
    //     return $this->hasOne(ConversationCategory::class, 'owner_user_id');
    // }
}
