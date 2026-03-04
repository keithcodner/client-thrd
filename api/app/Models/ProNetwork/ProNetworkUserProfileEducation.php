<?php

namespace App\Models\ProNetwork;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProNetworkUserProfileEducation extends Model
{
    use HasFactory;

    protected $table = 'pronetwork_user_profile_education';
    protected $primaryKey  = 'id';

    // Allow mass assignment on these fields
    protected $fillable = [
        'user_id',
        'school',
        'location_country',
        'degree',
        'field_of_study',
        'grade',
        'description',
        'location_city',
        'location_state_province',
        'location_state_province_abbrv',
        'start_date',
        'end_date',
        'status',
        'type',
        'order',
    ];

    // Casts for dates and datetime fields
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Enable timestamps
    public $timestamps = true;

    // Define the relationship with the User model
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


}
