<?php

declare(strict_types=1);

namespace App\Mpesa;

use App\Config\MpesaConfig;
use App\Http\HttpClientInterface;
use RuntimeException;

final class StkQueryService
{
    public function __construct(
        private readonly MpesaConfig $config,
        private readonly OAuthClient $oauth,
        private readonly HttpClientInterface $http,
    ) {
    }

    public function query(string $checkoutRequestId): string
    {
        $token = $this->oauth->getAccessToken();
        $timestamp = date('YmdHis');
        $password = base64_encode($this->config->shortCode . $this->config->passkey . $timestamp);

        $payload = [
            'BusinessShortCode' => $this->config->shortCode,
            'Password' => $password,
            'Timestamp' => $timestamp,
            'CheckoutRequestID' => $checkoutRequestId,
        ];

        $url = rtrim($this->config->baseUrl, '/') . '/mpesa/stkpushquery/v1/query';
        $response = $this->http->request('POST', $url, [
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ], json_encode($payload, JSON_THROW_ON_ERROR));

        if ($response->statusCode < 200 || $response->statusCode >= 300) {
            throw new RuntimeException('STK query HTTP ' . $response->statusCode . ': ' . $response->body);
        }

        return $response->body;
    }
}
