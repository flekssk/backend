<?php

declare(strict_types=1);

namespace App\Services\Games\Actions;

use App\Services\Games\Models\GameSession;
use App\Services\Games\Models\Slot;
use App\ValueObjects\Id;
use DB;
use FKS\Actions\Action;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserLatestGamesAction extends Action
{
    public function handle(Id $userId, int $counts = 6): array
    {
        $latestBySlot = GameSession::query()
            ->select('game_id', DB::raw('MAX(created_at) AS last_played_at'))
            ->where('user_id', $userId)
            ->groupBy('game_id');

        $slots = Slot::query()
            ->leftJoinSub($latestBySlot, 'gs', function ($join) {
                $join->on('gs.game_id', '=', 'slots.id');
            })
            ->select('slots.*', 'gs.last_played_at')
            ->orderByDesc('gs.last_played_at')
            ->limit($counts)
            ->get();

        return $slots->toArray();
    }

    public function asController(Request $request): JsonResponse
    {
        return response()->json($this->handle($request->user()->id));
    }
}
