<?php

declare(strict_types=1);

namespace App\Payments\Actions\Withdraws;

use App\Helpers\SettingsHelper;
use App\Models\User;
use App\Payments\Events\WithdrawCreatedEvent;
use App\Payments\Models\Withdraw;
use App\Services\Actions\Actions\ActionCreateAction;
use App\Services\Actions\DTO\ActionCreateDTO;
use App\Currencies\Enums\CurrenciesEnum;
use App\Currencies\Facades\CurrencyConverterFacade;
use App\Payments\DTO\CreateWithdrawDTO;
use App\Payments\Enum\PaymentStatusEnum;
use App\Payments\Enum\WithdrawStatusEnum;
use App\Payments\Traits\PaymentProvidersResolver;
use Carbon\Carbon;
use DomainException;
use FKS\Actions\Action;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 * @method static Withdraw run(CreateWithdrawDTO $dto)
 */
class WithdrawAction extends Action
{
    use PaymentProvidersResolver;

    public function handle(CreateWithdrawDTO $dto): Withdraw
    {
        $withdraw = DB::transaction(function () use ($dto) {
            $providerConfig = $this->resolveProviderConfig($dto->provider);
            $methodConfig = $providerConfig->getWithdrawMethodConfig($dto->method);
            $this->validateWithdraw($dto);

            $antifraudCacheKey = "withdraw_" . $dto->user->id;

            usleep(150 * random_int(1, 80));
            if (Cache::has($antifraudCacheKey)) {
                throw new DomainException('Выплата заблокирована на 1 минуту');
            }
            Cache::put($antifraudCacheKey, true, 60);

            $user = User::where('id', $dto->user->id)->lockForUpdate()->first();

            $amount = $dto->amount;

            $withdraw = Withdraw::create([
                'user_id' => $user->id,
                'wallet' => $dto->wallet,
                'provider' => $dto->provider,
                'amount' => $dto->amount,
                'amount_commission' => (100 - $methodConfig->commissionPercents) / 100,
                'method' => $dto->method,
                'variant' => $dto->variant,
                'status' => WithdrawStatusEnum::CREATE,
            ]);

            WithdrawCreatedEvent::dispatch($withdraw->id);

            return $withdraw;
        });


        return $withdraw;
    }

    public function validateWithdraw(CreateWithdrawDTO $dto): void
    {
        $method = $this->resolveMethodConfig($dto->method);
        $providerMethod = $this->resolveProviderConfig($dto->provider)
            ->getWithdrawMethodConfig($dto->method);

        $data = [
            'wallet' => $dto->wallet,
            'amount' => $dto->amount,
            'wager' => $dto->user->wager,
            'slots_wager' => $dto->user->slots_wager,
        ];

        $rules = [
            'wallet' => $method->walletValidationRules,
            'amount' => "numeric|min:$providerMethod->min|max:{$dto->user->balance}",
        ];

        if ($dto->user->wager_status === 1) {
            $rules['wager'] = 'numeric|max:0';
            $rules['slots_wager'] = 'numeric|max:0';
        }

        $walletErrors = Arr::mapWithKeys(
            $method->walletValidationErrors,
            fn($error, $key) => ['wallet.' . $key => $error]
        );

        $errors = array_merge(
            [
                'amount.min' => "Минимальная сумма вывода $providerMethod->min руб.",
                'amount.max' => "Недостаточно средств на счету",
                'wager.max' => "Необходимо отыграть еще {$dto->user->wager}",
                'slots_wager.max' => "Необходимо отыграть еще {$dto->user->slots_wager}",
            ],
            $walletErrors,
        );

        Validator::validate(
            $data,
            $rules,
            $errors
        );
    }

    public function isAutoWithdrawAvailable(Withdraw $withdraw): bool
    {
        return $this->resolveProvider($withdraw->system)?->isAutoWithdrawAvailable($withdraw) ?? false;
    }
}
