<?php

namespace App\Models\ProNetwork;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProNetworkUserProfileSkill extends Model
{
    use HasFactory;

    protected $table = 'pronetwork_user_profile_skills';
    protected $primaryKey  = 'id';

     // Allow mass assignment on these fields
     protected $fillable = [
        'user_id',
        'skill',
        'description',
        'votes',
        'status',
        'type',
        'order',
        'created_at',
        'updated_at'
    ];

    // Cast the fields to appropriate data types
    protected $casts = [
        'votes' => 'integer',
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


}
