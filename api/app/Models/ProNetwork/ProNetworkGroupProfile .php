<?php

namespace App\Models\ProNetwork;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProNetworkGroupProfile  extends Model
{
    use HasFactory;

    protected $table = 'pronetwork_group_profile';
    protected $primaryKey  = 'id';

    // Define the fields that can be mass-assigned
    protected $fillable = [
        'group_owner_user_id',
        'conversation_id',
        'header_image_id',
        'profile_image_id',
        'general_headline',
        'detailed_about',
        'general_location_city',
        'general_location_country',
        'general_location_state_province',
        'general_circle',
        'general_profession',
        'website_link',
        'social_media_link1',
        'social_media_link2',
        'social_media_link3',
        'social_media_link4',
        'social_media_link5',
        'social_media_link6',
        'views_count',
        'following_count',
        'contact_email',
        'general_skills',
        'created_at',
        'updated_at'
    ];

    // Optionally define casts for date fields and any other attributes
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];


}
