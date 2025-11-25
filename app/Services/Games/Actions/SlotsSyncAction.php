<?php

declare(strict_types=1);

namespace App\Services\Games\Actions;

use App\Services\Games\Enums\SlotsAggregatorEnum;
use App\Services\Games\Facades\SlotsServiceFacade;
use App\Services\Games\Models\SlotProvider;
use App\Services\Games\Repositories\SlotProviderRepository;
use App\Services\Games\Repositories\SlotRepository;
use FKS\Actions\Action;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SlotsSyncAction extends Action
{
    public $commandSignature = 'slots:sync';

    public function __construct(
        private readonly SlotRepository $slotsRepository,
        private readonly SlotProviderRepository $providerRepository,
    ) {
    }

    public function handle(): void
    {
        Log::info('Test log');

        /** @var array<string, SlotProvider> $providers */
        $providers = [];


        foreach (SlotsAggregatorEnum::cases() as $case) {
            $games = SlotsServiceFacade::getAggregatorGames($case);

            foreach ($games as $game) {
                $image = SlotsServiceFacade::getImageByName($game->title);

                if ($image === null) {
                    echo $game->name . PHP_EOL;
                    continue;
                }

                if (!isset($providers[$game->provider])) {
                    $providers[$game->provider] = $this->providerRepository->updateOrCreate(
                        [
                            'name' => $game->provider,
                            'alias' => Str::slug($game->provider),
                        ],
                        [],
                        [
                            'created_at' => now(),
                            'image' => 'default.png'
                        ],
                        [
                            'updated_at' => now(),
                        ],
                    );
                }

                if (isset($providers[$game->provider])) {
                    $this->slotsRepository->updateOrCreate(
                        [
                            'slot_aggregator_id' => $case->value,
                            'external_id' => $game->id,
                        ],
                        [
                            'slot_provider_id' => $providers[$game->provider]->id,
                            'title' => $game->title,
                            'name' => $game->name,
                            'alias' => $game->alias,
                            'is_hidden' => !$game->isEnabled,
                            'image' => $image
                        ],
                        [
                            'created_at' => now(),
                        ],
                        [
                            'updated_at' => now(),
                        ],
                    );
                }
            }
        }
    }
}
