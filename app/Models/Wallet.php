<?php

declare(strict_types=1);

namespace App\Models;

use App\Currencies\Enums\CurrenciesEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Wallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'currency_code',
        'balance',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'currency_code' => CurrenciesEnum::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
