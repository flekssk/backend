<?php

namespace App\Providers;

use App\Models\User;
use App\Observers\GameSessionObserver;
use App\Observers\UserObserver;
use App\Services\Games\Models\GameSession;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use SocialiteProviders\Manager\SocialiteWasCalled;
use SocialiteProviders\VKontakte\Provider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Event::listen(function (SocialiteWasCalled $event) {
            $event->extendSocialite('vkontakte', Provider::class);
        });
        User::observe(UserObserver::class);
        GameSession::observe(GameSessionObserver::class);
    }
}
