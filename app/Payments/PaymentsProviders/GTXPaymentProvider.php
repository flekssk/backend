<?php

declare(strict_types=1);

namespace App\Payments\PaymentsProviders;

use App\Payments\Api\GTX\GTXApiClient;
use App\Payments\PaymentProvider;
use App\Payments\ValueObjects\PaymentProviderConfig;

class GTXPaymentProvider extends PaymentProvider
{
    public function __construct(
        PaymentProviderConfig $config,
        public readonly GTXApiClient $apiClient
    ) {
        parent::__construct($config);
    }
}
