<?php

declare(strict_types=1);

namespace App\Models\Traits;

use App\ValueObjects\Id;
use Carbon\Carbon;
use Exception;
use FKS\Metadata\Models\Metadata;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

trait Only
{
    public function availableFields(): array
    {
        return [];
    }

    public function only($attributes = []): array
    {
        $results = [];

        if (!$this instanceof Model) {
            throw new Exception('Only works with Eloquent models');
        }

        if ($attributes === []) {
            $attributes = $this->availableFields();
        }

        foreach (is_array($attributes) ? $attributes : func_get_args() as $index => $attribute) {
            $attrName = is_string($attribute) ? $attribute : (string) $attribute;

            $key = is_string($index) ? $index : $attrName;

            $value = $this->{$attrName} ?? null;
            if ($value === null && $this->relationLoaded(Str::camel($attrName))) {
                $value = $this->getRelation(Str::camel($attrName));
            }

            if ($value instanceof Id) {
                $value = $value->uuid();
            }

            if (is_string($attrName) && str_ends_with($attrName, '_at')) {
                $value = Carbon::make($value)?->toISOString();
            } else {
                if ($value instanceof Collection) {
                    $collectionElements = [];
                    foreach ($value->all() as $item) {
                        if (is_object($item) && method_exists($item, 'only')) {
                            $availableFields = null;
                            if (
                                method_exists($item, 'availableFields')
                                && $item->availableFields() !== []
                            ) {
                                $availableFields = $item->availableFields();
                            }
                            $collectionElements[] = $item->only($availableFields ?? array_keys($item->getAttributes()));
                        } elseif (is_a($item, Metadata::class)) {
                            $collectionElements[] = [
                                'key' => $item->metadata_key,
                                'value' => $item->metadata_value,
                            ];
                        } else {
                            $collectionElements[] = $item;
                        }
                    }
                    $value = $collectionElements;
                }
            }

            $results[$key] = $value;
        }

        return $results;
    }
}
