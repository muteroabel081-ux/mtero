<?php
// api/stkpush.php
header('Content-Type: application/json');

// Read incoming JSON from frontend
$input = json_decode(file_get_contents('php://input'), true);

// Validate required fields
$required = ['phoneNumber', 'amount', 'tillNumber', 'orderId'];
foreach ($required as $field) {
    if (empty($input[$field])) {
        http_response_code(400);
        echo json_encode(['error' => "Missing field: $field"]);
        exit;
    }
}

$phone = $input['phoneNumber'];      // e.g., "254712345678"
$amount = $input['amount'];          // integer, e.g., 5000
$shortcode = $input['tillNumber'];   // your business shortcode
$orderId = $input['orderId'];

// Your credentials from Safaricom developer portal
$consumerKey = 'YOUR_CONSUMER_KEY';
$consumerSecret = 'YOUR_CONSUMER_SECRET';
$passkey = 'YOUR_PASSKEY';           // obtained from the app

// Generate access token
$credentials = base64_encode($consumerKey . ':' . $consumerSecret);
$url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Basic ' . $credentials]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

$tokenData = json_decode($response, true);
if (!isset($tokenData['access_token'])) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to get access token']);
    exit;
}
$accessToken = $tokenData['access_token'];

// Prepare STK push request
$timestamp = date('YmdHis');
$password = base64_encode($shortcode . $passkey . $timestamp);

$payload = [
    'BusinessShortCode' => $shortcode,
    'Password' => $password,
    'Timestamp' => $timestamp,
    'TransactionType' => 'CustomerPayBillOnline', // For Till use 'CustomerBuyGoodsOnline'
    'Amount' => $amount,
    'PartyA' => $phone,
    'PartyB' => $shortcode,
    'PhoneNumber' => $phone,
    'CallBackURL' => 'https://yourdomain.com/callback.php',
    'AccountReference' => $orderId,
    'TransactionDesc' => 'Gigi Stores Order'
];

// For Till number (Buy Goods), use 'CustomerBuyGoodsOnline' and set PartyB as the Till number
// If your shortcode is a PayBill, keep 'CustomerPayBillOnline' and set PartyB as the PayBill number.
// Adjust accordingly.

$url = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $accessToken,
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
curl_close($ch);

// Return the response from Safaricom to the frontend
echo $result;
?>