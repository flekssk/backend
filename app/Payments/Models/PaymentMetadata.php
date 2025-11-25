<?php

declare(strict_types=1);

namespace App\Payments\Models;

use Carbon\Carbon;
use FKS\Metadata\Models\Metadata;

/**
 * @property int $id
 * @property int $game_session_id
 * @property string $metadata_key
 * @property string $metadata_value
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class PaymentMetadata extends Metadata
{
    public $timestamps = true;
    protected $guarded = [];
}
