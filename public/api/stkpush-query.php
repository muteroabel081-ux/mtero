<?php

declare(strict_types=1);

$projectRoot = dirname(__DIR__, 2);
require $projectRoot . '/vendor/autoload.php';

use App\Bootstrap;
use App\Config\MpesaConfig;
use App\Http\CurlHttpClient;
use App\Mpesa\OAuthClient;
use App\Mpesa\StkQueryService;

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
$checkoutRequestId = \is_array($input) && isset($input['checkoutRequestId'])
    ? trim((string) $input['checkoutRequestId'])
    : '';

if ($checkoutRequestId === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Missing checkoutRequestId'], JSON_THROW_ON_ERROR);
    exit;
}

$http = new CurlHttpClient();
$oauth = new OAuthClient($config, $http);
$query = new StkQueryService($config, $oauth, $http);

try {
    echo $query->query($checkoutRequestId);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => 'STK query failed', 'detail' => $e->getMessage()], JSON_THROW_ON_ERROR);
}
