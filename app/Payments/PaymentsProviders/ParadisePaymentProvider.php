<?php

declare(strict_types=1);

namespace App\Payments\PaymentsProviders;

use App\Helpers\JsonFixer;
use App\Payments\Api\Paradise\ParadiseApiClient;
use App\Payments\Api\Paradise\Requests\ParadisePayRequest;
use App\Payments\Models\Payment;
use App\Payments\PaymentProvider;
use App\Payments\ValueObjects\PaymentErrorResult;
use App\Payments\ValueObjects\PaymentProviderConfig;
use App\Payments\ValueObjects\PaymentShowSBPResult;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ParadisePaymentProvider extends PaymentProvider
{
    public function __construct(
        PaymentProviderConfig $config,
        public readonly ParadiseApiClient $apiClient
    ) {
        parent::__construct($config);
    }

    public function pay(Payment $payment): PaymentShowSBPResult|PaymentErrorResult
    {
        try {
            $result = $this->apiClient->pay(new ParadisePayRequest($payment->id, $payment->user_id, $payment->amount * 100, request()->ip()));
        } catch (ClientException $e) {
            $content = $e->getResponse()->getBody()->getContents();

            if ($e->getCode() === Response::HTTP_FORBIDDEN) {
                $data = JsonFixer::decode($content);

                return new PaymentErrorResult(Arr::first($data['errors'])['message']);
            }

            throw $e;
        }


        if ($result->status === 'waiting') {
            Log::debug('Paradise response', ['response' => $result]);
            return new PaymentShowSBPResult(
                self::ACTION_SHOW_SBP_FORM,
                $result->paymentMethod->phone,
                $result->paymentMethod->name,
                $result->paymentMethod->bank,
                $result->amount / 100,
                $result->uuid,
            );
        }

        return new PaymentErrorResult(self::ACTION_SHOW_ERROR, 'Не удалось найти реквизиты');
    }
}
