<?php

declare(strict_types=1);

namespace App\Payments\Api\Paradise\Responses;

use App\Payments\Api\Paradise\ValueObjects\ParadisePaymentMethod;
use FKS\Serializer\SerializableObject;

class ParadiseOrderCreateResponse extends SerializableObject
{
    public function __construct(
        public string $status,
        public ?string $uuid = null,
        public int $amount,
        public ?ParadisePaymentMethod $paymentMethod = null,
    ) {
    }
}
