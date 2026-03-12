<?php

namespace App\Enums\TypesAndStatus\ProNetwork;

enum Type: string
{
    case Person = 'person';
    case Group = 'group';
}

enum Status: string
{
    case Active = 'active';
    case InActive = 'inactive';
}

