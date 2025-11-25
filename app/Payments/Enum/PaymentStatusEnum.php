<?php

declare(strict_types=1);

namespace App\Payments\Enum;

enum PaymentStatusEnum: int
{
    case PENDING = 0;
    case SUCCESS = 1;
    case FAILED  = 2;
    case EXPIRED  = 3;
    case PAYED  = 4;
    case CANCELED  = 5;
    case AWAIT  = 6;

    public function getHumanName(): string
    {
        return match ($this) {
            self::PENDING => 'Ожидание',
            self::SUCCESS => 'Зачислено на счет',
            self::FAILED => 'Неудача',
            self::EXPIRED => 'Просрочено',
            self::PAYED => 'Оплачен',
            self::CANCELED => 'Отменен',
            self::AWAIT => 'Ожидает подтверждения',
        };
    }
}
