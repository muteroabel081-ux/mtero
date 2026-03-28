<?php

declare(strict_types=1);

namespace App\Config;

use RuntimeException;

final class MpesaConfig
{
    public function __construct(
        public readonly string $consumerKey,
        public readonly string $consumerSecret,
        public readonly string $shortCode,
        public readonly string $passkey,
        public readonly string $callbackUrl,
        public readonly string $baseUrl,
        public readonly string $transactionType,
        public readonly string $transactionDesc,
    ) {
    }

    public static function fromEnvironment(): self
    {
        $get = static fn (string $key): string => self::requireString($key);

        return new self(
            consumerKey: $get('MPESA_CONSUMER_KEY'),
            consumerSecret: $get('MPESA_CONSUMER_SECRET'),
            shortCode: $get('MPESA_SHORTCODE'),
            passkey: $get('MPESA_PASSKEY'),
            callbackUrl: $get('MPESA_CALLBACK_URL'),
            baseUrl: self::optional('MPESA_BASE_URL', 'https://sandbox.safaricom.co.ke'),
            transactionType: self::optional('MPESA_TRANSACTION_TYPE', 'CustomerPayBillOnline'),
            transactionDesc: self::optional('MPESA_TRANSACTION_DESC', 'Gigi Stores Order'),
        );
    }

    private static function requireString(string $key): string
    {
        $v = getenv($key);
        if ($v === false || trim((string) $v) === '') {
            throw new RuntimeException("Missing or empty environment variable: {$key}");
        }

        return trim((string) $v);
    }

    private static function optional(string $key, string $default): string
    {
        $v = getenv($key);
        if ($v === false || trim((string) $v) === '') {
            return $default;
        }

        return trim((string) $v);
    }
}
