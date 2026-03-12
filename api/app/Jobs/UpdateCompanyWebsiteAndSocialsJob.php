<?php

namespace App\Jobs;

use App\Models\Posts\CompanyInfo;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UpdateCompanyWebsiteAndSocialsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 600; // 10 minutes timeout
    public $tries = 1; // Only try once

    protected $companyIds;
    protected $githubToken;
    protected $progressKey;

    /**
     * Create a new job instance.
     */
    public function __construct(array $companyIds, string $githubToken, string $progressKey)
    {
        $this->companyIds = $companyIds;
        $this->githubToken = $githubToken;
        $this->progressKey = $progressKey;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $total = count($this->companyIds);
        $processed = 0;
        $updated = 0;
        $skipped = 0;

        Log::info("=== UPDATE JOB STARTED ===");
        Log::info("Processing {$total} companies");
        Log::info("Company IDs: " . implode(', ', $this->companyIds));

        foreach ($this->companyIds as $companyId) {
            try {
                Log::info("--- Processing company ID: {$companyId} ---");
                $company = CompanyInfo::find($companyId);
                
                if (!$company) {
                    Log::warning("Company not found: ID {$companyId}");
                    $skipped++;
                    continue;
                }

                Log::info("Company found: {$company->company_name}");
                Log::info("Making API call...");
                
                // Generate website and social media info using AI
                $socialData = $this->generateWebsiteAndSocials($company->company_name, $company->company_description);
                
                Log::info("API call completed. Response: " . json_encode($socialData));

                // Only update if we got valid data
                if ($socialData['website'] !== null || $socialData['social_1'] !== null) {
                    Log::info("Updating company in database...");
                    $company->update([
                        'company_website' => $socialData['website'] ?? null,
                        'company_social_1' => $socialData['social_1'] ?? null,
                        'company_social_2' => $socialData['social_2'] ?? null,
                        'company_social_3' => $socialData['social_3'] ?? null,
                    ]);

                    Log::info("SUCCESS! Updated {$company->company_name} - Website: {$socialData['website']}");
                    $updated++;
                } else {
                    Log::warning("SKIPPED: No data returned for {$company->company_name}");
                    $skipped++;
                }
                
                $processed++;
                
                // Check if user requested to stop
                $currentProgress = Cache::get($this->progressKey, []);
                if (isset($currentProgress['stop_requested']) && $currentProgress['stop_requested']) {
                    Log::info("Job stopped by user request at {$processed}/{$total} companies");
                    $finalProgress = [
                        'status' => 'stopped',
                        'processed' => $processed,
                        'total' => $total,
                        'updated' => $updated,
                        'skipped' => $skipped,
                        'current_company' => null,
                        'percentage' => round(($processed / $total) * 100, 2),
                        'message' => "Job stopped by user. Processed {$processed} of {$total} companies. Updated: {$updated}, Skipped: {$skipped}",
                    ];
                    Cache::put($this->progressKey, $finalProgress, now()->addHours(1));
                    return;
                }
                
                Log::info("Sleeping 5 seconds before next company...");
                // Sleep for 5 seconds to respect rate limits (12 requests per minute = well under 24/60s limit)
                sleep(5);
                Log::info("Sleep complete");

                // Update progress in cache
                $progress = [
                    'status' => 'processing',
                    'total' => $total,
                    'processed' => $processed,
                    'updated' => $updated,
                    'skipped' => $skipped,
                    'percentage' => round(($processed / $total) * 100, 2),
                    'current_company' => $company->company_name,
                ];

                Cache::put($this->progressKey, $progress, now()->addHours(1));

            } catch (\Exception $e) {
                Log::error("Error updating company ID {$companyId}: " . $e->getMessage());
                $skipped++;
                // Continue with next company even if one fails
            }
        }

        // Mark as complete
        $finalProgress = [
            'status' => 'completed',
            'total' => $total,
            'processed' => $processed,
            'updated' => $updated,
            'skipped' => $skipped,
            'percentage' => 100,
            'message' => "Successfully updated {$updated} company records with website and social media info!",
        ];

        Cache::put($this->progressKey, $finalProgress, now()->addHours(1));
        Log::info("Update job completed: {$updated} updated, {$skipped} skipped");
    }

    /**
     * Generate website and social media links using GitHub Models API.
     */
    protected function generateWebsiteAndSocials(string $companyName, ?string $description): array
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
                Log::info("Trying model: {$model} for {$companyName}");
                
                $result = $this->tryGenerateWithModel($model, $companyName, $description);
                
                if ($result !== null) {
                    Log::info("Successfully generated with {$model}");
                    return $result;
                }
                
                // If we get null, it means rate limit hit - try next model
                Log::warning("Rate limit hit on {$model}, trying next model...");
                
            } catch (\Exception $e) {
                Log::warning("Error with {$model}: " . $e->getMessage());
                // Continue to next model
            }
        }

        // All models exhausted
        Log::error("All models exhausted for {$companyName}");
        return ['website' => null, 'social_1' => null, 'social_2' => null, 'social_3' => null];
    }

    /**
     * Try to generate socials with a specific model.
     * Returns array on success, null on rate limit.
     */
    protected function tryGenerateWithModel(string $model, string $companyName, ?string $description): ?array
    {
        try {
            $prompt = "Based on the company name \"{$companyName}\"" . 
                     ($description ? " and description: {$description}" : "") . 
                     ", provide the most likely official website URL and up to 3 social media profile URLs (LinkedIn, Twitter/X, Facebook, etc.). " .
                     "Return ONLY a valid JSON object in this exact format with no additional text: " .
                     '{"website": "https://...", "social_1": "https://...", "social_2": "https://...", "social_3": "https://..."}. ' .
                     "Use null for any links you cannot confidently determine. Be accurate - only include real, verified links for well-known companies.";

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->githubToken,
                'Content-Type' => 'application/json',
            ])->timeout(30)->post('https://models.inference.ai.azure.com/chat/completions', [
                'model' => $model,
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a research assistant that finds official company websites and social media profiles. Always return valid JSON only.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.3,
                'max_tokens' => 200,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $content = $data['choices'][0]['message']['content'] ?? '{}';
                
                // Try to parse the JSON response
                $content = trim($content);
                // Remove markdown code blocks if present
                $content = preg_replace('/```json\s*|\s*```/', '', $content);
                $content = trim($content);
                
                $socialData = json_decode($content, true);
                
                if (json_last_error() === JSON_ERROR_NONE && is_array($socialData)) {
                    return [
                        'website' => $socialData['website'] ?? null,
                        'social_1' => $socialData['social_1'] ?? null,
                        'social_2' => $socialData['social_2'] ?? null,
                        'social_3' => $socialData['social_3'] ?? null,
                    ];
                }
                
                Log::warning("Invalid JSON from {$model}: " . $content);
                return ['website' => null, 'social_1' => null, 'social_2' => null, 'social_3' => null];
            }
            
            $statusCode = $response->status();
            $errorBody = $response->json();
            
            // Check if it's a rate limit error - return null to try next model
            if ($statusCode === 429 || (isset($errorBody['error']['code']) && $errorBody['error']['code'] === 'RateLimitReached')) {
                Log::warning("Rate limit on {$model}");
                return null; // Signal to try next model
            }
            
            Log::warning("Failed with {$model} (Status: {$statusCode})");
            return ['website' => null, 'social_1' => null, 'social_2' => null, 'social_3' => null];

        } catch (\Exception $e) {
            Log::error("Error with {$model}: " . $e->getMessage());
            return null; // Try next model
        }
    }

    /**
     * Handle job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("UpdateCompanyWebsiteAndSocialsJob failed: " . $exception->getMessage());
        
        // Mark as failed in cache
        Cache::put($this->progressKey, [
            'status' => 'failed',
            'error' => $exception->getMessage(),
        ], now()->addHours(1));
    }
}
