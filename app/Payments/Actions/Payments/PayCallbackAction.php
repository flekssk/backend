<?php

declare(strict_types=1);

namespace App\Payments\Actions\Payments;

use App\Payments\Enum\PaymentProvidersEnum;
use App\Payments\PaymentsService;
use App\Payments\Traits\PaymentProvidersResolver;
use App\Payments\ValueObjects\PaymentErrorResult;
use App\Payments\ValueObjects\PaymentSuccessResult;
use FKS\Actions\Action;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class PayCallbackAction extends Action
{
    use PaymentProvidersResolver;

    public function __construct(public readonly PaymentsService $paymentsService)
    {
    }

    public function handle(PaymentProvidersEnum $provider, array $data): PaymentSuccessResult|PaymentErrorResult
    {
        return $this->resolveProvider($provider)->handleCreateCallback($data);
    }

    public function asController($provider, Request $request): Response
    {
        Log::info("Handle $provider callback", ['data' => $request->all()]);

        $this->handle(PaymentProvidersEnum::from($provider), $request->all());

        return response()->noContent();
    }
}
