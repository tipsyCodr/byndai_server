<?php

use Illuminate\Support\Facades\Route;
use App\AI\Controllers\ChatController;

Route::middleware('web')->group(function () {
    Route::get('/ai/chat', [ChatController::class, 'index'])->name('ai.chat');
    Route::post('/ai/chat', [ChatController::class, 'store'])->name('ai.chat.store');
    Route::post('/ai/chat/stream', [ChatController::class, 'stream'])->name('ai.chat.stream');
    Route::post('/ai/chat/clear', [ChatController::class, 'clear'])->name('ai.chat.clear');
    
    Route::get('/ai/create', [ChatController::class, 'create'])->name('ai.create');
    Route::post('/ai/create/plan', [ChatController::class, 'plan'])->name('ai.create.plan');
    Route::post('/ai/create/generate-section', [ChatController::class, 'generateSection'])->name('ai.create.generate-section');
    Route::post('/ai/create/generate', [ChatController::class, 'generate'])->name('ai.create.generate');
});
