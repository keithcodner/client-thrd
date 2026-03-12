<?php

namespace App\Enums\TypesAndStatus\Circle;

enum Status: string
{
    case Answered = 'answered';
    case Skipped = 'skipped';
}