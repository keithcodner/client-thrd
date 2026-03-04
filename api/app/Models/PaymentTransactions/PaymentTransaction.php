<?php

namespace App\Models\PaymentTransactions;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentTransaction extends Model
{
    use HasFactory;

    protected $table = 'trxn_payment_transaction';
    protected $primaryKey  = 'id';

    protected $fillable = [
        'id',
        'user_id',
        'order_id',
        'cart_id',
        'payload',
        'reward_point',
        'reward_dolar',
        'txn_number',
        'amount',
        'currency_sign',
        'currency_code',
        'currency_value',
        'method',
        'txnid',
        'details',
        'payload',
        'type',
        'status',
        'created_at',
        'updated_at',
    ];

    /**
     * Get the user that owns the payment transaction.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
