<?php

namespace App\Enums\TypesAndStatus\File;

enum Type: string
{
    case Passport         = 'passport';
    case DriversLicense   = 'drivers_license';
    case BirthCertificate = 'birth_certificate';
    case IdCard           = 'id_card';
    case StudentId        = 'student_id';

    /**
     * Optional: Readable label for UI or forms
     */
    public function label(): string
    {
        return match ($this) {
            self::Passport         => 'Passport',
            self::DriversLicense   => 'Drivers License',
            self::BirthCertificate => 'Birth Certificate',
            self::IdCard           => 'Identification Card',
            self::StudentId        => 'Student ID',
        };
    }
}

enum VerifyStatus: string
{
    case Active = 'active';
    case InActive = 'inactive';
}

enum Status: string
{
    case Denied = 'denied';
    case Verified = 'verified';
}

