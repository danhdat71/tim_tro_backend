<?php

namespace App\Enums;

enum SharedHouseEnum: int
{
    case NO_SHARED_HOUSE = 0;
    case SHARED_HOUSE = 1;

    public static function getKeys()
    {
        return [
            self::NO_SHARED_HOUSE->value,
            self::SHARED_HOUSE->value,
        ];
    }
}
