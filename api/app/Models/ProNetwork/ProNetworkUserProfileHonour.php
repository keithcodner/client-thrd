<?php

namespace App\Models\ProNetwork;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProNetworkUserProfileHonour extends Model
{
    use HasFactory;

    protected $table = 'pronetwork_user_profile_honours';
    protected $primaryKey  = 'id';

    // Allow mass assignment on these fields
    protected $fillable = [
        'user_id',
        'education_association_id',
        'title',
        'description',
        'issuer',
        'issuer_start_date',
        'status',
        'type',
        'order',
        'created_at',
        'updated_at'
    ];

    // Cast the fields to appropriate data types
    protected $casts = [
        'issuer_start_date' => 'date',
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

    // Define the relationship with the Education Association model
    public function educationAssociation()
    {
        //return $this->belongsTo(EducationAssociation::class, 'education_association_id');
        return '';
    }


}
