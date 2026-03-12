<?php

namespace App\Enums\TypesAndStatus\ProNetwork;

enum IsAccepted: string
{
    case True = 'true';
    case False = 'false';
}

enum Type: string
{
    case Person = 'person';
    case Group = 'group';
}

enum Status: string
{
    case Pending = 'pending';
    case Accepted = 'accepted';
    case Denied = 'denied';
}
