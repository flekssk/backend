<?php

declare(strict_types=1);

namespace App\Payments\DTO;

use App\Payments\Enum\PaymentStatusEnum;
use App\ValueObjects\Id;
use Carbon\Carbon;

readonly class PaymentStatusDTO
{
    public function __construct(
        public Id $paymentId,
        public PaymentStatusEnum $status,
    ) {
    }
}
