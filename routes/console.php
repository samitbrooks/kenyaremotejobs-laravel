<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Replaces the Next.js version's lazy "sync on request if stale" pattern —
// cPanel's Cron Jobs tool runs `php artisan schedule:run` every minute.
// Uses Schedule::call() to run in-process via Artisan::call() because cPanel/shared
// hosting environments disable `proc_open`, which crashes Symfony's Process component.
Schedule::call(function () {
    Artisan::call('jobs:sync');
})
    ->name('jobs:sync')
    ->everySixHours()
    ->onOneServer()
    ->withoutOverlapping();

// Automatically sends personalized job matches digest to registered users twice a week
// (Tuesdays & Fridays at 8:00 AM East Africa Time / 05:00 UTC)
Schedule::call(function () {
    Artisan::call('jobs:send-digest');
})
    ->name('jobs:send-digest')
    ->days([2, 5])
    ->at('05:00')
    ->onOneServer()
    ->withoutOverlapping();

// Automatically sends FlexJobs-style follow-up emails ("Still thinking about finding a remote job?")
// to non-subscribed users who registered or completed their trial
Schedule::call(function () {
    Artisan::call('email:send-follow-ups');
})
    ->name('email:send-follow-ups')
    ->dailyAt('06:00')
    ->onOneServer()
    ->withoutOverlapping();

// Process queued jobs (like broadcast emails) in background without blocking web requests
Schedule::call(function () {
    Artisan::call('queue:work', ['--stop-when-empty' => true, '--max-time' => 50]);
})
    ->name('queue:work')
    ->everyMinute()
    ->withoutOverlapping();
