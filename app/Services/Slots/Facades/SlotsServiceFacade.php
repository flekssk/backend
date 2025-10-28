<?php

declare(strict_types=1);

namespace App\Services\Slots\Facades;

use App\Services\Slots\Services\SlotsService;
use Illuminate\Support\Facades\Facade;

class SlotsServiceFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return SlotsService::class;
    }
}
