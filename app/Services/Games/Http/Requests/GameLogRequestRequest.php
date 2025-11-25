<?php

declare(strict_types=1);

namespace App\Services\Games\Http\Requests;

use App\Models\GameLog;
use FKS\Search\Requests\FilteringDefinitions;
use FKS\Search\Requests\SearchRequest;

class GameLogRequestRequest extends SearchRequest
{
    public static function getAvailableFields(): array
    {
        return GameLog::make()->availableFields();
    }

    public static function getSortingDefinitions(): array
    {
        return [
            'created_at',
        ];
    }

    public static function getFilteringDefinitions(): FilteringDefinitions
    {
        $definitions = new FilteringDefinitions();

        return $definitions;
    }
}
