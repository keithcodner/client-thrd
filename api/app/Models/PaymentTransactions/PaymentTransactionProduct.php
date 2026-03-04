<?php

namespace App\Models\PaymentTransactions;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentTransactionProduct extends Model
{
    use HasFactory;

    protected $table = 'trxn_product';
    protected $primaryKey  = 'id';

    protected $fillable = [ 
        'id',
        'sku',
        'product_type',
        'affiliate_link',
        'user_id',
        'category_id',
        'subcategory_id',
        'childcategory_id',
        'attributes',
        'name',
        'slug',
        'photo',
        'thumbnail',
        'file',
        'size',
        'size_qty',
        'size_price',
        'color',
        'price',
        'previous_price',
        'details',
        'stock',
        'color_all',
        'size_all',
        'stock_check',
        'policy',
        'status',
        'views',
        'tags',
        'features',
        'colors',
        'product_condition',
        'ship',
        'is_meta',
        'meta_tag',
        'meta_description',
        'youtube',
        'type',
        'license',
        'license_qty',
        'link',
        'platform',
        'region',
        'licence_type',
        'measure',
        'featured',
        'best',
        'top',
        'hot',
        'latest',
        'big',
        'trending',
        'sale',
        'created_at',
        'updated_at',
        'is_discount',
        'discount_date',
        'whole_sell_qty',
        'whole_sell_discount',
        'is_catalog',
        'catalog_id',
        'language_id',
        'order',
        'preordered',
        'minimum_qty'
    ];
}
