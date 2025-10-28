<?php

declare(strict_types=1);

namespace App\Payments\Collections;

use App\Payments\ValueObjects\WithdrawalMethodConfig;
use Illuminate\Support\Collection;

/**
 * @extends Collection<WithdrawalMethodConfig>
 */
class WithdrawalMethodCollection extends Collection
{
}
