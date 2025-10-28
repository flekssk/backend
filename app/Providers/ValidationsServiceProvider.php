<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\UuidInterface;

class ValidationsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Validator::extend('uuid_or_hex', static function ($attribute, $value, $parameters, $validator): bool {
            if ($value === null || $value === '') {
                return true;
            }

            if (interface_exists(UuidInterface::class) && $value instanceof UuidInterface) {
                $value = (string) $value;
            }

            if (!is_string($value)) {
                return false;
            }

            $value = trim($value);

            // UUID v1–v5 (RFC 4122)
            $uuidPattern = '/^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i';
            if (preg_match($uuidPattern, $value) === 1) {
                return true;
            }

            // Hex-строка с настраиваемой длиной (по умолчанию 24 и 32)
            $allowedLengths = array_values(array_filter(array_map('intval', (array) $parameters)));
            if (empty($allowedLengths)) {
                $allowedLengths = [24, 32];
            }

            if (preg_match('/^[0-9a-f]+$/i', $value) === 1 && in_array(strlen($value), $allowedLengths, true)) {
                return true;
            }

            return false;
        });

        // Кастомизация сообщения (опционально)
        Validator::replacer('uuid_or_hex', static function ($message, $attribute, $rule, $parameters) {
            $lengths = array_values(array_filter(array_map('intval', (array) $parameters)));
            $hint = empty($lengths)
                ? 'UUID или hex длиной 24/32 символа'
                : 'UUID или hex длиной: ' . implode(', ', $lengths);
            return str_replace(':hint', $hint, $message);
        });
    }
}
