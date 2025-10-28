<?php

declare(strict_types=1);

namespace App\Payments\Actions\Payments;

use App\Payments\DTO\CreatePaymentDTO;
use App\Payments\Http\Requests\PayRequest;
use App\Payments\Models\Payment;
use App\Payments\ValueObjects\PaymentErrorResult;
use App\Payments\ValueObjects\PaymentRedirectResult;
use App\Payments\ValueObjects\PaymentShowSBPResult;
use DomainException;
use FKS\Actions\Action;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

/**
 * @method static PaymentErrorResult|PaymentShowSBPResult|PaymentRedirectResult run(CreatePaymentDTO $dto)
 */
class PayAction extends Action
{
    public function handle(CreatePaymentDTO $dto): PaymentErrorResult|PaymentShowSBPResult|PaymentRedirectResult
    {
        $bonus = 0;
        $wager = 3;

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
            'payment_provider_method' => $dto->method,
        ]);

        $result = PayThoughtProviderAction::run($payment);

        if (
            !$result instanceof PaymentErrorResult
            && $result->orderId !== null
        ) {
            $payment->external_id = $result->orderId;
            $payment->save();
        }

        return $result;
    }

    public function asController(PayRequest $request): Response|JsonResponse
    {
        $this->handle(
            new CreatePaymentDTO(
                $request->amount,
                $request->payment_provider,
                $request->payment_method,
                $request->user(),
                $request->code,
            )
        );

        return response(status: 201)->json(['success' => true]);
    }
}
