<?php

declare(strict_types=1);

namespace App\Payments;

use App\Payments\Models\Payment;
use App\Models\User;
use App\Payments\Models\Withdraw;
use App\Repositories\Payments\PaymentsRepository;
use App\Repositories\Payments\WithdrawsRepository;
use App\Payments\Collections\MethodsCollection;
use App\Payments\Collections\PaymentSystemBalanceCollection;
use App\Payments\DTO\PaymentProvidersDTO;
use App\Payments\DTO\UserPaymentsCountsDTO;
use App\Payments\Enum\PaymentProvidersEnum;
use App\Payments\Enum\PaymentStatusEnum;
use App\Payments\Traits\PaymentProvidersResolver;
use Carbon\Carbon;
use FKS\Search\Collections\EntitiesCollection;
use FKS\Search\ValueObjects\SearchConditions;
use FKS\Serializer\SerializerFacade;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class PaymentsService
{
    use PaymentProvidersResolver;

    public function __construct(
        public readonly WithdrawsRepository $withdrawsRepository,
        public readonly PaymentsRepository $paymentsRepository,
    ) {}

    public function paymentProviders(User $user): PaymentProvidersDTO
    {
        $config = config('payment-providers');

        $providers = $config['providers'];
        $userPaymentsCount = $user->payments()->where('status', PaymentStatusEnum::SUCCESS->value)->count();

        foreach ($providers as $key => &$provider) {
            // Обрабатываем обе секции: payment и withdraw
            foreach (['payment', 'withdraw'] as $section) {
                $items = $provider[$section] ?? [];

                if (!is_array($items) || empty($items)) {
                    $provider[$section] = [];
                    continue;
                }

                $items = array_filter($items, static function (array $item) use ($userPaymentsCount): bool {
                    if (!empty($item['hidden']) && $item['hidden'] === true) {
                        return false;
                    }

                    if (isset($item['min_payments_count']) && is_numeric($item['min_payments_count'])) {
                        $minCount = (int)$item['min_payments_count'];
                        if ($userPaymentsCount < $minCount) {
                            return false;
                        }
                    }

                    return true;
                });

                usort($items, static function (array $a, array $b): int {
                    $posA = $a['position'] ?? PHP_INT_MAX;
                    $posB = $b['position'] ?? PHP_INT_MAX;
                    return $posA <=> $posB;
                });

                $provider[$section] = array_values($items);
            }

            if (empty($provider['payment']) && empty($provider['withdraw'])) {
                unset($providers[$key]);
            }
        }
        unset($provider);

        $config['providers'] = $providers;

        return SerializerFacade::deserializeFromArray($config, PaymentProvidersDTO::class);
    }

    public function methods(): MethodsCollection
    {
        return SerializerFacade::deserializeFromArray(config('payment-providers.methods'), MethodsCollection::class);
    }

    public function findDepositByExternalId(string $externalId): ?Payment
    {
        return Payment::query()
            ->where('external_id', $externalId)
            ->first();
    }

    public function findPaymentById(int $paymentId): ?Payment
    {
        return Payment::find($paymentId);
    }

    public function getBalance(PaymentProvidersEnum $provider): PaymentSystemBalanceCollection
    {
        return PaymentSystemBalanceCollection::make($this->resolveProvider($provider)->getBalance());
    }

    public function getProvideImage(Payment|Withdraw $model): ?string
    {
        if ($model->provider === null) {
            return null;
        }

        $config = $this->resolveProviderConfig($model->provider);

        if ($model instanceof Payment) {
            return $config?->getPaymentMethodConfig($model->method)?->image;
        }

        $methodConfig = $config?->getWithdrawMethodConfig($model->method);

        if ($model->variant === null) {
            return $methodConfig?->image;
        }

        return $methodConfig?->getVariantConfig($model->variant)?->image;
    }

    public function getWithdrawsList(SearchConditions $conditions): bool|EntitiesCollection|Builder|Collection
    {
        return $this->withdrawsRepository->search($conditions);
    }

    public function getPaymentsList(SearchConditions $conditions): bool|EntitiesCollection|Builder|Collection
    {
        return $this->paymentsRepository->search($conditions);
    }

    public function findPaymentBySecret(string $paymentSecret): ?Payment
    {
        return $this->paymentsRepository->findByWhere(['payment_secret' => $paymentSecret]);
    }

    public function getPaymentsCounts(int $userId, ?Carbon $createdFrom = null): UserPaymentsCountsDTO
    {
        $statuses = [
            PaymentStatusEnum::PENDING->value => 0,
            PaymentStatusEnum::SUCCESS->value => 0,
            PaymentStatusEnum::FAILED->value => 0,
        ];

        $counts = Payment::query()
            ->where('user_id', $userId)
            ->when($createdFrom, fn (Builder $q) => $q->where('created_at', '>=', $createdFrom))
            ->selectRaw('status, COUNT(*) AS cnt')
            ->groupBy('status')
            ->pluck('cnt', 'status');

        foreach ($counts as $status => $count) {
            $statuses[$status] = $count;
        }

        return new UserPaymentsCountsDTO($userId, $statuses);
    }
}
