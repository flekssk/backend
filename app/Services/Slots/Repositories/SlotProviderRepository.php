<?php

declare(strict_types=1);

namespace App\Services\Slots\Repositories;

use App\Services\Slots\Models\SlotProvider;
use FKS\Repositories\Repository;
use FKS\Search\Repositories\SearchRepository;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends Repository<SlotProvider>
 */
class SlotProviderRepository extends SearchRepository
{
    public static function getEntityInstance(): Model
    {
        return SlotProvider::make();
    }
}
