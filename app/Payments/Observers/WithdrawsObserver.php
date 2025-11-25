<?php

declare(strict_types=1);

namespace App\Payments\Observers;

use App\Payments\Models\Withdraw;
use App\ValueObjects\Id;

class WithdrawsObserver
{
    public function creating(Withdraw $withdraw): void
    {
        if ($withdraw->id === null) {
            $withdraw->id = Id::make();
        }
    }
}
