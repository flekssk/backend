<?php

declare(strict_types=1);

namespace App\Payments\Services;

use App\Payments\Models\Payment;
use FKS\Metadata\MetadataService;

class PaymentMetadataService extends MetadataService
{
    public static function getEntity(): string
    {
        return Payment::class;
    }
}
