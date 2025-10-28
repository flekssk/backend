<?php

declare(strict_types=1);

namespace App\Payments\Actions\Withdraws;

use App\Payments\Models\Withdraw;
use App\Services\Actions\Actions\ActionCreateAction;
use App\Services\Actions\DTO\ActionCreateDTO;
use App\Payments\Enum\WithdrawStatusEnum;
use FKS\Actions\Action;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * @method static run(Withdraw $withdraw, ?string $reason = null)
 */
class WithdrawRollbackToPending extends Action
{
    public function handle(Withdraw $withdraw, ?string $reason = null): void
    {
        ActionCreateAction::run(
            new ActionCreateDTO(
                $withdraw->user_id,
                Str::limit('Вывод не удался ' . $reason, 255),
                0,
                0,
                $reason
            )
        );

        $withdraw->update([
            'status' => WithdrawStatusEnum::CREATE->value,
            'reason' => Str::limit($reason, 255),
        ]);

        Log::error('Withdraw error: ', ['message' => $reason]);
    }
}
