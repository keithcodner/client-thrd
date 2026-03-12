<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserActivity extends Model
{
    use HasFactory;

    protected $table = 'users_activity';
    protected $primaryKey = 'id';

    // Enable Laravel's automatic timestamp management
    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'page',
        'action',
        'name',
        'value',
        'op1',
        'op2',
        'op3',
        'type',
        'status',
        'created_at',
        'updated_at',
    ];

    /**
     * Get the user that owns the activity
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
