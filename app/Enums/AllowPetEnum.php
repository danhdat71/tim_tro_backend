<?php

namespace App\Enums;

enum AllowPetEnum: int
{
    case NOT_ALLOW = 1;
    case RULE_ALLOW = 2;
    case ALLOW = 3;

    public static function getKeys()
    {
        return [
            self::NOT_ALLOW->value,
            self::RULE_ALLOW->value,
            self::ALLOW->value,
        ];
    }
}
