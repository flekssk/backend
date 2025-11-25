<?php

declare(strict_types=1);

namespace App\Payments\Http\Requests;

use App\Payments\Enum\PaymentSourceEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PayRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'payment_provider' => 'required|string|min:1',
            'payment_method' => 'required|string|min:1',
            'payment_source' => [
                Rule::enum(PaymentSourceEnum::class)
            ],
            'amount' => 'required|numeric|min:1',
            'code' => 'string|min:1',
            'external_id' => 'int|min:1',
        ];
    }
}
