<?php

namespace App\Http\Enums\TypesAndStatus\Transaction;

enum Type: string
{
    case Paid = 'paid';
    case Unpaid = 'unpaid';
}

