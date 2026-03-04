<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Search extends Model
{
    use HasFactory;

    protected $table = 'search';
    protected $primaryKey = 'id';

    // Define the fields that can be mass-assigned
    protected $fillable = [
        'user_id',
        'search_text',
        'ip',
        'type',
        'status',
        'page',
        'ttl',
        'result_num',
        'filter', // Column name is 'filter' not 'filters'
        'user_agent',
        'referrer',
        'created_at',
        'updated_at'
    ];

    // Define casts for date fields and any other attributes
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'user_id' => 'integer',
        'filter' => 'array', // Automatically decode JSON to array (column name is 'filter')
    ];

    /**
     * Get the user that performed the search
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
