<?php

declare(strict_types=1);

namespace App\Payments\PaymentsProviders;

use App\Payments\Models\Payment;
use App\Payments\Actions\Payments\PayThoughtProviderAction;
use App\Payments\PaymentProvider;
use App\Payments\ValueObjects\PaymentErrorResult;
use App\Payments\ValueObjects\PaymentRedirectResult;
use App\Payments\ValueObjects\PaymentShowSBPResult;
use Illuminate\Support\Facades\Log;

class CascadePaymentProvider extends PaymentProvider
{
    public function pay(Payment $payment): PaymentRedirectResult|PaymentShowSBPResult|PaymentErrorResult
    {
        if (!$this->config->getPaymentMethodConfig($payment->payment_provider_method)) {
            throw new \DomainException('Выберите платежный метод');
        }

        $methodConfig = $this->config->getPaymentMethodConfig($payment->payment_provider_method);
        $errors = [];

        if (!is_array($methodConfig->cascade) && count($methodConfig->cascade) === 0) {
            throw new \DomainException('Выберите платежный метод');
        }

        foreach ($methodConfig->cascade as $item) {
            $payment->payment_provider = $item->value;
            try {
                $result = PayThoughtProviderAction::run($payment);

                if ($result instanceof PaymentShowSBPResult) {
                    Log::error("Show SBP", ['data' => $result]);
                }

                if ($result instanceof PaymentErrorResult) {
                    $errors[] = $result->error;

                    Log::error('Cascade payment error', ['error' => $result->error]);
                    continue;
                }
                $payment->save();

                return $result;
            } catch (\Throwable $e) {
                Log::error('Cascade payment error', ['error' => $e->getMessage(), 'exception' => $e]);
            }
        }

        Payment::find($payment->id)->forceDelete();

        if ($errors === []) {
            $errors[] = 'Попробуйте сделать платеж через 15 минут.';
        }

        return new PaymentErrorResult($errors);
    }
}
