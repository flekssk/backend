<?php

declare(strict_types=1);

namespace App\Services\Slots\SlotsAggregators;

use App\Services\Slots\Api\Mobule\MobuleApiClient;

class MobuleSlotsAggregator extends SlotsAggregator
{
    public function __construct(private readonly MobuleApiClient $apiClient)
    {
    }

    public function getAggregatorSlots(): array
    {
        return $this->apiClient->getGamesList()->response;
    }
}
