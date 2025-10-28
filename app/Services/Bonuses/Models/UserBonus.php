<?php

declare(strict_types=1);

namespace App\Services\Bonuses\Models;

use App\Enums\Users\BonusTypeEnum;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $amount
 * @property int $wager
 * @property BonusTypeEnum $bonus_type_id
 */
class UserBonus extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'bonus_type_id' => BonusTypeEnum::class,
        ];
    }
}
