<?php

declare(strict_types=1);

namespace App\Payments\Api\Cryptobot\Requests;

use Illuminate\Contracts\Support\Arrayable;

class CryptobotCreateInvoiceRequest implements Arrayable
{
    public function __construct(
        public float|int $amount,
        public string $asset,
        public string $spendId
    ) {
    }

    public function toArray(): array
    {
        return [
            'currency_type' => 'fiat',
            'fiat' => "RUB",
            'asset' => $this->asset,
            'amount' => $this->amount,
            'comment' => $this->spendId,
        ];
    }
}
