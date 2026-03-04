<?php

namespace App\Models\PaymentTransactions;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentTransactionHistory extends Model
{
    use HasFactory;

    protected $table = 'trxn_payment_transaction_history';
    protected $primaryKey  = 'id';

    protected $fillable = [
        'id',
        'user_id',
        'order_id',
        'cart_id',
        'transaction_id',
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


}
