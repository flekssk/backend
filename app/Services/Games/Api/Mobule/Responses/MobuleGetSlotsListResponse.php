<?php

declare(strict_types=1);

namespace App\Services\Games\Api\Mobule\Responses;

use App\Services\Games\Api\Mobule\Entities\Slot;

readonly class MobuleGetSlotsListResponse
{
    /**
     * @param Slot[] $response
     */
    public function __construct(public array $response)
    {
    }
}
