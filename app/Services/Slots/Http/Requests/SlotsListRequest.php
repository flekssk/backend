<?php

declare(strict_types=1);

namespace App\Services\Slots\Http\Requests;

use App\Services\Slots\Enums\SlotsFilterPresetEnum;
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
        return [];
    }

    public static function getSettingsDefinitions(): SettingsDefinitions
    {
        $definitions = new SettingsDefinitions();

        $definitions->filterPreset(SlotsFilterPresetEnum::cases());

        return $definitions;
    }
}
