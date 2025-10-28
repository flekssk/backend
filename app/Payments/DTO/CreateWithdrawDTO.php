<?php

namespace App\Payments\DTO;

use App\Models\User;
use App\Payments\Enum\PaymentMethodEnum;
use App\Payments\Enum\PaymentProvidersEnum;

readonly class CreateWithdrawDTO
{
    public function __construct(
        public float $amount,
        public string $wallet,
        public PaymentProvidersEnum $provider,
        public PaymentMethodEnum $method,
        public User $user,
        public ?string $variant = null,
    ) {
    }
}
