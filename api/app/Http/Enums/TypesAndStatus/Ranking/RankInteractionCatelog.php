<?php

namespace App\Enums\TypesAndStatus\Ranking;

enum RankInteractName: string
{
    case POST_COMMENT = 'POST_COMMENT';
    case SUCCESSFULL_TRADE = 'SUCCESSFULL_TRADE';
    case POST_WITH_100_LIKES = 'POST_WITH_100_LIKES';
    case SIGNUP = 'SIGNUP';
    case REFER_SOMEONE = 'REFER_SOMEONE';
    case EVERY_1000_SUBS = 'EVERY_1000_SUBS';
    case VALIDATE_IMAGE = 'VALIDATE_IMAGE';
    case SUCCESSFULL_POST_REPORT = 'SUCCESSFULL_POST_REPORT';
    case EVERY_10000_LIKES = 'EVERY_10000_LIKES';
    case TEN_BAD_TRADES = 'TEN_BAD_TRADES';
    case YOU_FALSE_REPORT = 'YOU_FALSE_REPORT';
    case YOU_INAPPORPRIATE_POST = 'YOU_INAPPORPRIATE_POST';
    case YOU_ARE_SPAMMING = 'YOU_ARE_SPAMMING';
    case CREATE_pronetwork_PROFILE = 'CREATE_pronetwork_PROFILE';

    public function rank_interact_rate(): int
    {
        return match ($this) {
            self::POST_COMMENT    => 1,
            self::SUCCESSFULL_TRADE      => 1000,
            self::POST_WITH_100_LIKES     => 100,
            self::SIGNUP  => 1000,
            self::REFER_SOMEONE   => 500,
            self::EVERY_1000_SUBS => 100,
            self::VALIDATE_IMAGE       => 1,
            self::SUCCESSFULL_POST_REPORT     => 50,
            self::EVERY_10000_LIKES      => 100,
            self::TEN_BAD_TRADES   => 1000,
            self::YOU_FALSE_REPORT    => 50,
            self::YOU_INAPPORPRIATE_POST   => 100,
            self::YOU_ARE_SPAMMING    => 100,
            self::CREATE_pronetwork_PROFILE   => 100,
        };
    }

    public function rank_interact_op_2(): int
    {
        return match ($this) {
            self::POST_COMMENT    => 0,
            self::SUCCESSFULL_TRADE      => 0,
            self::POST_WITH_100_LIKES     => 100,
            self::SIGNUP  => 0,
            self::REFER_SOMEONE   => 0,
            self::EVERY_1000_SUBS => 0,
            self::VALIDATE_IMAGE       => 0,
            self::SUCCESSFULL_POST_REPORT     => 10,
            self::EVERY_10000_LIKES      => 10000,
            self::TEN_BAD_TRADES   => 10,
            self::YOU_FALSE_REPORT    => 5,
            self::YOU_INAPPORPRIATE_POST   => 5,
            self::YOU_ARE_SPAMMING    => 10,
            self::CREATE_pronetwork_PROFILE   => 0,
        };
    }
}


enum RankInteractType: string
{
    case ACTION = 'ACTION';
    case PASSIVE = 'PASSIVE';
}

enum RankInteractStatus: string
{
    case Active = 'active';
    case InActive = 'inactive';
}

enum RankInteractOption1: string
{
    case Gain = 'gain  ';
    case Loss = 'loss';
}


