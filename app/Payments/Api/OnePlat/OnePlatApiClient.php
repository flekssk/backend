<?php

declare(strict_types=1);

namespace App\Payments\Api\OnePlat;

use App\Payments\Api\OnePlat\Requests\OnePlatPayRequest;
use App\Payments\Api\OnePlat\Responses\OnePlatCreateResponse;
use FKS\Api\ApiClient;

class OnePlatApiClient extends ApiClient
{
    public function createPayment(OnePlatPayRequest $request): OnePlatCreateResponse
    {
        $response = $this->post(
            'merchant/order/create/by-api',
            [
                'json' => [
                    'merchant_order_id' => $request->paymentId,
                    'user_id' => (string) $request->userId,
                    'amount' => $request->amount,
                    'email' => $request->userId . "@socia.win",
                    'method' => 'sbp'
                ],
                'headers' => [
                    'x-shop' => 959,
                    'x-secret' => config('api-clients.1plat.secret'),
                    'Content-Type' => 'application/json',
                ]
            ]
        );

        return $this->handleResponse($response, OnePlatCreateResponse::class);
    }
}
