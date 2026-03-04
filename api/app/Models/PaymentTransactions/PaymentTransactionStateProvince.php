<?php

namespace App\Models\PaymentTransactions;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentTransactionStateProvince extends Model
{
    use HasFactory;

    protected $table = 'trxn_state_province';
    protected $primaryKey  = 'id';

    protected $fillable = [
        'id',
        'country_id',
        'state',
        'tax',
        'status',
        'owner_id',
    ];


}
