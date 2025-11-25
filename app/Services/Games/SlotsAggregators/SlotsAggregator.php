<?php

declare(strict_types=1);

namespace App\Services\Games\SlotsAggregators;

use _PHPStan_6597ef616\Nette\Neon\Exception;
use App\Services\Games\Models\GameSession;
use App\Services\Games\Models\Slot;
use App\Services\Games\ValueObjects\AggregatorSlot;

class SlotsAggregator
{
    /**
     * @return AggregatorSlot[]
     */
    public function getAggregatorSlots(): array
    {
        return [];
    }

    public function resolveStartUrl(Slot $slot, GameSession $session): string
    {
        throw new Exception('Not implemented');
    }
}
