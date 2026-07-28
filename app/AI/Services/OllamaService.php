<?php

namespace App\AI\Services;

use App\AI\Interface\AIService;
use Illuminate\Support\Facades\Http;

class OllamaService implements AIService
{
    protected string $baseUrl;
    protected string $model;

    public function __construct()
    {
        $this->baseUrl = config('services.ollama.url', 'http://localhost:11434');
        $this->model = config('services.ollama.model', 'gemma4:e4b');
    }

    public function chat(array $messages, array $options = []): string
    {
        $response = Http::timeout(120)->post("{$this->baseUrl}/api/chat", [
            'model' => $options['model'] ?? $this->model,
            'messages' => $messages,
            'stream' => false,
            'options' => array_merge([
                'num_predict' => 4096,
                'num_ctx' => 8192
            ], $options['options'] ?? [])
        ]);

        if ($response->failed()) {
            throw new \Exception("Ollama API request failed: " . $response->body());
        }

        return $response->json('message.content') ?? '';
    }
}
