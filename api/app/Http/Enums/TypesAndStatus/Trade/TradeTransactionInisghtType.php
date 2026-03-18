<?php

namespace App\Http\Enums\TypesAndStatus\Circle;

enum Status: string
{
    case Active = 'active';
    case InActive = 'inactive';
}

enum Type: string
{
    case MultipleChoice = 'multiple_choice';
    case CheckBox = 'check_box';
    case Text = 'text';
}
