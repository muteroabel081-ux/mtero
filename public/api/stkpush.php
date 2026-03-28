<?php

declare(strict_types=1);

$projectRoot = dirname(__DIR__, 2);
require $projectRoot . '/vendor/autoload.php';

use App\Bootstrap;
use App\Config\MpesaConfig;
use App\Http\CurlHttpClient;
use App\Mpesa\OAuthClient;
use App\Mpesa\StkPushService;
use App\Mpesa\StkPushValidationException;
use App\Mpesa\StkPushValidator;

Bootstrap::loadEnv($projectRoot);

header('Content-Type: application/json; charset=utf-8');

try {
    $config = MpesaConfig::fromEnvironment();
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server configuration error'], JSON_THROW_ON_ERROR);
    exit;
}

$raw = file_get_contents('php://input') ?: '';
$input = json_decode($raw, true);

$http = new CurlHttpClient();
$oauth = new OAuthClient($config, $http);
$stk = new StkPushService($config, $oauth, $http);
$validator = new StkPushValidator();

try {
    $validated = $validator->validate(\is_array($input) ? $input : null);
    echo $stk->initiate($validated);
} catch (StkPushValidationException $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()], JSON_THROW_ON_ERROR);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => 'STK request failed'], JSON_THROW_ON_ERROR);
}
