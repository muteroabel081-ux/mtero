<?php
$data = file_get_contents('php://input');
file_put_contents('logs/stk_callback.json', $data.PHP_EOL, FILE_APPEND);

$response = json_decode($data, true);

// Example: check if transaction was successful
if (isset($response['Body']['stkCallback']['ResultCode']) && $response['Body']['stkCallback']['ResultCode'] == 0) {
    // Success
    $amount = $response['Body']['stkCallback']['CallbackMetadata']['Item'][0]['Value'];
    $mpesaReceipt = $response['Body']['stkCallback']['CallbackMetadata']['Item'][1]['Value'];
    // Save to database or process further
}
?>
