<?php

declare(strict_types=1);

$projectRoot = dirname(__DIR__);
require $projectRoot . '/vendor/autoload.php';

use App\Bootstrap;
use App\Mpesa\StkCallbackHandler;
use App\Mpesa\StkCallbackParser;

Bootstrap::loadEnv($projectRoot);

header('Content-Type: application/json; charset=utf-8');

$logFile = $projectRoot . '/storage/logs/mpesa_callback.log';
$handler = new StkCallbackHandler(new StkCallbackParser(), $logFile);

$raw = file_get_contents('php://input') ?: '';
$response = $handler->handle($raw);

echo json_encode($response, JSON_THROW_ON_ERROR);
