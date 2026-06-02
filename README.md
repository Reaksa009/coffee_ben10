# Coffee Ben10 POS

Laravel POS project for Coffee Ben10 with product, order, payment, promo, report, profile, and KHQR payment flows.

## Local Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

## Vercel Deployment

This repo includes `vercel.json`, `.vercelignore`, and `api/index.php` so Vercel can run the Laravel app through `vercel-php`.

Set these environment variables in the Vercel project before deploying:

```text
APP_KEY=base64:...
APP_URL=https://coffee-ben10.vercel.app
DB_CONNECTION=mysql
DB_HOST=...
DB_PORT=3306
DB_DATABASE=...
DB_USERNAME=...
DB_PASSWORD=...
KHQR_BAKONG_ACCOUNT_ID=...
KHQR_ACCOUNT_NAME=...
KHQR_MERCHANT_CITY=PHNOM PENH
KHQR_CURRENCY=USD
```

For Vercel, use an external MySQL or PostgreSQL database. Do not use the local `.env` file or local SQLite database for production deployment.
