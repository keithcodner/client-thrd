<?php

namespace App\Models\ProNetwork;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProNetworkUserProfileAnalytics extends Model
{
    use HasFactory;

    protected $table = 'pronetwork_user_profile_analytics';
    protected $primaryKey  = 'id';

    // Allow mass assignment on these fields
    protected $fillable = [
        'user_id',
        'profile_views_count',
        'interactive_count',
        'connections_count',
        'name',
        'value',
        'op_1',
        'op_2',
        'op_3',
        'op_4',
        'op_5',
        'type',
        'status',
        'created_at',
        'updated_at'
    ];

    // Cast the fields to appropriate data types
    protected $casts = [
        'profile_views_count' => 'integer',
        'interactive_count' => 'integer',
        'connections_count' => 'integer',
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
