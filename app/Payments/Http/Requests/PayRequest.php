<?php

declare(strict_types=1);

namespace App\Payments\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PayRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'payment_provider' => 'required|string|min:1',
            'payment_method' => 'required|string|min:1',
            'amount' => 'required|numeric|min:1',
            'code' => 'string|min:1',
        ];
    }
}
