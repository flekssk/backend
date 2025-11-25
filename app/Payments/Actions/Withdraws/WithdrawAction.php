<?php

declare(strict_types=1);

namespace App\Payments\Actions\Withdraws;

use App\Helpers\SettingsHelper;
use App\Models\User;
use App\Payments\Events\WithdrawCreatedEvent;
use App\Payments\Http\Requests\WithdrawRequest;
use App\Payments\Models\Withdraw;
use App\Services\Actions\Actions\ActionCreateAction;
use App\Services\Actions\DTO\ActionCreateDTO;
use App\Currencies\Enums\CurrenciesEnum;
use App\Currencies\Facades\CurrencyConverterFacade;
use App\Payments\DTO\CreateWithdrawDTO;
use App\Payments\Enum\PaymentStatusEnum;
use App\Payments\Enum\WithdrawStatusEnum;
use App\Payments\Traits\PaymentProvidersResolver;
use App\ValueObjects\Id;
use Carbon\Carbon;
use DomainException;
use FKS\Actions\Action;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
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
        return DB::transaction(function () use ($dto) {
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
                'id' => Id::make(),
                'user_id' => $user->id,
                'wallet' => $dto->wallet,
                'provider' => $dto->provider,
                'amount' => $dto->amount,
                'method' => $dto->method,
                'variant' => $dto->variant,
                'status' => WithdrawStatusEnum::CREATE,
            ]);

            $user->getWallet()->decrement('balance', $amount);

            WithdrawCreatedEvent::dispatch($withdraw->id);

            return $withdraw;
        });
    }

    public function validateWithdraw(CreateWithdrawDTO $dto): void
    {
        $method = $this->resolveMethodConfig($dto->method);
        $providerMethod = $this->resolveProviderConfig($dto->provider)
            ->getWithdrawMethodConfig($dto->method);

        if ($dto->user->withdraws()->where('status', WithdrawStatusEnum::CREATE)->count() >= 1) {
            throw new DomainException('Дождитесь предыдущих выплат');
        }

        $data = [
            'wallet' => $dto->wallet,
            'amount' => $dto->amount,
            'wager' => $dto->user->playerProfile->wager,
        ];

        $rules = [
            'wallet' => $method->walletValidationRules,
            'amount' => "numeric|min:$providerMethod->min|max:{$dto->user->getWallet()->balance}",
            'wager' => 'numeric|max:0'
        ];

        $walletErrors = Arr::mapWithKeys(
            $method->walletValidationErrors,
            fn($error, $key) => ['wallet.' . $key => $error]
        );

        $errors = array_merge(
            [
                'amount.min' => "Минимальная сумма вывода $providerMethod->min руб.",
                'amount.max' => "Недостаточно средств на счету",
                'wager.max' => "Необходимо отыграть еще {$dto->user->playerProfile->wager}",
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

    public function asController(WithdrawRequest $request): JsonResponse
    {
        return response()->json($this->handle($request->toDTO()));
    }
}
