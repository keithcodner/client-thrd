<?php

namespace App\Enums\TypesAndStatus\Notification;

enum StatusColor: string
{
    case Red = 'red';
    case Indigo = 'indigo';
    case Blue = 'blue';
    case Green = 'green';
    case Purple = 'purple';
    case Pink = 'pink';
    case Yellow = 'yellow';
}

enum Status: string
{
    case Read = 'read';
    case Unread = 'unread';
}

