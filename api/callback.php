<?php
// api/callback.php
header('Content-Type: application/json');

// Read callback data
$callbackData = json_decode(file_get_contents('php://input'), true);

// Log the raw callback for debugging
file_put_contents('mpesa_callback.log', date('Y-m-d H:i:s') . "\n" . json_encode($callbackData) . "\n\n", FILE_APPEND);

if (isset($callbackData['Body']['stkCallback'])) {
    $stkCallback = $callbackData['Body']['stkCallback'];
    $resultCode = $stkCallback['ResultCode'];
    $resultDesc = $stkCallback['ResultDesc'];
    $merchantRequestId = $stkCallback['MerchantRequestID'];
    $checkoutRequestId = $stkCallback['CheckoutRequestID'];

    $metadata = [];
    if ($resultCode == 0 && isset($stkCallback['CallbackMetadata']['Item'])) {
        foreach ($stkCallback['CallbackMetadata']['Item'] as $item) {
            $metadata[$item['Name']] = $item['Value'];
        }
    }

    // Here you update your database:
    // - Find order by MerchantRequestID or CheckoutRequestID (you should have stored these earlier)
    // - Set payment_status = 'completed' if $resultCode == 0, else 'failed'
    // - Save $metadata['MpesaReceiptNumber'] if available
    // - Optionally send email/SMS to customer

    // Always respond with success to Safaricom
    echo json_encode(['ResultCode' => 0, 'ResultDesc' => 'Success']);
} else {
    echo json_encode(['ResultCode' => 1, 'ResultDesc' => 'Invalid callback structure']);
}
?>