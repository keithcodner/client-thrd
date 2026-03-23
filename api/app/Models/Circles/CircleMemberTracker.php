<?php

namespace App\Models\Circles;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

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

    /**
     * Get the user associated with the circle member.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the circle associated with the member.
     */
    public function circle()
    {
        return $this->belongsTo(Circle::class, 'circle_id');
    }
}