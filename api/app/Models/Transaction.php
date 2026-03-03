<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'credit_transactions';

    protected $fillable = [
        'user_id',
        'stripe_payment_intent_id',
        'credits_amount',
        'amount_paid',
        'currency',
        'status', //pending, completed, failed
        'paid_at',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
