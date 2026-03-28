<?php

declare(strict_types=1);

namespace App\Tests;

use App\Mpesa\StkCallbackParser;
use PHPUnit\Framework\TestCase;

final class StkCallbackParserTest extends TestCase
{
    public function testParsesSuccessfulStkCallbackWithMetadata(): void
    {
        $data = [
            'Body' => [
                'stkCallback' => [
                    'MerchantRequestID' => 'mr1',
                    'CheckoutRequestID' => 'co1',
                    'ResultCode' => 0,
                    'ResultDesc' => 'The service request is processed successfully.',
                    'CallbackMetadata' => [
                        'Item' => [
                            ['Name' => 'Amount', 'Value' => 100],
                            ['Name' => 'MpesaReceiptNumber', 'Value' => 'ABC123'],
                        ],
                    ],
                ],
            ],
        ];

        $parser = new StkCallbackParser();
        $parsed = $parser->parse($data);

        self::assertTrue($parsed['valid']);
        self::assertSame(0, $parsed['resultCode']);
        self::assertSame('mr1', $parsed['merchantRequestId']);
        self::assertSame('co1', $parsed['checkoutRequestId']);
        self::assertSame(100, $parsed['metadata']['Amount']);
        self::assertSame('ABC123', $parsed['metadata']['MpesaReceiptNumber']);
    }

    public function testReturnsInvalidWhenBodyMissing(): void
    {
        $parser = new StkCallbackParser();
        $parsed = $parser->parse(['foo' => 'bar']);

        self::assertFalse($parsed['valid']);
    }

    public function testReturnsInvalidWhenJsonWasNull(): void
    {
        $parser = new StkCallbackParser();
        $parsed = $parser->parse(null);

        self::assertFalse($parsed['valid']);
    }
}
