<?php

declare(strict_types=1);

namespace App\Services\Games\Models;

use App\Models\Traits\Only;
use App\Services\Games\Contracts\GameInterface;
use App\Services\Games\Enums\SlotsAggregatorEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int|null $id
 * @property string $title
 * @property SlotProvider $provider
 * @property string $image
 * @property string $alias
 * @property int|null $priority
 * @property SlotsAggregatorEnum $slot_aggregator_id
 * @property bool $is_hidden
 * @property string $created_at
 * @property string|null $updated_at
 */
class Slot extends Model implements GameInterface
{
    use Only;

    public $timestamps = true;
    protected $guarded = [];

    public function availableFields(): array
    {
        return [
            'id',
            'title',
            'provider',
            'image',
            'alias',
            'start_url',
        ];
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(SlotProvider::class, 'slot_provider_id', 'id');
    }

    protected function casts(): array
    {
        return [
            'slot_aggregator_id' => SlotsAggregatorEnum::class,
        ];
    }

    public function getName(): string
    {
        return $this->title;
    }

    public function getImage(): string
    {
        return $this->image;
    }
}
