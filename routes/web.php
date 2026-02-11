<?php

use App\Http\Controllers\frontend\site\IndexController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::view('/privacy', 'privacy');

Route::get('/{user_id?}', [IndexController::class, 'index'])->name('home-page');
Route::get('/product/{id}', [IndexController::class, 'product'])->name('single-product');
