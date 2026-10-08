<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'openrouter' => [
        'api_key' => env('OPENROUTER_API_KEY'),
        'model' => env('OPENROUTER_MODEL', 'google/gemini-2.0-flash-lite-preview-02-05:free'),
        'base_url' => env('OPENROUTER_BASE_URL', 'https://openrouter.ai/api/v1'),
    ],

    'whatsapp' => [
        'driver' => env('WA_GATEWAY_DRIVER', 'waha'),
    ],

    'waha' => [
        'base_url' => env('WAHA_BASE_URL', 'http://localhost:3000'),
        'api_key' => env('WAHA_API_KEY', 'kadin_api_hub'),
        'secret' => env('WAHA_SECRET', 'qwertyuiop'),
        'session' => env('WAHA_SESSION', 'default'),
    ],

    'n8n' => [
        'webhook_url' => env('N8N_WEBHOOK_URL', 'http://localhost:5678/webhook/kadin-wa-bot'),
        'api_key' => env('N8N_API_KEY'),
    ],

    'wablas' => [
        'token' => env('WABLAS_TOKEN', 'kadin_api_hub'),
        'secret' => env('WABLAS_SECRET', 'qwertyuiop'),
        'domain' => env('WABLAS_DOMAIN', 'https://kudus.wablas.com'),
    ],

];
