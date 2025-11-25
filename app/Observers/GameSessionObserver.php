<?php

declare(strict_types=1);

namespace App\Observers;

use App\Services\Games\Models\GameSession;
use App\ValueObjects\Id;

class GameSessionObserver
{
    public function creating(GameSession $session): void
    {
        if ($session->id === 0) {
            $session->id = Id::make();
        }
    }
}
