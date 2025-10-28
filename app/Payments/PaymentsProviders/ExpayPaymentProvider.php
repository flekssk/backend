<?php

declare(strict_types=1);

namespace App\Payments\PaymentsProviders;

use App\Payments\Api\Expay\ExpayApiClient;
use App\Payments\Api\Expay\Requests\CreatePaymentRequest;
use App\Models\Payment;
use App\Payments\PaymentProvider;
use App\Payments\ValueObjects\PaymentProviderConfig;
use App\Payments\ValueObjects\PaymentRedirectResult;
use Illuminate\Support\Facades\Log;

class ExpayPaymentProvider extends PaymentProvider
{
    public function __construct(PaymentProviderConfig $config, public readonly ExpayApiClient $apiClient)
    {
        parent::__construct($config);
    }

    public function pay(Payment $payment): PaymentRedirectResult
    {
        try {
            $result = $this->apiClient->createPayment(
                new CreatePaymentRequest($payment->sum, (string) $payment->id, (string) $payment->user_id)
            );

            return new PaymentRedirectResult(self::ACTION_REDIRECT, $result->alterRefer, $result->trackerId);
        } catch (\Throwable $e) {
            Log::error('Expay error', ['error' => $e->getMessage()]);

            throw new \DomainException('Не удалось создать платеж');
        }
    }
}
