<?php

namespace App\Http\Enums\TypesAndStatus\Circle;

class Circle
{
    // Circle Types
    public const TYPE_PRIVATE_CIRCLE = 'private_circle';
    public const TYPE_COMMUNITY_HUB = 'community_hub';

    // Circle User Types
    public const TYPE_OWNER = 'owner';
    public const TYPE_USER = 'user';

    // Circle Privacy States
    public const PRIVACY_PUBLIC = 'public';
    public const PRIVACY_PRIVATE = 'private';

    // Style Codes
    public const STYLE_SAGE = 'sage';
    public const STYLE_STONE = 'stone';
    public const STYLE_CLAY = 'clay';
    public const STYLE_SAND = 'sand';
    public const STYLE_DUSK = 'dusk';
    public const STYLE_AMBER = 'amber';

    // Get all circle types
    public static function getTypes(): array
    {
        return [
            self::TYPE_PRIVATE_CIRCLE,
            self::TYPE_COMMUNITY_HUB,
        ];
    }

    // Get all style codes
    public static function getStyleCodes(): array
    {
        return [
            self::STYLE_SAGE,
            self::STYLE_STONE,
            self::STYLE_CLAY,
            self::STYLE_SAND,
            self::STYLE_DUSK,
            self::STYLE_AMBER,
        ];
    }
}