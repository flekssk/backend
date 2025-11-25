<?php

declare(strict_types=1);

namespace App\Services\Games\Actions;

use App\Models\GameLog;
use App\Services\Games\Http\Requests\GameLogRequestRequest;
use App\Services\Games\Repositories\GameLogRepository;
use FKS\Actions\Action;
use FKS\Search\Collections\EntitiesCollection;
use FKS\Search\ValueObjects\SearchConditions;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;

class GameLastWinAction extends Action
{
    public function __construct(private readonly GameLogRepository $repository)
    {
    }

    public function handle(SearchConditions $conditions): EntitiesCollection|Builder|bool|Collection|null
    {
        return $this->repository->getLastWinLogs($conditions);
    }

    public function asController(GameLogRequestRequest $request): Response|JsonResponse
    {
        return response()->json(
            $this->handle($request->getSearchConditions())
                ->map(function (GameLog $session) {
                    return [
                        'game_title' => $session->gameSession->game->getName(),
                        'game_image' => $session->gameSession->game->getImage(),
                        'user_name' => $session->user->name,
                        'profit' => $session->profit,
                    ];
                })
                ->toArray()
        );
    }
}
