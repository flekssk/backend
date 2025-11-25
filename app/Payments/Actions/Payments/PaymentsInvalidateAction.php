<?php

declare(strict_types=1);

namespace App\Payments\Actions\Payments;

use App\Payments\DTO\PaymentStatusDTO;
use App\Payments\DTO\PaymentStatusListRequestDTO;
use App\Payments\Enum\PaymentProvidersEnum;
use App\Payments\Enum\PaymentStatusEnum;
use App\Payments\Facades\PaymentServiceFacade;
use App\Payments\Models\Payment;
use Carbon\Carbon;
use FKS\Actions\Action;
use Log;
use Throwable;

class PaymentsInvalidateAction extends Action
{
    public $commandSignature = 'payments:invalidate';

    public function handle(): void
    {
        Log::info('PaymentsInvalidateAction started');

        $providers = [
            PaymentProvidersEnum::ONE_PLAT,
            PaymentProvidersEnum::CRYPTOBOT,
            PaymentProvidersEnum::FK,
            PaymentProvidersEnum::FKS,
        ];

        foreach ($providers as $providerEnum) {
            try {
                $provider = PaymentServiceFacade::resolveProvider($providerEnum);

                $dateFrom = Carbon::now()->subMinutes(15);

                $dto = new PaymentStatusListRequestDTO(
                    $dateFrom,
                    Carbon::now(),
                    PaymentStatusEnum::SUCCESS
                );

                /** @var PaymentStatusDTO $payment */
                foreach ($provider->getPayments($dto) as $status) {
                    $payment = Payment::find($status->paymentId);

                    if (
                        $payment->status !== PaymentStatusEnum::SUCCESS
                        && $status->status === PaymentStatusEnum::SUCCESS
                    ) {
                        PaymentReleaseAction::run($status->paymentId);
                    }
                }

                Payment::query()
                    ->where('status', PaymentStatusEnum::PENDING)
                    ->where('created_at', '<=', $dateFrom)
                    ->where('payment_provider', $providerEnum)
                    ->update(['status' => PaymentStatusEnum::EXPIRED]);
            } catch (Throwable $exception) {
                Log::error(
                    'Payments invalidate error: ' . $exception->getMessage(),
                    [
                        'exception' => $exception,
                        'provider' => $providerEnum->value,
                    ]
                );

                throw $exception;
            }
        }

        $cascadeToClear = [
            PaymentProvidersEnum::FROM_100_SBP_CASCADE,
        ];

        foreach ($cascadeToClear as $item) {
            Payment::query()
                ->where('payment_provider', $item)
                ->where('created_at', '<=', Carbon::now()->subMinutes(5))
                ->forceDelete();
        }
    }
}
