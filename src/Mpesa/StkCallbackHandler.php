<?php

declare(strict_types=1);

namespace App\Mpesa;

final class StkCallbackHandler
{
    public function __construct(
        private readonly StkCallbackParser $parser,
        private readonly string $logFile,
    ) {
    }

    /**
     * @return array{ResultCode: int, ResultDesc: string}
     */
    public function handle(string $rawBody): array
    {
        $data = json_decode($rawBody, true);
        $parsed = $this->parser->parse(\is_array($data) ? $data : null);

        $line = date('Y-m-d H:i:s') . "\n" . $rawBody . "\n\n";
        @file_put_contents($this->logFile, $line, FILE_APPEND);

        if (!$parsed['valid']) {
            return ['ResultCode' => 1, 'ResultDesc' => 'Invalid callback data'];
        }

        return ['ResultCode' => 0, 'ResultDesc' => 'Success'];
    }
}
