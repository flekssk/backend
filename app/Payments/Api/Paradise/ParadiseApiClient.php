<?php

declare(strict_types=1);

namespace App\Payments\Api\Paradise;

use App\Payments\Api\Paradise\Requests\ParadisePayRequest;
use App\Payments\Api\Paradise\Responses\ParadiseOrderCreateResponse;
use FKS\Api\ApiClient;

class ParadiseApiClient extends ApiClient
{
    public function pay(ParadisePayRequest $request): ParadiseOrderCreateResponse
    {
        $response = $this->post(
            'payments',
            [
                'json' => [
                    'merchant_customer_id' => (string)$request->paymentId,
                    'return_url' => config('app.url'),
                    'amount' => $request->amount,
                    'description' => $request->userId . "@paradise.info",
                    'ip' => $request->ip,
                ],
                'headers' => [
                    "merchant-id" => config('api-clients.paradise.shop_id'),
                    "merchant-secret-key" => config('api-clients.paradise.api_secret'),
                    "Content-Type" => 'application/json',
                ]
            ]
        );
        return $this->handleResponse($response, ParadiseOrderCreateResponse::class);
    }
}
