<?php

namespace App\Enums;

enum UserTypeEnum: int
{
    case PROVIDER = 0;
    case FINDER = 1;
    case ADMIN = 10;

    public static function getKeys()
    {
        return [
            self::PROVIDER->value,
            self::FINDER->value,
            self::ADMIN->value,
        ];
    }
}
