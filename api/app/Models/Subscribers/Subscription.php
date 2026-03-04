<?php

namespace App\Models\Subscribers;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Subscription extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'subscriptions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'subbee',
        'subber',
        'isSubberSubbed',
        'op1',
        'op2',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user being subscribed to (subbee).
     */
    public function subbeeUser()
    {
        return $this->belongsTo(User::class, 'subbee', 'id');
    }

    /**
     * Get the user who is subscribing (subber).
     */
    public function subberUser()
    {
        return $this->belongsTo(User::class, 'subber', 'id');
    }
}
