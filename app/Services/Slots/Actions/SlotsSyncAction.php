<?php

declare(strict_types=1);

namespace App\Services\Slots\Actions;

use App\Services\Slots\Enums\AggregatorEnum;
use App\Services\Slots\Facades\SlotsServiceFacade;
use App\Services\Slots\Models\SlotProvider;
use App\Services\Slots\Repositories\SlotProviderRepository;
use App\Services\Slots\Repositories\SlotRepository;
use FKS\Actions\Action;
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
        /** @var array<string, SlotProvider> $providers */
        $providers = [];


        foreach (AggregatorEnum::cases() as $case) {
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
