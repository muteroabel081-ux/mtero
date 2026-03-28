<?php

declare(strict_types=1);

namespace App\Logging;

final class Logger
{
    public function __construct(
        private readonly string $logFile,
    ) {
    }

    public function log(string $message, array $context = []): void
    {
        $line = date('c') . ' ' . $message;
        if ($context !== []) {
            $line .= ' ' . json_encode($context, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }
        $line .= "\n";
        @file_put_contents($this->logFile, $line, FILE_APPEND);
    }
}
