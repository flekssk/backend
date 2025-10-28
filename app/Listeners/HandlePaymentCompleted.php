<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Models\User;
use App\Payments\Events\PaymentCompleteEvent;
use App\Payments\Facades\PaymentServiceFacade;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class HandlePaymentCompleted implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(PaymentCompleteEvent $event): void
    {
        $payment = PaymentServiceFacade::findPaymentById($event->paymentId);

        if ($payment === null) {
            throw new \Exception('Payment not found');
        }

        $user = User::find($payment->user_id);
    }
}
