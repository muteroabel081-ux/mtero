<?php
// api/stkpush.php
header('Content-Type: application/json');
require_once 'config.php';      // contains $consumerKey, $consumerSecret, $shortCode, $passkey
require_once 'access_token.php'; // contains generateToken() function

// Read incoming JSON from frontend
$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON payload']);
    exit;
}

// Validate required fields
$required = ['phoneNumber', 'amount', 'orderId'];
foreach ($required as $field) {
    if (empty($input[$field])) {
        http_response_code(400);
        echo json_encode(['error' => "Missing field: $field"]);
        exit;
    }
}

$phone = $input['phoneNumber'];      // expected in format 2547XXXXXXXX
$amount = (int)$input['amount'];
$orderId = $input['orderId'];

// Additional validation
if ($amount <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Amount must be greater than 0']);
    exit;
}
// Ensure phone number starts with 254 and is 12 digits
$phone = preg_replace('/^0/', '254', $phone); // convert 0712... to 254712...
if (!preg_match('/^254[0-9]{9}$/', $phone)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid phone number format. Use 2547XXXXXXXX']);
    exit;
}

// Get access token
$token = generateToken($consumerKey, $consumerSecret);
if (!$token) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to obtain access token']);
    exit;
}

// Prepare STK push payload
$timestamp = date('YmdHis');
$password = base64_encode($shortCode . $passkey . $timestamp);

$stkData = [
    'BusinessShortCode' => $shortCode,
    'Password' => $password,
    'Timestamp' => $timestamp,
    'TransactionType' => 'CustomerPayBillOnline', // For PayBill; use 'CustomerBuyGoodsOnline' for Till
    'Amount' => $amount,
    'PartyA' => $phone,
    'PartyB' => $shortCode,
    'PhoneNumber' => $phone,
    'CallBackURL' => 'https://gigistoreske.vercel.app/api/callback.php', // ✅ MUST be a PHP script on your server
    'AccountReference' => $orderId,
    'TransactionDesc' => 'Gigi Stores Order'
];

// Send request to Safaricom
$ch = curl_init('https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $token
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($stkData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($response === false) {
    http_response_code(500);
    echo json_encode(['error' => 'cURL error: ' . curl_error($ch)]);
    exit;
}

// Log the response for debugging (optional)
file_put_contents('stkpush_log.txt', date('Y-m-d H:i:s') . " - Order $orderId: $response\n", FILE_APPEND);

// Return the response to the frontend
http_response_code($httpCode);
echo $response;
?>