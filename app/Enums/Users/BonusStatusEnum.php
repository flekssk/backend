<?php

declare(strict_types=1);

namespace App\Enums\Users;

enum BonusStatusEnum: int
{
    case IN_PROGRESS = 1;
    case COMPLETED = 2;
    case EXPIRED = 3;
    case CANCELED = 4;
}
