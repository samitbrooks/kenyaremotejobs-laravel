<?php

return [

    /*
    |--------------------------------------------------------------------------
    | WhatsApp Cloud API Configuration (Meta Graph API)
    |--------------------------------------------------------------------------
    |
    | Enables official Meta WhatsApp Business API integration without needing a
    | physical phone hardware device. Allows sending job alerts, candidate
    | support, and managing all conversations directly from the admin portal.
    |
    */

    'access_token' => env('WHATSAPP_ACCESS_TOKEN'),

    'phone_number_id' => env('WHATSAPP_PHONE_NUMBER_ID'),

    'business_account_id' => env('WHATSAPP_BUSINESS_ACCOUNT_ID'),

    'webhook_verify_token' => env('WHATSAPP_WEBHOOK_VERIFY_TOKEN', 'krj-wa-verify-token'),

    'app_secret' => env('WHATSAPP_APP_SECRET'),

    'api_version' => env('WHATSAPP_API_VERSION', 'v21.0'),

    'default_channel_phone' => env('WHATSAPP_CHANNEL_PHONE', '+254700000000'),

];
