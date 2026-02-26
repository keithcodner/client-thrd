<?php

namespace App\Enums;

enum OperationEnum: string
{
    case GENERATIVE_FILL = 'generative-fill';
    case RESTORE = 'restore';
    case RECOLOUR = 'recolour';
    case REMOVE_OBJECTS = 'remove_objects';

    public function credits(): int
    {
        return match($this) {
            self::GENERATIVE_FILL => 10,
            self::RESTORE => 5,
            self::RECOLOUR => 7,
            self::REMOVE_OBJECTS => 8,
        };

    }

    public static function listOfCredits(): array
    {
         return [
          self::GENERATIVE_FILL->value => self::GENERATIVE_FILL->credits(),
          self::RESTORE->value => self::RESTORE->credits(),
          self::RECOLOUR->value => self::RECOLOUR->credits(),
          self::REMOVE_OBJECTS->value => self::REMOVE_OBJECTS->credits(),
         ];
    }
}
