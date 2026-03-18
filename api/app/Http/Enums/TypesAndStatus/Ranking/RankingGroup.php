<?php

namespace App\Http\Enums\TypesAndStatus\Ranking;

enum RankGroupType: string
{
    case Achievement = 'achievement';
    case Badge = 'badge';
    case Rank = 'rank';
}

enum RankGroupTier: string
{
    case Null = 'null';
    case CEO = 'ceo';
    case Director = 'director';
    case Manager = 'manager';
    case Specialist = 'specialist';
    case Analyst = 'analyst';
    case Expert = 'expert';
    case Employee = 'employee';
    case Trainee = 'trainee';
    case Apprentice = 'apprentice';
    case Coop = 'co-op';

    public function rank_group_order(): int
    {
        return match ($this) {
            self::Null    => -1,
            self::CEO      => 9,
            self::Director     => 8,
            self::Manager  => 7,
            self::Specialist   => 6,
            self::Analyst => 5,
            self::Expert       => 4,
            self::Employee     => 3,
            self::Trainee      => 2,
            self::Apprentice   => 1,
            self::Coop    => 0,
        };
    }

    public function rank_group_weighted_score_threshold(): int
    {
        return match ($this) {
            self::Null    => -1,
            self::CEO      => 25000000,
            self::Director     => 2000000,
            self::Manager  => 900000,
            self::Specialist   => 450000,
            self::Analyst => 90000,
            self::Expert       => 30000,
            self::Employee     => 9000,
            self::Trainee      => 3000,
            self::Apprentice   => 1500,
            self::Coop    => 0,
        };
    }
}

enum RankGroupStatus: string
{
    case Active = 'active';
    case InActive = 'in-active';
}


