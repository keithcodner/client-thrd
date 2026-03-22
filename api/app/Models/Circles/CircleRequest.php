<?php

namespace App\Models\Circles;

use Illuminate\Database\Eloquent\Model;
use App\Models\Circle;
use App\Models\User;

class CircleRequest extends Model
{
    protected $table = 'circles_requests';

    protected $fillable = [
        'circle_id',
        'requester_user_id',
        'requesting_to_join_user_id',
        'type',
        'status',
        'created_at',
        'updated_at',
    ];

    /**
     * Get the circle that this request belongs to
     */
    public function circle()
    {
        return $this->belongsTo(Circle::class, 'circle_id');
    }

    /**
     * Get the user who sent the request
     */
    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_user_id');
    }

    /**
     * Get the user being invited
     */
    public function requestedUser()
    {
        return $this->belongsTo(User::class, 'requesting_to_join_user_id');
    }
}