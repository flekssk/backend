<?php

declare(strict_types=1);

namespace App\Payments\Api\OnePlat;

use App\Payments\Api\OnePlat\Requests\OnePlatPayRequest;
use App\Payments\Api\OnePlat\Requests\PaymentStatusListRequest;
use App\Payments\Api\OnePlat\Responses\OnePlatCreateResponse;
use App\Payments\Api\OnePlat\Responses\OnePlayPaymentsStatusResponse;
use FKS\Api\ApiClient;

class OnePlatApiClient extends ApiClient
{
    public function createPayment(OnePlatPayRequest $request): OnePlatCreateResponse
    {
        $response = $this->post(
            'merchant/order/create/by-api',
            [
                'json' => [
                    'merchant_order_id' => $request->paymentId->uuid(),
                    'user_id' => $request->userId->uuid() . '_' . $request->paymentId->uuid(),
                    'amount' => $request->amount,
                    'email' => $request->userId->uuid() . "@socia.win",
                    'method' => 'sbp'
                ],
                'headers' => [
                    'x-shop' => 959,
                    'x-secret' => config('api-clients.1plat.secret'),
                    'Content-Type' => 'application/json',
                ],
            ]
        );

        return $this->handleResponse($response, OnePlatCreateResponse::class);
    }

    public function getPaymentsStatus(PaymentStatusListRequest $dto): OnePlayPaymentsStatusResponse
    {
        return $this->handleResponse(
            $this->post(
                'merchant/order/list/by-api',
                [
                    'json' => [
                        'date_start' => $dto->dateStart->format('Y-m-d H:i:s'),
                        'status' => $dto->status,
                    ],
                    'headers' => [
                        'x-shop' => 959,
                        'x-secret' => config('api-clients.1plat.secret'),
                        'Content-Type' => 'application/json',
                    ],
                ]
            ),
            OnePlayPaymentsStatusResponse::class
        );
    }
}
