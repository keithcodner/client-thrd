<?php

namespace App\Models\PaymentTransactions;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentTransactionShopper extends Model
{
    use HasFactory;

    protected $table = 'trxn_shopper';
    protected $primaryKey  = 'id';

    protected $fillable = [
        'trxn_billing_id',
        'trxn_shipping_id',
        'firstname',
        'lastname',
        'email',
        'phone_number',
    ];


}
