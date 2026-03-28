<?php

declare(strict_types=1);

namespace App\Tests;

use App\Mpesa\StkPushValidationException;
use App\Mpesa\StkPushValidator;
use PHPUnit\Framework\TestCase;

final class StkPushValidatorTest extends TestCase
{
    public function testValidatesAndNormalizes254Phone(): void
    {
        $v = new StkPushValidator();
        $out = $v->validate([
            'phoneNumber' => '0712345678',
            'amount' => 500,
            'orderId' => 'ORD-1',
        ]);

        self::assertSame('254712345678', $out['phoneNumber']);
        self::assertSame(500, $out['amount']);
        self::assertSame('ORD-1', $out['orderId']);
    }

    public function testThrowsOnMissingField(): void
    {
        $this->expectException(StkPushValidationException::class);
        $this->expectExceptionMessage('Missing field');

        (new StkPushValidator())->validate(['phoneNumber' => '254712345678', 'amount' => 1]);
    }

    public function testThrowsOnInvalidAmount(): void
    {
        $this->expectException(StkPushValidationException::class);

        (new StkPushValidator())->validate([
            'phoneNumber' => '254712345678',
            'amount' => 0,
            'orderId' => 'x',
        ]);
    }
}
