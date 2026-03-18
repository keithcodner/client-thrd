<?php

namespace App\Http\Enums\TypesAndStatus\Ranking;

enum RankPermissionName: string
{
    case PostLimitLow             = 'post_limit_low';
    case PostLimitMedium          = 'post_limit_medium';
    case PostLimitHigh            = 'post_limit_high';
    case PostLimitMax             = 'post_limit_max';
    case PostLimitUnlimited       = 'post_limit_unlimited';
    case CircleItemLimitLow        = 'circle_item_limit_low';
    case CircleItemLimitMedium     = 'circle_item_limit_medium';
    case CircleItemLimitHigh       = 'circle_item_limit_high';
    case CircleItemLimitMax        = 'circle_item_limit_max';
    case CircleItemLimitUnlimited  = 'circle_item_limit_unlimited';
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
