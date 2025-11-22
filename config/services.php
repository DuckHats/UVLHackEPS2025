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
    'gemini' => [
        'key'     => env('GEMINI_API_KEY'),
        'endpoint' => env('GEMINI_ENDPOINT', 'https://generativelanguage.googleapis.com/v1beta/openai/chat/completions'),
        'model'   => env('GEMINI_MODEL', 'gemini-1.5-pro'),
    ],
    'overpass' => [
        'url' => env('OVERPASS_URL', 'https://overpass-api.de/api/interpreter?data=%5Bout%3Ajson%5D%5Btimeout%3A30%5D%3B%28node%5B%22place%22~%22neighbourhood%7Csuburb%22%5D%2833.7034%2C-118.6682%2C34.3373%2C-118.1553%29%3Bway%5B%22place%22~%22neighbourhood%7Csuburb%22%5D%2833.7034%2C-118.6682%2C34.3373%2C-118.1553%29%3Brelation%5B%22place%22~%22neighbourhood%7Csuburb%22%5D%2833.7034%2C-118.6682%2C34.3373%2C-118.1553%29%3Brelation%5B%22boundary%22%3D%22administrative%22%5D%5B%22admin_level%22%3D%2210%22%5D%2833.7034%2C-118.6682%2C34.3373%2C-118.1553%29%3B%29%3Bout%20center%3B'),
    ],



];
