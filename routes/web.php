<?php

use App\Http\Controllers\frontend\site\IndexController;
use App\Http\Controllers\frontend\site\ReviewController;
use App\Http\Controllers\SmsTemplateController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
});

Route::view('/privacy', 'privacy');
Route::view('/terms', 'terms');

Route::resource('sms-templates', SmsTemplateController::class);

Route::get('/u/{username}', [IndexController::class, 'index'])->name('site.profile');
Route::get('/{user_id?}', [IndexController::class, 'index'])->name('home-page');
Route::get('/product/{id}', [IndexController::class, 'product'])->name('single-product');

Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
