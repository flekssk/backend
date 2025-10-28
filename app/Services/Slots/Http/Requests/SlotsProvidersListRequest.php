<?php

declare(strict_types=1);

namespace App\Services\Slots\Http\Requests;

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
            'created_at',
        ];
    }

    public static function getSortingDefinitions(): array
    {
        return [];
    }
}
