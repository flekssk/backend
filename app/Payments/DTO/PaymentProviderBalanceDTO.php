<?php

declare(strict_types=1);

namespace App\Payments\DTO;

use App\Currencies\Enums\CurrenciesEnum;
use App\Payments\Enum\PaymentProvidersEnum;

readonly class PaymentProviderBalanceDTO
{
    public function __construct(
        public PaymentProvidersEnum $paymentProvider,
        public int|float|string $balance,
        public CurrenciesEnum $currency
    ) {
    }
}
