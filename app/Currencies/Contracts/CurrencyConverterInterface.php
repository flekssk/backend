<?php

namespace App\Currencies\Contracts;

use App\Currencies\Enums\CurrenciesEnum;

interface CurrencyConverterInterface
{
    public function convert(CurrenciesEnum $from, CurrenciesEnum $to, int $amount): float|int|null;
}
