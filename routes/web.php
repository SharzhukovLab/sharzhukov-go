<?php

use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\CookieConsentController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MyLinksController;
use App\Http\Controllers\RedirectController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::post('/create', [HomeController::class, 'store'])->name('link.store');

Route::get('/s/{slug}', [RedirectController::class, 'go'])->name('link.go');
Route::get('/l/{slug}', [RedirectController::class, 'show'])->name('link.show');

Route::middleware('auth')->group(function () {
    Route::get('/my', [MyLinksController::class, 'index'])->name('my-links');
});

Route::post('/api/cookie-consent', [CookieConsentController::class, 'store'])->name('cookie-consent.store');

Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');
Route::get('/newsletter/confirm/{token}', [NewsletterController::class, 'confirm'])->name('newsletter.confirm');
Route::get('/newsletter/unsubscribe/{token}', [NewsletterController::class, 'unsubscribe'])->name('newsletter.unsubscribe');
