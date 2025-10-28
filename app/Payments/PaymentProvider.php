<?php

declare(strict_types=1);

namespace App\Payments;

use App\Payments\Models\Payment;
use App\Payments\Models\Withdraw;
use App\Payments\DTO\PaymentProviderBalanceDTO;
use App\Payments\ValueObjects\PaymentErrorResult;
use App\Payments\ValueObjects\PaymentProviderConfig;
use App\Payments\ValueObjects\PaymentRedirectResult;
use App\Payments\ValueObjects\PaymentShowSBPResult;
use App\Payments\ValueObjects\PaymentSuccessResult;
use App\Payments\ValueObjects\WithdrawResult;

abstract class PaymentProvider
{
    public const string ACTION_REDIRECT = 'redirect';
    public const string ACTION_SHOW_SBP_FORM = 'show_sbp_form';
    public const string ACTION_SHOW_ERROR = 'show_error';

    public function __construct(public readonly PaymentProviderConfig $config) {}

    public function isAutoWithdrawAvailable(Withdraw $withdraw): bool
    {
        return false;
    }

    public function withdraw(Withdraw $withdraw): WithdrawResult
    {
        throw new \Exception('Provider do not implement withdraw');
    }

    public function pay(Payment $payment): PaymentRedirectResult|PaymentShowSBPResult|PaymentErrorResult
    {
        throw new \Exception('Provider do not implement deposit');
    }

    /**
     * @return PaymentProviderBalanceDTO[]
     */
    public function getBalance(): array
    {
        throw new \Exception('Provider do not implement getBalance');
    }

    public function handleCreateCallback(array $data): PaymentSuccessResult|PaymentErrorResult
    {
        throw new \Exception('Provider do not implement handleCreateCallback');
    }

    public function reduceAmountByBonusPercents(Payment $payment): float|int
    {
        $amount = $payment->amount;
        $bonusPercents = $this->config->getPaymentMethodConfig($payment->method)->bonusPercent;

        if ($bonusPercents) {
            $amount = $payment->amount - ($payment->amount / (100 + $bonusPercents) * $bonusPercents);
        }

        return $amount;
    }
}
