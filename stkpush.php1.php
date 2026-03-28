<?php
include 'config.php1';
include 'access_token.php1';

$token = generateToken($consumerKey, $consumerSecret);

$timestamp = date('YmdHis');
$password = base64_encode($shortCode.$passkey.$timestamp);

$stkData = [
  'BusinessShortCode' => $shortCode,
  'Password' => $password,
  'Timestamp' => $timestamp,
  'TransactionType' => 'CustomerPayBillOnline',
  'Amount' => 100, // Example amount
  'PartyA' => '2547XXXXXXXX', // Customer phone number
  'PartyB' => $shortCode,
  'PhoneNumber' => '2547XXXXXXXX',
  'CallBackURL' => 'https://yourdomain.com/callback.php',
  'AccountReference' => 'Test123',
  'TransactionDesc' => 'Payment'
];

$ch = curl_init('https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
  'Content-Type: application/json',
  'Authorization: Bearer '.$token
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($stkData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

echo $response;
?>
