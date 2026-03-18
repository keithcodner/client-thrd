<?php

namespace App\Http\Enums\TypesAndStatus\Ranking;

enum RankTransData: string
{
    case PassiveStatusComplete = 'passive_status:complete';
    case PassiveStatusTransient = 'passive_status:transient';
}

enum RankTransStatus: string
{
    case Active = 'active';
    case Inactive = 'inactive';
}

enum RankTransReasonDescription: string
{
    case PassiveCalcGeneral = 'passive_calc_general';
}

enum RankPermissionDuration: string
{
    case OneDay = '1day';
    case OneMonth = '1month';
}

enum RankReasonTable: string
{
    case Post = 'post';
    case Subscription = 'subscription';
}