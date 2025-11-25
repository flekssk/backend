<?php

namespace App\Payments\DTO;

use App\Models\User;
use App\Payments\Enum\PaymentMethodEnum;
use App\Payments\Enum\PaymentProvidersEnum;
use App\Payments\Enum\PaymentSourceEnum;

readonly class CreatePaymentDTO
{
    public function __construct(
        public int $amount,
        public PaymentProvidersEnum $provider,
        public PaymentMethodEnum $method,
        public PaymentSourceEnum $source,
        public User $user,
        public ?int $externalId = null,
        public ?string $code = null,
    ) {
    }
}
