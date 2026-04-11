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
        Route::get('/{id}', [\App\Http\Controllers\Backend\SubscriptionManagementController::class, 'show'])->name('show');
        Route::post('/usage/{id}/update', [\App\Http\Controllers\Backend\SubscriptionManagementController::class, 'updateUsage'])->name('updateUsage');
        Route::post('/{id}/update-badges', [\App\Http\Controllers\Backend\SubscriptionManagementController::class, 'updateBadges'])->name('updateBadges');
    });

    Route::resource('users', \App\Http\Controllers\Backend\UserController::class, ['as' => 'admin'])->only(['index', 'update']);

    // Flyer Management
    Route::resource('flyer-categories', \App\Http\Controllers\Backend\FlyerCategoryController::class, ['as' => 'admin']);
    Route::resource('flyers', \App\Http\Controllers\Backend\FlyerController::class, ['as' => 'admin']);

    // Event Management
    Route::resource('events', \App\Http\Controllers\Backend\EventController::class, ['as' => 'admin'])->only(['index', 'destroy']);
    Route::post('events/{id}/toggle-status', [\App\Http\Controllers\Backend\EventController::class, 'toggleStatus'])->name('admin.events.toggle-status');
});

Route::get('/product/{id}', [IndexController::class, 'product'])->name('single-product');

Route::post('/reviews/generate', [ReviewController::class, 'generateReview'])->name('reviews.generate.web');
Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
