<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EmployerController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\JobsController;
use App\Http\Controllers\JournalController;
use App\Http\Controllers\PricingController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\UnsubscribeController;
use App\Http\Controllers\Webhooks\MpesaCallbackController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index']);

Route::get('/jobs', [JobsController::class, 'index']);
Route::get('/jobs/{id}', [JobsController::class, 'show']);

Route::get('/pricing', [PricingController::class, 'index']);

Route::get('/account', [AccountController::class, 'show']);
Route::post('/logout', [AccountController::class, 'logout']);

Route::get('/employers', [EmployerController::class, 'index']);
Route::get('/employers/post', [EmployerController::class, 'post']);
Route::get('/employers/dashboard', [EmployerController::class, 'dashboard']);
Route::post('/employers/jobs/{id}/remove', [EmployerController::class, 'removeJob']);

Route::get('/journal', [JournalController::class, 'index']);
Route::get('/journal/{blogPost}', [JournalController::class, 'show']);

Route::view('/match', 'match');
Route::view('/resume-builder', 'resume-builder');
Route::view('/about', 'about');
Route::view('/surveys', 'surveys');

// Signed so it works cold from an email client with no session — both verbs
// point at the same action: GET for a click from the footer link, POST for a
// mailbox provider's own one-click "Unsubscribe" button (RFC 8058), which
// fires List-Unsubscribe-Post as a bare POST with no confirmation page.
Route::match(['get', 'post'], '/unsubscribe/{user}', UnsubscribeController::class)
    ->name('unsubscribe')
    ->middleware('signed');

Route::get('/sitemap.xml', [SitemapController::class, 'sitemap']);
Route::get('/robots.txt', [SitemapController::class, 'robots']);

// The 'admin' middleware (App\Http\Middleware\EnsureUserIsAdmin) guards the
// whole group; every mutation a page embeds still acts against the
// authenticated user itself, never a value trusted from the request.
Route::middleware('admin')->prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard']);
    Route::get('/jobs', [AdminController::class, 'jobs']);
    Route::get('/jobs/{job}/edit', [AdminController::class, 'jobEdit']);
    Route::get('/blog', [AdminController::class, 'blog']);
    Route::get('/blog/{post:id}/edit', [AdminController::class, 'blogEdit']);
    Route::get('/users', [AdminController::class, 'users']);
    Route::get('/users/{user}', [AdminController::class, 'userShow']);
    Route::get('/email', [AdminController::class, 'email']);
    Route::get('/payments', [AdminController::class, 'payments']);
});

// Safaricom posts here directly (no browser session, no CSRF token to send)
// once a DarajaGateway exists — see App\Http\Controllers\Webhooks\MpesaCallbackController.
// Exempted from CSRF via preventRequestForgery(except:) in bootstrap/app.php,
// not a route-level withoutMiddleware() — Laravel 13's CSRF middleware
// (PreventRequestForgery) reads its exclusion list from there.
Route::post('/webhooks/mpesa/callback', MpesaCallbackController::class);
