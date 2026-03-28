<?php
// callback.php
header('Content-Type: application/json');

// Read the callback data
$callbackData = json_decode(file_get_contents('php://input'), true);

// Log the callback for debugging (optional)
file_put_contents('mpesa_callback.log', date('Y-m-d H:i:s') . "\n" . json_encode($callbackData) . "\n\n", FILE_APPEND);

// Extract relevant info
if (isset($callbackData['Body']['stkCallback'])) {
    $stkCallback = $callbackData['Body']['stkCallback'];
    $resultCode = $stkCallback['ResultCode'];
    $resultDesc = $stkCallback['ResultDesc'];
    $merchantRequestId = $stkCallback['MerchantRequestID'];
    $checkoutRequestId = $stkCallback['CheckoutRequestID'];

    // You may also get metadata like Amount, MpesaReceiptNumber if payment succeeded
    $metadata = [];
    if ($resultCode == 0 && isset($stkCallback['CallbackMetadata']['Item'])) {
        foreach ($stkCallback['CallbackMetadata']['Item'] as $item) {
            $metadata[$item['Name']] = $item['Value'];
        }
    }

    // Here you would update your database with the payment status.
    // For example:
    // - Find the order by MerchantRequestID or CheckoutRequestID
    // - Set payment status to 'completed' if resultCode == 0
    // - Optionally send an email or SMS to the customer

    // Send a response back to Safaricom (they expect a simple acknowledgement)
    echo json_encode(['ResultCode' => 0, 'ResultDesc' => 'Success']);
} else {
    echo json_encode(['ResultCode' => 1, 'ResultDesc' => 'Invalid callback data']);
}
?>