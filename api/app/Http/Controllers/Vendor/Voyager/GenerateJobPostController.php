<?php

namespace App\Http\Controllers\Vendor\Voyager;

use App\Http\Controllers\Controller;
use App\Models\Posts\JobPost;
use App\Models\Posts\CompanyInfo;
use App\Models\Core\FilePostStored;
use App\Jobs\GenerateCompanyInfoJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Carbon\Carbon;

class GenerateJobPostController extends Controller
{
    /**
     * Display the job post generator page
     */
    public function index()
    {
        return Inertia::render('Admin/ContentManagement/GenerateJobPost', [
            'auth' => [
                'user' => auth()->user()
            ]
        ]);
    }

    /**
     * Test AI connection
     */
    public function testAiConnection(Request $request)
    {
        Log::info('=== AI Connection Test Started ===');
        
        $githubToken = config('services.github.token');
        Log::info('GitHub token exists: ' . ($githubToken ? 'YES' : 'NO'));
        Log::info('Token length: ' . ($githubToken ? strlen($githubToken) : 0));
        Log::info('Token prefix: ' . ($githubToken ? substr($githubToken, 0, 20) . '...' : 'NONE'));
        
        if (!$githubToken) {
            Log::warning('GitHub token not configured in services.github.token');
            return response()->json([
                'success' => false,
                'message' => '❌ GitHub token not configured',
                'details' => 'Please add GITHUB_TOKEN to your .env file',
                'status' => 'not_configured',
                'error' => 'No token found in config(services.github.token) or env(GITHUB_TOKEN)'
            ]);
        }

        try {
            $model = $request->input('model', config('services.github.model', 'gpt-4o'));
            Log::info('Using model: ' . $model);
            $endpoint = "https://models.inference.ai.azure.com/chat/completions";
            Log::info('API Endpoint: ' . $endpoint);
            $client = new \GuzzleHttp\Client();
            Log::info('Sending request to GitHub Models API...');
            $response = $client->post($endpoint, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $githubToken,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => $model,
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => 'Respond with: AI connection successful'
                        ]
                    ],
                    'max_tokens' => (int) 50,
                ],
                'timeout' => 10,
            ]);
            Log::info('Response received, status: ' . $response->getStatusCode());
            $result = json_decode($response->getBody()->getContents(), true);
            $aiResponse = $result['choices'][0]['message']['content'] ?? '';
            $tokenInfo = $result['usage'] ?? null;
            $tokensLeft = $tokenInfo['tokens_left'] ?? null;
            $resetTime = $tokenInfo['reset_time'] ?? null;
            Log::info('AI Response: ' . $aiResponse);
            Log::info('=== AI Connection Test SUCCESS ===');
            return response()->json([
                'success' => true,
                'message' => '✅ AI Connection Successful!',
                'details' => "Model: {$model}",
                'ai_response' => $aiResponse,
                'status' => 'connected',
                'tokens_left' => $tokensLeft,
                'reset_time' => $resetTime,
                'current_model' => $model,
            ]);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $statusCode = $e->getResponse()->getStatusCode();
            $errorBody = $e->getResponse()->getBody()->getContents();
            Log::error('=== AI Connection Test FAILED (ClientException) ===');
            Log::error('Status Code: ' . $statusCode);
            Log::error('Error Body: ' . $errorBody);
            $errorJson = json_decode($errorBody, true);
            $tokensLeft = $errorJson['usage']['tokens_left'] ?? null;
            $resetTime = $errorJson['usage']['reset_time'] ?? null;
            $errorMsg = $errorJson['error']['message'] ?? $errorBody;
            if ($statusCode === 401) {
                Log::error('Authentication failed - Invalid token or missing scope');
                return response()->json([
                    'success' => false,
                    'message' => '❌ Authentication Failed',
                    'details' => 'Invalid GitHub token or missing "model" scope. Generate a new token at: https://github.com/settings/tokens',
                    'status' => 'unauthorized',
                    'error' => $errorMsg,
                    'tokens_left' => $tokensLeft,
                    'reset_time' => $resetTime,
                ]);
            } elseif ($statusCode === 404) {
                Log::error('Model not found: ' . $model);
                return response()->json([
                    'success' => false,
                    'message' => '❌ Model Not Found',
                    'details' => 'The specified model is not available. Try: gpt-4o, gpt-4o-mini, or claude-3-5-sonnet-20241022',
                    'status' => 'model_not_found',
                    'current_model' => $model,
                    'tokens_left' => $tokensLeft,
                    'reset_time' => $resetTime,
                    'error' => $errorMsg,
                ]);
            } elseif ($statusCode === 429) {
                // Rate limit or quota exceeded
                $waitSeconds = $resetTime ? max(0, strtotime($resetTime) - time()) : null;
                $waitHours = $waitSeconds ? round($waitSeconds / 3600, 2) : null;
                return response()->json([
                    'success' => false,
                    'message' => '❌ Model Limit Reached',
                    'details' => 'You have reached the usage limit for this model. Please wait before trying again.',
                    'status' => 'limit_reached',
                    'tokens_left' => $tokensLeft,
                    'reset_time' => $resetTime,
                    'wait_seconds' => $waitSeconds,
                    'wait_hours' => $waitHours,
                    'error' => $errorMsg,
                ]);
            } else {
                Log::error('API Error with status: ' . $statusCode);
                return response()->json([
                    'success' => false,
                    'message' => '❌ API Error',
                    'details' => "Status: {$statusCode}",
                    'status' => 'api_error',
                    'error' => $errorMsg,
                    'tokens_left' => $tokensLeft,
                    'reset_time' => $resetTime,
                ]);
            }
        } catch (\Exception $e) {
            Log::error('=== AI Connection Test FAILED (Exception) ===');
            Log::error('Exception: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => '❌ Connection Failed',
                'details' => $e->getMessage(),
                'status' => 'error'
            ]);
        }
    }

    /**
     * Generate complete job post with AI - auto-populates all fields
     * This is the main generation endpoint that extracts all data from raw description
     */
    public function generateJobPost(Request $request)
    {
        $request->validate([
            'raw_description' => 'required|string|min:50',
            'model' => 'nullable|string',
        ]);

        try {
            $rawDescription = $request->input('raw_description');
            
            Log::info('=== Generate Job Post Started ===');
            Log::info('Raw description length: ' . strlen($rawDescription));
            Log::info('Raw description contains HTML: ' . (strpos($rawDescription, '<') !== false ? 'YES' : 'NO'));
            
            // CRITICAL DEBUG: Check GitHub token at the very start
            Log::info('=== GITHUB TOKEN DEBUG ===');
            $envToken = env('GITHUB_TOKEN');
            $configToken = config('services.github.token');
            Log::info('env(GITHUB_TOKEN) exists: ' . ($envToken ? 'YES' : 'NO'));
            Log::info('env(GITHUB_TOKEN) length: ' . ($envToken ? strlen($envToken) : 0));
            Log::info('config(services.github.token) exists: ' . ($configToken ? 'YES' : 'NO'));
            Log::info('config(services.github.token) length: ' . ($configToken ? strlen($configToken) : 0));
            Log::info('config(services.github.model): ' . config('services.github.model', 'NOT SET'));
            Log::info('=== END GITHUB TOKEN DEBUG ===');
            
            // AGGRESSIVE HTML cleaning before sending to AI
            
            // First, try to extract just the text content using DOMDocument
            $cleanText = $rawDescription;
            
            // If it's HTML, use DOMDocument to properly extract text
            if (strpos($rawDescription, '<') !== false) {
                libxml_use_internal_errors(true); // Suppress HTML parsing warnings
                $dom = new \DOMDocument();
                $dom->loadHTML('<?xml encoding="UTF-8">' . $rawDescription, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
                $cleanText = $dom->textContent ?? '';
                libxml_clear_errors();
            }
            
            // Fallback: strip_tags if DOMDocument didn't work
            if (empty($cleanText) || strlen($cleanText) < 50) {
                $cleanText = strip_tags($rawDescription);
            }
            
            // Decode HTML entities
            $cleanText = html_entity_decode($cleanText, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            
            // Remove excessive whitespace, newlines, tabs
            $cleanText = preg_replace('/[\r\n\t]+/', ' ', $cleanText);
            $cleanText = preg_replace('/\s+/', ' ', $cleanText);
            $cleanText = trim($cleanText);
            
            Log::info('=== TEXT CLEANING ===');
            Log::info('Original length: ' . strlen($rawDescription) . ' | Contains HTML: ' . (strpos($rawDescription, '<') !== false ? 'YES' : 'NO'));
            Log::info('Cleaned text length: ' . strlen($cleanText));
            Log::info('Cleaned text preview (first 400 chars): ' . substr($cleanText, 0, 400));
            Log::info('Clean text still contains HTML tags: ' . (strpos($cleanText, '<') !== false ? 'YES - PROBLEM!' : 'NO - Good'));
            Log::info('=== END TEXT CLEANING ===');
            
            // Validate cleaned text
            if (strlen($cleanText) < 50) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please enter actual job description content (minimum 50 characters of text).'
                ], 400);
            }
            
            // Call GitHub Copilot API to generate structured job post data
            // Pass the cleanText instead of rawDescription
            $aiResponse = $this->callGitHubCopilotApi($cleanText, $request->input('model'));
            
            if (!$aiResponse) {
                Log::warning('AI API returned null, using fallback extraction');
                // Fallback to template-based extraction if API fails
                $aiResponse = $this->extractFieldsFromRawDescription($rawDescription);
            } else {
                Log::info('AI API returned data successfully');
                Log::info('Extracted company_name: ' . ($aiResponse['company_name'] ?? 'N/A'));
                Log::info('Extracted position: ' . ($aiResponse['position'] ?? 'N/A'));
                Log::info('Extracted title: ' . ($aiResponse['title'] ?? 'N/A'));
            }

            return response()->json([
                'success' => true,
                'data' => $aiResponse,
                'message' => 'Job post generated successfully. Review and edit fields as needed before saving.'
            ]);

        } catch (\Exception $e) {
            Log::error('Error generating job post: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate job post: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Call GitHub Models API to generate job post data
     * Uses your GitHub Copilot subscription
     */
    private function callGitHubCopilotApi($rawDescription, $modelOverride = null)
    {
        Log::info('=== callGitHubCopilotApi START ===');
        
        // Debug both ways to get the token
        $githubToken = config('services.github.token');
        $directEnv = env('GITHUB_TOKEN');
        
        Log::info('INSIDE callGitHubCopilotApi:');
        Log::info('  config(services.github.token) result: ' . ($githubToken ? 'EXISTS' : 'NULL/EMPTY'));
        Log::info('  config(services.github.token) length: ' . ($githubToken ? strlen($githubToken) : 0));
        Log::info('  env(GITHUB_TOKEN) result: ' . ($directEnv ? 'EXISTS' : 'NULL/EMPTY'));
        Log::info('  env(GITHUB_TOKEN) length: ' . ($directEnv ? strlen($directEnv) : 0));
        
        if (!$githubToken) {
            Log::error('GitHub token not configured. Set GITHUB_TOKEN in .env file.');
            Log::error('Get your token at: https://github.com/settings/tokens (requires "model" scope)');
            Log::error('CRITICAL: config(services.github.token) returned empty but env(GITHUB_TOKEN) = ' . ($directEnv ? 'EXISTS' : 'EMPTY'));
            return null;
        }

        Log::info('GitHub token found, length: ' . strlen($githubToken));
        Log::info('Token starts with: ' . substr($githubToken, 0, 15) . '...');

        try {
            $prompt = $this->buildAiPrompt($rawDescription);
            $model = $modelOverride ?: config('services.github.model', 'gpt-4o');
            Log::info('Using model: ' . $model);
            Log::info('Max tokens: ' . config('services.github.max_tokens', 2000));
            Log::info('Sending request to GitHub Models API...');
            $client = new \GuzzleHttp\Client();
            $response = $client->post("https://models.inference.ai.azure.com/chat/completions", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $githubToken,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => $model,
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'You are an expert job posting assistant. Extract structured data from job descriptions and return ONLY valid JSON, no markdown formatting, no explanations.'
                        ],
                        [
                            'role' => 'user',
                            'content' => $prompt
                        ]
                    ],
                    'max_tokens' => (int) config('services.github.max_tokens', 2000),
                    'temperature' => 0.7,
                    'response_format' => ['type' => 'json_object'],
                ],
                'timeout' => 30,
            ]);
            Log::info('API Response Status: ' . $response->getStatusCode());
            $result = json_decode($response->getBody()->getContents(), true);
            $aiContent = $result['choices'][0]['message']['content'] ?? '';
            Log::info('Raw AI response length: ' . strlen($aiContent));
            Log::info('Raw AI response preview: ' . substr($aiContent, 0, 500));
            $parsed = $this->parseAiJsonResponse($aiContent);
            if ($parsed) {
                Log::info('Successfully parsed AI response');
                Log::info('Parsed data keys: ' . implode(', ', array_keys($parsed)));
            } else {
                Log::error('Failed to parse AI response');
            }
            Log::info('=== callGitHubCopilotApi END ===');
            return $parsed;

        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $statusCode = $e->getResponse()->getStatusCode();
            $errorBody = $e->getResponse()->getBody()->getContents();
            
            Log::error('=== GitHub Models API ClientException ===');
            Log::error('Status Code: ' . $statusCode);
            Log::error('Error Response: ' . $errorBody);
            
            return null;
            
        } catch (\Exception $e) {
            Log::error('=== GitHub Models API Exception ===');
            Log::error('Exception type: ' . get_class($e));
            Log::error('Error message: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return null;
        }
    }

    /**
     * Build AI prompt for job post generation
     * Note: $rawDescription should already be cleaned (HTML stripped) before calling this
     */
    private function buildAiPrompt($rawDescription)
    {
        Log::info('Building AI prompt with text: ' . substr($rawDescription, 0, 200));
        
        return <<<PROMPT
You are a job posting extraction expert with deep knowledge of USA salary data. Analyze the following job posting text and extract structured data.

CRITICAL EXTRACTION RULES:

1. **Company name**: Usually at the top or in headers, NOT in the position title (e.g., "W3Global", "Microsoft", "Google")

2. **Position**: The job role ONLY (e.g., "Software Engineer", "Marketing Manager", "Data Analyst")

3. **Title**: The full job posting title (e.g., "Senior Software Engineer - Remote", "Marketing Manager - Full Time")

4. **Location**: Look for "Remote", "Vancouver, BC", "San Francisco, CA", etc.

5. **SALARY - ALWAYS REQUIRED**:
   - If salary range is provided (e.g., "\$50,000 - \$70,000"), use those exact values
   - If only ONE salary is provided:
     * For HOURLY: max = given amount, min = given amount - \$1 (e.g., given \$25/hr → min: 24, max: 25)
     * For YEARLY: max = given amount, min = given amount - \$5000 (e.g., given \$60,000 → min: 55000, max: 60000)
   - If NO salary mentioned, research and use USA national average for the position:
     * For HOURLY positions: Use national hourly average ± \$1
     * For YEARLY positions: Use national yearly average with realistic range (±10%)
   - NEVER leave salary_min or salary_max as null - ALWAYS provide realistic estimates

6. **Skills** (use spaces and lowercase, NOT underscores):
   - Format: ["skill name one", "skill name two"]
   - Example: ["motivational interviewing", "patient education", "device usage monitoring", "emr documentation", "hipaa compliance", "cross functional collaboration", "technical aptitude for device troubleshooting"]
   - Include tools, technologies, certifications, and key competencies
   - Multi-word skills MUST use SPACES (e.g., "patient education" not "patient_education")

7. **Secondary Tags** (use spaces and proper capitalization, NOT underscores):
   - Format: ["Proper Case Tag", "Another Tag"]
   - Example: ["Leadership Development", "Personal Growth", "Coaching", "Remote Business", "Career Change", "Educator Pivot"]
   - Extract 3-6 relevant categorical tags
   - Multi-word tags use SPACES (e.g., "Leadership Development" not "Leadership_Development")

8. **Meta Description** (use spaces and proper punctuation):
   - Write a compelling 150-160 character summary
   - Example: "Remote Full-time Patient Care Coach at AdaptHealth. Supports patient therapy adherence for DME. Requires healthcare administrative/customer service background and HIPAA knowledge."
   - Use proper sentences with spaces and punctuation

9. **Meta Keywords** (comma-separated with spaces, proper capitalization):
   - Format: "Keyword One, Keyword Two, Keyword Three"
   - Example: "Patient Care Coach, Remote Healthcare, AdaptHealth, DME, Therapy Adherence, Motivational Interviewing, EMR, HIPAA, Sleep Apnea, Diabetes"
   - 8-12 keywords, comma-separated with spaces between words

10. **Position** (the job role title only, use spaces):
   - Example: "Patient Care Coach", "Software Engineer", "Marketing Manager"
   - NOT the full title, just the core role name
   - Use proper capitalization and spaces

Return ONLY a valid JSON object with these exact fields (PLAIN TEXT except job_description):

{
  "company_name": "Company Name (plain text with spaces)",
  "position": "Patient Care Coach (job role with spaces, NOT underscores)",
  "title": "Full Job Title - Location Type (plain text with spaces)",
  "job_description": "HTML with Tailwind CSS classes",
  "apply_url": "Plain text URL",
  "location_type": "Remote, Hybrid, or Onsite",
  "location_country": "USA, Canada, UK, etc.",
  "location_state_province": "State/province abbreviation",
  "location_city": "City name",
  "employer_type": "Full-Time, Part-Time, Contract, Temporary, or Internship",
  "currency": "USD, CAD, EUR, etc.",
  "salary_min": REQUIRED - numeric value (never null),
  "salary_max": REQUIRED - numeric value (never null),
  "payment_frequency": "Yearly, Monthly, Weekly, or Hourly",
  "primary_tag": "Main Category (with spaces)",
  "secondary_tags": ["Leadership Development", "Personal Growth", "Remote Business"],
  "skills": ["motivational interviewing", "patient education", "emr documentation"],
  "benefits": ["Health Insurance", "Remote Work", "Flexible Schedule"],
  "meta_description": "Remote Full-time Patient Care Coach at AdaptHealth. Supports patient therapy adherence for DME.",
  "meta_keywords": "Patient Care Coach, Remote Healthcare, AdaptHealth, DME, Therapy Adherence, Motivational Interviewing"
}

For job_description HTML:
- Use Tailwind CSS classes (text-gray-700, font-bold, mb-4, etc.)
- Structure: Overview, Responsibilities, Requirements, Benefits sections
- Use proper semantic HTML (h2, ul, li, p tags)
- Keep it clean and professional

Raw Job Posting Text:
$rawDescription

Return ONLY the JSON object with no markdown formatting or extra text.
PROMPT;
    }

    /**
     * Parse AI JSON response into structured data
     * Strips HTML from all fields except job_description
     */
    private function parseAiJsonResponse($aiContent)
    {
        // Extract JSON from AI response (it might have markdown code blocks)
        $jsonMatch = null;
        if (preg_match('/\{[\s\S]*\}/', $aiContent, $jsonMatch)) {
            $jsonStr = $jsonMatch[0];
            $data = json_decode($jsonStr, true);
            
            if ($data) {
                // Strip HTML tags from all fields except job_description
                $cleanData = [];
                foreach ($data as $key => $value) {
                    if ($key === 'job_description') {
                        // Keep HTML for job_description field
                        $cleanData[$key] = $value;
                    } elseif (is_string($value)) {
                        // Strip HTML tags from string fields
                        $cleanData[$key] = strip_tags($value);
                        // Also clean up excessive whitespace
                        $cleanData[$key] = preg_replace('/\s+/', ' ', $cleanData[$key]);
                        $cleanData[$key] = trim($cleanData[$key]);
                    } elseif (is_array($value)) {
                        // Strip HTML from array values
                        $cleanData[$key] = array_map(function($item) {
                            if (is_string($item)) {
                                $cleaned = strip_tags($item);
                                $cleaned = preg_replace('/\s+/', ' ', $cleaned);
                                return trim($cleaned);
                            }
                            return $item;
                        }, $value);
                    } else {
                        // Keep non-string values as-is (numbers, booleans, etc.)
                        $cleanData[$key] = $value;
                    }
                }
                
                Log::info('Cleaned data - company_name: ' . ($cleanData['company_name'] ?? 'N/A'));
                Log::info('Cleaned data - position: ' . ($cleanData['position'] ?? 'N/A'));
                Log::info('Cleaned data - title: ' . ($cleanData['title'] ?? 'N/A'));
                
                return $cleanData;
            }
        }
        
        return null;
    }

    /**
     * Fallback method to extract fields without AI API
     */
    private function extractFieldsFromRawDescription($rawDescription)
    {
        $lines = explode("\n", $rawDescription);
        
        return [
            'company_name' => $this->extractCompanyName($rawDescription) ?: 'Company Name',
            'position' => $this->extractPosition($rawDescription) ?: 'Position',
            'title' => $this->extractTitle($rawDescription) ?: 'Job Title',
            'job_description' => $this->formatJobDescription($rawDescription, 'the company', 'this position'),
            'apply_url' => '',
            'location_type' => $this->extractLocationType($rawDescription),
            'location_country' => 'USA',
            'location_state_province' => '',
            'location_city' => '',
            'employer_type' => $this->extractEmployerType($rawDescription),
            'currency' => 'USD',
            'salary_min' => $this->extractSalaryRange($rawDescription)['min'],
            'salary_max' => $this->extractSalaryRange($rawDescription)['max'],
            'payment_frequency' => 'Yearly',
            'primary_tag' => 'Other',
            'secondary_tags' => [],
            'skills' => $this->extractSkills($rawDescription),
            'benefits' => $this->extractBenefits($rawDescription),
            'meta_description' => substr(strip_tags($rawDescription), 0, 160),
            'meta_keywords' => implode(', ', array_slice($this->extractSkills($rawDescription), 0, 5)),
        ];
    }

    /**
     * Generate AI-enhanced job description from raw input
     * This endpoint can be called to get AI-generated content before submitting
     * @deprecated Use generateJobPost instead
     */
    public function generateDescription(Request $request)
    {
        $request->validate([
            'raw_description' => 'required|string|min:50',
            'company_name' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
        ]);

        try {
            $rawDescription = $request->input('raw_description');
            $companyName = $request->input('company_name', 'the company');
            $position = $request->input('position', 'this position');

            $aiGeneratedDescription = $this->formatJobDescription($rawDescription, $companyName, $position);

            return response()->json([
                'success' => true,
                'generated_description' => $aiGeneratedDescription,
                'message' => 'Job description generated successfully. You can edit it before submitting.'
            ]);

        } catch (\Exception $e) {
            Log::error('Error generating job description: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate job description: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a generated job post
     */
    public function store(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'raw_description' => 'required|string',
            'job_description' => 'required|string',
            'apply_url' => 'required|url|max:5000',
            'company_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:10240',
            
            // Additional fields
            'location_type' => 'nullable|string|max:50',
            'location_country' => 'nullable|string|max:100',
            'location_state_province' => 'nullable|string|max:100',
            'location_city' => 'nullable|string|max:100',
            'employer_type' => 'nullable|string|max:50',
            'currency' => 'nullable|string|max:10',
            'salary_min' => 'nullable|numeric|min:0',
            'salary_max' => 'nullable|numeric|min:0',
            'payment_frequency' => 'nullable|string|max:50',
            'primary_tag' => 'nullable|string|max:100',
            'secondary_tags' => 'nullable|array',
            'skills' => 'nullable|array',
            'benefits' => 'nullable|array',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();

        try {
            // Generate slug from title using the same method as ManageJobPostsController
            $slug = $this->createSlugFromTitle($request->input('title'));

            // Set expires_at to 90 days from now
            $expiresAt = Carbon::now()->addDays(90)->endOfDay();

            // Get location data
            $locationCountry = $request->input('location_country', 'USA');
            $locationState = $request->input('location_state_province');
            $locationCity = $request->input('location_city');
            $locationType = $request->input('location_type', 'Remote');
            
            // Normalize "United States" to "USA"
            if ($locationCountry === 'United States' || $locationCountry === 'US') {
                $locationCountry = 'USA';
            }
            
            // Format location_restriction based on location data
            // ALWAYS set location_restriction (even for Remote positions)
            $locationRestriction = '';
            if ($locationCountry) {
                if ($locationCountry === 'Canada' || $locationCountry === 'USA') {
                    if ($locationCity && $locationState) {
                        // Format: "Toronto, ON, Canada" or "San Antonio, TX, USA"
                        $locationRestriction = $locationCity . ', ' . $locationState . ', ' . $locationCountry;
                    } elseif ($locationState) {
                        // Only state provided: "Ontario, Canada" or "Texas, USA"
                        $locationRestriction = $locationState . ', ' . $locationCountry;
                    } else {
                        // Only country provided
                        $locationRestriction = $locationCountry;
                    }
                } else {
                    // For other countries - just use the country name
                    $locationRestriction = $locationCountry;
                }
            }
            
            // Fallback: if location_restriction is still empty, default to USA
            if (empty($locationRestriction)) {
                $locationRestriction = 'USA';
            }

            // Prepare job post data matching exact column order
            $jobData = [
                'author_id' => 6, // Admin user
                'order_id' => null,
                'category_id' => null,
                'draft_id' => $slug,
                'expires_at' => $expiresAt,
                'updated_at' => Carbon::now(),
                'created_at' => Carbon::now(),
                'company_name' => $request->input('company_name'),
                'title' => $request->input('title'),
                'job_description' => $request->input('job_description'),
                'slug' => $slug,
                'status' => 'COMMITTED',
                'position' => $request->input('position'),
                'apply_url' => $request->input('apply_url'),
                
                // Location fields
                'location_type' => $locationType,
                'location_restriction' => $locationRestriction,
                'location_country' => $locationCountry,
                'location_state_province' => $locationState,
                'location_city' => $locationCity,
                
                // Benefits and body
                'benefits' => $request->input('benefits', []), // Cast handles JSON encoding
                'body' => null,
                'featured' => 0,
                
                // Employment details
                'employer_type' => $request->input('employer_type', 'Full-Time'),
                'currency' => $request->input('currency', 'USD'),
                'salary_min' => $request->input('salary_min'),
                'salary_max' => $request->input('salary_max'),
                'payment_frequency' => $request->input('payment_frequency', 'Hourly'),
                
                // Tags and metadata
                'primary_tag' => $request->input('primary_tag', 'Other'),
                'secondary_tags' => $request->input('secondary_tags', []), // Cast handles JSON encoding
                'meta_description' => $request->input('meta_description', ''),
                'image' => null,
                'meta_keywords' => $request->input('meta_keywords', ''),
                'skills' => $request->input('skills', []), // Cast handles JSON encoding
                'company_logo' => null, // Will be updated if logo is uploaded
                
                // Sticky notes and features
                'sticky_note_24_hour' => 0,
                'sticky_note_month' => 1, // Set to 1 as per requirements
                'sticky_note_week' => 0,
                'show_company_logo' => 0,
                'base_post' => 0,
                'email_blast_job' => 0,
                'create_qr_code' => 1,
                'auto_match_applicant' => 0,
                'highlight_post' => 0,
                'highlight_company_with_color' => 0,
                'geo_lock_post' => '0',
                'brand_color' => null,
                'location_zip_postal' => null,
                'excerpt' => null,
                'budget' => null,
                'location_long' => null,
                'locaiton_lat' => null,
                'seo_title' => $request->input('title'),
                'how_to_apply' => 'Apply via the provided URL.',
                'apply_email_address' => null,
                'company_twitter' => null,
                'company_email' => null,
                'invoice_email' => null,
                'invoice_address' => null,
                'invoice_notes_po_box_number' => null,
                'feedback_box' => null,
                'pay_later' => 0,
                'views' => 0,
                'clicks' => null,
                'highlight_company' => '',
                'slug_trans' => 1, // Mark as transformed using new slug format
            ];

            // Create the job post
            $job = JobPost::create($jobData);

            // Handle company logo upload if provided
            if ($request->hasFile('company_logo')) {
                $logoPath = $this->handleLogoUpload($request->file('company_logo'), $job->id);
                
                if ($logoPath) {
                    $job->company_logo = $logoPath;
                    $job->show_company_logo = 1;
                    $job->save();
                }
            }

            // Check if company info exists, if not, queue AI generation
            $companyInfoExists = $this->checkAndGenerateCompanyInfo($request->input('company_name'));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Job post generated successfully!' . (!$companyInfoExists ? ' Company profile will be generated with AI.' : ''),
                'job' => $job,
                'company_info_exists' => $companyInfoExists,
                'company_info_queued' => !$companyInfoExists,
                'redirect' => route('admin.manage-job-posts.index')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error generating job post: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate job post: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle company logo upload
     */
    private function handleLogoUpload($file, $jobId)
    {
        try {
            $userFolder = 'user_6'; // Admin user folder
            $foldername = 'job_' . $jobId . '_' . time();
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            
            // Target folder structure: store_data/posts/draft/user_6/job_{id}_{timestamp}/
            $targetFolder = 'store_data/posts/draft/' . $userFolder . '/' . $foldername;
            $targetFile = $targetFolder . '/' . $filename;

            // Create directory and store file
            Storage::disk('public')->makeDirectory($targetFolder);
            Storage::disk('public')->putFileAs($targetFolder, $file, $filename);

            // Create file record
            FilePostStored::create([
                'post_id' => $jobId,
                'file_store_an_id' => $jobId,
                'filename' => $filename,
                'foldername' => $userFolder . '/' . $foldername,
                'status' => 'active',
                'type' => 'job_post',
                'order' => 'first',
            ]);

            // Return the relative path for the database
            return 'logos/' . $filename;

        } catch (\Exception $e) {
            Log::error('Error uploading company logo: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Format job description with HTML structure
     * This provides a base template that AI can enhance
     */
    private function formatJobDescription($rawDescription, $companyName, $position)
    {
        // Clean and format the raw description
        $cleaned = strip_tags($rawDescription);
        $lines = explode("\n", $cleaned);
        $lines = array_filter(array_map('trim', $lines));

        // Build structured HTML
        $html = '<!DOCTYPE html>' . "\n";
        $html .= '<html lang="en">' . "\n";
        $html .= '<head>' . "\n";
        $html .= '<meta charset="UTF-8">' . "\n";
        $html .= '<title>' . htmlspecialchars($position . ' - ' . $companyName) . '</title>' . "\n";
        $html .= '<meta name="viewport" content="width=device-width, initial-scale=1">' . "\n";
        $html .= '<script src="https://cdn.tailwindcss.com"></script>' . "\n";
        $html .= '</head>' . "\n";
        $html .= '<body class="bg-slate-100 font-sans text-slate-800">' . "\n";
        $html .= '<div class="max-w-2xl mx-auto my-10 p-8 bg-white rounded-xl shadow-md">' . "\n\n";

        // Add overview section
        $html .= '<h3 class="text-xl font-bold text-slate-900 mb-2">Overview</h3>' . "\n";
        $html .= '<p class="mb-8">' . "\n";
        $html .= htmlspecialchars(implode(' ', array_slice($lines, 0, 3))) . "\n";
        $html .= '</p>' . "\n\n";

        // Add remaining content as sections
        if (count($lines) > 3) {
            $html .= '<h3 class="text-xl font-bold text-slate-900 mb-2">Details</h3>' . "\n";
            $html .= '<ul class="list-disc pl-6 space-y-2 mb-6">' . "\n";
            foreach (array_slice($lines, 3) as $line) {
                $html .= '<li>' . htmlspecialchars($line) . '</li>' . "\n";
            }
            $html .= '</ul>' . "\n\n";
        }

        $html .= '</div>' . "\n";
        $html .= '</body>' . "\n";
        $html .= '</html>';

        return $html;
    }

    /**
     * Parse AI-generated content and extract structured fields
     * This helps auto-populate fields from AI responses
     */
    public function parseAiContent(Request $request)
    {
        $request->validate([
            'ai_content' => 'required|string',
        ]);

        try {
            $content = $request->input('ai_content');
            
            // Extract structured data from AI content
            // This is a simple parser - in production, use more sophisticated NLP
            $parsed = [
                'title' => $this->extractTitle($content),
                'company_name' => $this->extractCompanyName($content),
                'position' => $this->extractPosition($content),
                'skills' => $this->extractSkills($content),
                'benefits' => $this->extractBenefits($content),
                'requirements' => $this->extractRequirements($content),
                'location_type' => $this->extractLocationType($content),
                'employer_type' => $this->extractEmployerType($content),
                'salary_range' => $this->extractSalaryRange($content),
            ];

            return response()->json([
                'success' => true,
                'parsed' => $parsed
            ]);

        } catch (\Exception $e) {
            Log::error('Error parsing AI content: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to parse AI content'
            ], 500);
        }
    }

    // Helper methods for parsing AI content
    private function extractTitle($content)
    {
        // Simple regex to find title patterns
        if (preg_match('/title[:\s]+([^\n]+)/i', $content, $matches)) {
            return trim($matches[1]);
        }
        return null;
    }

    private function extractCompanyName($content)
    {
        if (preg_match('/company[:\s]+([^\n]+)/i', $content, $matches)) {
            return trim($matches[1]);
        }
        return null;
    }

    private function extractPosition($content)
    {
        if (preg_match('/position[:\s]+([^\n]+)/i', $content, $matches)) {
            return trim($matches[1]);
        }
        return null;
    }

    private function extractSkills($content)
    {
        $skills = [];
        if (preg_match('/skills?[:\s]+([^\n]+)/i', $content, $matches)) {
            $skillsText = $matches[1];
            $skills = array_map('trim', preg_split('/[,;]+/', $skillsText));
            // Convert underscores to spaces
            $skills = array_map(function($skill) {
                return str_replace('_', ' ', $skill);
            }, $skills);
        }
        return $skills;
    }

    private function extractBenefits($content)
    {
        $benefits = [];
        if (preg_match('/benefits?[:\s]+([^\n]+)/i', $content, $matches)) {
            $benefitsText = $matches[1];
            $benefits = array_map('trim', preg_split('/[,;]+/', $benefitsText));
        }
        return $benefits;
    }

    private function extractRequirements($content)
    {
        $requirements = [];
        if (preg_match('/requirements?[:\s]+([^\n]+)/i', $content, $matches)) {
            $reqText = $matches[1];
            $requirements = array_map('trim', preg_split('/[,;]+/', $reqText));
        }
        return $requirements;
    }

    private function extractLocationType($content)
    {
        if (preg_match('/(remote|hybrid|onsite|on-site)/i', $content, $matches)) {
            return ucfirst(strtolower($matches[1]));
        }
        return 'Remote';
    }

    private function extractEmployerType($content)
    {
        if (preg_match('/(full-time|part-time|contract|contractor|temporary|internship)/i', $content, $matches)) {
            $type = strtolower($matches[1]);
            return $type === 'contract' ? 'contractor' : ucfirst($type);
        }
        return 'Full-Time';
    }

    private function extractSalaryRange($content)
    {
        $range = ['min' => null, 'max' => null, 'currency' => 'USD'];
        
        // Match patterns like "$50,000 - $70,000" or "$50k-$70k"
        if (preg_match('/\$?([\d,]+)k?\s*-\s*\$?([\d,]+)k?/i', $content, $matches)) {
            $min = str_replace(',', '', $matches[1]);
            $max = str_replace(',', '', $matches[2]);
            
            // If 'k' notation, multiply by 1000
            if (stripos($matches[0], 'k') !== false) {
                $min *= 1000;
                $max *= 1000;
            }
            
            $range['min'] = $min;
            $range['max'] = $max;
        }
        
        return $range;
    }

    /**
     * Create a unique slug from the job title
     * Uses the same logic as ManageJobPostsController
     * Converts title to lowercase, removes special chars, and appends 12 random digits
     */
    private function createSlugFromTitle($title)
    {
        // Convert to lowercase and replace spaces/special chars with hyphens
        $slug = strtolower($title);
        
        // Remove special characters except spaces and hyphens
        $slug = preg_replace('/[^a-z0-9\s\-]/', '', $slug);
        
        // Replace multiple spaces or hyphens with single hyphen
        $slug = preg_replace('/[\s\-]+/', '-', $slug);
        
        // Trim hyphens from start and end
        $slug = trim($slug, '-');
        
        // Generate 12 random digits
        $randomDigits = '';
        for ($i = 0; $i < 12; $i++) {
            $randomDigits .= rand(0, 9);
        }
        
        // Append random digits
        $slug = $slug . '-' . $randomDigits;
        
        return $slug;
    }

    /**
     * Check if company info exists and queue AI generation if it doesn't
     * Returns true if company info already exists, false if it was queued for generation
     */
    private function checkAndGenerateCompanyInfo($companyName)
    {
        if (empty($companyName)) {
            return false;
        }

        // Check if company info already exists (case-insensitive)
        $existingCompany = CompanyInfo::whereRaw('LOWER(company_name) = ?', [strtolower($companyName)])->first();

        if ($existingCompany) {
            Log::info("Company info already exists for: {$companyName}");
            return true;
        }

        // Company doesn't exist, queue AI generation job
        Log::info("Company info doesn't exist for: {$companyName}, queuing AI generation");
        
        try {
            $githubToken = config('services.github.token');
            $progressKey = 'company_info_generation_' . uniqid();
            
            GenerateCompanyInfoJob::dispatch(
                [$companyName], // Pass as array
                $githubToken,
                $progressKey
            )->onQueue('default');
            
            Log::info("Successfully queued company info generation for: {$companyName}");
            return false;
        } catch (\Exception $e) {
            Log::error("Failed to queue company info generation for {$companyName}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Parse location string from LinkedIn to extract country, state/province, and city
     * Handles formats like:
     * - "Canada (Remote)"
     * - "Ontario, Canada (Remote)"
     * - "Toronto, Ontario, Canada (Remote)"
     * - "United States (Remote)"
     * - "California, United States"
     */
    private function parseLocationString($locationString)
    {
        $result = [
            'country' => null,
            'state_province' => null,
            'city' => null,
            'is_remote' => false,
        ];

        if (empty($locationString)) {
            return $result;
        }

        // Remove parenthetical info (e.g., "(Remote)", "(Hybrid)")
        $cleanLocation = preg_replace('/\s*\([^)]*\)/i', '', $locationString);
        $cleanLocation = trim($cleanLocation);

        // Check if it contains remote/hybrid indicators
        $result['is_remote'] = stripos($locationString, 'remote') !== false;

        // List of known countries to check for
        $countries = [
            'Canada' => 'Canada',
            'United States' => 'USA',
            'USA' => 'USA',
            'US' => 'USA',
            'United Kingdom' => 'United Kingdom',
            'UK' => 'United Kingdom',
            'Australia' => 'Australia',
            'New Zealand' => 'New Zealand',
            'Ireland' => 'Ireland',
            'Germany' => 'Germany',
            'France' => 'France',
            'Netherlands' => 'Netherlands',
            'Switzerland' => 'Switzerland',
            'Singapore' => 'Singapore',
            'India' => 'India',
        ];

        // Split by comma to get parts
        $parts = array_map('trim', explode(',', $cleanLocation));
        $parts = array_filter($parts); // Remove empty parts

        // Check each part for country match (case-insensitive)
        $foundCountry = null;
        $foundCountryIndex = -1;
        
        foreach ($parts as $index => $part) {
            foreach ($countries as $countryName => $countryCode) {
                if (strcasecmp($part, $countryName) === 0) {
                    $foundCountry = $countryCode;
                    $foundCountryIndex = $index;
                    break 2; // Break out of both loops
                }
            }
        }

        if ($foundCountry) {
            $result['country'] = $foundCountry;

            // Assign remaining parts based on position
            if ($foundCountryIndex === 0) {
                // Country is first (e.g., "Canada")
                // No state/city info
            } elseif ($foundCountryIndex === 1) {
                // Format: "State, Country" or "City, Country"
                $result['state_province'] = $parts[0];
            } elseif ($foundCountryIndex === 2) {
                // Format: "City, State, Country"
                $result['city'] = $parts[0];
                $result['state_province'] = $parts[1];
            }
        } else {
            // No country found, default to USA if it looks like US location
            // Otherwise leave country as null and let the caller decide
            if (count($parts) > 0) {
                // Could be just a city or state
                $result['state_province'] = $parts[0];
                if (count($parts) > 1) {
                    $result['city'] = $parts[0];
                    $result['state_province'] = $parts[1];
                }
            }
        }

        return $result;
    }

    /**
     * Import job from LinkedIn Chrome Extension
     * Public endpoint that receives job data from the extension, processes it with AI, and stores it
     */
    public function importFromLinkedIn(Request $request)
    {
        // Add CORS headers for Chrome Extension access
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Accept, Authorization');
        
        // Handle preflight OPTIONS request
        if ($request->getMethod() === 'OPTIONS') {
            return response()->json(['status' => 'ok'], 200);
        }
        
        Log::info('=== LinkedIn Job Import Started ===');
        Log::info('Request data: ' . json_encode($request->all()));

        // Validate password first
        $password = $request->input('password');
        $expectedPassword = '\\/8!6t31<[Ma';
        
        if ($password !== $expectedPassword) {
            Log::warning('=== LinkedIn Job Import UNAUTHORIZED ===');
            Log::warning('Invalid password attempt');
            
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: Invalid password',
                'error' => 'Authentication failed'
            ], 401);
        }
        
        Log::info('Password validated successfully');

        $request->validate([
            'title' => 'required|string',
            'company' => 'required|string',
            'location' => 'nullable|string',
            'description' => 'required|string|min:50',
            'url' => 'required|url',
            'applyUrl' => 'nullable|url',
            'source' => 'nullable|string',
            'jobType' => 'nullable|string',
            'remote' => 'nullable',
            'seniority' => 'nullable|string',
            'companyLogoUrl' => 'nullable|url',
            'salaryMin' => 'nullable|numeric',
            'salaryMax' => 'nullable|numeric',
            'salaryCurrency' => 'nullable|string',
            'password' => 'required|string',
        ]);

        DB::beginTransaction();

        try {
            // Step 1: Extract the LinkedIn job data from request
            $linkedInData = [
                'title' => $request->input('title'),
                'company' => $request->input('company'),
                'location' => $request->input('location'),
                'description' => $request->input('description'),
                'url' => $request->input('url'),
                'applyUrl' => $request->input('applyUrl'),
                'source' => $request->input('source', 'linkedin'),
                'url' => $request->input('url'),
                'jobType' => $request->input('jobType'),
                'remote' => $request->input('remote'),
                'seniority' => $request->input('seniority'),
            ];

            Log::info('LinkedIn data extracted: ' . json_encode($linkedInData));

            // Step 2: Send to AI endpoint to generate the right fields
            Log::info('Sending job description to AI for processing...');
            
            $rawDescription = $linkedInData['description'];
            
            // Clean the description
            $cleanText = $rawDescription;
            if (strpos($rawDescription, '<') !== false) {
                libxml_use_internal_errors(true);
                $dom = new \DOMDocument();
                $dom->loadHTML('<?xml encoding="UTF-8">' . $rawDescription, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
                $cleanText = $dom->textContent ?? '';
                libxml_clear_errors();
            }
            
            if (empty($cleanText) || strlen($cleanText) < 50) {
                $cleanText = strip_tags($rawDescription);
            }
            
            $cleanText = html_entity_decode($cleanText, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $cleanText = preg_replace('/[\r\n\t]+/', ' ', $cleanText);
            $cleanText = preg_replace('/\s+/', ' ', $cleanText);
            $cleanText = trim($cleanText);

            // Call AI to generate structured data
            $aiResponse = $this->callGitHubCopilotApi($cleanText, null);
            
            if (!$aiResponse) {
                Log::warning('AI API returned null, using fallback extraction');
                $aiResponse = $this->extractFieldsFromRawDescription($rawDescription);
                
                // Override with LinkedIn data
                $aiResponse['company_name'] = $linkedInData['company'];
                $aiResponse['title'] = $linkedInData['title'];
                // Use direct apply URL if available, otherwise use LinkedIn URL
                $aiResponse['apply_url'] = $linkedInData['applyUrl'] ?? $linkedInData['url'];
                
                // Parse LinkedIn location to extract country, state/province, and city
                if (!empty($linkedInData['location'])) {
                    Log::info('Parsing LinkedIn location (fallback): ' . $linkedInData['location']);
                    $parsedLocation = $this->parseLocationString($linkedInData['location']);
                    
                    Log::info('Parsed location data (fallback): ' . json_encode($parsedLocation));
                    
                    // Override fallback location data with parsed location
                    if ($parsedLocation['country']) {
                        $aiResponse['location_country'] = $parsedLocation['country'];
                    }
                    if ($parsedLocation['state_province']) {
                        $aiResponse['location_state_province'] = $parsedLocation['state_province'];
                    }
                    if ($parsedLocation['city']) {
                        $aiResponse['location_city'] = $parsedLocation['city'];
                    }
                    
                    // Use parsed remote status if detected in location string
                    if ($parsedLocation['is_remote']) {
                        $aiResponse['location_type'] = 'Remote';
                    }
                } else {
                    // LinkedIn location is empty - use fallback location (defaults to USA)
                    Log::info('LinkedIn location is empty, using fallback defaults (USA)');
                }
            } else {
                // Merge LinkedIn data with AI response (LinkedIn data takes precedence for key fields)
                $aiResponse['company_name'] = $linkedInData['company'];
                // Use direct apply URL if available, otherwise use LinkedIn URL
                $aiResponse['apply_url'] = $linkedInData['applyUrl'] ?? $linkedInData['url'];
                
                // Handle location logic:
                // 1. If LinkedIn location is provided, use it (override AI)
                // 2. If LinkedIn location is empty, use AI-determined location
                // 3. If both are empty, default to USA
                if (!empty($linkedInData['location'])) {
                    Log::info('Parsing LinkedIn location: ' . $linkedInData['location']);
                    $parsedLocation = $this->parseLocationString($linkedInData['location']);
                    
                    Log::info('Parsed location data: ' . json_encode($parsedLocation));
                    
                    // Override AI response with parsed location data
                    if ($parsedLocation['country']) {
                        $aiResponse['location_country'] = $parsedLocation['country'];
                    }
                    if ($parsedLocation['state_province']) {
                        $aiResponse['location_state_province'] = $parsedLocation['state_province'];
                    }
                    if ($parsedLocation['city']) {
                        $aiResponse['location_city'] = $parsedLocation['city'];
                    }
                    
                    // Use parsed remote status if detected in location string
                    if ($parsedLocation['is_remote'] && empty($linkedInData['remote'])) {
                        $aiResponse['location_type'] = 'Remote';
                    }
                } else {
                    // LinkedIn location is empty - use AI-determined location
                    Log::info('LinkedIn location is empty, using AI-determined location');
                    Log::info('AI determined country: ' . ($aiResponse['location_country'] ?? 'N/A'));
                    Log::info('AI determined state/province: ' . ($aiResponse['location_state_province'] ?? 'N/A'));
                    Log::info('AI determined city: ' . ($aiResponse['location_city'] ?? 'N/A'));
                    
                    // If AI also didn't determine a location, default to USA
                    if (empty($aiResponse['location_country'])) {
                        Log::info('AI also did not determine location, defaulting to USA');
                        $aiResponse['location_country'] = 'USA';
                    }
                }
                
                // Use LinkedIn job type if available
                if (!empty($linkedInData['jobType'])) {
                    $aiResponse['employer_type'] = $linkedInData['jobType'];
                }
                
                // Use LinkedIn remote info if available (takes precedence over parsed location)
                if ($linkedInData['remote'] === true || $linkedInData['remote'] === 'true') {
                    $aiResponse['location_type'] = 'Remote';
                } elseif ($linkedInData['remote'] === 'hybrid') {
                    $aiResponse['location_type'] = 'Hybrid';
                }
            }

            Log::info('AI processing complete. Company: ' . $aiResponse['company_name']);

            // Step 3: Prepare and insert job post data (logo will be added after job creation)
            $slug = $this->createSlugFromTitle($aiResponse['title']);
            $expiresAt = Carbon::now()->addDays(90)->endOfDay();

            // Format location restriction
            $locationCountry = $aiResponse['location_country'] ?? 'USA';
            $locationState = $aiResponse['location_state_province'] ?? '';
            $locationCity = $aiResponse['location_city'] ?? '';
            $locationType = $aiResponse['location_type'] ?? 'Remote';
            
            if ($locationCountry === 'United States' || $locationCountry === 'US') {
                $locationCountry = 'USA';
            }
            
            $locationRestriction = '';
            if ($locationCountry) {
                if ($locationCountry === 'Canada' || $locationCountry === 'USA') {
                    if ($locationCity && $locationState) {
                        $locationRestriction = $locationCity . ', ' . $locationState . ', ' . $locationCountry;
                    } elseif ($locationState) {
                        $locationRestriction = $locationState . ', ' . $locationCountry;
                    } else {
                        $locationRestriction = $locationCountry;
                    }
                } else {
                    $locationRestriction = $locationCountry;
                }
            }
            
            if (empty($locationRestriction)) {
                $locationRestriction = 'USA';
            }

            // Create job post data
            $jobData = [
                'author_id' => 6,
                'order_id' => null,
                'category_id' => null,
                'draft_id' => $slug,
                'expires_at' => $expiresAt,
                'updated_at' => Carbon::now(),
                'created_at' => Carbon::now(),
                'company_name' => $aiResponse['company_name'],
                'title' => $aiResponse['title'],
                'job_description' => $aiResponse['job_description'],
                'slug' => $slug,
                'status' => 'COMMITTED',
                'position' => $aiResponse['position'] ?? $aiResponse['title'],
                'apply_url' => $aiResponse['apply_url'],
                'location_type' => $locationType,
                'location_restriction' => $locationRestriction,
                'location_country' => $locationCountry,
                'location_state_province' => $locationState,
                'location_city' => $locationCity,
                'benefits' => $aiResponse['benefits'] ?? [],
                'body' => null,
                'featured' => 0,
                'employer_type' => $aiResponse['employer_type'] ?? 'Full-Time',
                'currency' => $request->input('salaryCurrency') ?? $aiResponse['currency'] ?? 'USD',
                //'salary_min' => $request->input('salaryMin') ?? $aiResponse['salary_min'] ?? null,
                //'salary_max' => $request->input('salaryMax') ?? $aiResponse['salary_max'] ?? null,
                'salary_min' => $request->input('salaryMin') ?? null,
                'salary_max' => $request->input('salaryMax') ?? null,
                'payment_frequency' => $aiResponse['payment_frequency'] ?? 'Yearly',
                'primary_tag' => $aiResponse['primary_tag'] ?? 'Other',
                'secondary_tags' => $aiResponse['secondary_tags'] ?? [],
                'meta_description' => $aiResponse['meta_description'] ?? '',
                'image' => null,
                'meta_keywords' => $aiResponse['meta_keywords'] ?? '',
                'skills' => $aiResponse['skills'] ?? [],
                'company_logo' => null, // Will be updated after download
                'show_company_logo' => 0, // Will be updated after download
                'sticky_note_24_hour' => 0,
                'sticky_note_month' => 1,
                'sticky_note_week' => 0,
                'base_post' => 0,
                'email_blast_job' => 0,
                'create_qr_code' => 1,
                'auto_match_applicant' => 0,
                'highlight_post' => 0,
                'highlight_company_with_color' => 0,
                'geo_lock_post' => '0',
                'brand_color' => null,
                'location_zip_postal' => null,
                'excerpt' => null,
                'budget' => null,
                'location_long' => null,
                'locaiton_lat' => null,
                'seo_title' => $aiResponse['title'],
                'how_to_apply' => 'Apply via the provided URL.',
                'apply_email_address' => null,
                'company_twitter' => null,
                'company_email' => null,
                'invoice_email' => null,
                'invoice_address' => null,
                'invoice_notes_po_box_number' => null,
                'feedback_box' => null,
                'pay_later' => 0,
                'views' => 0,
                'clicks' => null,
                'highlight_company' => '',
                'slug_trans' => 1,
            ];

            // Create the job post (without logo first)
            $job = JobPost::create($jobData);
            Log::info('Job post created with ID: ' . $job->id);

            // Step 4b: Download and store company logo using handleLogoUpload structure
            if ($request->input('companyLogoUrl')) {
                try {
                    $logoUrl = $request->input('companyLogoUrl');
                    Log::info('=== Company Logo Download ===');
                    Log::info('Logo URL received: ' . $logoUrl);
                    Log::info('Job ID: ' . $job->id);
                    Log::info('Downloading and storing logo...');
                    
                    $logoPath = $this->downloadAndStoreLogoFromUrl($logoUrl, $job->id);
                    
                    if ($logoPath) {
                        $job->company_logo = $logoPath;
                        $job->show_company_logo = 1;
                        $job->save();
                        
                        Log::info('✅ Logo download and storage successful!');
                        Log::info('Stored at: ' . $logoPath);
                        Log::info('Public URL: ' . asset('storage/' . $logoPath));
                    } else {
                        Log::warning('Logo download returned null/empty path');
                    }
                } catch (\Exception $e) {
                    Log::error('❌ Failed to download company logo');
                    Log::error('Error: ' . $e->getMessage());
                    Log::error('Stack trace: ' . $e->getTraceAsString());
                }
            } else {
                Log::warning('No company logo URL provided in request');
            }

            // Step 5: Check and generate company info if needed
            $companyInfoExists = $this->checkAndGenerateCompanyInfo($aiResponse['company_name']);

            DB::commit();

            Log::info('=== LinkedIn Job Import Completed Successfully ===');

            return response()->json([
                'success' => true,
                'message' => 'Job post imported successfully from LinkedIn!' . (!$companyInfoExists ? ' Company profile will be generated with AI.' : ''),
                'job' => [
                    'id' => $job->id,
                    'title' => $job->title,
                    'company_name' => $job->company_name,
                    'slug' => $job->slug,
                    'position' => $job->position,
                ],
                'company_info_exists' => $companyInfoExists,
                'company_info_queued' => !$companyInfoExists,
                'redirect_url' => route('admin.manage-job-posts.index'),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('=== LinkedIn Job Import Failed ===');
            Log::error('Error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to import job post: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download image from URL and store it using handleLogoUpload structure
     * Same storage structure as handleLogoUpload: store_data/posts/draft/user_6/job_{id}_{timestamp}/
     */
    private function downloadAndStoreLogoFromUrl($imageUrl, $jobId)
    {
        try {
            Log::info('Downloading logo from URL: ' . $imageUrl);
            
            // Download the image
            $client = new \GuzzleHttp\Client();
            $response = $client->get($imageUrl, [
                'timeout' => 30,
                'verify' => false, // For LinkedIn images with SSL
            ]);
            
            $imageContent = $response->getBody()->getContents();
            Log::info('Image downloaded, size: ' . strlen($imageContent) . ' bytes');
            
            // Detect image type from content
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->buffer($imageContent);
            Log::info('Detected MIME type: ' . $mimeType);
            
            $extension = 'jpg';
            if ($mimeType === 'image/png') {
                $extension = 'png';
            } elseif ($mimeType === 'image/gif') {
                $extension = 'gif';
            } elseif ($mimeType === 'image/webp') {
                $extension = 'webp';
            } elseif ($mimeType === 'image/svg+xml') {
                $extension = 'svg';
            } elseif ($mimeType === 'image/jpeg') {
                $extension = 'jpg';
            }
            
            // Use the same folder structure as handleLogoUpload
            $userFolder = 'user_6'; // Admin user folder
            $foldername = 'job_' . $jobId . '_' . time();
            $filename = Str::uuid() . '.' . $extension;
            
            // Target folder structure: store_data/posts/draft/user_6/job_{id}_{timestamp}/
            $targetFolder = 'store_data/posts/draft/' . $userFolder . '/' . $foldername;
            $targetFile = $targetFolder . '/' . $filename;
            
            Log::info('Creating directory: ' . $targetFolder);
            Storage::disk('public')->makeDirectory($targetFolder);
            
            Log::info('Storing file: ' . $targetFile);
            Storage::disk('public')->put($targetFile, $imageContent);
            
            // Create file record (same as handleLogoUpload)
            Log::info('Creating FilePostStored record');
            FilePostStored::create([
                'post_id' => $jobId,
                'file_store_an_id' => $jobId,
                'filename' => $filename,
                'foldername' => $userFolder . '/' . $foldername,
                'status' => 'active',
                'type' => 'job_post',
                'order' => 'first',
            ]);
            
            // Return the relative path for the database (same format as handleLogoUpload)
            $relativePath = 'logos/' . $filename;
            Log::info('Logo stored successfully, returning path: ' . $relativePath);
            
            return $relativePath;
            
        } catch (\Exception $e) {
            Log::error('Failed to download and store logo from ' . $imageUrl);
            Log::error('Error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return null;
        }
    }

    /**
     * Download image from URL and store it
     * @deprecated Use downloadAndStoreLogoFromUrl instead for proper handleLogoUpload structure
     */
    private function downloadAndStoreImage($imageUrl, $prefix = 'image')
    {
        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->get($imageUrl);
            
            $imageContent = $response->getBody()->getContents();
            
            // Detect image type from content
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->buffer($imageContent);
            
            $extension = 'jpg';
            if ($mimeType === 'image/png') {
                $extension = 'png';
            } elseif ($mimeType === 'image/gif') {
                $extension = 'gif';
            } elseif ($mimeType === 'image/webp') {
                $extension = 'webp';
            } elseif ($mimeType === 'image/svg+xml') {
                $extension = 'svg';
            }
            
            $filename = $prefix . '_' . time() . '_' . Str::random(8) . '.' . $extension;
            $path = 'logos/' . $filename;
            
            // Store the image
            Storage::disk('public')->put($path, $imageContent);
            
            return $path;
            
        } catch (\Exception $e) {
            Log::error('Failed to download and store image from ' . $imageUrl . ': ' . $e->getMessage());
            throw $e;
        }
    }
}


