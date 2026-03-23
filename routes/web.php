<?php

use App\Http\Controllers\frontend\site\IndexController;
use App\Http\Controllers\frontend\site\ReviewController;
use App\Http\Controllers\SmsTemplateController;
use Illuminate\Support\Facades\Route;


// Backend Auth & Dashboard
use App\Http\Controllers\Backend\AuthController;
use App\Http\Controllers\Backend\DashboardController;

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
});

Route::get('/product/{id}', [IndexController::class, 'product'])->name('single-product');

Route::post('/reviews/generate', [ReviewController::class, 'generateReview'])->name('reviews.generate.web');
Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
