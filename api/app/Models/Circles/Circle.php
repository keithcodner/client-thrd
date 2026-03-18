<?php

namespace App\Models\Circles;

use Illuminate\Database\Eloquent\Model;

class Circle extends Model
{
    protected $table = 'circles';

    protected $fillable = [
        'user_owner_id',
        'name',
        'type',
        'status',
        'created_at',
        'updated_at',
    ];

    /**
     * Get all circles by the user owner ID.
     *
     * @param int $userOwnerId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getByUserOwnerId(int $userOwnerId)
    {
        return self::where('user_owner_id', $userOwnerId)->get();
    }

    /**
     * Get the details associated with the circle.
     */
    public function details()
    {
        return $this->hasOne(CircleDetail::class, 'circle_id');
    }

    /**
     * Get the members associated with the circle.
     */
    public function members()
    {
        return $this->hasMany(CircleMemberTracker::class, 'circle_id');
    }

    /**
     * Get the idea board associated with the circle.
     */
    public function ideaBoard()
    {
        return $this->hasOne(CircleIdeaBoard::class, 'circle_id');
    }

    /**
     * Get the requests associated with the circle.
     */
    public function requests()
    {
        return $this->hasMany(CircleRequest::class, 'circle_id');
    }
}