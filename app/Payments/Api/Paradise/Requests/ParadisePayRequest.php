<?php

declare(strict_types=1);

namespace App\Payments\Api\Paradise\Requests;

use App\ValueObjects\Id;

readonly class ParadisePayRequest
{
    public function __construct(
        public Id $paymentId,
        public Id $userId,
        public int|float $amount,
        public string $ip,
    ) {
    }
}
