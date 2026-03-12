<?php

namespace App\Enums\TypesAndStatus\Circle;

enum CircleItemStatus: string
{
    case Active = 'active';
    case InActive = 'in-active';
}

enum CircleItemCategoryStatus: string
{
    case Item = 'item';
    case Service = 'service';
    case Pet = 'Pet';
    case Auto = 'Auto';
}

