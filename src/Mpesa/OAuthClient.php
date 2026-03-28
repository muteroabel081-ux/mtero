<?php

declare(strict_types=1);

namespace App\Mpesa;

use App\Config\MpesaConfig;
use App\Http\HttpClientInterface;
use RuntimeException;

final class OAuthClient
{
    public function __construct(
        private readonly MpesaConfig $config,
        private readonly HttpClientInterface $http,
    ) {
    }

    public function getAccessToken(): string
    {
        $url = rtrim($this->config->baseUrl, '/') . '/oauth/v1/generate?grant_type=client_credentials';
        $credentials = base64_encode($this->config->consumerKey . ':' . $this->config->consumerSecret);
        $response = $this->http->request('GET', $url, [
            'Authorization' => 'Basic ' . $credentials,
            'Accept' => 'application/json',
        ], null);

        $data = json_decode($response->body, true);
        if (!\is_array($data) || !isset($data['access_token'])) {
            throw new RuntimeException('Daraja OAuth failed: ' . $response->body);
        }

        return (string) $data['access_token'];
    }
}
