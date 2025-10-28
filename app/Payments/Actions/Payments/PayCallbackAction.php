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

class PayCallbackAction extends Action
{
    use PaymentProvidersResolver;

    public function __construct(public readonly PaymentsService $paymentsService)
    {
    }

    public function handle(PaymentProvidersEnum $provider, Request $request): PaymentSuccessResult|PaymentErrorResult
    {
        return $this->resolveProvider($provider)->handleCreateCallback($request->all());
    }
}
