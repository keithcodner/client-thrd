<?php

namespace App\Enums\TypesAndStatus\File;

enum Type: string
{
    case BackgroundProfileImage = 'background_profile_image';
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

