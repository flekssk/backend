<?php

namespace App\Models;

use App\Currencies\Enums\CurrenciesEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $avatar
 * @property CurrenciesEnum $current_currency
 * @property int $vk_id
 * @property int $tg_id
 * @property bool $welcome_bonus_use
 * @property bool $limit_payment
 */
class PlayerProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'avatar',
        'current_currency',
        'vk_id',
        'tg_id',
        'welcome_bonus_use',
    ];


    protected function casts(): array
    {
        return [
            'vk_id' => 'integer',
            'welcome_bonus_use' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'current_currency' => CurrenciesEnum::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
