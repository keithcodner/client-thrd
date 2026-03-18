<?php

namespace App\Http\Enums\TypesAndStatus\File;

enum Type: string
{
    case Image = 'image';
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
    case NonVerified = 'non-verified';
    case Verified = 'verified';
}

