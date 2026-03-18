<?php

namespace App\Http\Enums\TypesAndStatus\Circle;

enum Status: string
{
    case Active = 'active';
    case InActive = 'inactive';
}

enum Type: string
{
    case QuestionAnswer = 'question_answer';
    case Review = 'review';
}