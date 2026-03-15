<?php

namespace App\Models\Circles;

use Illuminate\Database\Eloquent\Model;

class CircleIdeaBoard extends Model
{
    protected $table = 'circles_idea_board';

    protected $fillable = [
        'circle_id',
        'file_store_circle_an_id',
        'details',
        'type',
        'status',
        'created_at',
        'update_at',
    ];
}