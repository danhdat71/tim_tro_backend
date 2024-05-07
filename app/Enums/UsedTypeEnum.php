<?php

namespace App\Enums;

enum UsedTypeEnum: int
{
    case HOSTEL = 1;
    case FULL_HOUSE = 2;
    case SLEEP_BOX = 3;
    case APARTMENT = 4;
    case OFFICE = 5;
    case TOGETHER = 6;
    case OTHER = 7;

    public static function getKeys()
    {
        return [
            self::HOSTEL->value,
            self::FULL_HOUSE->value,
            self::SLEEP_BOX->value,
            self::APARTMENT->value,
            self::OFFICE->value,
            self::TOGETHER->value,
            self::OTHER->value,
        ];
    }
}
