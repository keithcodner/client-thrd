<?php

namespace App\Enums\TypesAndStatus\Transaction;

enum Status: string
{
    case Active = 'active';
    case InActive = 'inactive';
}
