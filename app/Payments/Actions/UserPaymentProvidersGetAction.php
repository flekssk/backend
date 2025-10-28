<?php

declare(strict_types=1);

namespace App\Payments\Actions;

use App\Models\User;
use App\Payments\DTO\PaymentProvidersDTO;
use App\Payments\Facades\PaymentServiceFacade;
use FKS\Actions\Action;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserPaymentProvidersGetAction extends Action
{
    public function handle(User $user): PaymentProvidersDTO
    {
        return PaymentServiceFacade::paymentProviders($user);
    }

    public function asController(Request $request): Response|JsonResponse
    {
        return response()->json($this->handle($request->user()));
    }
}
