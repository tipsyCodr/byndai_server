<?php

use App\Http\Controllers\frontend\site\IndexController;
use App\Http\Controllers\frontend\site\ReviewController;
use App\Http\Controllers\SmsTemplateController;
use Illuminate\Support\Facades\Route;


// Backend Auth & Dashboard
use App\Http\Controllers\Backend\AuthController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\NotificationController;

Route::get('/', function () {
    return view('index');
});

Route::view('/privacy', 'privacy');
Route::view('/terms', 'terms');

Route::resource('sms-templates', SmsTemplateController::class);

Route::get('/u/{username}', [IndexController::class, 'index'])->name('site.profile');

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('admin.notifications.index');
    Route::post('/notifications/send', [NotificationController::class, 'send'])->name('admin.notifications.send');

    // Subscription Management
    Route::resource('plans', \App\Http\Controllers\Backend\PlanController::class, ['as' => 'admin']);
    Route::post('plans/{id}/features', [\App\Http\Controllers\Backend\PlanController::class, 'addFeature'])->name('admin.plans.addFeature');
    Route::delete('plans/{id}/features/{featureId}', [\App\Http\Controllers\Backend\PlanController::class, 'removeFeature'])->name('admin.plans.removeFeature');

    Route::resource('features', \App\Http\Controllers\Backend\FeatureController::class, ['as' => 'admin']);

    Route::prefix('subscriptions')->name('admin.subscriptions.')->group(function() {
        Route::get('/', [\App\Http\Controllers\Backend\SubscriptionManagementController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Backend\SubscriptionManagementController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Backend\SubscriptionManagementController::class, 'store'])->name('store');
        Route::get('/{id}', [\App\Http\Controllers\Backend\SubscriptionManagementController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [\App\Http\Controllers\Backend\SubscriptionManagementController::class, 'edit'])->name('edit');
        Route::put('/{id}', [\App\Http\Controllers\Backend\SubscriptionManagementController::class, 'update'])->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\Backend\SubscriptionManagementController::class, 'destroy'])->name('destroy');
        Route::post('/usage/{id}/update', [\App\Http\Controllers\Backend\SubscriptionManagementController::class, 'updateUsage'])->name('updateUsage');
        Route::post('/{id}/update-badges', [\App\Http\Controllers\Backend\SubscriptionManagementController::class, 'updateBadges'])->name('updateBadges');
    });

    Route::resource('users', \App\Http\Controllers\Backend\UserController::class, ['as' => 'admin'])->only(['index', 'update', 'destroy']);

    // Flyer Management
    Route::resource('flyer-categories', \App\Http\Controllers\Backend\FlyerCategoryController::class, ['as' => 'admin']);
    Route::resource('flyers', \App\Http\Controllers\Backend\FlyerController::class, ['as' => 'admin']);

    // Event Management
    Route::resource('events', \App\Http\Controllers\Backend\EventController::class, ['as' => 'admin'])->only(['index', 'destroy']);
    Route::post('events/{id}/toggle-status', [\App\Http\Controllers\Backend\EventController::class, 'toggleStatus'])->name('admin.events.toggle-status');

    // Admin Referrer Management
    Route::prefix('admin/referrers')->name('admin.referrers.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Backend\ReferrerManagementController::class, 'index'])->name('index');
        Route::get('/sales', [\App\Http\Controllers\Backend\ReferrerManagementController::class, 'sales'])->name('sales');
        Route::get('/payouts', [\App\Http\Controllers\Backend\ReferrerManagementController::class, 'payouts'])->name('payouts');
        Route::post('/payouts', [\App\Http\Controllers\Backend\ReferrerManagementController::class, 'storePayout'])->name('store-payout');
        Route::get('/reports', [\App\Http\Controllers\Backend\ReferralReportController::class, 'index'])->name('reports');
        Route::get('/reports/export', [\App\Http\Controllers\Backend\ReferralReportController::class, 'exportReport'])->name('export-report');
        Route::get('/{referrer}', [\App\Http\Controllers\Backend\ReferrerManagementController::class, 'show'])->name('show');
        Route::post('/{referrer}/status', [\App\Http\Controllers\Backend\ReferrerManagementController::class, 'updateStatus'])->name('update-status');
        Route::post('/{referrer}/toggle-active', [\App\Http\Controllers\Backend\ReferrerManagementController::class, 'toggleActive'])->name('toggle-active');
        Route::post('/{referrer}/coupon', [\App\Http\Controllers\Backend\ReferrerManagementController::class, 'updateCoupon'])->name('update-coupon');
        Route::post('/{referrer}/regenerate-code', [\App\Http\Controllers\Backend\ReferrerManagementController::class, 'regenerateCode'])->name('regenerate-code');
    });
});

// Referrer Portal Routes (Guest)
Route::prefix('referrer')->name('referrer.')->group(function () {
    Route::get('/login', [\App\Http\Controllers\Referrer\ReferrerAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [\App\Http\Controllers\Referrer\ReferrerAuthController::class, 'login'])->name('login.post');
    Route::get('/register', [\App\Http\Controllers\Referrer\ReferrerAuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [\App\Http\Controllers\Referrer\ReferrerAuthController::class, 'register'])->name('register.post');
    Route::post('/logout', [\App\Http\Controllers\Referrer\ReferrerAuthController::class, 'logout'])->name('logout');

    // Protected Referrer Routes
    Route::middleware(['auth', \App\Http\Middleware\EnsureApprovedReferrer::class])->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Referrer\ReferrerDashboardController::class, 'index'])->name('dashboard');
        Route::get('/profile', [\App\Http\Controllers\Referrer\ReferrerDashboardController::class, 'profile'])->name('profile');
        Route::get('/export-csv', [\App\Http\Controllers\Referrer\ReferrerDashboardController::class, 'exportCsv'])->name('export-csv');
    });
});

Route::get('/product/{id}', [IndexController::class, 'product'])->name('single-product');

Route::post('/reviews/generate', [ReviewController::class, 'generateReview'])->name('reviews.generate.web');
Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');

