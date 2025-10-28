<?php

declare(strict_types=1);

namespace App\Payments\Enum;

enum PaymentProvidersEnum: string
{
    case CRYPTOBOT = 'cryptobot';
    case FK = 'fk';
    case PARADISE = 'paradise';
    case BLVCKPAY = 'blvckpay';
    case ONE_PLAT = '1plat';
    case GTX = 'GTX';
    case EXPAY = 'expay';
    case ONEPAY = 'onepayment';
    case USDT = 'usdt';
    case GOTHAM = 'gotham';
    case FROM_100_SBP_CASCADE = 'from_100_sbp_cascade';
    case C2C_CASCADE = 'from_100_c2c_cascade';
}
