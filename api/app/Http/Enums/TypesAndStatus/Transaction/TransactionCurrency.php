<?php

namespace App\Enums\TypesAndStatus\Transaction;

enum TxnCurrency: string
{
    case USD = 'USD'; // $
    case BDT = 'BDT'; // ৳
    case EUR = 'EUR'; // €
    case INR = 'INR'; // ₹
    case NGN = 'NGN'; // ₦
    case BRL = 'BRL'; // R$
}