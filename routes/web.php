<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\EmployerController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\JobsController;
use App\Http\Controllers\PricingController;
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

// Safaricom posts here directly (no browser session, no CSRF token to send)
// once a DarajaGateway exists — see App\Http\Controllers\Webhooks\MpesaCallbackController.
// Exempted from CSRF via preventRequestForgery(except:) in bootstrap/app.php,
// not a route-level withoutMiddleware() — Laravel 13's CSRF middleware
// (PreventRequestForgery) reads its exclusion list from there.
Route::post('/webhooks/mpesa/callback', MpesaCallbackController::class);
