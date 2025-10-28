<?php

declare(strict_types=1);

namespace App\Payments\Helpers;

use App\Payments\Enum\PaymentProvidersEnum;
use App\Payments\ValueObjects\PaymentProviderConfig;
use FKS\Serializer\SerializerFacade;

class PaymentProviderConfigHelper
{
    public static function getConfig(PaymentProvidersEnum $provider): PaymentProviderConfig
    {
        $config = config('payments.providers.' . $provider->value);

        if ($config === null) {
            throw new \Exception('Config for provider ' . $provider->value . ' not found');
        }

        return SerializerFacade::deserializeFromArray($config, PaymentProviderConfig::class);
    }
}
