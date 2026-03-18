<?php

namespace App\Http\Enums\TypesAndStatus\ProNetwork;

enum IsConnected: string
{
    case True = 'true';
    case False = 'false';
}

enum Type: string
{
    case Connection = 'connection';
    case GroupConnection = 'group_connection';
}

enum Status: string
{
    case Active = 'active';
    case InActive = 'inactive';
}

