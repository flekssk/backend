<?php

declare(strict_types=1);

namespace App\Payments\Api\Cryptobot;

use App\Currencies\Enums\CurrenciesEnum;
use App\Payments\Api\Cryptobot\Requests\CryptobotCreateInvoiceRequest;
use App\Payments\Api\Cryptobot\Requests\CryptobotTransferRequest;
use App\Payments\Api\Cryptobot\Responses\CryptobotCreateInvoseResponse;
use App\Payments\Api\Cryptobot\Responses\CryptobotExchangeRates;
use App\Payments\Api\Cryptobot\Responses\CryptobotTransferResponse;
use App\Payments\DTO\PaymentProviderBalanceDTO;
use App\Payments\Enum\PaymentProvidersEnum;
use FKS\Api\ApiClient;
use Illuminate\Support\Facades\Log;

class CryptobotApiClient extends ApiClient
{
    public function transfer(CryptobotTransferRequest $request): CryptobotTransferResponse
    {
        Log::info('Withdraw CryptoBot postData', $request->toArray());

        $response = $this->post(
            'transfer',
            [
                'json' => $request->toArray(),
                'headers' => [
                    "Crypto-Pay-API-Token" => config('api-clients.cryptobot.app_token'),
                    "Content-Type" => "application/json",
                ]
            ]
        );

        Log::info('Withdraw response ', [$response]);

        $data = json_decode($response->getBody()->getContents(), true);

        return new CryptobotTransferResponse($data['result']['status']);
    }

    public function createInvoce(CryptobotCreateInvoiceRequest $request): CryptobotCreateInvoseResponse
    {
        Log::info('Withdraw CryptoBot postData', $request->toArray());

        $response = $this->post(
            'createInvoice',
            [
                'json' => $request->toArray(),
                'headers' => [
                    "Crypto-Pay-API-Token" => config('api-clients.cryptobot.app_token'),
                    "Content-Type" => "application/json",
                ]
            ]
        );

        $data = json_decode($response->getBody()->getContents(), true);

        return new CryptobotCreateInvoseResponse($data['result']['status'], $data['result']['invoice_id'], $data['result']['mini_app_invoice_url']);
    }

    public function balance(): array
    {
        $response = $this->get(
            'getBalance',
            [
                'headers' => [
                    "Crypto-Pay-API-Token" => config('api-clients.cryptobot.app_token'),
                    "Content-Type" => "application/json",
                ]
            ]
        );

        $data = collect(json_decode($response->getBody()->getContents(), true)['result']);
        $balances = [];
        foreach (CurrenciesEnum::cases() as $item) {
            if (!$data->where('currency_code', $item->value)->isEmpty()) {
                $balances[] = new PaymentProviderBalanceDTO(
                    PaymentProvidersEnum::CRYPTOBOT,
                    $data->where('currency_code', $item->value)->first()['available'],
                    $item
                );
            }
        }
        return $balances;
    }

    public function getExchangeRates(): CryptobotExchangeRates
    {
        $response = $this->get('getExchangeRates');

        $data = json_decode($response->getBody()->getContents(), true);

        return new CryptobotExchangeRates($data['result']);
    }
}
