<?php

namespace App\Models\PaymentTransactions;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentTransactionSettingProcessorMoneris extends Model
{
    use HasFactory;

    protected $table = 'trxn_setting_processor_moneris';
    protected $primaryKey  = 'id';

    protected $fillable = ['name', 'value', 'op1', 'op2', 'op3', 'description'];


}
