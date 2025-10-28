<?php

declare(strict_types=1);

namespace App\Payments\Models;

use App\Casts\IdCast;
use App\Models\User;
use App\Payments\Enum\PaymentProvidersEnum;
use App\Payments\Enum\PaymentStatusEnum;
use App\ValueObjects\Id;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property Id $id
 * @property int $user_id
 * @property int $amount
 * @property PaymentProvidersEnum $payment_provider
 * @property PaymentStatusEnum $status
 * @property string $external_id
 *
 * @property-read User $user
 */
class Payment extends Model
{
    use HasUuids;

    public $timestamps = true;
    public $keyType = 'string';
    public $incrementing = false;
    protected $guarded = [];


    protected function casts(): array
    {
        return [
            'id' => IdCast::class,
            'payment_provider' => PaymentProvidersEnum::class,
            'status' => PaymentStatusEnum::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
