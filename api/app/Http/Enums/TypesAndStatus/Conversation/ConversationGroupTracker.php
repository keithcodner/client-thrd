<?php

namespace App\Enums\TypesAndStatus\Conversation;

enum TrackerStatus: string
{
    case Active = 'active';
    case InActive = 'inactive';
}


enum TrackerType: string
{
    case Default = 'default';
}
