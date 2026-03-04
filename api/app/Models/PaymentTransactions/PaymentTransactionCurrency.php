<?php

namespace App\Models\PaymentTransactions;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentTransactionCurrency extends Model
{
    use HasFactory;

    protected $table = 'trxn_currency';
    protected $primaryKey  = 'id';

    protected $fillable = [
        'id',
        'name',
        'sign',
        'value',
        'is_default',
    ];


}
