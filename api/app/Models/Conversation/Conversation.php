<?php

namespace App\Models\Conversation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    protected $table = 'conversations';

    protected $fillable = [
        'owner_user_id',
        'to_id',
        'circle_id',
        'conv_an_id',
        'title',
        'content',
        'deleted_by_user_id',
        'deleted_by_from_id',
        'deleted_by_group_ids',
        'status',
        'status_second',
        'type',
        'type_second',
        'created_at',
        'updated_at',
    ];
}
