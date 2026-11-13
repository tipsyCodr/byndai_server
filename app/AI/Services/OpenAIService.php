<?php

namespace App\AI\Services;

use App\AI\Interface\AIService;
use Illuminate\Support\Facades\Http;

class OpenAIService implements AIService
{
    protected string $apiKey;
    protected string $model;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey  = config('services.openai.key') ?: env('OPENAI_API_SECRET_KEY') ?: env('OPENAI_API_KEY', '');
        $this->model   = config('services.openai.model', 'gpt-4o');
        $this->baseUrl = config('services.openai.url', 'https://api.openai.com/v1');
    }

    /**
     * Non-streaming chat (used by other parts of the app)
     */
    public function chat(array $messages, array $options = []): string
    {
        $response = Http::withToken($this->apiKey)
            ->post("{$this->baseUrl}/chat/completions", [
                'model'    => $options['model'] ?? $this->model,
                'messages' => $messages,
                'stream'   => false,
            ]);

        if ($response->failed()) {
            throw new \Exception("OpenAI API request failed: " . $response->body());
        }

        return $response->json('choices.0.message.content') ?? '';
    }
}
