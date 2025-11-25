<?php

declare(strict_types=1);

namespace App\Payments\Actions\Payments;

use App\Payments\Enum\PaymentStatusEnum;
use App\Payments\Models\Payment;
use FKS\Actions\Action;
use Illuminate\Http\JsonResponse;

class PaymentCancelAction extends Action
{
    public function handle(Payment $payment): Payment
    {
        $payment->status = PaymentStatusEnum::CANCELED;
        $payment->save();

        return $payment;
    }

    public function asController(string $paymentId): JsonResponse
    {
        return response()->json($this->handle(Payment::findOrFail($paymentId))->only());
    }
}
