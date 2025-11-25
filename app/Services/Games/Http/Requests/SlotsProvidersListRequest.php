<?php

declare(strict_types=1);

namespace App\Services\Games\Http\Requests;

use FKS\Search\Requests\SearchRequest;

class SlotsProvidersListRequest extends SearchRequest
{
    public static function getAvailableFields(): array
    {
        return [
            'id',
            'name',
            'alias',
            'image',
            'icon',
            'created_at',
        ];
    }

    public static function getSortingDefinitions(): array
    {
        return [];
    }
}
