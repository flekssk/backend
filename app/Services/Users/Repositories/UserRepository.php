<?php

declare(strict_types=1);

namespace App\Services\Users\Repositories;

use App\Models\User;
use FKS\Repositories\Repository;
use FKS\Search\Repositories\SearchRepository;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends Repository<User>
 */
class UserRepository extends SearchRepository
{
    public static function getEntityInstance(): Model
    {
        return User::make();
    }
}
