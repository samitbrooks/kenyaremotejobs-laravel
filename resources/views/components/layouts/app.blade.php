@props(['title' => null, 'description' => null, 'canonical' => null, 'ogImage' => null, 'ogType' => 'website'])

<!DOCTYPE html>
<html lang="en" class="h-full antialiased">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ $title ? "{$title} | ".config('site.name') : config('site.name')." — ".config('site.default_title') }}</title>
        <meta name="description" content="{{ $description ?? config('site.default_description') }}">
        <meta name="keywords" content="{{ config('site.keywords') }}">
        <link rel="canonical" href="{{ $canonical ?? url()->current() }}">

        {{-- Open Graph / Facebook --}}
        <meta property="og:type" content="{{ $ogType }}">
        <meta property="og:site_name" content="{{ config('site.name') }}">
        <meta property="og:url" content="{{ $canonical ?? url()->current() }}">
        <meta property="og:title" content="{{ $title ? "{$title} | ".config('site.name') : config('site.name').' — '.config('site.default_title') }}">
        <meta property="og:description" content="{{ $description ?? config('site.default_description') }}">
        <meta property="og:image" content="{{ $ogImage ?? url(config('site.default_og_image')) }}">
        <meta property="og:image:alt" content="{{ config('site.name') }} — {{ config('site.default_title') }}">
        <meta property="og:locale" content="en_KE">

        {{-- Twitter --}}
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:site" content="{{ config('site.twitter_handle') }}">
        <meta name="twitter:title" content="{{ $title ? "{$title} | ".config('site.name') : config('site.name').' — '.config('site.default_title') }}">
        <meta name="twitter:description" content="{{ $description ?? config('site.default_description') }}">
        <meta name="twitter:image" content="{{ $ogImage ?? url(config('site.default_og_image')) }}">

        <meta name="robots" content="index, follow, max-image-preview:large">

        @if (config('site.google_site_verification'))
            <meta name="google-site-verification" content="{{ config('site.google_site_verification') }}">
        @endif

        <script type="application/ld+json">{!! \App\Support\Seo::organizationAndWebsiteJsonLd() !!}</script>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Comfortaa:wght@400;500;600;700&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="min-h-full flex flex-col font-sans">
        <div class="no-print">
            <x-navbar />
        </div>

        <main class="flex-1">
            {{ $slot }}
        </main>

        <div class="no-print">
            <x-footer />
            <x-scroll-conversion-popup />
            <x-action-center />
        </div>

        @livewireScripts
    </body>
</html>
