<?php

namespace App\Models\ProNetwork;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProNetworkUserProfileInterest extends Model
{
    use HasFactory;

    protected $table = 'pronetwork_user_profile_interests';
    protected $primaryKey  = 'id';

    // Allow mass assignment on these fields
    protected $fillable = [
        'user_id',
        'group_id',
        'type',
        'status',
        'order',
        'created_at',
        'updated_at'
    ];

    // Cast the fields to appropriate data types
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Enable timestamps (created_at and updated_at)
    public $timestamps = true;

    // Define the relationship with the User model
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Define the relationship with the Group model
    public function group()
    {
        //return $this->belongsTo(Group::class, 'group_id');
        return 'this';
    }


}
