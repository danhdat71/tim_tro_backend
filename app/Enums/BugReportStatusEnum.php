<?php

namespace App\Enums;

enum BugReportStatusEnum: int
{
    case WAITING = 1;
    case VIEWED = 2;
    case NOT_FIX = 3;
    case FIXED = 4;
}
