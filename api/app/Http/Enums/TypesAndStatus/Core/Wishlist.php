<?php

namespace App\Http\Enums\TypesAndStatus\Core;

enum ParentType: string
{
    case Item = 'item';
}

enum CommStatus: string
{
    case Active = 'Active';
    case InActive = 'In-Active';
}