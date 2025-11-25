<?php

declare(strict_types=1);

namespace App\Payments\Models;

use App\Casts\IdCast;
use App\Payments\Enum\PaymentProvidersEnum;
use App\Payments\Enum\WithdrawStatusEnum;
use App\ValueObjects\Id;
use Illuminate\Database\Eloquent\Model;

/**
 * @property Id $id
 * @property Id $user_id
 */
class Withdraw extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'id' => IdCast::class,
            'user_id' => IdCast::class,
            'status' => WithdrawStatusEnum::class,
            'provider' => PaymentProvidersEnum::class
        ];
    }
}
