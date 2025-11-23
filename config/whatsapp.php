<?php

return [
    /*
    |--------------------------------------------------------------------------
    | WhatsApp Business API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for WhatsApp Business Cloud API integration.
    | Get your credentials from: https://developers.facebook.com/apps
    |
    */

    'enabled' => env('WHATSAPP_ENABLED', false),

    'api_version' => env('WHATSAPP_API_VERSION', 'v21.0'),

    'phone_number_id' => env('WHATSAPP_PHONE_NUMBER_ID'),

    'access_token' => env('WHATSAPP_ACCESS_TOKEN'),

    'base_url' => env('WHATSAPP_BASE_URL', 'https://graph.facebook.com'),

    /*
    |--------------------------------------------------------------------------
    | Template Configuration
    |--------------------------------------------------------------------------
    |
    | Pre-approved WhatsApp template message configuration.
    | Templates must be created and approved in Meta Business Manager.
    |
    */

    'template' => [
        'name' => env('WHATSAPP_TEMPLATE_NAME', 'neighborhood_result'),
        'language' => env('WHATSAPP_TEMPLATE_LANGUAGE', 'en'),
    ],
];
