<?php

declare(strict_types=1);

namespace App\Payments\DTO;

use App\Payments\Enum\PaymentStatusEnum;
use Carbon\Carbon;

readonly class PaymentStatusListRequestDTO
{
    public function __construct(
        public Carbon $dateStart,
        public Carbon $dateEnd,
        public PaymentStatusEnum $status
    ) {
    }
}
