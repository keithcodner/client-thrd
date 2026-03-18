<?php

namespace App\Http\Enums\TypesAndStatus\Transaction;

enum CouponType: string
{
    case Category = 'category';
    case ChildCategory = 'child_category';
}

