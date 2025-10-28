<?php

namespace App\Enums\Users;

enum UserRolesEnum: string
{
    case ADMIN = 'admin';
    case WORKER = 'worker';
}
