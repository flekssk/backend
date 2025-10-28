<?php

declare(strict_types=1);

namespace App\Payments\Facades;

use App\Payments\PaymentsService;
use Illuminate\Support\Facades\Facade;

class PaymentServiceFacade extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return PaymentsService::class;
    }
}
