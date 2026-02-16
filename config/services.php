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

    'maib' => [
        'base_url' => env('MAIB_BASE_URL', 'https://api.maibmerchants.md'),
        'project_id' => env('MAIB_PROJECT_ID'),
        'project_secret' => env('MAIB_PROJECT_SECRET'),
        'signature_key' => env('MAIB_SIGNATURE_KEY'),
        'currency' => env('MAIB_CURRENCY', 'MDL'),
        'lang' => env('MAIB_LANG', 'ro'),
    ],




];
