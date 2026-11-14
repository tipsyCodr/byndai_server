<?php

namespace App\Services\Flyer;

use App\Models\AiFlyerSetting;
use App\Models\FlyerGeneration;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class FlyerRetentionService
{
    /**
     * Run retention cleanup for a specific user after successful generation
     */
    public function cleanupForUser(User $user): int
    {
        $limit = (int) AiFlyerSetting::get('max_saved_flyers_per_user', 10);
        if ($limit <= 0) return 0;

        // Fetch completed generations for this user ordered newest first
        $completedFlyers = FlyerGeneration::where('user_id', $user->id)
            ->where('status', 'completed')
            ->orderBy('id', 'desc')
            ->get();

        if ($completedFlyers->count() <= $limit) {
            return 0;
        }

        // Slice beyond the limit (oldest items)
        $toDelete = $completedFlyers->slice($limit);
        $deletedCount = 0;

        foreach ($toDelete as $flyer) {
            $this->deleteFlyerWithFiles($flyer);
            $deletedCount++;
        }

        Log::info("FlyerRetentionService: cleaned up {$deletedCount} old flyers for user {$user->id}.");
        return $deletedCount;
    }

    /**
     * Clean up all users' flyers across the database (used by Artisan command)
     */
    public function cleanupAll(): int
    {
        $limit = (int) AiFlyerSetting::get('max_saved_flyers_per_user', 10);
        if ($limit <= 0) return 0;

        $userIds = FlyerGeneration::where('status', 'completed')
            ->select('user_id')
            ->distinct()
            ->pluck('user_id');

        $totalCleaned = 0;
        foreach ($userIds as $userId) {
            $user = User::find($userId);
            if ($user) {
                $totalCleaned += $this->cleanupForUser($user);
            }
        }

        return $totalCleaned;
    }

    /**
     * Delete a flyer record and its associated physical storage files
     */
    public function deleteFlyerWithFiles(FlyerGeneration $flyer): bool
    {
        $disk = Storage::disk('public');

        // Delete composed image file
        if ($flyer->image_path && $disk->exists($flyer->image_path)) {
            $disk->delete($flyer->image_path);
        }

        // Delete raw AI background image file
        if ($flyer->raw_ai_image_path && $disk->exists($flyer->raw_ai_image_path)) {
            $disk->delete($flyer->raw_ai_image_path);
        }

        return (bool) $flyer->delete();
    }
}

