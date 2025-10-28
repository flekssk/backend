<?php

declare(strict_types=1);

namespace App\Payments\PaymentsProviders;

use App\Payments\Api\Gotham\GothamApiClient;
use App\Payments\Api\Gotham\Requests\GothamCreatePaymentRequest;
use App\Models\Payment;
use App\Services\Currencies\Enums\CurrenciesEnum;
use App\Payments\PaymentProvider;
use App\Payments\ValueObjects\PaymentErrorResult;
use App\Payments\ValueObjects\PaymentProviderConfig;
use App\Payments\ValueObjects\PaymentRedirectResult;
use App\Payments\ValueObjects\PaymentShowSBPResult;
use App\Payments\ValueObjects\PaymentSuccessResult;
use Illuminate\Support\Facades\Log;

class GothamPaymentsProvider extends PaymentProvider
{

    public function __construct(
        PaymentProviderConfig $config,
        private readonly GothamApiClient $apiClient
    ) {
        parent::__construct($config);
    }

    public function pay(Payment $payment): PaymentShowSBPResult|PaymentErrorResult|PaymentRedirectResult
    {
        if ($payment->sum < 999) {
            $response = $this->apiClient->createCardNumberOrder(
                new GothamCreatePaymentRequest(
                    (int) $payment->sum,
                    CurrenciesEnum::RUB,
                    (string) $payment->id,
                    $payment->callback_secret
                )
            );
        } else {
            $this->apiClient->createSBPOrder(
                new GothamCreatePaymentRequest(
                    (int) $payment->sum,
                    CurrenciesEnum::RUB,
                    (string) $payment->id,
                    $payment->callback_secret
                )
            );
        }
    }

    public function handleCreateCallback(Payment $payment, array $data): PaymentSuccessResult|PaymentErrorResult
    {
        Log::debug('Gotham callback', ['data' => $data]);

        return new PaymentSuccessResult();
    }
}
