<?php

declare(strict_types=1);

namespace App\Payments\Models;

use App\Casts\IdCast;
use App\Models\Traits\Only;
use App\Models\User;
use App\Payments\Enum\PaymentMethodEnum;
use App\Payments\Enum\PaymentProvidersEnum;
use App\Payments\Enum\PaymentSourceEnum;
use App\Payments\Enum\PaymentStatusEnum;
use App\ValueObjects\Id;
use Carbon\Carbon;
use FKS\Metadata\Collection\MetadataCollection;
use FKS\Metadata\Models\Traits\HasMetadata;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property Id $id
 * @property Id $user_id
 * @property int $amount
 * @property PaymentProvidersEnum $payment_provider
 * @property PaymentMethodEnum $payment_provider_method
 * @property PaymentSourceEnum $payment_source
 * @property PaymentStatusEnum $status
 * @property string $external_id
 * @property Collection $metadata
 *
 * @property-read User $user
 */
class Payment extends Model
{
    use HasUuids;
    use HasMetadata;
    use Only;

    public $timestamps = true;
    public $keyType = 'string';
    public $incrementing = false;
    protected $guarded = [];


    protected function casts(): array
    {
        return [
            'id' => IdCast::class,
            'user_id' => IdCast::class,
            'payment_provider' => PaymentProvidersEnum::class,
            'payment_provider_method' => PaymentMethodEnum::class,
            'payment_source' => PaymentSourceEnum::class,
            'status' => PaymentStatusEnum::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function availableFields(): array
    {
        return [
            'id',
            'amount',
            'payment_provider',
            'payment_provider_method',
            'payment_source',
            'remaining_seconds_to_pay',
            'status',
            'metadata',
            'created_at',
        ];
    }

    public function getRemainingSecondsToPayAttribute(): ?int
    {
        if ($this->status !== PaymentStatusEnum::PENDING) {
            return null;
        }

        return $this->created_at->addMinutes(15)->timestamp - Carbon::now()->timestamp;
    }
}
