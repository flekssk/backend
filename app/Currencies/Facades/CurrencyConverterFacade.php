<?php

declare(strict_types=1);

namespace App\Currencies\Facades;

use App\Currencies\CurrenciesConverter;
use Illuminate\Support\Facades\Facade;

class CurrencyConverterFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return CurrenciesConverter::class;
    }
}
