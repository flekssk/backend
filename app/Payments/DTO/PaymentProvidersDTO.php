<?php

declare(strict_types=1);

namespace App\Payments\DTO;

use App\Models\User;
use App\Payments\Collections\MethodsCollection;
use App\Payments\Collections\PaymentProvidersConfigCollection;
use FKS\Serializer\SerializableObject;

class PaymentProvidersDTO extends SerializableObject
{
    public function __construct(
        public PaymentProvidersConfigCollection $providers,
        public MethodsCollection $methods,
    ) {
    }

    public function toArray(): array
    {
        return [
            'providers' => $this->providers->toArray(),
            'methods' => $this->methods->toArray(),
        ];
    }
}
