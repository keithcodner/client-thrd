<?php

namespace App\Models\PaymentTransactions;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentTransactionPaymentProcessor extends Model
{
    use HasFactory;

    protected $table = 'testzzz';
    protected $primaryKey  = 'id';

    protected $fillable = [
        'id',
        'subtitle',
        'title',
        'details',
        'name',
        'type',
        'information',
        'keyword',
        'currency_id',
        'checkout',
        'deposit',
        'subscription',
        'created_at',
        'updated_at',
    ];


}
