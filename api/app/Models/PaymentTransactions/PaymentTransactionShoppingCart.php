<?php

namespace App\Models\PaymentTransactions;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentTransactionShoppingCart extends Model
{
    use HasFactory;

    protected $table = 'trxn_shopping_cart';
    protected $primaryKey  = 'id';

    protected $fillable = [
        'id',
        'user_id',
        'cart_id',
        'status', //default:active
        'cart_data',
        'expire_threshold', //default: 30 mins
        'created_at',
        'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }


}
