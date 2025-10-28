<?php

declare(strict_types=1);

namespace App\Payments\Api\Gotham\Enums;

enum GothamTrafficTypeEnum: string
{
    case CARD = 'card_number';
    case SBP = 'sbp';
}
