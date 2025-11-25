<?php

declare(strict_types=1);

namespace App\Payments\PaymentsProviders;

use App\Payments\DTO\PaymentStatusDTO;
use App\Payments\DTO\PaymentStatusListRequestDTO;
use App\Payments\Enum\PaymentProvidersEnum;
use App\Payments\Enum\PaymentStatusEnum;
use App\Payments\Models\Payment;
use App\Payments\PaymentProvider;
use App\Payments\ValueObjects\PaymentErrorResult;
use App\Payments\ValueObjects\PaymentProviderConfig;
use App\Payments\ValueObjects\PaymentShowSBPResult;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Support\Facades\Log;

class FKSPaymentProvider extends PaymentProvider
{
    public function __construct(
        PaymentProviderConfig $config,
    ) {
        parent::__construct($config);
    }

    public function pay(Payment $payment): PaymentShowSBPResult|PaymentErrorResult
    {
        try {
            return new PaymentShowSBPResult(
                self::ACTION_SHOW_SBP_FORM,
                '+79536306359',
                'Ксения М.',
                'Альфа Банк',
                $payment->amount,
            );
        } catch (ServerException $exception) {
            if ($exception->getCode() === 500) {
                $response = json_decode($exception->getResponse()->getBody()->getContents(), true);
                Log::error('FKS error', ['error' => $response['error']]);
                return new PaymentErrorResult($response['error']);
            }

            throw $exception;
        }
    }

    public function getPayments(PaymentStatusListRequestDTO $dto): array
    {
        return [];
    }
}
