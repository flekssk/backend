<?php

declare(strict_types=1);

namespace App\Services\Games\SlotsAggregators;

use App\Services\Games\Api\Mobule\MobuleApiClient;
use App\Services\Games\Models\GameSession;
use App\Services\Games\Models\Slot;

class MobuleSlotsAggregator extends SlotsAggregator
{
    public function __construct(private readonly MobuleApiClient $apiClient)
    {
    }

    public function getAggregatorSlots(): array
    {
        return $this->apiClient->getGamesList()->response;
    }

    public function resolveStartUrl(Slot $slot, GameSession $session, bool $mobile = false): string
    {
        \Log::info('resolve url for ' . $session->id);

        $partner = "socia";
        $currency = "RUB";
        $mobile = $mobile ? 'true' : 'false';
        $lang = "ru";
        $lobbyUrl = config('app.url');

        return config('slots-aggregators.' . $slot->slot_aggregator_id->value . '.api_url') . "/games.start?partner.alias=" . $partner . "&partner.session={$session->id->uuid()}&game.provider={$slot->provider->alias}&game.alias={$slot->alias}&lang={$lang}&lobby_url={$lobbyUrl}&currency={$currency}&mobile={$mobile}";
    }
}
