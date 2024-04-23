<?php

namespace App\Enums;

enum SyncStatusEnum: int
{
    case ATTACH = 1;
    case DETACH = 0;

    public static function getKeys()
    {
        return [
            self::ATTACH->value,
            self::DETACH->value,
        ];
    }
}
