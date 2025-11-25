<?php

declare(strict_types=1);

namespace App\Payments\Api\OnePlat\Responses;

use App\Payments\Api\OnePlat\ValueObjects\OnePlatSBPPayment;

class OnePlayPaymentsStatusResponse
{
    /**
     * @param OnePlatSBPPayment[] $payments
     */
    public function __construct(
        public array $payments,
    ) {
    }
}
