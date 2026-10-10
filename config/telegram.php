<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Telegram Bot & Channel Configuration
    |--------------------------------------------------------------------------
    |
    | Official Telegram Bot integration for KenyaRemoteJobs.
    | Powers:
    | 1. Automated daily broadcast of verified remote jobs to the channel/group.
    | 2. Automatic welcoming of new community members.
    | 3. Integrated Google AI (Gemini via Daisy AI) to answer questions, guide
    |    users on remote work, payments (Wise/M-Pesa), and resolve inquiries.
    | 4. Admin broadcast controls restricted to the sole admin.
    |
    */

    'bot_token' => env('TELEGRAM_BOT_TOKEN'),

    'bot_username' => env('TELEGRAM_BOT_USERNAME', 'KenyaRemoteJobsBot'),

    'channel_id' => env('TELEGRAM_CHANNEL_ID'),

    'group_id' => env('TELEGRAM_GROUP_ID'),

    'admin_user_id' => env('TELEGRAM_ADMIN_USER_ID'),

    'webhook_secret' => env('TELEGRAM_WEBHOOK_SECRET'),

];
