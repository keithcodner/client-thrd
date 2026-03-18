<?php

namespace App\Http\Enums\TypesAndStatus\Transaction;

enum PaymentStatus: string
{
    case Paid = 'paid';
    case Unpaid = 'unpaid';
}

