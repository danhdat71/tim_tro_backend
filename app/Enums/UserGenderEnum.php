<?php

namespace App\Enums;

enum UserGenderEnum: int
{
    case MALE = 0;
    case FEMALE = 1;
    case OTHER = 2;

    public static function getKeys()
    {
        return [
            self::MALE->value,
            self::FEMALE->value,
            self::OTHER->value,
        ];
    }
}
