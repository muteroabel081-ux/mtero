<?php

declare(strict_types=1);

namespace App;

use Dotenv\Dotenv;

final class Bootstrap
{
    public static function loadEnv(string $projectRoot): void
    {
        $path = $projectRoot . DIRECTORY_SEPARATOR . '.env';
        if (is_readable($path)) {
            Dotenv::createImmutable($projectRoot)->safeLoad();
        }
    }
}
