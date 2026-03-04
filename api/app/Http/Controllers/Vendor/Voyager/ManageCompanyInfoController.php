<?php

namespace App\Http\Controllers\Vendor\Voyager;

use App\Http\Controllers\Controller;
use App\Models\Posts\CompanyInfo;
use App\Models\Posts\JobPost;
use App\Models\Core\FilePostStored;
use App\Models\AdminActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
use Inertia\Inertia;

class ManageCompanyInfoController extends Controller
{
    /**
     * Display a listing of company info.
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        $search = $request->get('search', '');
        $perPage = $request->get('per_page', 25);

        $query = CompanyInfo::query()
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        // Search by company name or description
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                    ->orWhere('company_description', 'like', "%{$search}%");
            });
        }

        $companies = $query->paginate($perPage);

        // Get counts for each status
        $statusCounts = [
            'all' => CompanyInfo::count(),
            'gathered' => CompanyInfo::where('status', 'gathered')->count(),
            'active' => CompanyInfo::where('status', 'active')->count(),
            'inactive' => CompanyInfo::where('status', 'inactive')->count(),
        ];

        return Inertia::render('Admin/ContentManagement/ManageCompanyInfo', [
            'companies' => $companies,
            'statusCounts' => $statusCounts,
            'filters' => [
                'status' => $status,
                'search' => $search,
                'per_page' => $perPage,
            ]
        ]);
    }

    /**
     * Get a specific company info.
     */
    public function getCompany(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:company_info,id'
        ]);

        $company = CompanyInfo::findOrFail($request->company_id);

        return response()->json($company);
    }

    /**
     * Update company info.
     */
    public function update(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:company_info,id',
            'company_name' => 'required|string|max:500',
            'company_description' => 'nullable|string',
            'status' => 'required|in:gathered,active,inactive',
            'type' => 'nullable|string|max:50',
        ]);

        try {
            $company = CompanyInfo::findOrFail($request->company_id);
            
            $company->update([
                'company_name' => $request->company_name,
                'company_description' => $request->company_description,
                'status' => $request->status,
                'type' => $request->type,
            ]);

            // Log the action
            AdminActivityLog::create([
                'admin_id' => auth()->id(),
                'action' => 'company_info_updated',
                'target_type' => 'CompanyInfo',
                'target_id' => $company->id,
                'description' => "Updated company info: {$company->company_name}",
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Company info updated successfully!',
                'company' => $company
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating company info: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update company info: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete company info.
     */
    public function delete(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:company_info,id'
        ]);

        try {
            $company = CompanyInfo::findOrFail($request->company_id);
            $companyName = $company->company_name;
            
            $company->delete();

            // Log the action
            AdminActivityLog::create([
                'admin_id' => auth()->id(),
                'action' => 'company_info_deleted',
                'target_type' => 'CompanyInfo',
                'target_id' => null,
                'description' => "Deleted company info: {$companyName}",
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Company info deleted successfully!'
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting company info: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete company info: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check progress of company generation job.
     */
    public function checkProgress(Request $request)
    {
        $progressKey = $request->input('progress_key');
        
        if (!$progressKey) {
            return response()->json([
                'success' => false,
                'message' => 'Progress key is required',
            ], 400);
        }

        $progress = Cache::get($progressKey);

        if (!$progress) {
            return response()->json([
                'success' => false,
                'message' => 'Progress data not found. Job may have expired or not started.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'progress' => $progress,
        ]);
    }

    /**
     * Generate company info from job posts using AI (dispatches background job).
     * 
     * This method:
     * 1. Extracts unique company names from job_posts table
     * 2. Dispatches a background job to process them with AI
     * 3. Returns a progress key for tracking
     */
    public function generateFromJobPosts(Request $request)
    {
        Log::info('=== Generate Company Info Started ===', [
            'env' => app()->environment(),
            'queue' => config('queue.default'),
            'user_id' => auth()->id(),
            'php' => PHP_VERSION,
        ]);

        try {
            // ---- GitHub token check
            $githubToken = config('services.github.token');
            Log::info('GitHub token check', [
                'exists' => !empty($githubToken),
                'length' => $githubToken ? strlen($githubToken) : 0,
            ]);

            if (!$githubToken) {
                return response()->json([
                    'success' => false,
                    'message' => 'GitHub token not configured.',
                ], 500);
            }

            // ---- Fetch companies
            Log::info('Querying unique companies');

            $uniqueCompanies = JobPost::select('company_name')
                ->whereNotNull('company_name')
                ->where('company_name', '!=', '')
                ->whereNotIn('company_name', function ($query) {
                    $query->select('company_name')
                        ->from('company_info')
                        ->whereNotNull('company_name');
                })
                ->distinct()
                ->pluck('company_name')
                ->toArray();

            Log::info('Company query completed', [
                'count' => count($uniqueCompanies),
                'companies' => $uniqueCompanies,
            ]);

            if (empty($uniqueCompanies)) {
                return response()->json([
                    'success' => true,
                    'message' => 'No new companies to process.',
                    'total_companies' => 0,
                ]);
            }

            // ---- Progress key
            $progressKey = 'company_generation_progress_' . auth()->id() . '_' . time();
            Log::info('Generated progress key', ['key' => $progressKey]);

            // ---- Cache write test
            Cache::put($progressKey, [
                'status' => 'queued',
                'total' => count($uniqueCompanies),
                'processed' => 0,
                'created' => 0,
                'skipped' => 0,
                'linked' => 0,
                'percentage' => 0,
                'message' => 'Job queued and starting...',
            ], now()->addHours(1));

            Log::info('Cache initialized for progress key');

            // ---- Dispatch job
            Log::info('Dispatching GenerateCompanyInfoJob', [
                'job_class' => \App\Jobs\GenerateCompanyInfoJob::class,
                'queue_connection' => config('queue.default'),
            ]);

            dispatch(new \App\Jobs\GenerateCompanyInfoJob(
                $uniqueCompanies,
                $githubToken,
                $progressKey
            ));

            Log::info('Job dispatch call completed', [
                'progress_key' => $progressKey,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Company generation job started!',
                'progress_key' => $progressKey,
                'total_companies' => count($uniqueCompanies),
            ]);

        } catch (\Throwable $e) {
            Log::error('GenerateCompanyInfo FAILED', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to generate company info',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Generate company description using AI.
     */
    private function generateCompanyDescription($companyName, $githubToken)
    {
        try {
            $prompt = "Generate a brief, professional 2-3 sentence description for a company named \"{$companyName}\". " .
                      "Focus on what they likely do based on their name. Keep it general and professional. " .
                      "Return ONLY the description text, no quotes or extra formatting.";

            $client = new \GuzzleHttp\Client();
            $response = $client->post("https://models.inference.ai.azure.com/chat/completions", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $githubToken,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => config('services.github.model', 'gpt-4o'),
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'You are a professional business analyst. Generate concise company descriptions.'
                        ],
                        [
                            'role' => 'user',
                            'content' => $prompt
                        ]
                    ],
                    'max_tokens' => 150,
                    'temperature' => 0.7,
                ],
                'timeout' => 15,
            ]);

            $result = json_decode($response->getBody()->getContents(), true);
            $description = $result['choices'][0]['message']['content'] ?? '';

            // Clean up the description
            $description = trim($description);
            $description = trim($description, '"\'');

            Log::info("Generated description for {$companyName}: " . substr($description, 0, 100));

            return $description ?: "Company information to be updated.";

        } catch (\Exception $e) {
            Log::error("Failed to generate description for {$companyName}: " . $e->getMessage());
            return "Company information to be updated.";
        }
    }

    /**
     * Update existing company records with website and social media information.
     * Processes in batches of 20 to handle API rate limits (24 requests per 60 seconds).
     */
    public function updateWebsiteAndSocials(Request $request)
    {
        Log::info('=== Update Website and Socials Started ===');

        try {
            $githubToken = config('services.github.token');
            if (!$githubToken) {
                return response()->json([
                    'success' => false,
                    'message' => 'GitHub token not configured. Cannot update records.',
                ], 500);
            }

            // Get batch number from request (default to 1 for first batch)
            $batchNumber = $request->input('batch', 1);
            $batchSize = 10; // Process 10 companies at a time (safer for rate limits)

            // Get all company IDs that need updates (where website is null or empty)
            $allCompanyIds = CompanyInfo::whereNull('company_website')
                ->orWhere('company_website', '')
                ->pluck('id')
                ->toArray();

            $totalCompanies = count($allCompanyIds);
            Log::info('Found ' . $totalCompanies . ' total companies needing updates');

            if (empty($allCompanyIds)) {
                return response()->json([
                    'success' => true,
                    'message' => 'No companies need updates. All companies already have website information.',
                    'total_companies' => 0,
                    'total_batches' => 0,
                ]);
            }

            // Calculate batch details
            $totalBatches = ceil($totalCompanies / $batchSize);
            $offset = ($batchNumber - 1) * $batchSize;
            
            // Get company IDs for this specific batch
            $batchCompanyIds = array_slice($allCompanyIds, $offset, $batchSize);
            $batchCompanyCount = count($batchCompanyIds);

            Log::info("Processing batch {$batchNumber}/{$totalBatches} with {$batchCompanyCount} companies");

            if (empty($batchCompanyIds)) {
                return response()->json([
                    'success' => true,
                    'message' => "Batch {$batchNumber} is empty. All companies processed.",
                    'total_companies' => $totalCompanies,
                    'current_batch' => $batchNumber,
                    'total_batches' => $totalBatches,
                    'batch_complete' => true,
                ]);
            }

            // Generate unique progress key for this job
            $progressKey = 'company_update_progress_' . auth()->id() . '_' . time();

            // Initialize progress in cache with batch information
            Cache::put($progressKey, [
                'status' => 'queued',
                'total' => $batchCompanyCount,
                'processed' => 0,
                'updated' => 0,
                'skipped' => 0,
                'percentage' => 0,
                'current_batch' => $batchNumber,
                'total_batches' => $totalBatches,
                'total_companies' => $totalCompanies,
                'message' => "Batch {$batchNumber}/{$totalBatches} queued and starting...",
            ], now()->addHours(1));

            // Dispatch the job to background queue with only this batch
            \App\Jobs\UpdateCompanyWebsiteAndSocialsJob::dispatch($batchCompanyIds, $githubToken, $progressKey);

            Log::info("Update job dispatched with progress key: {$progressKey}");

            return response()->json([
                'success' => true,
                'message' => "Batch {$batchNumber}/{$totalBatches} started!",
                'progress_key' => $progressKey,
                'batch_size' => $batchCompanyCount,
                'current_batch' => $batchNumber,
                'total_batches' => $totalBatches,
                'total_companies' => $totalCompanies,
                'has_more_batches' => $batchNumber < $totalBatches,
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating company info: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Failed to update company info: ' . $e->getMessage()
            ], 500);
        }
    }

    public function stopUpdateJob(Request $request)
    {
        $progressKey = $request->input('progress_key');
        
        if (!$progressKey) {
            return response()->json(['error' => 'Progress key is required'], 400);
        }

        // Get current progress and set stop flag
        $progress = Cache::get($progressKey, []);
        $progress['stop_requested'] = true;
        Cache::put($progressKey, $progress, now()->addHours(1));

        Log::info("Stop requested for job with progress key: {$progressKey}");

        return response()->json(['success' => true, 'message' => 'Stop request sent']);
    }

    /**
     * Show public company profile page.
     */
    public function show($companySlug)
    {
        // Try to find company by exact name match or slug-like match
        $companyName = str_replace('-', ' ', $companySlug);
        
        $company = CompanyInfo::where('company_name', 'like', $companyName)
            ->orWhere('company_name', 'like', str_replace('-', '%', $companySlug))
            ->first();

        // If not found, try more flexible search
        if (!$company) {
            $company = CompanyInfo::whereRaw('LOWER(REPLACE(company_name, " ", "-")) = ?', [strtolower($companySlug)])
                ->first();
        }

        if (!$company) {
            abort(404, 'Company not found');
        }

        // Get a company logo from one of their job posts (if available)
        $companyLogo = null;
        $jobWithLogo = JobPost::where('company_name', $company->company_name)
            ->where('status', 'COMMITTED')
            ->first();

        if ($jobWithLogo) {
            // Check for uploaded image using FilePostStored model
            $imageFile = FilePostStored::where('post_id', $jobWithLogo->id)
                ->where('type', 'job_post')
                ->where('status', 'active')
                ->first();
            
            if ($imageFile) {
                $companyLogo = asset('storage/store_data/posts/draft/' . $imageFile->foldername . '/' . $imageFile->filename);
            } elseif ($jobWithLogo->company_logo) {
                // Fallback to company_logo field if exists
                $companyLogo = $jobWithLogo->company_logo;
            }
        }

        return Inertia::render('Front/CompanyAbout', [
            'company' => array_merge($company->toArray(), [
                'logo' => $companyLogo,
                'id' => $company->id
            ]),
            'auth' => [
                'user' => auth()->user(),
            ],
        ]);
    }

    /**
     * Restart the queue worker.
     */
    public function restartQueue()
    {
        try {
            // Check if we're on Windows or Unix
            $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
            
            if ($isWindows) {
                // Windows: Kill existing php artisan queue:work processes
                exec('taskkill /F /IM php.exe /FI "WINDOWTITLE eq *queue:work*" 2>&1', $killOutput, $killReturnCode);
                
                // Wait a moment
                sleep(1);
                
                // Start new queue worker in background
                $command = 'start /B php ' . base_path('artisan') . ' queue:work --tries=3 --timeout=300 > NUL 2>&1';
                pclose(popen($command, 'r'));
                
                Log::info('Queue worker restarted (Windows)', [
                    'kill_output' => $killOutput,
                    'kill_return_code' => $killReturnCode
                ]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Queue worker restarted successfully. New worker is now running in the background.'
                ]);
            } else {
                // Unix/Linux: Use artisan queue:restart command
                Artisan::call('queue:restart');
                
                Log::info('Queue restart signal sent (Unix/Linux)');
                
                return response()->json([
                    'success' => true,
                    'message' => 'Queue restart signal sent. Workers will gracefully restart after finishing their current jobs.'
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error restarting queue', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Track social media click.
     */
    public function trackSocialClick(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:company_info,id',
            'social_index' => 'required|integer|in:1,2,3'
        ]);

        try {
            $company = CompanyInfo::findOrFail($request->company_id);
            $field = 'social_clicks_' . $request->social_index;
            
            $company->increment($field);

            return response()->json([
                'success' => true,
                'message' => 'Social click tracked'
            ]);
        } catch (\Exception $e) {
            Log::error('Error tracking social click: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error tracking click'
            ], 500);
        }
    }

    /**
     * Track search click.
     */
    public function trackSearchClick(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:company_info,id'
        ]);

        try {
            $company = CompanyInfo::findOrFail($request->company_id);
            $company->increment('search_click');

            return response()->json([
                'success' => true,
                'message' => 'Search click tracked'
            ]);
        } catch (\Exception $e) {
            Log::error('Error tracking search click: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error tracking click'
            ], 500);
        }
    }
}
