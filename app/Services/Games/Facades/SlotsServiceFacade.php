<?php

declare(strict_types=1);

namespace App\Services\Games\Facades;

use App\Services\Games\Services\SlotsService;
use Illuminate\Support\Facades\Facade;

class SlotsServiceFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return SlotsService::class;
    }
}
