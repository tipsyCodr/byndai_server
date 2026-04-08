<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MigrateEventImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:event-images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate event images from public/assets to storage/app/public';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $images = \App\Models\MasterEventImage::where('event_img_path', 'LIKE', 'assets/uploads/event/%')->get();

        if ($images->isEmpty()) {
            $this->info('No images found to migrate.');

            return;
        }

        $this->info('Migrating '.$images->count().' images...');
        $bar = $this->output->createProgressBar($images->count());
        $bar->start();

        foreach ($images as $image) {
            $oldRelativePath = $image->event_img_path; // e.g., assets/uploads/event/Title/file.jpg
            $oldFullPath = public_path($oldRelativePath);

            if (\Illuminate\Support\Facades\File::exists($oldFullPath)) {

                // Extract parts
                $parts = explode('/', $oldRelativePath);
                // Expected: [assets, uploads, event, Title, file.jpg]
                if (count($parts) >= 5) {
                    $title = $parts[3];
                    $filename = $parts[4];

                    // Sanitize title (matching the controller logic)
                    $folderTitle = preg_replace('/[^a-zA-Z0-9_-]/', '_', $title);
                    $newRelativePath = "event/{$folderTitle}/{$filename}";
                    $newStoragePath = "storage/{$newRelativePath}";

                    // Ensure directory exists in storage
                    \Illuminate\Support\Facades\Storage::disk('public')->makeDirectory("event/{$folderTitle}");

                    // Copy file to storage
                    \Illuminate\Support\Facades\Storage::disk('public')->put(
                        "event/{$folderTitle}/{$filename}",
                        \Illuminate\Support\Facades\File::get($oldFullPath)
                    );

                    // Update database
                    $image->update([
                        'event_img_path' => $newStoragePath,
                    ]);

                    $this->info("\nMigrated: {$oldRelativePath} -> {$newStoragePath}");
                }
            } else {
                $this->warn("\nFile not found: {$oldFullPath}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->info("\nMigration complete!");
    }
}
