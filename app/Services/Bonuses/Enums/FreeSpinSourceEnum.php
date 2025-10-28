<?php

declare(strict_types=1);

namespace App\Services\Bonuses\Enums;

enum FreeSpinSourceEnum: int
{
    case DEPOSIT = 1;
    case WELCOME = 2;
    case BONUS = 3;
}
