<?php

declare(strict_types=1);

namespace App\Payments\PaymentsProviders;

use App\Payments\Api\FK\FKApiClient;
use App\Payments\DTO\PaymentStatusListRequestDTO;
use App\Payments\Enum\PaymentProvidersEnum;
use App\Payments\Enum\WithdrawStatusEnum;
use App\Payments\Models\Payment;
use App\Payments\Models\Withdraw;
use App\Payments\PaymentProvider;
use App\Payments\ValueObjects\PaymentProviderConfig;
use App\Payments\ValueObjects\PaymentRedirectResult;
use App\Payments\ValueObjects\WithdrawResult;
use Illuminate\Support\Facades\Log;

class FKPaymentProvider extends PaymentProvider
{
    public function __construct(
        PaymentProviderConfig $config,
        public readonly FKApiClient $client
    ) {
        parent::__construct($config);
    }

    public function pay(Payment $payment): PaymentRedirectResult
    {
        $terminalId = config('api-clients.fk.terminal_id');
        $terminalSecret1 = config('api-clients.fk.terminal_secret_1');
        $sign = md5($terminalId . ':' . $payment->amount . ':' . $terminalSecret1 . ':RUB:' . $payment->id->uuid());

        $data = [
            'm' => $terminalId,
            'oa' => $payment->amount,
            'o' => $payment->id->uuid(),
            'currency' => 'RUB',
            's' => $sign,
            'i' => 1
        ];

        return new PaymentRedirectResult(
            self::ACTION_REDIRECT,
            "https://pay.fk.money/?" . http_build_query($data),
        );
    }

    public function withdraw(Withdraw $withdraw): WithdrawResult
    {
        if ($withdraw->provider !== PaymentProvidersEnum::FK) {
            return new WithdrawResult(false, WithdrawStatusEnum::DECLINE);
        }

        try {
            Log::debug('FK Withdraw', ['withdraw' => $withdraw->toArray()]);
            $transferId = $this->client->transfer($withdraw->wallet, (int)$withdraw->sumWithCom, (string)$withdraw->id);
        } catch (\Exception $e) {
            Log::error('FK Withdraw error', ['error' => $e->getMessage()]);
            return new WithdrawResult(false, WithdrawStatusEnum::DECLINE, $e->getMessage());
        }

        return new WithdrawResult(true, WithdrawStatusEnum::SUCCESS);
    }

    public function isAutoWithdrawAvailable(Withdraw $withdraw): bool
    {
        $successWitdraws = Withdraw::where('user_id', $withdraw->user_id)
            ->where('status', 1)
            ->count();

        return ($successWitdraws >= 3 && empty($withdraw->user->auto_withdraw));
    }

    public function getBalance(): array
    {
        return $this->client->balance() ?? [];
    }

    public function getPayments(PaymentStatusListRequestDTO $dto): array
    {
        return [];
    }
}
