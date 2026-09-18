<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:media="http://search.yahoo.com/mrss/">
    <channel>
        <title>{{ $siteName }} — Verified Remote Jobs for Kenya</title>
        <link>{{ $siteUrl }}</link>
        <description>Curated international remote opportunities screened for East Africa Time (UTC+3) compatibility, USD/KES compensation, and zero visa restrictions.</description>
        <language>en-ke</language>
        <lastBuildDate>{{ $lastBuildDate }}</lastBuildDate>
        <atom:link href="{{ $feedUrl }}" rel="self" type="application/rss+xml" />

        <image>
            <url>{{ $siteUrl }}/images/logo.png</url>
            <title>{{ $siteName }}</title>
            <link>{{ $siteUrl }}</link>
        </image>

        @foreach ($items as $item)
        <item>
            <title><![CDATA[{!! $item['title'] !!}]]></title>
            <link>{{ $item['link'] }}</link>
            <guid isPermaLink="true">{{ $item['guid'] }}</guid>
            <pubDate>{{ $item['pubDate'] }}</pubDate>
            <description><![CDATA[{!! $item['description'] !!}]]></description>
            @foreach ($item['categories'] as $cat)
            <category><![CDATA[{{ $cat }}]]></category>
            @endforeach
            <media:content url="{{ $siteUrl }}/images/og-banner.svg" medium="image" />
        </item>
        @endforeach
    </channel>
</rss>
