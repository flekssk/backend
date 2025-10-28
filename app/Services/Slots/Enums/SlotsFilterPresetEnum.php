<?php

declare(strict_types=1);

namespace App\Services\Slots\Enums;

enum SlotsFilterPresetEnum: string
{
    case LATEST = 'latest';
    case POPULAR = 'popular';
}
