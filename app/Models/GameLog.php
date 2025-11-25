<?php

namespace App\Models;

use App\Casts\IdCast;
use App\Models\Traits\Only;
use App\Services\Games\Models\GameSession;
use App\ValueObjects\Id;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property float $profit
 * @property Id $user_id
 * @property Id $game_session_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property-read GameSession $gameSession
 */
class GameLog extends Model
{
    use Only;

    protected $guarded = [];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'user_id' => IdCast::class,
        'game_session_id' => IdCast::class,
    ];

    public function availableFields(): array
    {
        return [
            'id',
            'profit',
            'game_session_id',
            'user_id',
            'gameSession',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function gameSession(): BelongsTo
    {
        return $this->belongsTo(GameSession::class);
    }
}
