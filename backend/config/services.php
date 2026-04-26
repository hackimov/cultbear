<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'dadata' => [
        'base_url' => env('DADATA_BASE_URL', 'https://suggestions.dadata.ru'),
        'token' => env('DADATA_TOKEN'),
        'secret' => env('DADATA_SECRET'),
    ],

    'tochka' => [
        /*
         * База UAPI Точки (без завершающего слэша). Эндпоинт эквайринга: /acquiring/v1.0/payments
         * @see https://developers.tochka.com/docs/tochka-api/opisanie-metodov/platyozhnye-ssylki
         */
        'base_url' => env('TOCHKA_API_URL', 'https://enter.tochka.com/uapi'),
        'api_key' => env('TOCHKA_API_KEY'),
        'customer_code' => env('TOCHKA_CUSTOMER_CODE'),
        'merchant_id' => env('TOCHKA_MERCHANT_ID'),
        'payment_modes' => array_values(array_filter(array_map('trim', explode(',', (string) env('TOCHKA_PAYMENT_MODES', 'card,sbp'))))),
        'success_redirect_url' => env('TOCHKA_SUCCESS_REDIRECT_URL'),
        'fail_redirect_url' => env('TOCHKA_FAIL_REDIRECT_URL'),
        /*
         * Вебхук — тело text/plain: JWT RS256. Проверка публичным ключом OpenAPI Точки.
         * Можно задать PEM (TOCHKA_WEBHOOK_PUBLIC_KEY_PEM) или JWK JSON (TOCHKA_WEBHOOK_JWK_PUBLIC).
         */
        'webhook_public_key_pem' => env('TOCHKA_WEBHOOK_PUBLIC_KEY_PEM'),
        'webhook_jwk_public' => env('TOCHKA_WEBHOOK_JWK_PUBLIC'),
        'webhook_jwk_default' => '{"kty":"RSA","e":"AQAB","n":"rwm77av7GIttq-JF1itEgLCGEZW_zz16RlUQVYlLbJtyRSu61fCec_rroP6PxjXU2uLzUOaGaLgAPeUZAJrGuVp9nryKgbZceHckdHDYgJd9TsdJ1MYUsXaOb9joN9vmsCscBx1lwSlFQyNQsHUsrjuDk-opf6RCuazRQ9gkoDCX70HV8WBMFoVm-YWQKJHZEaIQxg_DU4gMFyKRkDGKsYKA0POL-UgWA1qkg6nHY5BOMKaqxbc5ky87muWB5nNk4mfmsckyFv9j1gBiXLKekA_y4UwG2o1pbOLpJS3bP_c95rm4M9ZBmGXqfOQhbjz8z-s9C11i-jmOQ2ByohS-ST3E5sqBzIsxxrxyQDTw--bZNhzpbciyYW4GfkkqyeYoOPd_84jPTBDKQXssvj8ZOj2XboS77tvEO1n1WlwUzh8HPCJod5_fEgSXuozpJtOggXBv0C2ps7yXlDZf-7Jar0UYc_NJEHJF-xShlqd6Q3sVL02PhSCM-ibn9DN9BKmD"}',
    ],

    'telegram' => [
        'bot_token' => env('TELEGRAM_BOT_TOKEN'),
        'chat_id' => env('TELEGRAM_CHAT_ID'),
    ],

];
