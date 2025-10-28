<?php

declare(strict_types=1);

namespace App\Payments\Api\OnePlat\Requests;

use App\ValueObjects\Id;

readonly class OnePlatPayRequest
{
    public function __construct(
        public Id $paymentId,
        public Id $userId,
        public int|float $amount,
    ) {
    }
}
