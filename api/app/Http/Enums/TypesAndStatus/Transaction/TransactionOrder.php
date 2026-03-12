<?php

namespace App\Enums\TypesAndStatus\Transaction;

enum PaymentStatus: string
{
    case Paid = 'paid';
    case Unpaid = 'unpaid';
}

