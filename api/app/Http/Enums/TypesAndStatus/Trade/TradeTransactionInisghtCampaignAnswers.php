<?php

namespace App\Enums\TypesAndStatus\Trade;

enum Status: string
{
    case Answered = 'answered';
    case Skipped = 'skipped';
}