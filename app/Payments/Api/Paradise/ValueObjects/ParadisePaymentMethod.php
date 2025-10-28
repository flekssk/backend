<?php

declare(strict_types=1);

namespace App\Payments\Api\Paradise\ValueObjects;

readonly class ParadisePaymentMethod
{
    public function __construct(
        public string $phone,
        public string $name,
        public string $bank,
    ) {
    }
}
