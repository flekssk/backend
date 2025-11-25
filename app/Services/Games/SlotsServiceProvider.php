<?php

declare(strict_types=1);

namespace App\Services\Games;

use App\Services\Games\Api\Mobule\MobuleApiClient;
use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;

class SlotsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(MobuleApiClient::class, function () {
            return new MobuleApiClient(
                new Client([
                    'base_uri' => config('api-clients.mobule.base_url'),
                ])
            );
        });
    }
}
