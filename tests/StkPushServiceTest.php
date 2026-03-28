<?php

declare(strict_types=1);

namespace App\Tests;

use App\Config\MpesaConfig;
use App\Http\HttpResponse;
use App\Mpesa\OAuthClient;
use App\Mpesa\StkPushService;
use PHPUnit\Framework\TestCase;

final class StkPushServiceTest extends TestCase
{
    public function testInitiateSendsPostToProcessrequestWithBearerAndPayload(): void
    {
        $http = new FakeHttpClient();
        $http->queueResponse(new HttpResponse(200, json_encode(['access_token' => 'token-1'])));
        $http->queueResponse(new HttpResponse(200, '{"ResponseCode":"0"}'));

        $config = new MpesaConfig(
            consumerKey: 'ck',
            consumerSecret: 'cs',
            shortCode: '174379',
            passkey: 'test-passkey',
            callbackUrl: 'https://example.com/callback.php',
            baseUrl: 'https://sandbox.safaricom.co.ke',
            transactionType: 'CustomerPayBillOnline',
            transactionDesc: 'Gigi Stores Order',
        );

        $oauth = new OAuthClient($config, $http);
        $service = new StkPushService($config, $oauth, $http);

        $body = $service->initiate([
            'phoneNumber' => '254712345678',
            'amount' => 100,
            'orderId' => 'ORD-99',
        ]);

        self::assertSame('{"ResponseCode":"0"}', $body);
        self::assertCount(2, $http->requests);
        self::assertSame('POST', $http->requests[1]['method']);
        self::assertStringContainsString('/mpesa/stkpush/v1/processrequest', $http->requests[1]['url']);
        self::assertSame('Bearer token-1', $http->requests[1]['headers']['Authorization']);

        $payload = json_decode((string) $http->requests[1]['body'], true, 512, JSON_THROW_ON_ERROR);
        self::assertSame('174379', $payload['BusinessShortCode']);
        self::assertSame('CustomerPayBillOnline', $payload['TransactionType']);
        self::assertSame(100, $payload['Amount']);
        self::assertSame('254712345678', $payload['PhoneNumber']);
        self::assertSame('https://example.com/callback.php', $payload['CallBackURL']);
        self::assertSame('ORD-99', $payload['AccountReference']);
    }
}
