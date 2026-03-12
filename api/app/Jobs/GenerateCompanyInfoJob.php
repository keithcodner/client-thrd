<?php

namespace App\Jobs;

use App\Models\Posts\CompanyInfo;
use App\Models\Posts\JobPost;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GenerateCompanyInfoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 600; // 10 minutes timeout
    public $tries = 1; // Only try once

    public $companyNames;
    public $githubToken;
    public $progressKey;

    /**
     * Create a new job instance.
     */
    public function __construct(array $companyNames, ?string $githubToken, string $progressKey)
    {
        $this->companyNames = $companyNames;
        $this->githubToken = $githubToken;
        $this->progressKey = $progressKey;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info("Starting GenerateCompanyInfoJob with " . count($this->companyNames) . " companies.");

            // Skip if no GitHub token configured
            if (empty($this->githubToken)) {
                Log::warning("GitHub token not configured - skipping AI generation for company info");
                
                Cache::put($this->progressKey, [
                    'status' => 'skipped',
                    'message' => 'GitHub token not configured',
                ], now()->addHours(1));
                
                return;
            }
        
        $total = count($this->companyNames);
        $processed = 0;
        $created = 0;
        $skipped = 0;
        $linked = 0;

        Log::info("Job started: Processing {$total} companies");

        foreach ($this->companyNames as $companyName) {
            try {
                // Check if company already exists
                $existingCompany = CompanyInfo::where('company_name', $companyName)->first();

                if ($existingCompany) {
                    Log::info("Company already exists: {$companyName}");
                    $skipped++;
                    $companyId = $existingCompany->id;
                } else {
                    // Generate AI description for the company
                    $description = $this->generateCompanyDescription($companyName);

                    // Create new CompanyInfo record
                    $company = CompanyInfo::create([
                        'company_name' => $companyName,
                        'company_description' => $description,
                        'status' => 'gathered',
                        'type' => 'auto-generated',
                    ]);

                    Log::info("Created company: {$companyName} (ID: {$company->id})");
                    $created++;
                    $companyId = $company->id;
                }

                // Link job posts to this company
                $updatedJobs = JobPost::where('company_name', $companyName)
                    ->whereNull('company_info_id')
                    ->update(['company_info_id' => $companyId]);

                $linked += $updatedJobs;
                $processed++;

                // Update progress in cache
                $progress = [
                    'status' => 'processing',
                    'total' => $total,
                    'processed' => $processed,
                    'created' => $created,
                    'skipped' => $skipped,
                    'linked' => $linked,
                    'percentage' => round(($processed / $total) * 100, 2),
                    'current_company' => $companyName,
                ];

                Cache::put($this->progressKey, $progress, now()->addHours(1));

            } catch (\Exception $e) {
                Log::error("Error processing company {$companyName}: " . $e->getMessage());
                // Continue with next company even if one fails
            }
        }

        // Mark as complete
        $finalProgress = [
            'status' => 'completed',
            'total' => $total,
            'processed' => $processed,
            'created' => $created,
            'skipped' => $skipped,
            'linked' => $linked,
            'percentage' => 100,
            'message' => "Successfully generated {$created} company records and linked {$linked} job posts!",
        ];

        Cache::put($this->progressKey, $finalProgress, now()->addHours(1));
        Log::info("Job completed: {$created} created, {$skipped} skipped, {$linked} linked");

        } catch (\Exception $e) {
            // Logging failure should not stop the job
            Log::error("Job failed: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            throw $e; // Let Laravel mark it failed
        }
    }

    /**
     * Generate company description using GitHub Models API with fallback models.
     */
    protected function generateCompanyDescription(string $companyName): string
    {
        // Available models in order of preference (highest quota first)
        $models = [
            'gpt-4o-mini',           // 15,000 requests/day
            'Phi-3.5-mini-instruct', // 2,000 requests/day  
            'Mistral-large',         // 150 requests/day
            'gpt-4o',                // 50 requests/day (fallback)
        ];

        foreach ($models as $model) {
            try {
                $result = $this->tryGenerateWithModel($model, $companyName);
                
                if ($result !== null) {
                    return $result;
                }
                
                // Rate limit hit, try next model
                Log::warning("Rate limit on {$model} for {$companyName}, trying next...");
                
            } catch (\Exception $e) {
                Log::warning("Error with {$model}: " . $e->getMessage());
            }
        }

        // All models exhausted
        Log::error("All models exhausted for {$companyName}");
        return 'Company information to be updated.';
    }

    /**
     * Try to generate description with a specific model.
     */
    protected function tryGenerateWithModel(string $model, string $companyName): ?string
    {
        try {
            $prompt = "Write a brief 2-3 sentence professional description for a company named \"{$companyName}\". Focus on what type of business they likely are based on the name. Be concise and professional.";

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->githubToken,
                'Content-Type' => 'application/json',
            ])->timeout(30)->post('https://models.inference.ai.azure.com/chat/completions', [
                'model' => $model,
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a professional business analyst writing company descriptions.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.7,
                'max_tokens' => 150,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['choices'][0]['message']['content'] ?? null;
            }

            $statusCode = $response->status();
            
            // If rate limit, return null to try next model
            if ($statusCode === 429) {
                return null;
            }

            Log::warning("Failed with {$model} for {$companyName} (Status: {$statusCode})");
            return null;

        } catch (\Exception $e) {
            Log::error("Error with {$model} for {$companyName}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Handle job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("GenerateCompanyInfoJob failed: " . $exception->getMessage());
        
        // Mark as failed in cache
        Cache::put($this->progressKey, [
            'status' => 'failed',
            'error' => $exception->getMessage(),
        ], now()->addHours(1));
    }
}
