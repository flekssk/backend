<?php

declare(strict_types=1);

namespace App\Payments\PaymentsProviders;

use App\Payments\Api\Blvckpay\BlvckpayApiClient;
use App\Payments\Models\Payment;
use App\Payments\PaymentProvider;
use App\Payments\ValueObjects\PaymentRedirectResult;
use App\Payments\ValueObjects\PaymentProviderConfig;

class BlvcpayPymentProvider extends PaymentProvider
{
    public function __construct(
        PaymentProviderConfig $config,
        private readonly BlvckpayApiClient $apiClient
    ) {
        parent::__construct($config);
    }

    public function pay(Payment $payment): PaymentRedirectResult
    {
        $response = $this->apiClient->createSbpOrder($payment->amount);


        return new PaymentRedirectResult(
            self::ACTION_REDIRECT,
            $this->apiClient->getQR($response->orderId),
            $response->orderId
        );
    }
}
