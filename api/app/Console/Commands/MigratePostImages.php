<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Models\Core\FilePostStored;

class MigratePostImages extends Command
{
    protected $signature = 'migrate:post-images';
    protected $description = 'Move post images from private/public to public storage';

    public function handle()
    {
        $this->info('Starting migration of post images...');
        
        $imageFiles = FilePostStored::where('type', 'job_post')
            ->where('status', 'active')
            ->get();
        
        $movedCount = 0;
        $errorCount = 0;
        $alreadyCorrect = 0;
        
        foreach ($imageFiles as $imageFile) {
            $filename = $imageFile->filename;
            $foldername = $imageFile->foldername;
            
            // New correct path (in public disk)
            $newPath = 'store_data/posts/draft/' . $foldername . '/' . $filename;
            
            // Check if already in correct location
            if (Storage::disk('public')->exists($newPath)) {
                $this->line("✓ Already correct: Post #{$imageFile->post_id} - {$newPath}");
                $alreadyCorrect++;
                continue;
            }
            
            // Old incorrect path (in default/private disk)
            $oldPath = 'public/store_data/posts/draft/' . $foldername . '/' . $filename;
            
            // Check if file exists in old location
            if (Storage::exists($oldPath)) {
                try {
                    // Get file contents from old location
                    $fileContents = Storage::get($oldPath);
                    
                    // Create directory in new location
                    $directory = dirname($newPath);
                    Storage::disk('public')->makeDirectory($directory);
                    
                    // Save file to new location
                    Storage::disk('public')->put($newPath, $fileContents);
                    
                    // Verify the file was created
                    if (Storage::disk('public')->exists($newPath)) {
                        // Delete from old location
                        Storage::delete($oldPath);
                        
                        // Try to clean up empty directories
                        $oldDirectory = dirname($oldPath);
                        try {
                            Storage::deleteDirectory($oldDirectory);
                        } catch (\Exception $e) {
                            // Directory not empty, that's fine
                        }
                        
                        $this->info("✓ Moved: Post #{$imageFile->post_id} - {$filename}");
                        $movedCount++;
                    } else {
                        $this->error("✗ Failed to verify: Post #{$imageFile->post_id}");
                        $errorCount++;
                    }
                } catch (\Exception $e) {
                    $this->error("✗ Error moving Post #{$imageFile->post_id}: " . $e->getMessage());
                    $errorCount++;
                }
            } else {
                $this->warn("⚠ File not found: Post #{$imageFile->post_id} - {$oldPath}");
                $errorCount++;
            }
        }
        
        $this->newLine();
        $this->info('Migration complete!');
        $this->table(
            ['Status', 'Count'],
            [
                ['Moved', $movedCount],
                ['Already Correct', $alreadyCorrect],
                ['Errors/Not Found', $errorCount],
                ['Total', $imageFiles->count()],
            ]
        );
        
        return 0;
    }
}
