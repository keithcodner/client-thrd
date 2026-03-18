<?php

namespace App\Http\Enums\TypesAndStatus\Circle;

enum Status: string
{
    case Answered = 'answered';
    case Skipped = 'skipped';
}