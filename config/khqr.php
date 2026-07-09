<?php

return [
    'provider' => env('KHQR_PROVIDER', env('KHQR_API_BASE_URL') ? 'khqr_link' : 'local'),
    'bakong_account_id' => env('KHQR_BAKONG_ACCOUNT_ID', env('MERCHANT_ID', env('KHQR_QR_EMAIL'))),
    'account_name' => env('KHQR_ACCOUNT_NAME', env('ACCOUNT_NAME', 'POS System')),
    'merchant_city' => env('KHQR_MERCHANT_CITY', 'PHNOM PENH'),
    'currency' => env('KHQR_CURRENCY', 'USD'),
    'api_token' => env('KHQR_API_TOKEN'),
    'api_base_url' => env('KHQR_API_BASE_URL', 'https://api.khqr.link'),
    'api_key' => env('KHQR_LINK_API_KEY', env('KHQR_API_KEY', env('KHQR_API_TOKEN'))),
    'dynamic_qr_expires_in' => env('KHQR_DYNAMIC_QR_EXPIRES_IN', 180),
    'merchant_id' => env('KHQR_MERCHANT_ID'),
    'acquiring_bank' => env('KHQR_ACQUIRING_BANK'),
];
