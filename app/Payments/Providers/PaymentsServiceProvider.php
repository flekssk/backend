<?php

declare(strict_types=1);

namespace App\Payments\Providers;

use App\Payments\Api\Blvckpay\BlvckpayApiClient;
use App\Payments\Api\Cryptobot\CryptobotApiClient;
use App\Payments\Api\Exotic\ExoticApiClient;
use App\Payments\Api\Expay\ExpayApiClient;
use App\Payments\Api\FK\FKApiClient;
use App\Payments\Api\Gotham\GothamApiClient;
use App\Payments\Api\GTX\GTXApiClient;
use App\Payments\Api\OnePayment\OnePaymentsApiClient;
use App\Payments\Api\OnePlat\OnePlatApiClient;
use App\Payments\Api\Paradise\ParadiseApiClient;
use App\Payments\Models\Payment;
use App\Payments\Observers\PaymentsObserver;
use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;

class PaymentsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->registerApiClients();

        Payment::observe(PaymentsObserver::class);
    }

    public function registerApiClients(): void
    {
        $this->app->singleton(BlvckpayApiClient::class, function () {
            return new BlvckpayApiClient(
                new Client([
                    'base_uri' => config('api-clients.blvckpay.base_url'),
                    'timeout' => 10.0,
                ])
            );
        });
        $this->app->singleton(FKApiClient::class, function () {
            return new FKApiClient(
                new Client([
                    'base_uri' => config('api-clients.fk.base_url'),
                    'timeout' => 10.0,
                ])
            );
        });
        $this->app->singleton(CryptobotApiClient::class, function () {
            return new CryptobotApiClient(
                new Client([
                    'base_uri' => config('api-clients.cryptobot.base_url'),
                    'timeout' => 10.0,
                ])
            );
        });
        $this->app->singleton(ParadiseApiClient::class, function () {
            return new ParadiseApiClient(
                new Client([
                    'base_uri' => config('api-clients.paradise.base_url'),
                    'timeout' => 10.0,
                ])
            );
        });
        $this->app->singleton(ExpayApiClient::class, function () {
            return new ExpayApiClient(
                new Client([
                    'base_uri' => config('api-clients.expay.base_url'),
                    'timeout' => 10.0,
                ])
            );
        });
        $this->app->singleton(ExoticApiClient::class, function () {
            return new ExoticApiClient(
                new Client([
                    'base_uri' => config('api-clients.expay.base_url'),
                    'timeout' => 10.0,
                ])
            );
        });
        $this->app->singleton(OnePlatApiClient::class, function () {
            return new OnePlatApiClient(
                new Client([
                    'base_uri' => config('api-clients.1plat.base_url'),
                    'timeout' => 10.0,
                ])
            );
        });
        $this->app->singleton(OnePaymentsApiClient::class, function () {
            return new OnePaymentsApiClient(
                new Client([
                    'base_uri' => config('api-clients.onepayments.base_url'),
                    'timeout' => 10.0,
                    'headers' => [
                        'Authorization' => 'Bearer ' . config('api-clients.onepayments.api_key'),
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json',
                    ]
                ])
            );
        });
        $this->app->singleton(GothamApiClient::class, function () {
            return new GothamApiClient(
                new Client([
                    'base_uri' => config('api-clients.gotham.base_url'),
                    'timeout' => 10.0,
                    'headers' => [
                        'Authorization' => 'Bearer ' . config('api-clients.gotham.api_key'),
                        'X-Username' => config('api-clients.gotham.user_name'),
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json',
                    ]
                ])
            );
        });
        $this->app->singleton(GTXApiClient::class, function () {
            return new GTXApiClient(
                new Client([
                    'base_uri' => config('api-clients.gtx.base_url'),
                    'timeout' => 10.0,
                    'headers' => [
                        'Authorization' => 'Bearer ' . config('api-clients.gtx.api_token'),
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json',
                    ]
                ])
            );
        });
    }
}
