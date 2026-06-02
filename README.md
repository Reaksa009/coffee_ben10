# Coffee Ben10 POS

Laravel POS project for Coffee Ben10 with product, order, payment, promo, customer loyalty, report, profile, and KHQR payment flows.

## Local Setup

Install the PHP MongoDB extension for your local PHP/XAMPP version first.

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan serve
```

Set `DB_URI` in `.env` to your MongoDB Atlas connection string. MongoDB does not use the SQL migrations in this project.

## Vercel Deployment

This repo includes `vercel.json`, `.vercelignore`, and `api/index.php` so Vercel can run the Laravel app through `vercel-php`.

Set these environment variables in the Vercel project before deploying:

```text
APP_KEY=base64:...
APP_URL=https://coffee-ben10.vercel.app
DB_CONNECTION=mongodb
DB_URI=mongodb+srv://...
DB_DATABASE=coffee_ben10
KHQR_BAKONG_ACCOUNT_ID=...
KHQR_ACCOUNT_NAME=...
KHQR_MERCHANT_CITY=PHNOM PENH
KHQR_CURRENCY=USD
KHQR_PROVIDER=khqr_link
KHQR_API_BASE_URL=https://api.khqr.link
KHQR_LINK_API_KEY=... # optional if KHQR Link requires merchant API keys
```

If you connect MongoDB Atlas through Vercel Marketplace, populate `MONGODB_URI`; the app falls back to that value when `DB_URI` is not set.
