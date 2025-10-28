<?php

declare(strict_types=1);

namespace App\Services\Slots\Repositories;

use App\Services\Slots\Models\Slot;
use FKS\Repositories\Repository;
use FKS\Search\Repositories\SearchRepository;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends Repository<Slot>
 */
class SlotRepository extends SearchRepository
{
    public static function getEntityInstance(): Model
    {
        return Slot::make();
    }
}
