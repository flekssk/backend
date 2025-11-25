<?php

declare(strict_types=1);

namespace App\Payments\Api\OnePlat\ValueObjects;

use FKS\Serializer\SerializableObject;

class OnePlatSBPPayment extends SerializableObject
{
    public function __construct(
        public OnePlatSBPNote $note,
        public int $amountToPay,
        public int $status,
        public string $merchantId,
    ) {
    }
}
