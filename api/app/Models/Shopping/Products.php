<?php

namespace App\Models\Shopping;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use \App\Models\User;
use \App\Models\Core\FileProduct;

class Products extends Model
{
    use HasFactory;

    protected $table = 'products';

    protected $fillable = [
        'user_id',
        'file_product_id',
        'productCode',
        'productName',
        'productLine',
        'productScale',
        'productVendor',
        'productDescription',
        'quantityInStock',
        'buyPrice',
        'MSRP',
        'href',
        'tags',
        'type',
        'type_second',
        'status',
        'features',
        'mostPopular',
    ];

    protected $casts = [
        'quantityInStock' => 'integer',
        'buyPrice' => 'decimal:2',
        'MSRP' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship: Product belongs to a User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function images()
    {
        return $this->belongsTo(FileProduct::class, 'file_product_id', 'id');
    }
}