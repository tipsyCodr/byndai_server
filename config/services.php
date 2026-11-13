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

    'resend' => [
        'key' => env('RESEND_KEY'),
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

    'saakshisms' => [
        'user' => env('SAAKSHI_USER'),
        'password' => env('SAAKSHI_PASSWORD'),
        'sender_id' => env('SAAKSHI_SENDER_ID'),
        'channel' => env('SAAKSHI_CHANNEL', 'Promo'),
        'route' => env('SAAKSHI_ROUTE', '##'),
        'default_peid' => env('SAAKSHI_DEFAULT_PEID'),
    ],

    'razorpay' => [
        'key'    => env('RAZORPAY_KEY'),
        'secret' => env('RAZORPAY_SECRET'),
    ],

    // AI provider — change AI_DRIVER in .env to switch (ollama|openai)
    'ai' => [
        'driver' => env('AI_DRIVER', (env('OPENAI_API_KEY') || env('OPENAI_API_SECRET_KEY')) ? 'openai' : 'ollama'),
    ],

    'openai' => [
        'key'   => env('OPENAI_API_KEY', env('OPENAI_API_SECRET_KEY')),
        'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
        'url'   => env('OPENAI_BASE_URL', 'https://api.openai.com/v1'),
    ],

    'ollama' => [
        'url'   => env('OLLAMA_URL', 'http://localhost:11434'),
        'model' => env('OLLAMA_MODEL', 'gemma4:e4b'),
    ],

    // FCM HTTP v1 — uses service account for OAuth2 (legacy server key is dead since Jul 2024)
    'fcm' => [
        'project_id'           => env('FCM_PROJECT_ID'),
        'service_account_path' => env('FCM_SERVICE_ACCOUNT_PATH', storage_path('app/firebase/service-account.json')),
    ],

];
