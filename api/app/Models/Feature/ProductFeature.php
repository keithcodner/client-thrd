<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductFeature extends Model
{
    use HasFactory;

    protected $table = 'product_features';

    protected $fillable = [
        'product_id',
        'for_user_id',
        'feature_name',
        'feature_code',
        'description',
        'type',
        'status',
        'start_date',
        'end_date',
    ];

    public $timestamps = true; // Laravel will automatically handle created_at and updated_at

    /**
     * Relationship: ProductFeature belongs to a Product
     */
    public function product()
    {
       // return $this->belongsTo(\App\Models\Shopping\Products::class, 'product_id');
    }
}
