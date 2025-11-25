<?php

declare(strict_types=1);

namespace App\Services\Games\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int|null $id
 * @property string $name
 * @property string $alias
 * @property string $image
 * @property string $icon
 * @property string $created_at
 * @property string|null $updated_at
 */
class SlotProvider extends Model
{
    public $timestamps = true;
    protected $guarded = [];
}
