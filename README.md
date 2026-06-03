# Coffee Ben10 POS

Laravel POS project for Coffee Ben10 with product, order, payment, promo, customer loyalty, report, profile, and KHQR payment flows.

## Local Setup

Create a MySQL database first. The default local database name is `POS_Project`.

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

For XAMPP, make sure Apache/MySQL are running and your `.env` contains the local MySQL settings:

```text
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=POS_Project
DB_USERNAME=root
DB_PASSWORD=
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
KHQR_PROVIDER=khqr_link
KHQR_API_BASE_URL=https://api.khqr.link
KHQR_LINK_API_KEY=... # optional if KHQR Link requires merchant API keys
```
