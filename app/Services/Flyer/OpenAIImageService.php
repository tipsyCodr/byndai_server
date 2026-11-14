<?php

namespace App\Services\Flyer;

use App\Models\AiFlyerSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class OpenAIImageService
{
    protected string $apiKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.openai.key')
            ?: env('OPENAI_API_SECRET_KEY')
            ?: env('OPENAI_API_KEY')
            ?: (string) AiFlyerSetting::get('openai_api_key', '');
        $this->baseUrl = config('services.openai.url', 'https://api.openai.com/v1');
    }

    /**
     * Generate image via OpenAI API and save to local storage
     *
     * @return array [ 'raw_path' => string, 'model_used' => string ]
     */
    public function generate(string $prompt, string $aspectRatio = '1:1', ?string $requestedModel = null): array
    {
        if (empty($this->apiKey)) {
            $this->apiKey = (string) AiFlyerSetting::get('openai_api_key', '');
        }

        if (empty($this->apiKey)) {
            throw new \Exception("OpenAI API key is not configured on the server. Please configure it in .env or AI Flyer Settings.");
        }

        $model = $requestedModel ?: AiFlyerSetting::get('openai_model', 'gpt-image-2.6-flare');
        $quality = AiFlyerSetting::get('image_quality', 'standard');

        // Dimensions mapping supported by OpenAI Image API
        $size = match ($aspectRatio) {
            '9:16', '4:5' => '1024x1792',
            default => '1024x1024',
        };

        $result = $this->requestImage($model, $prompt, $size, $quality);

        // Fallback to dall-e-3 if gpt-image-2.6-flare or another model is not accessible
        if (!$result['success'] && $model !== 'dall-e-3') {
            Log::warning("OpenAI model {$model} failed: {$result['error']}. Attempting fallback to dall-e-3.");
            $model = 'dall-e-3';
            $result = $this->requestImage($model, $prompt, $size, $quality);
        }

        if (!$result['success']) {
            throw new \Exception("OpenAI Image Generation failed: " . $result['error']);
        }

        $imageBytes = null;
        if (!empty($result['url'])) {
            // Download image binary
            $downloadResponse = Http::timeout(60)->get($result['url']);
            if ($downloadResponse->failed()) {
                throw new \Exception("Failed to download generated image from OpenAI.");
            }
            $imageBytes = $downloadResponse->body();
        } elseif (!empty($result['b64'])) {
            $imageBytes = base64_decode($result['b64']);
        }

        if (empty($imageBytes)) {
            throw new \Exception("No image binary received from OpenAI.");
        }

        $fileName = 'flyers/raw/' . Str::uuid() . '.png';
        Storage::disk('public')->put($fileName, $imageBytes);

        return [
            'raw_path' => $fileName,
            'model_used' => $model,
        ];
    }

    protected function requestImage(string $model, string $prompt, string $size, string $quality): array
    {
        try {
            $payload = [
                'model' => $model,
                'prompt' => $prompt,
                'n' => 1,
                'size' => $size,
            ];

            if ($model === 'dall-e-3' || str_contains($model, 'dall-e')) {
                $payload['quality'] = $quality;
            }

            $response = Http::withToken($this->apiKey)
                ->timeout(120)
                ->post("{$this->baseUrl}/images/generations", $payload);

            if ($response->successful()) {
                $url = $response->json('data.0.url');
                $b64 = $response->json('data.0.b64_json');
                if ($url) {
                    return ['success' => true, 'url' => $url];
                }
                if ($b64) {
                    return ['success' => true, 'b64' => $b64];
                }
            }

            $errorMsg = $response->json('error.message') ?? $response->body();
            return ['success' => false, 'error' => $errorMsg];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}

