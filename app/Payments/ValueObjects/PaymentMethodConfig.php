<?php

declare(strict_types=1);

namespace App\Payments\ValueObjects;

use App\Payments\Enum\PaymentMethodEnum;
use FKS\Serializer\SerializableObject;

class PaymentMethodConfig extends SerializableObject
{
    public function __construct(
        public int $min,
        public int $max,
        public ?PaymentMethodEnum $method = null,
        public bool $hot = false,
        public bool $hidden = false,
        public bool $onlyMobile = false,
        public int $commissionPercents = 0,
        public int $bonusPercent = 0,
        public int $minDepositsCount = 0,
        public ?int $position = null,
        public mixed $cascade = null,
        public ?string $image = null,
        public ?bool $firstBonusGranted = false,
    ) {
    }
}
