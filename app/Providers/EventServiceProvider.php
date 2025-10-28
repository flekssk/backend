<?php

declare(strict_types=1);

namespace App\Providers;

use App\Listeners\HandlePaymentCompleted;
use App\Payments\Events\PaymentCompleteEvent;
use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected array $listen = [
        PaymentCompleteEvent::class => [
            HandlePaymentCompleted::class,
        ],
    ];
}
