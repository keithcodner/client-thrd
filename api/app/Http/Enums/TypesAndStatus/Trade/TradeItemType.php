<?php

namespace App\Enums\TypesAndStatus\Trade;

enum TradeItemStatus: string
{
    case Active = 'active';
    case InActive = 'in-active';
}

enum TradeItemCategoryStatus: string
{
    case Item = 'item';
    case Service = 'service';
    case Pet = 'Pet';
    case Auto = 'Auto';
}

