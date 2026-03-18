<?php

namespace App\Http\Enums\TypesAndStatus\Conversation;

enum CategoryExpandState: string
{
    case Opened = 'opened';
    case Closed = 'closed';
}

enum CategoryType: string
{
    case Default = 'default';
    case Archive = 'archive';
}

enum CategoryName: string
{
    case Default = 'default';
    case Archive = 'archive';
}

enum CategoryStatus: string
{
    case Active = 'active';
    case InActive = 'inactive';
}

enum CategoryDescription: string
{
    case Default = 'default';
}

