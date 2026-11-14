<?php

namespace App\Services\Flyer;

use App\Jobs\GenerateFlyerJob;
use App\Models\AiFlyerSetting;
use App\Models\BrandKit;
use App\Models\FlyerGeneration;
use App\Models\Occasion;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class FlyerGenerationService
{
    public function __construct(
        protected FlyerPromptBuilder $promptBuilder,
        protected FlyerRetentionService $retentionService
    ) {}

    /**
     * Check if user is eligible to generate
     */
    public function checkUserQuota(User $user): array
    {
        $enabled = AiFlyerSetting::get('ai_flyer_enabled', '1');
        if ($enabled !== '1' && $enabled !== true) {
            return [
                'allowed' => false,
                'message' => 'AI Flyer generation is currently disabled by administrator.',
                'remaining' => 0,
            ];
        }

        $dailyLimit = (int) AiFlyerSetting::get('daily_generation_limit_per_user', 5);

        $todayGenerationsCount = FlyerGeneration::where('user_id', $user->id)
            ->whereDate('created_at', Carbon::today())
            ->whereIn('status', ['pending', 'processing', 'completed'])
            ->count();

        $remaining = max(0, $dailyLimit - $todayGenerationsCount);

        if ($todayGenerationsCount >= $dailyLimit) {
            return [
                'allowed' => false,
                'message' => "Daily generation limit of {$dailyLimit} flyers reached. Please try again tomorrow.",
                'remaining' => 0,
            ];
        }

        return [
            'allowed' => true,
            'remaining' => $remaining,
            'limit' => $dailyLimit,
            'used_today' => $todayGenerationsCount,
        ];
    }

    /**
     * Create pending generation and dispatch queue job
     */
    public function createGeneration(User $user, array $data): FlyerGeneration
    {
        $quota = $this->checkUserQuota($user);
        if (!$quota['allowed']) {
            throw new \Exception($quota['message']);
        }

        $type = $data['type'] ?? 'special';
        $category = $data['category'] ?? 'Business Promotion';
        $occasionId = $data['occasion_id'] ?? null;
        $language = $data['language'] ?? 'bilingual';
        $designStyle = $data['design_style'] ?? 'Modern';
        $aspectRatio = $data['aspect_ratio'] ?? '1:1';
        $customPrompt = $data['custom_prompt'] ?? null;
        $brandingOptions = $data['branding_options'] ?? [];

        $occasion = null;
        if ($occasionId) {
            $occasion = Occasion::find($occasionId);
            if ($occasion) {
                $type = 'occasion';
                $category = $occasion->name;
            }
        }

        $brandKit = BrandKit::where('user_id', $user->id)->first();

        // Build rich prompt & headlines
        $promptData = $this->promptBuilder->build(
            $type,
            $category,
            $occasion,
            $language,
            $designStyle,
            $aspectRatio,
            $customPrompt,
            $brandKit
        );

        $generation = FlyerGeneration::create([
            'user_id' => $user->id,
            'type' => $type,
            'category' => $category,
            'occasion_id' => $occasion?->id,
            'prompt' => $promptData['prompt'],
            'headline' => $promptData['headline'],
            'subheadline' => $promptData['subheadline'],
            'language' => $language,
            'design_style' => $designStyle,
            'aspect_ratio' => $aspectRatio,
            'status' => 'pending',
            'branding_options' => $brandingOptions,
        ]);

        // Dispatch background job
        GenerateFlyerJob::dispatch($generation->id);

        return $generation;
    }

    /**
     * Retry or regenerate a flyer
     */
    public function regenerate(User $user, FlyerGeneration $generation): FlyerGeneration
    {
        if ($generation->user_id !== $user->id) {
            throw new \Exception("Unauthorized access to flyer.");
        }

        $quota = $this->checkUserQuota($user);
        if (!$quota['allowed']) {
            throw new \Exception($quota['message']);
        }

        // Reset state & re-dispatch
        $generation->update([
            'status' => 'pending',
            'error_message' => null,
        ]);

        GenerateFlyerJob::dispatch($generation->id);

        return $generation;
    }
}

