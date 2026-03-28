<?php

declare(strict_types=1);

namespace App\Mpesa;

final class StkPushValidator
{
    /**
     * @param array<string, mixed>|null $input
     * @return array{phoneNumber: string, amount: int, orderId: string}
     */
    public function validate(?array $input): array
    {
        if ($input === null) {
            throw new StkPushValidationException('Invalid JSON body');
        }

        $required = ['phoneNumber', 'amount', 'orderId'];
        foreach ($required as $field) {
            if (!isset($input[$field]) || $input[$field] === '' || $input[$field] === null) {
                throw new StkPushValidationException("Missing field: {$field}");
            }
        }

        $phone = $this->normalizePhone((string) $input['phoneNumber']);
        $amount = filter_var($input['amount'], FILTER_VALIDATE_INT);
        if ($amount === false || $amount < 1) {
            throw new StkPushValidationException('Invalid amount');
        }

        $orderId = trim((string) $input['orderId']);
        if ($orderId === '') {
            throw new StkPushValidationException('Invalid orderId');
        }

        return [
            'phoneNumber' => $phone,
            'amount' => $amount,
            'orderId' => $orderId,
        ];
    }

    private function normalizePhone(string $raw): string
    {
        $digits = preg_replace('/\D+/', '', $raw) ?? '';
        if ($digits === '') {
            throw new StkPushValidationException('Invalid phoneNumber');
        }

        if (str_starts_with($digits, '0')) {
            $digits = '254' . substr($digits, 1);
        } elseif (str_starts_with($digits, '7') && \strlen($digits) === 9) {
            $digits = '254' . $digits;
        } elseif (!str_starts_with($digits, '254')) {
            throw new StkPushValidationException('Phone must be in 254XXXXXXXXX or local format');
        }

        if (\strlen($digits) < 12) {
            throw new StkPushValidationException('Invalid phoneNumber length');
        }

        return $digits;
    }
}
