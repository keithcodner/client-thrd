<?php

namespace App\Models\PaymentTransactions;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentTransactionInvoice extends Model
{
    use HasFactory;

    protected $table = 'trxn_invoice';
    protected $primaryKey  = 'id';

    protected $fillable = [
        'id',
        'test',
        'test',
        'test',
        'test',
        'test',
        'test',
    ];


}
