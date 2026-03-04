<?php

namespace App\Models\ProNetwork;

use App\Models\Core\FileProNetwork;
use App\Models\ProNetwork\ProNetworkConnections;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProNetworkUserProfile extends Model
{
    use HasFactory;

    protected $table = 'pronetwork_user_profile';
    protected $primaryKey  = 'id';

    protected $fillable = [
        'user_id',
        'header_image_id', //refs files_pronetwork table
        'profile_image_id', //refs files_pronetwork table
        'general_headline',
        'detailed_about',
        'general_location_city',
        'general_location_country',
        'general_location_state_province',
        'general_trade',
        'general_profession',
        'website_link',
        'social_media_link1',
        'social_media_link2',
        'social_media_link3',
        'social_media_link4',
        'social_media_link5',
        'social_media_link6',
        'views_count',
        'connections_count',
        'contact_email',
        'general_skills',
        'type', //people, business
        'status',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'views_count' => 'integer',
        'connections_count' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function profile_header()
    {
        return $this->belongsTo(FileProNetwork::class, 'header_image_id');
    }

   


}
