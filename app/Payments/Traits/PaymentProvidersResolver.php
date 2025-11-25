<?php

declare(strict_types=1);

namespace App\Payments\Traits;

use App\Payments\Enum\PaymentMethodEnum;
use App\Payments\Enum\PaymentProvidersEnum;
use App\Payments\PaymentProvider;
use App\Payments\ValueObjects\MethodConfig;
use App\Payments\ValueObjects\PaymentProviderConfig;
use Exception;
use FKS\Serializer\SerializerFacade;

trait PaymentProvidersResolver
{
    /**
     * @var array<string, PaymentProviderConfig>
     */
    public static array $configs = [];

    public function resolveProvider(string|PaymentProvidersEnum $provider): ?PaymentProvider
    {
        $providerConfig = $this->resolveProviderConfig($provider);

        if ($providerConfig === null) {
            return null;
        }

        $service = app($providerConfig->class, ['config' => $providerConfig]);

        if (!$service instanceof PaymentProvider) {
            throw new Exception("Payment provider $provider not found}");
        }

        return $service;
    }

    private function resolveProviderConfig(string|PaymentProvidersEnum $provider): ?PaymentProviderConfig
    {
        $provider = $provider instanceof PaymentProvidersEnum ? $provider->value : $provider;

        if (!array_key_exists($provider, self::$configs)) {
            $config = config('payment-providers.providers.' . $provider);

            if ($config === null) {
                return null;
            }

            self::$configs[$provider] = SerializerFacade::deserializeFromArray($config, PaymentProviderConfig::class);
        }

        return self::$configs[$provider];
    }

    public function resolveMethodConfig(PaymentMethodEnum $method): ?MethodConfig
    {
        $config = config('payment-providers.methods.' . $method->value);

        if ($config === null) {
            return null;
        }

        return SerializerFacade::deserializeFromArray($config, MethodConfig::class);
    }
}
