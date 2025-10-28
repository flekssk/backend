<?php

declare(strict_types=1);

namespace App\Payments\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WithdrawCreatedEvent
{
    use Dispatchable, SerializesModels;

    public function __construct(public int $withdrawId)
    {
    }
}
