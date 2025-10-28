<?php

declare(strict_types=1);

namespace App\Payments\Actions\Payments;

use App\Payments\Enum\PaymentStatusEnum;
use App\Payments\Models\Payment;
use FKS\Actions\Action;

/**
 * @method static void run(Payment $payment)
 */
class PaymentFailAction extends Action
{
    public function handle(Payment $payment): void
    {
        $payment->status = PaymentStatusEnum::FAILED;
        $payment->save();
    }
}
