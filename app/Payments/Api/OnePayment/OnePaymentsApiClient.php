<?php

declare(strict_types=1);

namespace App\Payments\Api\OnePayment;

use App\Payments\Api\OnePayment\Responses\OnePaymentsBalanceResponse;
use FKS\Api\ApiClient;
use FKS\Serializer\SerializerFacade;

class OnePaymentsApiClient extends ApiClient
{
    public function pay()
    {

    }

    public function getBalance(): OnePaymentsBalanceResponse
    {
        return SerializerFacade::deserializeFromArray(
            json_decode($this->client->get('v1/balance')->getBody()->getContents(), true)['data'],
            OnePaymentsBalanceResponse::class
        );
    }
}
