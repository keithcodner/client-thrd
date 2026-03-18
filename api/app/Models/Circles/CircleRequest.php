<?php

namespace App\Models\Circles;

use Illuminate\Database\Eloquent\Model;

class CircleRequest extends Model
{
    protected $table = 'circles_requests';

    protected $fillable = [
        'circle_id',
        'requesting_to_join_user_id',
        'type',
        'status',
        'created_at',
        'updated_at',
    ];
}