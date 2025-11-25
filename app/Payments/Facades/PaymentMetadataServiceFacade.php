<?php

declare(strict_types=1);

namespace App\Payments\Facades;

use App\Payments\Services\PaymentMetadataService;
use Illuminate\Support\Facades\Facade;

class PaymentMetadataServiceFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return PaymentMetadataService::class;
    }
}
