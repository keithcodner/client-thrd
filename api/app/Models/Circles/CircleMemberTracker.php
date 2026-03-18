<?php

namespace App\Models\Circles;

use Illuminate\Database\Eloquent\Model;

class CircleMemberTracker extends Model
{
    protected $table = 'circles_member_tracker';

    protected $fillable = [
        'circle_id',
        'user_id',
        'type',
        'status',
        'created_at',
        'updated_at',
    ];
}