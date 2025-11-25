<?php

declare(strict_types=1);

namespace App\Services\Games\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SlotWinEvent implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public string $gameName,
        public string $gameImage,
        public float $amount,
        public string $userName,
    ) {
    }

    public function broadcastOn(): Channel
    {
        return new Channel('slots');
    }
}
