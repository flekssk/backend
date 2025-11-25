<?php

declare(strict_types=1);

namespace App\Payments\ValueObjects;

use App\Payments\Collections\WithdrawVariantsCollection;
use App\Payments\Enum\PaymentMethodEnum;
use FKS\Serializer\SerializableObject;

class WithdrawalMethodConfig extends SerializableObject
{
    public function __construct(
        public int $min,
        public int $commissionPercents,
        public PaymentMethodEnum $method,
        public bool $hot = false,
        public bool $hidden = false,
        public ?int $max = null,
        public ?WithdrawVariantsCollection $variants = null,
        public ?string $image = null,
    ) {}

    public function getVariantConfig(string $variant): ?WithdrawVariant
    {
        return $this->variants->where('name', $variant)->first();
    }

    public function toArray(): array
    {
        return [
            'min' => $this->min,
            'max' => $this->max,
            'commission_percents' => $this->commissionPercents,
            'hot' => $this->hot,
            'hidden' => $this->hidden,
            'method' => $this->method,
            'variants' => $this->variants->toArray(),
            'image' => $this->image,
        ];
    }
}
