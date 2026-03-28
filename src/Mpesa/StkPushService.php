<?php

declare(strict_types=1);

namespace App\Mpesa;

use App\Config\MpesaConfig;
use App\Http\HttpClientInterface;
use RuntimeException;

final class StkPushService
{
    public function __construct(
        private readonly MpesaConfig $config,
        private readonly OAuthClient $oauth,
        private readonly HttpClientInterface $http,
    ) {
    }

    /**
     * @param array{phoneNumber: string, amount: int, orderId: string} $validated
     */
    public function initiate(array $validated): string
    {
        $token = $this->oauth->getAccessToken();
        $timestamp = date('YmdHis');
        $password = base64_encode($this->config->shortCode . $this->config->passkey . $timestamp);

        $payload = [
            'BusinessShortCode' => $this->config->shortCode,
            'Password' => $password,
            'Timestamp' => $timestamp,
            'TransactionType' => $this->config->transactionType,
            'Amount' => $validated['amount'],
            'PartyA' => $validated['phoneNumber'],
            'PartyB' => $this->config->shortCode,
            'PhoneNumber' => $validated['phoneNumber'],
            'CallBackURL' => $this->config->callbackUrl,
            'AccountReference' => $validated['orderId'],
            'TransactionDesc' => $this->config->transactionDesc,
        ];

        $url = rtrim($this->config->baseUrl, '/') . '/mpesa/stkpush/v1/processrequest';
        $response = $this->http->request('POST', $url, [
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ], json_encode($payload, JSON_THROW_ON_ERROR));

        if ($response->statusCode < 200 || $response->statusCode >= 300) {
            throw new RuntimeException('STK push HTTP ' . $response->statusCode . ': ' . $response->body);
        }

        return $response->body;
    }
}
