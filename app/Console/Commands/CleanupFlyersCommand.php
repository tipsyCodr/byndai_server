<?php

namespace App\Console\Commands;

use App\Services\Flyer\FlyerRetentionService;
use Illuminate\Console\Command;

class CleanupFlyersCommand extends Command
{
    protected $signature = 'flyers:cleanup';
    protected $description = 'Clean up old user generated flyers according to retention policy';

    public function handle(FlyerRetentionService $retentionService): int
    {
        $this->info('Starting AI Flyer retention cleanup...');
        $count = $retentionService->cleanupAll();
        $this->info("Retention cleanup completed. Cleaned up {$count} flyers.");
        return Command::SUCCESS;
    }
}

