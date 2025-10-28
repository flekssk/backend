<?php

declare(strict_types=1);

namespace App\Payments\Api\OnePayment\ValueObjects;

use App\Services\Currencies\Enums\CurrenciesEnum;

readonly class OnePaymentsBalanceAttributes
{
    public function __construct(
        public string $id,
        public string $amount,
        public CurrenciesEnum $currency,
    ) {
    }
}
