<?php

declare(strict_types=1);

namespace App\Enums\Users;

enum BonusTypeEnum: int
{
    case FREE_SPINS = 1;
    case WELCOME = 2;
    case CASHBACK = 3;
}
