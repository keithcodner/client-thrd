<?php

namespace App\Models\ProNetwork;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProNetworkUserProfileVolunteering extends Model
{
    use HasFactory;

    protected $table = 'pronetwork_user_profile_volunteering';
    protected $primaryKey  = 'id';

    // Allow mass assignment on these fields
    protected $fillable = [
        'user_id',
        'position',
        'volunteer_company',
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

    // Cast the fields to appropriate data types
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
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
