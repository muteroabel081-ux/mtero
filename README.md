# Gigi Stores (mtero)

Static storefront with **M-Pesa Daraja** STK push (Lipa na M-Pesa Online) via PHP. Intended for **sandbox** first; switch `MPESA_BASE_URL` and credentials when moving to production.

## Requirements

- **Docker** and **Docker Compose** (recommended on all platforms)
- Optional: **Composer** and **PHP 8.2+** on the host if you run tests or `composer install` without Docker

## Quick start (Docker)

1. Copy environment template and fill in Daraja sandbox values (same ones you use in Postman):

   ```bash
   cp .env.example .env
   ```

2. Build and run:

   ```bash
   docker compose build
   docker compose up
   ```

3. Open **http://localhost:8080** — document root is `public/`.

`docker-compose.yml` loads `.env` when present (`required: false`), so you can run `docker compose run --rm web composer test` before creating `.env`.

4. Set `MPESA_CALLBACK_URL` to a **public HTTPS** URL that reaches your machine (e.g. ngrok or Cloudflare Tunnel) pointing at `/callback.php`, otherwise Safaricom cannot POST STK results in real sandbox tests.

## Environment variables

See [`.env.example`](.env.example). Required: `MPESA_CONSUMER_KEY`, `MPESA_CONSUMER_SECRET`, `MPESA_SHORTCODE`, `MPESA_PASSKEY`, `MPESA_CALLBACK_URL`.

Optional: `MPESA_BASE_URL` (defaults to Daraja sandbox), `MPESA_TRANSACTION_TYPE`, `MPESA_TRANSACTION_DESC`.

## API endpoints (under `public/`)

- **`POST /api/stkpush.php`** — JSON: `phoneNumber`, `amount`, `orderId` — initiates STK push
- **`POST /callback.php`** — Daraja STK callback (JSON body from Safaricom)
- **`POST /api/stkpush-query.php`** — JSON: `checkoutRequestId` — optional STK status query

Logs: `storage/logs/mpesa_callback.log` (callback payloads).

## Postman parity

Daraja URLs and payloads match the **Safaricom APIs** Postman flow (OAuth, then Bearer on STK). See [`docs/postman/README.md`](docs/postman/README.md) for variable mapping.

## Tests

Tests use **PHPUnit** and **mock HTTP** only (no network, no API keys):

```bash
docker compose run --rm web composer test
```

Or with PHP on the host: `composer install && composer test`.

## Local callback without a tunnel

Send a sample STK callback payload to your running container:

```powershell
.\scripts\send-mock-callback.ps1
```

Adjust `-BaseUrl` if you use another port.

## Project layout

- `public/` — web root (`index.html`, `callback.php`, `api/*.php`)
- `src/` — `Config`, `Http`, `Mpesa` services
- `tests/` — PHPUnit tests
- `scripts/` — mock STK callback JSON and PowerShell helper
- `docker/` — Apache vhost and entrypoint

## Testing with Daraja sandbox (local)

1. **App and keys** — In [Safaricom Developer Portal](https://developer.safaricom.co.ke/), open **My Apps**, select your sandbox app, and copy **Consumer Key**, **Consumer Secret**, **Shortcode**, and **Passkey** (Lipa na M-Pesa / STK). Use the **sandbox** base URL (`https://sandbox.safaricom.co.ke`); it is already the default in `.env`.

2. **`.env`** — A [`.env`](.env) file exists at the project root (same keys as [`.env.example`](.env.example)). Fill in the four secrets and `MPESA_CALLBACK_URL` (next step). Restart the stack after changes: `docker compose up --build` or restart the `web` container.

3. **Public callback URL** — Daraja must POST STK results to an **HTTPS** URL. On your machine, run a tunnel and point it at the app (port **8080**):
   - **ngrok** (example): `ngrok http 8080` → set  
     `MPESA_CALLBACK_URL=https://<your-subdomain>.ngrok-free.app/callback.php`  
   - **Cloudflare Tunnel** or similar: same idea — path must be `/callback.php` as served from `public/`.

4. **Run the app**

   ```bash
   docker compose build
   docker compose up
   ```

   Open **http://localhost:8080**, add items to the cart, checkout, and use a **sandbox test MSISDN** (Safaricom documents test numbers for sandbox; often `254708374149` works for simulation — confirm in current Daraja docs).

5. **Verify** — You should get an STK prompt on the test line. Callbacks appear in `storage/logs/mpesa_callback.log`. If OAuth or STK fails, check container logs: `docker compose logs -f web`.

6. **Postman** — You can compare behavior with your **Safaricom APIs** collection: same OAuth URL and STK `processrequest` body as [`docs/postman/README.md`](docs/postman/README.md).

7. **Without a tunnel** — You can still POST a fake callback for development: [`scripts/send-mock-callback.ps1`](scripts/send-mock-callback.ps1). That does not prove end-to-end Daraja delivery.

## Security

If credentials were ever committed in plain text, **rotate** consumer key, secret, and passkey in the [Safaricom Developer Portal](https://developer.safaricom.co.ke/).
