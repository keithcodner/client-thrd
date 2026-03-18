<?php

namespace App\Http\Enums\TypesAndStatus\Conversation;

class Conversation
{
    // Status Constants
    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';
    public const STATUS_MARKED_FOR_DELETION = 'marked_for_deletion';

    // Second Status Constants
    public const SECOND_STATUS_COMPLETE = 'completed';

    // Type Constants
    public const TYPE_COUPLE = 'couple';
    public const TYPE_GROUP = 'group';

    // TypeSecond Constants
    public const TYPE_SECOND_SOCIAL = 'social';
    public const TYPE_SECOND_CIRCLE = 'circle';
    public const TYPE_SECOND_EVENT = 'event';
    public const TYPE_SECOND_SERVICE = 'service';
    public const TYPE_SECOND_PRIVATE = 'private';
    public const TYPE_SECOND_SYSTEM = 'system';
    public const TYPE_SECOND_ADMIN = 'admin';

    // DeletedBy Constants
    public const DELETED_BY_USER_TRUE = 'true';
    public const DELETED_BY_USER_FALSE = 'false';
    public const DELETED_BY_FROM_TRUE = 'true';
    public const DELETED_BY_FROM_FALSE = 'false';

    // Get all statuses
    public static function getStatuses(): array
    {
        return [
            self::STATUS_ACTIVE,
            self::STATUS_INACTIVE,
            self::STATUS_MARKED_FOR_DELETION,
        ];
    }

    // Get all second statuses
    public static function getSecondStatuses(): array
    {
        return [
            self::SECOND_STATUS_COMPLETE,
        ];
    }

    // Get all types
    public static function getTypes(): array
    {
        return [
            self::TYPE_COUPLE,
            self::TYPE_GROUP,
        ];
    }

    // Get all TypeSecond values
    public static function getTypeSeconds(): array
    {
        return [
            self::TYPE_SECOND_SOCIAL,
            self::TYPE_SECOND_CIRCLE,
            self::TYPE_SECOND_EVENT,
            self::TYPE_SECOND_SERVICE,
            self::TYPE_SECOND_PRIVATE,
            self::TYPE_SECOND_SYSTEM,
            self::TYPE_SECOND_ADMIN,
        ];
    }

    // Get label for TypeSecond
    public static function getTypeSecondLabel(string $typeSecond): string
    {
        return match ($typeSecond) {
            self::TYPE_SECOND_SOCIAL => 'Social',
            self::TYPE_SECOND_CIRCLE => 'Circle',
            self::TYPE_SECOND_EVENT => 'Event',
            self::TYPE_SECOND_SERVICE => 'Service',
            self::TYPE_SECOND_PRIVATE => 'Private',
            self::TYPE_SECOND_SYSTEM => 'System',
            self::TYPE_SECOND_ADMIN => 'Admin',
            default => 'Unknown',
        };
    }

    // Get all DeletedByUserId values
    public static function getDeletedByUserIds(): array
    {
        return [
            self::DELETED_BY_USER_TRUE,
            self::DELETED_BY_USER_FALSE,
        ];
    }

    // Get all DeletedByFromId values
    public static function getDeletedByFromIds(): array
    {
        return [
            self::DELETED_BY_FROM_TRUE,
            self::DELETED_BY_FROM_FALSE,
        ];
    }
}