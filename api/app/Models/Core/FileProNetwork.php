<?php

namespace App\Models\Core;

use App\Models\Item;
use App\Models\ProNetwork\ProNetworkUserProfile;
use App\Models\Posts;
use App\Models\Wishlist;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileProNetwork extends Model
{
    use HasFactory;

    protected $table = 'files_pronetwork';
    protected $primaryKey  = 'id';
    
    // Allow mass assignment on these fields
    protected $fillable = [
        'reference_id', // 0 = default
        'table_reference_name',
        'file_store_an_id',
        'filename',
        'foldername',
        'status', //active
        'verify_status',
        'type', //background_profile_image, file?, video?,
        'file_order',
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

    // Define any necessary relationships (if applicable)
    // Example: If you have a User model related to this
    // public function user()
    // {
    //     return $this->belongsTo(User::class, 'reference_id');
    // }

    public function profile_header()
    {
        return $this->hasOne(ProNetworkUserProfile::class, 'header_image_id');
    }
    
}
