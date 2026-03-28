<?php

declare(strict_types=1);

namespace App\Mpesa;

final class StkCallbackParser
{
    /**
     * @param array<string, mixed>|null $data
     * @return array{
     *   valid: bool,
     *   resultCode: int|null,
     *   resultDesc: string|null,
     *   merchantRequestId: string|null,
     *   checkoutRequestId: string|null,
     *   metadata: array<string, mixed>
     * }
     */
    public function parse(?array $data): array
    {
        $empty = [
            'valid' => false,
            'resultCode' => null,
            'resultDesc' => null,
            'merchantRequestId' => null,
            'checkoutRequestId' => null,
            'metadata' => [],
        ];

        if ($data === null || !isset($data['Body']['stkCallback'])) {
            return $empty;
        }

        $stk = $data['Body']['stkCallback'];
        if (!\is_array($stk)) {
            return $empty;
        }

        $resultCode = isset($stk['ResultCode']) ? (int) $stk['ResultCode'] : null;
        $resultDesc = isset($stk['ResultDesc']) ? (string) $stk['ResultDesc'] : null;
        $merchantRequestId = isset($stk['MerchantRequestID']) ? (string) $stk['MerchantRequestID'] : null;
        $checkoutRequestId = isset($stk['CheckoutRequestID']) ? (string) $stk['CheckoutRequestID'] : null;

        $metadata = [];
        if ($resultCode === 0 && isset($stk['CallbackMetadata']['Item']) && \is_array($stk['CallbackMetadata']['Item'])) {
            foreach ($stk['CallbackMetadata']['Item'] as $item) {
                if (\is_array($item) && isset($item['Name'])) {
                    $metadata[(string) $item['Name']] = $item['Value'] ?? null;
                }
            }
        }

        return [
            'valid' => true,
            'resultCode' => $resultCode,
            'resultDesc' => $resultDesc,
            'merchantRequestId' => $merchantRequestId,
            'checkoutRequestId' => $checkoutRequestId,
            'metadata' => $metadata,
        ];
    }
}
