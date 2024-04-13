<?php

namespace App\Enums;

enum ProductStatusEnum: int
{
    case DRAFT = 0;
    case REALITY = 1;
    case HIDDEN = 2;
}
