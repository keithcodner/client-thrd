<?php

namespace App\Models\ProNetwork;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProNetworkUserProfileExperience extends Model
{
    use HasFactory;

    protected $table = 'pronetwork_user_profile_experience';
    protected $primaryKey  = 'id';

    // Define which fields are mass-assignable (fillable)
    protected $fillable = [
        'user_id',
        'position',
        'company',
        'location_city',
        'location_country',
        'location_state_province',
        'start_date',
        'end_date',
        'status',
        'type',
        'order',
        'created_at',
        'updated_at'
    ];

    // Optionally define the cast types for date fields
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // If you want to disable timestamps, you can set this to false
    public $timestamps = true;

    // Define relationships, if necessary (e.g., belongs to a user)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


}
