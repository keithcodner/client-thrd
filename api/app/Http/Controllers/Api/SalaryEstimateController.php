<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SalaryEstimate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class SalaryEstimateController extends Controller
{
    /**
     * Get salary estimate for a job title and location
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function estimate(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'location' => 'required|string',
        ]);

        $title = $request->input('title');
        $location = $request->input('location');
        
        Log::info("Salary estimate requested for: {$title} in {$location}");

        // Step 1: Try to find in database first
        $estimate = $this->findInDatabase($title, $location);
        
        if ($estimate) {
            Log::info("Found salary in database");
            return response()->json([
                'min' => (float) $estimate->min_salary,
                'max' => (float) $estimate->max_salary,
                'currency' => $estimate->currency,
                'source' => 'database'
            ]);
        }

        // Step 2: Try to fetch from Bureau of Labor Statistics API
        $blsData = $this->fetchFromBLS($title, $location);
        
        if ($blsData) {
            Log::info("Found salary from BLS API");
            
            // Store in database for future use
            $this->storeEstimate($title, $location, $blsData);
            
            return response()->json([
                'min' => $blsData['min'],
                'max' => $blsData['max'],
                'currency' => $blsData['currency'],
                'source' => 'bls'
            ]);
        }

        // Step 3: Return fallback/no data found
        Log::warning("No salary data found for: {$title} in {$location}");
        
        return response()->json([
            'error' => 'No salary data found',
            'message' => 'Please use client-side estimation'
        ], 404);
    }

    /**
     * Find salary estimate in database
     */
    private function findInDatabase($title, $location)
    {
        $normalizedTitle = SalaryEstimate::normalizeTitle($title);
        $normalizedLocation = SalaryEstimate::normalizeLocation($location);

        // Try exact match first
        $estimate = SalaryEstimate::where('normalized_title', $normalizedTitle)
            ->where('location', 'LIKE', "%{$normalizedLocation}%")
            ->first();

        if ($estimate) {
            return $estimate;
        }

        // Try fuzzy match on job title
        $estimate = SalaryEstimate::where('normalized_title', 'LIKE', "%{$normalizedTitle}%")
            ->where('location', 'LIKE', "%{$normalizedLocation}%")
            ->first();

        return $estimate;
    }

    /**
     * Fetch salary data from Bureau of Labor Statistics API
     * 
     * BLS API Documentation: https://www.bls.gov/developers/api_signature_v2.htm
     */
    private function fetchFromBLS($title, $location)
    {
        // BLS API requires occupation codes (SOC codes)
        // Map common job titles to SOC codes
        $socCode = $this->getTitleToSOCCode($title);
        
        if (!$socCode) {
            Log::info("No SOC code mapping found for: {$title}");
            return null;
        }

        // Cache BLS responses for 30 days to avoid rate limits
        $cacheKey = "bls_salary_{$socCode}_{$location}";
        
        return Cache::remember($cacheKey, now()->addDays(30), function () use ($socCode, $location, $title) {
            try {
                // BLS API endpoint for Occupational Employment and Wage Statistics
                // Note: BLS API is free but has rate limits (25 requests per day without key, 500 with key)
                $response = Http::timeout(10)
                    ->get('https://api.bls.gov/publicAPI/v2/timeseries/data/OEUW' . $socCode, [
                        'registrationkey' => env('BLS_API_KEY', ''), // Optional: Get free key at https://data.bls.gov/registrationEngine/
                        'startyear' => now()->year - 1,
                        'endyear' => now()->year,
                    ]);

                if (!$response->successful()) {
                    Log::warning("BLS API returned non-OK status: " . $response->status());
                    return null;
                }

                $data = $response->json();
                
                if (!isset($data['Results']['series'][0]['data'][0])) {
                    Log::warning("BLS API returned no data");
                    return null;
                }

                // Parse BLS response
                $latestData = $data['Results']['series'][0]['data'][0];
                $annualMeanWage = (float) $latestData['value'];
                
                // BLS provides mean wage, calculate range (±15%)
                $min = round($annualMeanWage * 0.85);
                $max = round($annualMeanWage * 1.15);
                
                // Adjust for location if not USA
                list($adjustedMin, $adjustedMax, $currency) = $this->adjustForLocation($min, $max, $location);
                
                return [
                    'min' => $adjustedMin,
                    'max' => $adjustedMax,
                    'currency' => $currency,
                    'metadata' => [
                        'soc_code' => $socCode,
                        'mean_wage' => $annualMeanWage,
                        'year' => $latestData['year'] ?? now()->year,
                    ]
                ];

            } catch (\Exception $e) {
                Log::error("BLS API error: " . $e->getMessage());
                return null;
            }
        });
    }

    /**
     * Map job title to BLS SOC code
     * Full SOC code list: https://www.bls.gov/soc/2018/major_groups.htm
     */
    private function getTitleToSOCCode($title)
    {
        $titleLower = strtolower($title);
        
        // Common tech and professional occupations
        $mapping = [
            // Software/Tech
            'software engineer' => '151252',     // Software Developers
            'software developer' => '151252',
            'web developer' => '151254',         // Web Developers
            'data scientist' => '152051',        // Data Scientists
            'data analyst' => '152098',          // Data Analysts
            'database administrator' => '151241', // Database Administrators
            'network engineer' => '151244',      // Network and Computer Systems Administrators
            'systems administrator' => '151244',
            'cybersecurity' => '151212',         // Information Security Analysts
            'security analyst' => '151212',
            'devops engineer' => '151252',
            
            // Design
            'ux designer' => '271021',           // Graphic Designers
            'ui designer' => '271021',
            'product designer' => '271021',
            
            // Business
            'product manager' => '113021',       // Computer and Information Systems Managers
            'project manager' => '119199',       // Managers, All Other
            'business analyst' => '131161',      // Market Research Analysts
            'marketing manager' => '112021',     // Marketing Managers
            'sales manager' => '112022',         // Sales Managers
            'account manager' => '419031',       // Sales Representatives, Services
            
            // Legal
            'lawyer' => '231011',                // Lawyers
            'attorney' => '231011',
            'legal counsel' => '231011',
            'counsel' => '231011',
            'paralegal' => '232011',             // Paralegals and Legal Assistants
            
            // HR
            'recruiter' => '131071',             // Human Resources Specialists
            'hr manager' => '113121',            // Human Resources Managers
            
            // Finance
            'accountant' => '132011',            // Accountants and Auditors
            'financial analyst' => '132051',     // Financial Analysts
        ];
        
        // Find best match
        foreach ($mapping as $keyword => $socCode) {
            if (stripos($titleLower, $keyword) !== false) {
                return $socCode;
            }
        }
        
        return null; // No mapping found
    }

    /**
     * Adjust salary for location (country)
     */
    private function adjustForLocation($min, $max, $location)
    {
        $locationLower = strtolower($location);
        
        // Currency and cost of living adjustments
        if (stripos($locationLower, 'canada') !== false) {
            // Canadian salaries ~90% of US, in CAD
            return [
                round($min * 0.9),
                round($max * 0.9),
                'CAD'
            ];
        } elseif (stripos($locationLower, 'uk') !== false || stripos($locationLower, 'united kingdom') !== false) {
            // UK salaries ~65% of US, in GBP
            return [
                round($min * 0.65),
                round($max * 0.65),
                'GBP'
            ];
        } elseif (stripos($locationLower, 'europe') !== false || 
                   stripos($locationLower, 'germany') !== false ||
                   stripos($locationLower, 'france') !== false ||
                   stripos($locationLower, 'netherlands') !== false) {
            // EU salaries ~75% of US, in EUR
            return [
                round($min * 0.75),
                round($max * 0.75),
                'EUR'
            ];
        }
        
        // Default: USA salaries in USD
        return [$min, $max, 'USD'];
    }

    /**
     * Store salary estimate in database
     */
    private function storeEstimate($title, $location, $data)
    {
        try {
            SalaryEstimate::create([
                'job_title' => $title,
                'location' => SalaryEstimate::normalizeLocation($location),
                'normalized_title' => SalaryEstimate::normalizeTitle($title),
                'min_salary' => $data['min'],
                'max_salary' => $data['max'],
                'currency' => $data['currency'],
                'source' => 'BLS',
                'metadata' => $data['metadata'] ?? null,
            ]);
            
            Log::info("Stored salary estimate in database for: {$title}");
        } catch (\Exception $e) {
            Log::error("Failed to store salary estimate: " . $e->getMessage());
        }
    }
}
