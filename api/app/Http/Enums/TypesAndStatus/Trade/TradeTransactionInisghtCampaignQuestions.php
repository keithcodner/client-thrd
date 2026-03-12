<?php

namespace App\Enums\TypesAndStatus\Circle;

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