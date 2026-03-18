<?php

namespace App\Http\Enums\TypesAndStatus\Status;

enum LKType: string
{
    case Like = 'like';
    case Favourite = 'favourite';
    case FavouriteItem = 'favourite_item';
}

enum LKStatus: string
{
    case Active = 'active';
    case InActive = 'in-active';
}

