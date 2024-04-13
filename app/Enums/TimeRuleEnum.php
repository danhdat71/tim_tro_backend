<?php

namespace App\Enums;

enum TimeRuleEnum: int
{
    case NO_RULE = 0;
    case RULE = 1;

    public static function getKeys()
    {
        return [
            self::NO_RULE->value,
            self::RULE->value,
        ];
    }
}
