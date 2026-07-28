<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->loadViewsFrom(app_path('AI/Views'), 'ai');

        // Resolve AI service driver from .env AI_DRIVER (ollama|openai)
        $this->app->bind(\App\AI\Interface\AIService::class, function () {
            return match (config('services.ai.driver', 'ollama')) {
                'openai' => new \App\AI\Services\OpenAIService(),
                default  => new \App\AI\Services\OllamaService(),
            };
        });

        \Illuminate\Support\Facades\Route::group([], app_path('AI/routes.php'));

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        date_default_timezone_set('Asia/Kolkata');

        Carbon::serializeUsing(function ($carbon) {
            return $carbon->timezone('Asia/Kolkata')->format('Y-m-d H:i:s');
        });
    }
}
