<?php

declare(strict_types=1);

namespace App\Payments\Enum;

enum PaymentProviderIconsEnum: string
{
    case FK = '/images/withdraw/bank-fk.png';
    case CRYPTOBOT = '/images/withdraw/bank-cryptobot.png';
    case SBP = "/images/withdraw/bank-sb.png";
    case CARD = "/images/withdraw/bank-cards.png";
    case USDT = '/images/withdraw/bank-usdc.png';
}
