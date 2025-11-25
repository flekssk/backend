<?php

declare(strict_types=1);

namespace App\Services\Games\Http\Requests;

use App\Services\Games\Enums\SlotsFilterPresetEnum;
use FKS\Search\Requests\FilteringDefinitions;
use FKS\Search\Requests\SearchRequest;
use FKS\Search\Requests\SettingsDefinitions;

class SlotsListRequest extends SearchRequest
{
    public static function getAvailableFields(): array
    {
        return [
            'id',
            'title',
            'alias',
            'image',
            'created_at',
        ];
    }

    public static function getSortingDefinitions(): array
    {
        return [
            'priority',
        ];
    }

    public static function getFilteringDefinitions(): FilteringDefinitions
    {
        $definitions = new FilteringDefinitions();

        $definitions->containsInteger('slot_provider_id');

        return $definitions;
    }

    public static function getSettingsDefinitions(): SettingsDefinitions
    {
        $definitions = new SettingsDefinitions();

        $definitions->filterPreset(SlotsFilterPresetEnum::cases());

        return $definitions;
    }
}
