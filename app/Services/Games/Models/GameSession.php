<?php

declare(strict_types=1);

namespace App\Services\Games\Models;

use App\Casts\IdCast;
use App\Models\Traits\Only;
use App\Services\Games\Contracts\GameInterface;
use App\Services\Games\Enums\GameTypeEnum;
use App\ValueObjects\Id;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property Id $id
 * @property Id $user_id
 *
 * @property-read GameInterface $game
 */
class GameSession extends Model
{
    use Only;
    use HasUuids;

    protected $keyType = 'string';
    public $timestamps = true;
    protected $guarded = [];

    public function availableFields(): array
    {
        return [
            'id',
            'game_type_id',
            'game_id',
        ];
    }

    protected function casts(): array
    {
        return [
            'id' => IdCast::class,
            'user_id' => IdCast::class,
            'game_type_id' => GameTypeEnum::class,
        ];
    }

    public function game(): BelongsTo
    {
        return $this->belongsTo(Slot::class, 'game_id', 'id');
    }
}
