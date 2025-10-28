<?php

declare(strict_types=1);

namespace App\Payments\PaymentsProviders;

use App\Payments\Api\OnePlat\OnePlatApiClient;
use App\Payments\Api\OnePlat\Requests\OnePlatPayRequest;
use App\Payments\Enum\PaymentStatusEnum;
use App\Payments\Models\Payment;
use App\Payments\PaymentProvider;
use App\Payments\ValueObjects\PaymentErrorResult;
use App\Payments\ValueObjects\PaymentProviderConfig;
use App\Payments\ValueObjects\PaymentShowSBPResult;
use App\Payments\ValueObjects\PaymentSuccessResult;
use App\ValueObjects\Id;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Support\Facades\Log;

class OnePlatPaymentProvider extends PaymentProvider
{
    public function __construct(
        PaymentProviderConfig $config,
        public readonly OnePlatApiClient $apiClient
    ) {
        parent::__construct($config);
    }

    public function pay(Payment $payment): PaymentShowSBPResult|PaymentErrorResult
    {
        try {
            $result = $this->apiClient->createPayment(
                new OnePlatPayRequest($payment->id, $payment->user_id, $payment->amount)
            );

            Log::error('OnePlat result', ['result' => $result]);

            return new PaymentShowSBPResult(
                self::ACTION_SHOW_SBP_FORM,
                $result->payment->note->pan,
                $result->payment->note->fio,
                $result->payment->note->bank,
                $result->payment->amountToPay,
                $result->guid,
            );
        } catch (ServerException $exception) {
            if ($exception->getCode() === 500) {
                $response = json_decode($exception->getResponse()->getBody()->getContents(), true);
                Log::error('OnePlat error', ['error' => $response['error']]);
                return new PaymentErrorResult($response['error']);
            }

            throw $exception;
        }
    }

    public function handleCreateCallback(array $data): PaymentSuccessResult|PaymentErrorResult
    {
        $id = Id::makeNullable($data['merchant_id']);

        if ($id === null) {
            return new PaymentErrorResult('Merchant meta not passed');
        }

        $payment = Payment::find($id);
        $amount = $data['amount'];

        if (!$payment) {
            return new PaymentErrorResult('Order not found');
        }

        if ($amount > 0 && $payment->status != 1) {
            if ($amount !== $payment->sum) {
                $payment->amount = $amount;
                $payment->save();
            }

            $incrementSum = $payment->bonus !== 0
                ? $payment->amount + (($payment->amount * $payment->bonus) / 100)
                : $payment->amount;

            $payment->user->increment('wager', $payment->sum * 3);
            $payment->user->increment('balance', $incrementSum);

            $payment->status = PaymentStatusEnum::SUCCESS;
            $payment->save();
        }
    }
}
