<?php

declare(strict_types=1);

namespace App\Payments\ValueObjects;

use App\Payments\Enum\PaymentProviderIconsEnum;
use FKS\Serializer\SerializableObject;

class MethodConfig extends SerializableObject
{
    public function __construct(
        public string $title,
        public string $walletInputPlaceholder,
        public string $walletInputTitle,
        public PaymentProviderIconsEnum $icon,
        public int $commissionPercents = 0,
        public array $walletValidationRules = [],
        public array $walletValidationErrors = [],
    ) {
    }
}
