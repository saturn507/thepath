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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'yandex' => [
        'client_id' => env('OAUTH_YANDEX_CLIENT_ID', 'c88fb192c63d439d98fa3266dd5284aa'),
        'client_secret' => env('OAUTH_YANDEX_CLIENT_SECRET', '191141525af74ee1a9388ce3fa161d4b'),
        'redirect' => 'http://thepath.local/auth/yandex/callback',
    ],

    'vkontakte' => [
        'client_id' => env('VKONTAKTE_CLIENT_ID', '52763887'),
        'client_secret' => env('VKONTAKTE_CLIENT_SECRET', 'GNAJt5oW8CZy5dxQmMw9'),
        'redirect' => env('VKONTAKTE_REDIRECT_URI', 'https://thepath.fun/backend/auth/vkontakte/callback')
    ],

    'telegram' => [
        'bot' => env('TELEGRAM_BOT_NAME', 'the_path_bot'),  // The bot's username
        'client_id' => null,
        'client_secret' => env('TELEGRAM_TOKEN', ''),
        'redirect' => env('TELEGRAM_REDIRECT_URI', 'https://thepath.fun/backend/auth/telegram/callback'),
    ],

];
