<?php

namespace App\Payments\DTO;

use App\Models\User;
use App\Payments\Enum\PaymentMethodEnum;
use App\Payments\Enum\PaymentProvidersEnum;

readonly class CreatePaymentDTO
{
    public function __construct(
        public int $amount,
        public PaymentProvidersEnum $provider,
        public PaymentMethodEnum $method,
        public User $user,
        public ?string $code = null,
    ) {
    }
}
