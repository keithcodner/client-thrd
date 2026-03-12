<?php

namespace App\Enums\TypesAndStatus\Posts;

enum Type: string
{
    case General = 'general';
}

enum Status: string
{
    case Active = 'active';
    case InActive = 'inactive';
}

enum IsVisible: string
{
    case True = 'true';
    case False = 'false';
}

