<?php

namespace App\Enums\TypesAndStatus\Conversation;

enum Status: string
{
    case Active = 'active';
    case InActive = 'inactive';
    case MarkedForDeletion = 'marked_for_deletion';
}

enum SecondStatus: string
{
    case Complete = 'completed';
}

enum Type: string
{
    case Couple = 'couple';
    case Group = 'group';
}

enum TypeSecond: string
{
    case SOCIAL = 'social';
    case TRADE = 'trade';
    case EVENT = 'event';
    case SERVICE = 'service';
    case PRIVATE = 'private';
    case GROUP = 'group';
    case SYSTEM = 'system';
    case ADMIN = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::SOCIAL    => 'Social',
            self::TRADE      => 'Trade',
            self::EVENT     => 'Event',
            self::SERVICE  => 'Service',
            self::PRIVATE   => 'Private',
            self::GROUP => 'Group',
            self::SYSTEM       => 'System',
            self::ADMIN     => 'Admin'
        };
    }
}

enum DeteledByUserId: string
{
    case True = 'true';
    case False = 'false';
}

enum DeteledByFromId: string
{
    case True = 'true';
    case False = 'false';
}
