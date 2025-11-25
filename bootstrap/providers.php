<?php

use App\Payments\Providers\PaymentsServiceProvider;

return [
    App\Providers\AppServiceProvider::class,
    App\Services\Games\SlotsServiceProvider::class,
    PaymentsServiceProvider::class,
    FKS\Serializer\SerializerProvider::class,
    FKS\Search\SearchComponentProvider::class,
];
