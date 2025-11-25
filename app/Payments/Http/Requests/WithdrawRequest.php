<?php

declare(strict_types=1);

namespace App\Payments\Http\Requests;

use App\Payments\DTO\CreateWithdrawDTO;
use App\Payments\Enum\PaymentMethodEnum;
use App\Payments\Enum\PaymentProvidersEnum;
use Illuminate\Foundation\Http\FormRequest;

class WithdrawRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'payment_provider' => 'required|string|min:1',
            'payment_method' => 'required|string|min:1',
            'payment_variant' => 'nullable|string|min:1',
            'amount' => 'required|numeric|min:1',
            'wallet' => 'min:1',
        ];
    }

    public function toDTO(): CreateWithdrawDTO
    {
        return new CreateWithdrawDTO(
            (float) $this->get('amount'),
            (string) $this->get('wallet'),
            PaymentProvidersEnum::tryFrom($this->get('payment_provider')),
            PaymentMethodEnum::tryFrom($this->get('payment_method')),
            $this->user(),
            $this->get('payment_variant'),
        );
    }
}
