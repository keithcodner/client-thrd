<?php

namespace App\Models\PaymentTransactions;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentTransactionCoupon extends Model
{
    use HasFactory;

    protected $table = 'trxn_coupon';
    protected $primaryKey  = 'id';

    protected $fillable = [
        'id',
        'code',
        'type',
        'price',
        'times',
        'used',
        'status',
        'start_date',
        'end_date',
        'coupon_type',
        'category',
        'sub_category',
        'child_category',
    ];


}
