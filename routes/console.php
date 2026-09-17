<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Replaces the Next.js version's lazy "sync on request if stale" pattern —
// cPanel's Cron Jobs tool runs `php artisan schedule:run` every minute
// natively, so a real scheduled task is a better fit than a workaround built
// to avoid needing one.
Schedule::command('jobs:sync')->everySixHours()->onOneServer()->withoutOverlapping();

// Automatically sends personalized job matches digest to registered users twice a week
// (Tuesdays & Fridays at 8:00 AM East Africa Time / 05:00 UTC)
Schedule::command('jobs:send-digest')
    ->days([2, 5])
    ->at('05:00')
    ->onOneServer()
    ->withoutOverlapping();
