<?php

declare(strict_types=1);

namespace App\Payments\Actions\Payments;

use App\Payments\Models\Payment;
use App\Payments\Traits\PaymentProvidersResolver;
use App\Payments\ValueObjects\PaymentErrorResult;
use App\Payments\ValueObjects\PaymentRedirectResult;
use App\Payments\ValueObjects\PaymentShowSBPResult;
use FKS\Actions\Action;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

/**
 * @method static PaymentErrorResult|PaymentShowSBPResult|PaymentRedirectResult dispatch(Payment $payment)
 * @method static PaymentErrorResult|PaymentShowSBPResult|PaymentRedirectResult run(Payment $payment)
 */
class PayThoughtProviderAction extends Action
{
    use PaymentProvidersResolver;

    public function handle(Payment $payment): PaymentErrorResult|PaymentShowSBPResult|PaymentRedirectResult
    {
        return DB::transaction(function () use ($payment) {
            $this->validatePayment($payment);

            $result = $this->resolveProvider($payment->payment_provider)->pay($payment);

            Log::error('Test ', ['result' => $result]);

            if (
                $result instanceof PaymentShowSBPResult
                && $result->amount !== (int) $payment->amount
            ) {
                $payment->update([
                    'amount' => $result->amount
                ]);
            }

            return $result;
        });
    }

    private function validatePayment(Payment $payment): void
    {
        $config = $this->resolveProviderConfig($payment->payment_provider)
            ->getPaymentMethodConfig($payment->payment_provider_method);

        if ($config === null) {
            throw new \Exception("Payment method $payment->payment_provider_method not found");
        }

        $validationRules = [
            'amount' => 'required|numeric|min:' . $config->min . '|max:' . $config->max,
        ];

        Validator::make(['amount' => $payment->amount], $validationRules)->validate();
    }
}
