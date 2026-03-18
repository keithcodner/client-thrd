<?php

namespace App\Models\Circles;

use Illuminate\Database\Eloquent\Model;

class CircleIdeaBoardPost extends Model
{
    protected $table = 'circles_idea_board_posts';

    protected $fillable = [
        'circles_idea_board_id',
        'user_owner_id',
        'name',
        'type',
        'status',
        'created_at',
        'updated_at',
    ];
}