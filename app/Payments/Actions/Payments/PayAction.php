<?php

declare(strict_types=1);

namespace App\Payments\Actions\Payments;

use App\Payments\DTO\CreatePaymentDTO;
use App\Payments\Enum\PaymentMethodEnum;
use App\Payments\Enum\PaymentProvidersEnum;
use App\Payments\Enum\PaymentSourceEnum;
use App\Payments\Facades\PaymentMetadataServiceFacade;
use App\Payments\Http\Requests\PayRequest;
use App\Payments\Models\Payment;
use App\Payments\ValueObjects\PaymentErrorResult;
use App\Payments\ValueObjects\PaymentRedirectResult;
use App\Payments\ValueObjects\PaymentShowSBPResult;
use DomainException;
use FKS\Actions\Action;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

/**
 * @method static PaymentErrorResult|PaymentShowSBPResult|PaymentRedirectResult run(CreatePaymentDTO $dto)
 */
class PayAction extends Action
{
    public function handle(CreatePaymentDTO $dto): Payment|PaymentErrorResult|PaymentShowSBPResult
    {
        $bonus = 0;
        $wager = 5;

        if ($dto->user->playerProfile->limit_payment) {
            throw new DomainException('Платежи ограничены');
        }

        if ($dto->code !== null) {
            $promocodeBonus = $this->promocodeService->applyCode($dto->code, $dto->user);
            $bonus = $promocodeBonus->bonus;
            $wager = $promocodeBonus->wager;
        }

        $payment = Payment::create([
            'user_id' => $dto->user->id,
            'amount' => $dto->amount,
            'payment_provider' => $dto->provider,
            'payment_source' => $dto->source,
            'payment_provider_method' => $dto->method,
        ]);

        $result = PayThoughtProviderAction::run($payment);

        if ($result instanceof PaymentShowSBPResult) {
            PaymentMetadataServiceFacade::upsertMetadataChunk((string) $payment->id, $result->toArray());
            if ($dto->source === PaymentSourceEnum::STIMULE) {
                PaymentMetadataServiceFacade::upsertMetadataChunk(
                    (string) $payment->id,
                    [
                        'stimule_payment_id' => $dto->externalId,
                    ]
                );

                return $result;
            }
        } elseif($result instanceof PaymentRedirectResult) {
            PaymentMetadataServiceFacade::upsertMetadataChunk(
                (string) $payment->id,
                [
                    'url' => $result->url,
                ]
            );
        }

        if ($result instanceof PaymentErrorResult) {
            return $result;
        }

        $payment->external_id = $result->orderId;
        $payment->save();

        $payment->refresh();

        $payment->load('metadata');

        return $payment;
    }

    public function asController(PayRequest $request): Response|JsonResponse
    {
        $response = $this->handle(
            new CreatePaymentDTO(
                $request->amount,
                PaymentProvidersEnum::from($request->payment_provider),
                PaymentMethodEnum::from($request->payment_method),
                $request->payment_source ? PaymentSourceEnum::from($request->payment_source) : PaymentSourceEnum::SOCIA,
                $request->user(),
                $request->external_id,
                $request->code,
            )
        );

        return response()->json(
            $response instanceof Model ? $response->only() : $response->toArray(),
        );
    }
}
