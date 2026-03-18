<?php

namespace App\Http\Enums\TypesAndStatus\Transaction;

enum Status: string
{
    case Active = 'active';
    case InActive = 'inactive';
}
