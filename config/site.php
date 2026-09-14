<?php

// Ported from the Next.js version's src/lib/seo.ts.

return [
    'url' => env('APP_URL', 'https://kenyaremotejobs.com'),
    'name' => 'KenyaRemoteJobs',
    'default_title' => 'Online & Remote Jobs in Kenya, Matched to You',
    'default_description' => 'Browse vetted online and remote jobs open to Kenya from top global employers and distributed teams — every listing scored for timezone, location, and visa fit, with a Kenya-Friendly Match badge so you skip the fine print.',
    'google_site_verification' => env('GOOGLE_SITE_VERIFICATION'),
    'default_og_image' => env('SITE_OG_IMAGE', '/images/og-banner.svg'),
    'twitter_handle' => env('SITE_TWITTER_HANDLE', '@KenyaRemoteJobs'),
    'keywords' => 'remote jobs kenya, online jobs kenya, work from home kenya, remote tech jobs nairobi, virtual assistant kenya, freelance kenya',
];
