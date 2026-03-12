<?php

namespace App\Jobs;

use App\Models\Posts\JobPost;
use App\Models\SalaryEstimate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class PopulateSalaryEstimatesFromJobPostsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 3600; // 1 hour timeout
    
    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('=== Starting Salary Estimates Population from Job Posts ===');
        
        $processedCount = 0;
        $skippedCount = 0;
        $errorCount = 0;
        
        // Get all job posts (including inactive ones)
        JobPost::query()
            ->whereNotNull('title')
            ->chunk(100, function ($jobPosts) use (&$processedCount, &$skippedCount, &$errorCount) {
                foreach ($jobPosts as $jobPost) {
                    try {
                        $result = $this->processSalaryData($jobPost);
                        
                        if ($result === 'processed') {
                            $processedCount++;
                        } elseif ($result === 'skipped') {
                            $skippedCount++;
                        }
                        
                    } catch (\Exception $e) {
                        $errorCount++;
                        Log::error("Error processing job post ID {$jobPost->id}: " . $e->getMessage());
                    }
                }
            });
        
        Log::info("=== Salary Estimates Population Complete ===");
        Log::info("Processed: {$processedCount}, Skipped: {$skippedCount}, Errors: {$errorCount}");
    }
    
    /**
     * Process salary data from a job post
     */
    private function processSalaryData(JobPost $jobPost): string
    {
        // Validate required fields
        if (!$jobPost->title || !$jobPost->salary_min || !$jobPost->salary_max) {
            return 'skipped';
        }
        
        // Validate salary data quality
        if (!$this->isValidSalaryData($jobPost)) {
            Log::debug("Skipping job post ID {$jobPost->id}: Invalid salary data");
            return 'skipped';
        }
        
        // Check if estimate already exists
        $normalizedTitle = SalaryEstimate::normalizeTitle($jobPost->title);
        $normalizedLocation = SalaryEstimate::normalizeLocation($jobPost->location_country ?? 'USA');
        
        $exists = SalaryEstimate::where('normalized_title', $normalizedTitle)
            ->where('location', $normalizedLocation)
            ->where('source', 'job_posts')
            ->exists();
        
        if ($exists) {
            return 'skipped';
        }
        
        // Create salary estimate
        SalaryEstimate::create([
            'job_title' => $jobPost->title,
            'location' => $normalizedLocation,
            'normalized_title' => $normalizedTitle,
            'min_salary' => $jobPost->salary_min,
            'max_salary' => $jobPost->salary_max,
            'currency' => $jobPost->currency ?? 'USD',
            'source' => 'job_posts',
            'metadata' => json_encode([
                'job_post_id' => $jobPost->id,
                'employer_type' => $jobPost->employer_type,
                'payment_frequency' => $jobPost->payment_frequency,
                'extracted_at' => now()->toDateTimeString(),
            ]),
        ]);
        
        Log::debug("Created salary estimate for: {$jobPost->title} in {$normalizedLocation}");
        
        return 'processed';
    }
    
    /**
     * Validate salary data quality
     */
    private function isValidSalaryData(JobPost $jobPost): bool
    {
        $min = $jobPost->salary_min;
        $max = $jobPost->salary_max;
        
        // Both must be present and numeric
        if (!is_numeric($min) || !is_numeric($max)) {
            return false;
        }
        
        $min = (float) $min;
        $max = (float) $max;
        
        // Min must be less than max
        if ($min >= $max) {
            return false;
        }
        
        // Both must be positive
        if ($min <= 0 || $max <= 0) {
            return false;
        }
        
        // Check reasonable ranges based on payment frequency
        $frequency = $jobPost->payment_frequency ?? 'Yearly';
        
        switch ($frequency) {
            case 'Hourly':
                // Hourly: $10 - $200/hour is reasonable
                if ($min < 10 || $max > 200) {
                    return false;
                }
                // Range shouldn't be too wide (more than 10x)
                if ($max / $min > 10) {
                    return false;
                }
                break;
                
            case 'Weekly':
                // Weekly: $400 - $8,000/week is reasonable
                if ($min < 400 || $max > 8000) {
                    return false;
                }
                if ($max / $min > 5) {
                    return false;
                }
                break;
                
            case 'Monthly':
                // Monthly: $2,000 - $40,000/month is reasonable
                if ($min < 2000 || $max > 40000) {
                    return false;
                }
                if ($max / $min > 5) {
                    return false;
                }
                break;
                
            case 'Yearly':
            default:
                // Yearly: $20,000 - $500,000/year is reasonable
                if ($min < 20000 || $max > 500000) {
                    return false;
                }
                // Range shouldn't be more than 3x (e.g., 60k-180k is reasonable, 60k-500k is not)
                if ($max / $min > 3) {
                    return false;
                }
                break;
        }
        
        return true;
    }
}
