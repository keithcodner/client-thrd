<?php

namespace App\Enums\TypesAndStatus\Auth;

enum Status: string
{
    case Active = 'active';
    case InActive = 'inactive';
}

enum Searchable: string
{
    case True = 'true';
    case False = 'false';
}

enum EmailIsVerified: string
{
    case Yes = 'yes';
    case No = 'no';
}

enum UserIsVerified: string
{
    case Yes = 'yes';
    case No = 'no';
}