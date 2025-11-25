<?php

declare(strict_types=1);

namespace App\Models;

use App\Casts\IdCast;
use App\Currencies\Enums\CurrenciesEnum;
use App\Models\Traits\Only;
use App\Payments\Models\Payment;
use App\Payments\Models\Withdraw;
use App\ValueObjects\Id;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property Id $id
 * @property-read PlayerProfile $playerProfile
 * @property-read Collection<Wallet> $wallets
 * @property-read Collection<Withdraw> $withdraws
 * @property-read Collection<Payment> $payments
 */
class User extends Authenticatable
{
    use HasApiTokens;

    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use HasUuids;
    use Only;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    protected $appends = [
        'profile_photo_url',
    ];

    protected $keyType = 'string';

    protected function casts(): array
    {
        return [
            'id' => IdCast::class,
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function playerProfile(): HasOne
    {
        return $this->hasOne(PlayerProfile::class);
    }

    public function wallets(): User|HasMany
    {
        return $this->hasMany(Wallet::class);
    }

    public function payments(): User|HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function withdraws(): User|HasMany
    {
        return $this->hasMany(Withdraw::class);
    }

    public function getWallet(CurrenciesEnum $currency = CurrenciesEnum::RUB): Wallet
    {
        $wallet = $this->wallets->where('currency_code', $currency)->first();

        if ($wallet === null) {
            throw new \Exception('Wallet not found');
        }

        return $wallet;
    }
}
