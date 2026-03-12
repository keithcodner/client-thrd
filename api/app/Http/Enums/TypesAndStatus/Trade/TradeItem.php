<?php

namespace App\Enums\TypesAndStatus\Circle;

enum IpType: string
{
    case Item = 'item';
    case Services = 'services';
    case Pet = 'pet';
    case Auto = 'auto';
}

enum IpCondition: string
{
    case LikeNew = 'like_new';
    case LightUse = 'light_use';
    case NormalUse = 'normal_use';
    case HeavyUse = 'heavy_use';
}

enum IpLanguage: string
{
    case French = 'french';
    case English = 'english';
    case Spanish = 'spanish';
}

enum ItemColourType: string
{
    case Red = 'red';
    case Blue = 'blue';
    case Green = 'green';
    case Yellow = 'yellow';
    case Purple = 'purple';
    case White = 'white';
    case Black = 'black';
    case Orange = 'orange';
    case Gray = 'gray';
    case Other = 'other';
}

enum IpStatus: string
{
    case Active = 'active';
    case InActive = 'in-active';
}

enum IpIsFree: string
{
    case True = 'true';
    case False = 'false';
}

