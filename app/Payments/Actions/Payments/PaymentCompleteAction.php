<?php

declare(strict_types=1);

namespace App\Payments\Actions\Payments;

use App\Payments\Enum\PaymentStatusEnum;
use App\Payments\Events\PaymentCompleteEvent;
use App\Payments\Models\Payment;
use FKS\Actions\Action;

/**
 * @method static void run(Payment $payment)
 */
class PaymentCompleteAction extends Action
{
    public function handle(Payment $payment): void
    {
        $payment->status = PaymentStatusEnum::SUCCESS;
        $payment->save();

        PaymentCompleteEvent::dispatch($payment->id);
    }
}
