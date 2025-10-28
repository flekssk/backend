<?php

namespace App\Services\Slots\Api\Mobule;

use App\Services\Slots\Api\Mobule\Responses\MobuleGetSlotsListResponse;
use FKS\Api\ApiClient;

class MobuleApiClient extends ApiClient
{
    public function getGamesList(): MobuleGetSlotsListResponse
    {
        return $this->handleResponse($this->post('games.list'), MobuleGetSlotsListResponse::class);
    }
}
