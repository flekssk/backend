<?php

namespace App\Services\Slots\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int|null $id
 * @property string $title
 * @property string $provider
 * @property string $image
 * @property string $alias
 * @property int|null $priority
 * @property bool $is_hidden
 * @property string $created_at
 * @property string|null $updated_at
 */
class Slot extends Model
{
    public $timestamps = true;
    protected $guarded = [];
}
