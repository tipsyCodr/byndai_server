<?php

namespace App\Jobs;

use App\Models\BrandKit;
use App\Models\FlyerGeneration;
use App\Services\Flyer\FlyerCompositionService;
use App\Services\Flyer\FlyerRetentionService;
use App\Services\Flyer\OpenAIImageService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateFlyerJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 180;
    public int $tries = 2;

    public function __construct(public int $generationId)
    {
    }

    public function handle(
        OpenAIImageService $openAIImageService,
        FlyerCompositionService $compositionService,
        FlyerRetentionService $retentionService
    ): void {
        $generation = FlyerGeneration::with('user')->find($this->generationId);
        if (!$generation) {
            Log::error("GenerateFlyerJob: FlyerGeneration {$this->generationId} not found.");
            return;
        }

        if ($generation->status === 'completed') {
            return;
        }

        try {
            $generation->update([
                'status' => 'processing',
                'error_message' => null,
            ]);

            // 1. Generate Raw Image via OpenAI
            $aiResult = $openAIImageService->generate(
                $generation->prompt,
                $generation->aspect_ratio
            );

            $rawPath = $aiResult['raw_path'];
            $modelUsed = $aiResult['model_used'];

            $generation->update([
                'raw_ai_image_path' => $rawPath,
                'generation_metadata' => [
                    'model' => $modelUsed,
                    'generated_at' => now()->toIso8601String(),
                ],
            ]);

            // 2. Compose Branding, Logo & Typography
            $user = $generation->user;
            $brandKit = BrandKit::where('user_id', $user->id)->first();

            // The raw AI image contains the seamless painted headline.
            // Flutter app handles the responsive, interactive branding footer overlay.
            $generation->update([
                'image_path' => $rawPath,
                'status' => 'completed',
            ]);

            // 4. Run Retention Cleanup
            $retentionService->cleanupForUser($user);

            Log::info("GenerateFlyerJob: Successfully generated flyer #{$generation->id} for user #{$user->id}");
        } catch (\Throwable $e) {
            Log::error("GenerateFlyerJob failed for #{$generation->id}: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            $generation->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}

