<?php

declare(strict_types=1);

namespace App\Payments\Api\OnePlat\Requests;

use Carbon\Carbon;
use FKS\Serializer\SerializableObject;

class PaymentStatusListRequest extends SerializableObject
{
    public function __construct(
        public Carbon $dateStart,
        public Carbon $dateEnd,
        public int $status,
    ) {
    }
}
