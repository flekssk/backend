<?php

declare(strict_types=1);

namespace App\Payments\Api\GTX;

use App\Payments\Api\ApiClient;
use App\Payments\Api\GTX\Requests\GTXPayRequest;
use App\Payments\Enum\PaymentMethodEnum;
use GuzzleHttp\Exception\RequestException;

class GTXApiClient extends ApiClient
{
    public function pay(GTXPayRequest $request)
    {
        if ($request->method !== PaymentMethodEnum::C2C) {
            throw new \LogicException('GTX accept only c2c method');
        }

        $payload = [
            'amount' => $request->amount,
            'callbackUri' => config('app.url') . '/callback/gtx',
            'currency' => 'RUB',
            'failUri' => config('app.url') . '/pay',
            'merchantId' => config('api-clients.gtx.merchant_id'),
            'orderId' => $request->orderId,
            'successUri' => config('app.url') . '/pay',
            'userId' => $request->userId,
            'method' => $request->method->value,
        ];

        $signature = hash_hmac(
            'sha256',
            json_encode($payload),
            config('api-clients.gtx.api_secret_key')
        );


        try {
            $response = $this->post(
                'payments',
                [
                    'json' => $payload,
                    'headers' => [
                        'Signature' => $signature,
                    ]
                ],
            );
        } catch (RequestException $e) {
            dd(json_decode($e->getResponse()->getBody()->getContents(), true));
        }

        $data = json_decode($response->getBody()->getContents(), true);
        dd($data);
    }
}
