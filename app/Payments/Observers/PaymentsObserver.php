<?php

declare(strict_types=1);

namespace App\Payments\Observers;

use App\Payments\Models\Payment;
use App\ValueObjects\Id;

class PaymentsObserver
{
    public function creating(Payment $payment): void
    {
        if ($payment->id === null) {
            $payment->id = Id::make();
        }
    }
}
