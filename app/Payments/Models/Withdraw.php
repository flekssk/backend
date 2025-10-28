<?php

declare(strict_types=1);

namespace App\Payments\Models;

use App\Casts\IdCast;
use App\ValueObjects\Id;
use Illuminate\Database\Eloquent\Model;

/**
 * @property Id $id
 * @property int $user_id
 */
class Withdraw extends Model
{
    protected function casts(): array
    {
        return [
            'id' => IdCast::class,
            'user_id' => IdCast::class,
        ];
    }
}
