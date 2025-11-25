<?php

declare(strict_types=1);

namespace App\Services\Games\Repositories;

use App\Models\GameLog;
use FKS\Repositories\Repository;
use FKS\Search\Collections\EntitiesCollection;
use FKS\Search\Repositories\SearchRepository;
use FKS\Search\ValueObjects\SearchConditions;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends Repository<GameLog>
 */
class GameLogRepository extends SearchRepository
{
    public static function getEntityInstance(): Model
    {
        return GameLog::make();
    }

    public function getLastWinLogs(SearchConditions $searchConditions): bool|EntitiesCollection|Builder|\Illuminate\Support\Collection
    {
        $query = $this->getQuery()
            ->orderBy('created_at', 'desc')
            ->where('profit', '>', 0);

        return $this->search($searchConditions, $query);
    }

    public static function getMapAvailableFieldToWith(): array
    {
        return [
            'gameSession' => ['gameSession', 'gameSession.game', 'user'],
        ];
    }
}
