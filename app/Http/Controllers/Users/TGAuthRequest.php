<?php

declare(strict_types=1);

namespace App\Http\Controllers\Users;

use Illuminate\Foundation\Http\FormRequest;

class TGAuthRequest extends FormRequest
{
    public function rules()
    {
        return [
            'hash' => 'required|string',
            'auth_date' => [
                'required',
                'integer',
                function (string $attribute, $value, \Closure $fail): void {
                    $now = now()->timestamp;

                    // Не старше 3 минут (180 секунд)
                    if ($value < $now - 180) {
                        $fail('Метка времени просрочена.');
                    }

                    // Небольшая защита от слишком «будущих» значений
                    if ($value > $now + 30) {
                        $fail('Метка времени некорректна.');
                    }
                },
            ],
            'photo_url' => 'nullable|string',
        ];
    }
}
