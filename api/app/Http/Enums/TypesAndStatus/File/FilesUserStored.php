<?php

namespace App\Http\Enums\TypesAndStatus\File;

enum Type: string
{
    case Profile = 'profile';
    case File = 'file';
    case Video = 'video';
}

enum Status: string
{
    case Active = 'active';
    case InActive = 'inactive';
}

enum VerifyStatus: string
{
    case Active = 'active';
    case InActive = 'inactive';
}

