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

// ─── SEO ───
Route::get('/robots.txt', fn () => response(
    "User-agent: *\nDisallow: /\n\nSitemap: https://main.sharzhukov.com/sitemap.xml\n",
    200,
    ['Content-Type' => 'text/plain']
))->name('robots');

// ── Notifications ──
Route::middleware(['auth'])->group(function () {
    Route::get('/notifications', [\App\Http\Controllers\NotificationsController::class, 'index'])->name('notifications.index');
    Route::get('/api/notifications/recent', [\App\Http\Controllers\NotificationsController::class, 'recent'])->name('notifications.recent');
    Route::get('/api/notifications/unread-count', [\App\Http\Controllers\NotificationsController::class, 'unreadCount'])->name('notifications.unread');
    Route::post('/api/notifications/{id}/read', [\App\Http\Controllers\NotificationsController::class, 'markRead'])->name('notifications.read');
    Route::post('/api/notifications/mark-all-read', [\App\Http\Controllers\NotificationsController::class, 'markAllRead'])->name('notifications.mark-all');
    Route::delete('/api/notifications/{id}', [\App\Http\Controllers\NotificationsController::class, 'destroy'])->name('notifications.destroy');
});
