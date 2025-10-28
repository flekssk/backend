<?php

declare(strict_types=1);

namespace App\Payments\ValueObjects;

use App\Currencies\Enums\CurrenciesEnum;
use App\Payments\Collections\PaymentMethodCollection;
use App\Payments\Collections\WithdrawalMethodCollection;
use App\Payments\Enum\PaymentMethodEnum;
use App\Payments\Enum\PaymentProvidersEnum;
use App\Payments\PaymentProvider;

readonly class PaymentProviderConfig
{
    /**
     * @param class-string<PaymentProvider> $class
     */
    public function __construct(
        public string $class,
        public PaymentProvidersEnum $provider,
        public CurrenciesEnum $baseCurrency,
        public ?PaymentMethodCollection $payment,
        public ?WithdrawalMethodCollection $withdraw,
    ) {
    }

    public function acceptWithdraw(): bool
    {
        return !empty($this->withdraw);
    }

    public function acceptPayment(): bool
    {
        return !empty($this->payment);
    }

    public function getPaymentMethods(): array
    {
        return $this->payment->toArray();
    }

    public function getWithdrawMethods(): array
    {
        return $this->withdraw->toArray();
    }

    public function getPaymentMethodConfig(PaymentMethodEnum  $paymentMethod): ?PaymentMethodConfig
    {
        if (!$this->acceptPayment()) {
            return null;
        }

        return collect($this->payment)->where('method', $paymentMethod)->first();
    }

    public function getWithdrawMethodConfig(PaymentMethodEnum $paymentMethod): ?WithdrawalMethodConfig
    {
        if (!$this->acceptWithdraw()) {
            return null;
        }

        return collect($this->withdraw)->where('method', $paymentMethod)->first();
    }
}
