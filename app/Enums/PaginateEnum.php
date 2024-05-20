<?php

namespace App\Enums;

enum PaginateEnum: int
{
    case PAGINATE_5 = 5;
    case PAGINATE_10 = 10;
    case PAGINATE_15 = 15;
    case PAGINATE_20 = 20;
}
