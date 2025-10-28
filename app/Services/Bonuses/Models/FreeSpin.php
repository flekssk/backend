<?php

declare(strict_types=1);

namespace App\Services\Bonuses\Models;

use App\Models\User;
use App\Services\Bonuses\Enums\FreeSpinSourceEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FreeSpin extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'slot_id',
        'amount',
        'expires_at',
        'source',
    ];

    protected $casts = [
        'source' => FreeSpinSourceEnum::class,
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
