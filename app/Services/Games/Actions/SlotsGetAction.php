<?php

declare(strict_types=1);

namespace App\Services\Games\Actions;

use App\Models\User;
use App\Services\Games\Enums\GameTypeEnum;
use App\Services\Games\Facades\SlotsServiceFacade;
use App\Services\Games\Models\Slot;
use App\Services\Games\Models\GameSession;
use FKS\Actions\Action;
use Illuminate\Contracts\Queue\EntityNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SlotsGetAction extends Action
{
    public function handle(int $slotId, User $user, bool $mobile = false): array
    {
        $slot = Slot::find($slotId);

        if (!$slot || $slot->is_hidden === false) {
            throw new EntityNotFoundException(Slot::class, $slotId);
        }

        $gameSession = GameSession::create([
            'user_id' => $user->id,
            'game_id' => $slot->id,
            'game_type_id' => GameTypeEnum::SLOT,
            'created_at' => now(),
        ]);

        return [
            'slot' => $slot->only(),
            'start_url' => SlotsServiceFacade::resolveAggregatorService($slot->slot_aggregator_id)->resolveStartUrl($slot, $gameSession, $mobile)
        ];
    }


    public function asController(int $slotId, Request $request): JsonResponse
    {
        Log::info('Game started');
        return response()->json($this->handle($slotId, $request->user(), (bool) $request->mobile));
    }
}
