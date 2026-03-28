<?php

declare(strict_types=1);

namespace App\Tests;

use App\Config\MpesaConfig;
use App\Mpesa\OAuthClient;
use App\Http\HttpResponse;
use PHPUnit\Framework\TestCase;

final class OAuthClientTest extends TestCase
{
    public function testRequestsOAuthTokenWithBasicAuth(): void
    {
        $http = new FakeHttpClient();
        $http->queueResponse(new HttpResponse(200, json_encode(['access_token' => 'tok-xyz', 'expires_in' => '3599'])));

        $config = new MpesaConfig(
            consumerKey: 'ck',
            consumerSecret: 'cs',
            shortCode: '174379',
            passkey: 'pk',
            callbackUrl: 'https://example.com/callback.php',
            baseUrl: 'https://sandbox.safaricom.co.ke',
            transactionType: 'CustomerPayBillOnline',
            transactionDesc: 'Test',
        );

        $client = new OAuthClient($config, $http);
        $token = $client->getAccessToken();

        self::assertSame('tok-xyz', $token);
        self::assertCount(1, $http->requests);
        self::assertSame('GET', $http->requests[0]['method']);
        self::assertStringContainsString('/oauth/v1/generate', $http->requests[0]['url']);
        self::assertSame('Basic ' . base64_encode('ck:cs'), $http->requests[0]['headers']['Authorization']);
    }
}
