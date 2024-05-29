<?php

namespace App\Enums;

enum UserStatusEnum: int
{
    case INACTIVE = 0;
    case ACTIVE = 1;
    case LEAVE = 2;
    case BLOCKED = 3;
}
