# Daraja sandbox and Postman

This app calls the same **Safaricom Daraja** sandbox endpoints as the official **Safaricom APIs** Postman collection:

1. **OAuth** — `GET /oauth/v1/generate?grant_type=client_credentials` with HTTP Basic (consumer key + secret).
2. **STK push** — `POST /mpesa/stkpush/v1/processrequest` with `Authorization: Bearer <access_token>` and the Lipa na M-Pesa Online JSON body (including generated `Password` and `Timestamp`).
3. **STK query (optional)** — `POST /mpesa/stkpushquery/v1/query` with `CheckoutRequestID` — exposed as `POST /api/stkpush-query.php` in this project.

## Mapping Postman variables to `.env`

- **Basic auth username / password** → `MPESA_CONSUMER_KEY`, `MPESA_CONSUMER_SECRET`
- **Business shortcode** → `MPESA_SHORTCODE`
- **Passkey** (for STK `Password` generation) → `MPESA_PASSKEY`
- **`CallBackURL` in STK body** → `MPESA_CALLBACK_URL`
- **Sandbox host** → `MPESA_BASE_URL` (default `https://sandbox.safaricom.co.ke`)

Do **not** commit Postman exports that still contain embedded Basic auth or secrets. Use Postman **Environments** for keys, or store only placeholders in a sanitized collection.

## Collection hygiene

If you copy `Safaricom APIs.postman_collection.json` into this folder for reference, remove real consumer keys, secrets, and passkeys first.
