<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id',
        'role_id',
        'alpha_num_id',
        'firstname',
        'lastname',
        'username',
        'name',
        'email',
        'avatar',
        'email_IsVerified',
        'user_IsVerified',
        'email_VerifiedToken',
        'change_PasswordToken',
        'password',
        'password_try',
        'status',
        'remember_token',
        'user_settings',
        'type',
        'telephone',
        'about',
        'contact',
        'links',
        'history',
        'friend_list',
        'vid_fav',
        'trade_fav',
        'phone_num',
        'isStoreOpen',
        'identity',
        'intrests',
        'yourLocation',
        'who_i_sub_to',
        'who_sub_to_me',
        'who_i_sub_to_count',
        'who_sub_to_me_count',
        'registerIP',
        'lastLoginIP',
        'suspend_reactive',
        'email_verified_at',
        'email_verification_token',
        'birthdate',
        'last_login',
        'user_lat',
        'user_long',
        'user_city',
        'default_km_range',
        'language',
        'searchable',
        'google_id',
        'profile_photo_path',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'alpha_num_id',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
