@props(['title' => null, 'description' => null])

<!DOCTYPE html>
<html lang="en" class="h-full antialiased">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ $title ? "{$title} | ".config('site.name') : config('site.name')." — ".config('site.default_title') }}</title>
        <meta name="description" content="{{ $description ?? config('site.default_description') }}">
        <meta property="og:type" content="website">
        <meta property="og:site_name" content="{{ config('site.name') }}">
        <meta property="og:title" content="{{ $title ?? config('site.name').' — '.config('site.default_title') }}">
        <meta property="og:description" content="{{ $description ?? config('site.default_description') }}">
        <meta name="twitter:card" content="summary_large_image">
        <meta name="robots" content="index, follow">

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
        </div>

        @livewireScripts
    </body>
</html>
