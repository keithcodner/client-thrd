<?php

namespace App\Enums\TypesAndStatus\Ranking;

enum RankPermissionName: string
{
    case PostLimitLow             = 'post_limit_low';
    case PostLimitMedium          = 'post_limit_medium';
    case PostLimitHigh            = 'post_limit_high';
    case PostLimitMax             = 'post_limit_max';
    case PostLimitUnlimited       = 'post_limit_unlimited';
    case TradeItemLimitLow        = 'trade_item_limit_low';
    case TradeItemLimitMedium     = 'trade_item_limit_medium';
    case TradeItemLimitHigh       = 'trade_item_limit_high';
    case TradeItemLimitMax        = 'trade_item_limit_max';
    case TradeItemLimitUnlimited  = 'trade_item_limit_unlimited';
}

enum RankPermissionType: string
{
    case Post = 'post';
    case Item = 'item';
}

enum RankPermissionStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
}

enum RankPermissionDuration: string
{
    case OneDay = '1day';
    case OneMonth = '1month';
}
