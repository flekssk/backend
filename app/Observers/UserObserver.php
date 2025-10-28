<?php

declare(strict_types=1);

namespace App\Observers;

use App\Currencies\Enums\CurrenciesEnum;
use App\Models\User;
use App\ValueObjects\Id;

class UserObserver
{
    public function creating(User $user): void
    {
        if ($user->id === null) {
            $user->id = Id::make();
        }
    }

    public function created(User $user): void
    {
        $user->playerProfile()->create([
            'current_currency' => CurrenciesEnum::RUB,
        ]);
        $user->wallets()->create([
            'currency_code' => CurrenciesEnum::RUB,
        ]);
    }
}
