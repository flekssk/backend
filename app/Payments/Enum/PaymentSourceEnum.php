<?php

declare(strict_types=1);

namespace App\Payments\Enum;

enum PaymentSourceEnum: string
{
    case SOCIA = 'socia';
    case STIMULE = 'stimule';
}
