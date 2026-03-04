<?php

namespace App\Http\Controllers\Support;

use App\Http\Controllers\Controller;
use App\Mail\SiteMailServer;
use App\Models\ProNetwork\ProNetworkUserProfileEducation;
use App\Models\ProNetwork\ProNetworkUserProfileExperience;
use App\Models\ProNetwork\ProNetworkUserProfileHonour;
use App\Models\ProNetwork\ProNetworkUserProfileInterest;
use App\Models\ProNetwork\ProNetworkUserProfileSkill;
use App\Models\ProNetwork\ProNetworkUserProfileVolunteering;
use App\Models\WebActivityLog;
use App\Services\GeoIPService;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use SebastianBergmann\Environment\Console;

class WebActivityLogController extends Controller
{
    public function __construct()
    {
        //$this->middleware(['auth']);
    }

    /**
     * Log web activity for analytics
     */
    public function logActivity(Request $request)
    {
        try {
            $ipAddress = $request->ip();
            
            // Get geographic data
            $geoData = GeoIPService::lookup($ipAddress);

            WebActivityLog::create([
                'user_id' => auth()->id(),
                'visitor_uuid' => $request->session()->getId(),
                'ip_address' => $ipAddress,
                'user_agent' => $request->userAgent() ?? 'Unknown',
                'http_method' => $request->method(),
                'url' => $request->input('url', $request->fullUrl()),
                'route_name' => $request->route() ? $request->route()->getName() : null,
                'query_string' => $request->getQueryString(),
                'referrer' => $request->input('referrer', $request->header('referer')),
                'country' => $geoData['country'],
                'city' => $geoData['city'],
                'region' => $geoData['region'],
                'timezone' => $geoData['timezone'],
                'isp' => $geoData['isp'],
                'device_type' => $this->detectDeviceType($request->userAgent()),
                // Status code (200 for successful tracking)
                'status_code' => 200,
                // Engagement metrics from frontend
                'scroll_depth_percent' => $request->input('scroll_depth_percent'),
                'time_spent_seconds' => $request->input('time_spent_seconds'),
                'is_exit' => $request->input('is_exit', false),
                'interactions' => $request->input('interactions') ? json_encode($request->input('interactions')) : null,
            ]);

            return response()->json(['message' => 'Activity logged'], 200);
        } catch (\Exception $e) {
            Log::error('WebActivityLog error: ' . $e->getMessage());
            return response()->json(['message' => 'Error logging activity'], 500);
        }
    }

    /**
     * Detect device type from user agent
     */
    private function detectDeviceType($userAgent)
    {
        if (!$userAgent) return 'Unknown';
        
        if (preg_match('/mobile|android|iphone|ipod|blackberry|iemobile/i', $userAgent)) {
            return 'Mobile';
        } elseif (preg_match('/tablet|ipad/i', $userAgent)) {
            return 'Tablet';
        }
        
        return 'Desktop';
    }

    /**
     * Get dashboard analytics data
     */
    public function data(Request $request)
    {
        $logs = [];
        $logs[] = 'Starting data fetch...';
        
        try {
            // Get current user's IP and identify admin IPs to exclude
            $currentUserIP = $request->ip();
            $excludeIPs = [$currentUserIP];
            $logs[] = 'IP excluded: ' . $currentUserIP;
            
            // Add admin user IPs (if the current user is admin, get their IP history)
            if (auth()->check() && auth()->user()->is_admin) {
                $logs[] = 'User is admin, fetching admin IPs...';
                $adminIPs = WebActivityLog::where('user_id', auth()->id())
                    ->distinct()
                    ->pluck('ip_address')
                    ->toArray();
                $excludeIPs = array_merge($excludeIPs, $adminIPs);
                $logs[] = 'Admin IPs fetched: ' . count($adminIPs);
            }
            
            // Remove duplicates
            $excludeIPs = array_unique($excludeIPs);
            $logs[] = 'Total IPs to exclude: ' . count($excludeIPs);
            
            // Check total count (public-facing only)
            $logs[] = 'Fetching total count...';
            $totalCount = WebActivityLog::where('url', 'not like', '%/api/%')
                ->where('url', 'not like', '%/admin/%')
                ->count();
            Log::info('WebActivityLog total count: ' . $totalCount);
            $logs[] = 'Total count: ' . $totalCount;
            
            // Latest logs (last 20) - Exclude API and admin routes
            $logs[] = 'Fetching latest logs...';
            $latestLogs = WebActivityLog::with('user')
                ->where('url', 'not like', '%/api/%')
                ->where('url', 'not like', '%/admin/%')
                ->orderBy('created_at', 'desc')
                ->take(20)
                ->get();
            
            Log::info('Latest logs count: ' . $latestLogs->count());
            $logs[] = 'Latest logs fetched: ' . $latestLogs->count();

            // Traffic by user agent
            $logs[] = 'Fetching traffic by user agent...';
            $trafficByUserAgent = WebActivityLog::where('url', 'not like', '%/api/%')
                ->where('url', 'not like', '%/admin/%')
                ->select('user_agent', DB::raw('count(*) as count'))
                ->groupBy('user_agent')
                ->orderBy('count', 'desc')
                ->take(10)
                ->get()
                ->map(function ($item) {
                    // Extract browser name from user agent
                    $browser = 'Unknown';
                    if (preg_match('/Chrome/i', $item->user_agent)) {
                        $browser = 'Chrome';
                    } elseif (preg_match('/Firefox/i', $item->user_agent)) {
                        $browser = 'Firefox';
                    } elseif (preg_match('/Safari/i', $item->user_agent) && !preg_match('/Chrome/i', $item->user_agent)) {
                        $browser = 'Safari';
                    } elseif (preg_match('/Edge/i', $item->user_agent)) {
                        $browser = 'Edge';
                    } elseif (preg_match('/MSIE|Trident/i', $item->user_agent)) {
                        $browser = 'Internet Explorer';
                    }
                    
                    return [
                        'browser' => $browser,
                        'count' => $item->count,
                        'user_agent' => $item->user_agent
                    ];
                });
            $logs[] = 'User agents fetched: ' . $trafficByUserAgent->count();

            // Visits per day (last 30 days) - including today
            // Build complete 30-day range with actual counts
            $logs[] = 'Building visits per day...';
            $completeVisitsPerDay = collect();
            
            // Get today's date explicitly
            $today = \Carbon\Carbon::now();
            
            for ($i = 29; $i >= 0; $i--) {
                $date = $today->copy()->subDays($i);
                $dateString = $date->format('Y-m-d');
                
                // Count visits for this specific date
                $count = WebActivityLog::whereDate('created_at', $dateString)
                    ->where('url', 'not like', '%/api/%')
                    ->where('url', 'not like', '%/admin/%')
                    ->count();
                
                $completeVisitsPerDay->push((object)[
                    'date' => $dateString,
                    'count' => $count
                ]);
            }
            
            // Reverse to show most recent first (descending order)
            $visitsPerDay = $completeVisitsPerDay->reverse()->values();
            $logs[] = 'Visits per day calculated: ' . $visitsPerDay->count() . ' days';
            
            // Debug: Add current server date to response
            Log::info('Daily visits query - Today is: ' . $today->format('Y-m-d H:i:s') . ', Visit count for today: ' . WebActivityLog::whereDate('created_at', $today->format('Y-m-d'))->count());

            // Unique IPs with visit counts and last visit datetime
            $logs[] = 'Checking for is_banned column...';
            $uniqueIpsSelect = [
                'ip_address',
                DB::raw('count(*) as visit_count'),
                DB::raw('MAX(created_at) as last_visit_datetime'),
                DB::raw('MAX(country) as country')
            ];
            if (Schema::hasColumn('web_activity_logs', 'is_banned')) {
                $uniqueIpsSelect[] = DB::raw('MAX(is_banned) as is_banned');
                $logs[] = 'is_banned column exists';
            } else {
                $logs[] = 'is_banned column MISSING';
            }
            $logs[] = 'Fetching unique IPs...';
            $uniqueIps = WebActivityLog::where('url', 'not like', '%/api/%')
                ->where('url', 'not like', '%/admin/%')
                ->select($uniqueIpsSelect)
                ->groupBy('ip_address')
                ->orderBy('last_visit_datetime', 'desc')
                ->take(50)
                ->get();
            $logs[] = 'Unique IPs fetched: ' . $uniqueIps->count();

            // Operating System Statistics
            $logs[] = 'Fetching OS statistics...';
            $allLogs = WebActivityLog::where('url', 'not like', '%/api/%')
                ->where('url', 'not like', '%/admin/%')
                ->select('user_agent')->get();
            
            $osStats = $allLogs->map(function ($item) {
                    // Detect OS from user agent
                    $os = 'Unknown';
                    $userAgent = $item->user_agent ?? '';
                    
                    if (preg_match('/Windows NT 10/i', $userAgent)) {
                        $os = 'Windows 10/11';
                    } elseif (preg_match('/Windows NT 6.3/i', $userAgent)) {
                        $os = 'Windows 8.1';
                    } elseif (preg_match('/Windows NT 6.2/i', $userAgent)) {
                        $os = 'Windows 8';
                    } elseif (preg_match('/Windows NT 6.1/i', $userAgent)) {
                        $os = 'Windows 7';
                    } elseif (preg_match('/Windows/i', $userAgent)) {
                        $os = 'Windows Other';
                    } elseif (preg_match('/Macintosh|Mac OS X/i', $userAgent)) {
                        $os = 'macOS';
                    } elseif (preg_match('/Linux/i', $userAgent) && !preg_match('/Android/i', $userAgent)) {
                        $os = 'Linux';
                    } elseif (preg_match('/Android/i', $userAgent)) {
                        $os = 'Android';
                    } elseif (preg_match('/iPhone|iPad|iPod/i', $userAgent)) {
                        $os = 'iOS';
                    } elseif (preg_match('/CrOS/i', $userAgent)) {
                        $os = 'Chrome OS';
                    }
                    
                    return $os;
                })
                ->countBy()
                ->map(function ($count, $os) {
                    return [
                        'os' => $os,
                        'count' => $count
                    ];
                })
                ->values()
                ->sortByDesc('count')
                ->values();

            // 1. Device Type Distribution
            $deviceStats = WebActivityLog::where('url', 'not like', '%/api/%')
                ->where('url', 'not like', '%/admin/%')
                ->select('device_type', DB::raw('count(*) as count'))
                ->whereNotNull('device_type')
                ->groupBy('device_type')
                ->orderBy('count', 'desc')
                ->get();

            // 2. Peak Hours (24-hour breakdown)
            $peakHours = WebActivityLog::where('url', 'not like', '%/api/%')
                ->where('url', 'not like', '%/admin/%')
                ->select(
                    DB::raw('HOUR(created_at) as hour'),
                    DB::raw('count(*) as count')
                )
                ->groupBy('hour')
                ->orderBy('hour', 'asc')
                ->get()
                ->mapWithKeys(function ($item) {
                    return [$item->hour => $item->count];
                });
            
            // Fill in missing hours with 0 and format as array of objects
            $completePeakHours = collect(range(0, 23))->map(function ($hour) use ($peakHours) {
                return [
                    'hour' => $hour,
                    'count' => $peakHours->get($hour, 0)
                ];
            })->values();

            // 3. Top Pages/Routes (top 20)
            $topPages = WebActivityLog::where('url', 'not like', '%/api/%')
                ->where('url', 'not like', '%/admin/%')
                ->select('route_name', 'url', DB::raw('count(*) as count'))
                ->groupBy('route_name', 'url')
                ->orderBy('count', 'desc')
                ->take(20)
                ->get()
                ->map(function ($item) {
                    // Use route name if available, otherwise extract from URL
                    $displayName = $item->route_name;
                    
                    if (!$displayName && $item->url) {
                        // Extract path from URL
                        $path = parse_url($item->url, PHP_URL_PATH);
                        $displayName = $path ?: $item->url;
                        
                        // Clean up the path for better display
                        $displayName = trim($displayName, '/');
                        if (empty($displayName)) {
                            $displayName = 'Home Page';
                        }
                    }
                    
                    if (!$displayName) {
                        $displayName = 'Unknown Route';
                    }
                    
                    return [
                        'route_name' => ucwords(str_replace(['/', '-', '_'], [' > ', ' ', ' '], $displayName)),
                        'url' => $item->url,
                        'count' => $item->count
                    ];
                });

            // 4. Referrer Sources (top 15)
            $referrerSources = WebActivityLog::where('url', 'not like', '%/api/%')
                ->where('url', 'not like', '%/admin/%')
                ->select('referrer', DB::raw('count(*) as count'))
                ->whereNotNull('referrer')
                ->where('referrer', '!=', '')
                ->groupBy('referrer')
                ->orderBy('count', 'desc')
                ->take(15)
                ->get()
                ->map(function ($item) {
                    // Extract domain from referrer
                    $domain = 'Direct / Unknown';
                    if ($item->referrer) {
                        $parsedUrl = parse_url($item->referrer);
                        $domain = $parsedUrl['host'] ?? $item->referrer;
                    }
                    return [
                        'source' => $domain,
                        'count' => $item->count,
                        'full_url' => $item->referrer
                    ];
                });
            
            // Add direct traffic count
            $directTraffic = WebActivityLog::where('url', 'not like', '%/api/%')
                ->where('url', 'not like', '%/admin/%')
                ->where(function($q) {
                    $q->whereNull('referrer')->orWhere('referrer', '');
                })->count();
            
            if ($directTraffic > 0) {
                $referrerSources->prepend([
                    'source' => 'Direct / None',
                    'count' => $directTraffic,
                    'full_url' => null
                ]);
            }

            // 5. Status Code Distribution (errors tracking)
            $statusCodes = WebActivityLog::where('url', 'not like', '%/api/%')
                ->where('url', 'not like', '%/admin/%')
                ->select('status_code', DB::raw('count(*) as count'))
                ->whereNotNull('status_code')
                ->where('status_code', '>', 0) // Exclude zero values (old records)
                ->groupBy('status_code')
                ->orderBy('count', 'desc')
                ->get()
                ->map(function ($item) {
                    $code = $item->status_code;
                    $label = 'Unknown';
                    $category = 'unknown';
                    
                    if ($code >= 200 && $code < 300) {
                        $label = $code . ' - Success';
                        $category = 'success';
                    } elseif ($code >= 300 && $code < 400) {
                        $label = $code . ' - Redirect';
                        $category = 'redirect';
                    } elseif ($code >= 400 && $code < 500) {
                        $label = $code . ' - Client Error';
                        $category = 'client_error';
                    } elseif ($code >= 500) {
                        $label = $code . ' - Server Error';
                        $category = 'server_error';
                    }
                    
                    return [
                        'code' => $code,
                        'label' => $label,
                        'category' => $category,
                        'count' => $item->count
                    ];
                });

            // 6. Average Time Spent (where tracked)
            $avgTimeSpent = WebActivityLog::where('url', 'not like', '%/api/%')
                ->where('url', 'not like', '%/admin/%')
                ->whereNotNull('time_spent_seconds')
                ->where('time_spent_seconds', '>', 0)
                ->avg('time_spent_seconds');
            
            $timeSpentStats = [
                'average_seconds' => $avgTimeSpent ? round($avgTimeSpent, 2) : 0,
                'total_tracked' => WebActivityLog::where('url', 'not like', '%/api/%')->where('url', 'not like', '%/admin/%')->whereNotNull('time_spent_seconds')->where('time_spent_seconds', '>', 0)->count(),
            ];
            
            Log::info('Time Spent Stats - Average: ' . ($timeSpentStats['average_seconds'] ?? 0) . 's, Tracked: ' . ($timeSpentStats['total_tracked'] ?? 0));

            // 7. UTM Campaign Performance
            $utmStats = collect();
            try {
                if (Schema::hasColumn('web_activity_logs', 'utm_source')) {
                    $utmStats = WebActivityLog::where('url', 'not like', '%/api/%')
                        ->where('url', 'not like', '%/admin/%')
                        ->select(
                            'utm_source',
                            'utm_medium', 
                            'utm_campaign',
                            DB::raw('count(*) as count')
                        )
                        ->where(function($q) {
                            $q->whereNotNull('utm_source')
                              ->orWhereNotNull('utm_medium')
                              ->orWhereNotNull('utm_campaign');
                        })
                        ->groupBy('utm_source', 'utm_medium', 'utm_campaign')
                        ->orderBy('count', 'desc')
                        ->take(10)
                        ->get()
                        ->map(function ($item) {
                            $campaign = [];
                            if ($item->utm_source) $campaign[] = 'Source: ' . $item->utm_source;
                            if ($item->utm_medium) $campaign[] = 'Medium: ' . $item->utm_medium;
                            if ($item->utm_campaign) $campaign[] = 'Campaign: ' . $item->utm_campaign;
                            
                            return [
                                'campaign' => implode(' | ', $campaign) ?: 'Unknown Campaign',
                                'source' => $item->utm_source,
                                'medium' => $item->utm_medium,
                                'campaign_name' => $item->utm_campaign,
                                'count' => $item->count
                            ];
                        });
                }
            } catch (\Exception $e) {
                Log::warning('UTM stats failed: ' . $e->getMessage());
            }

            // 8. Bot vs Human Traffic
            $botStats = [
                'human' => 0,
                'bot' => 0,
                'total' => WebActivityLog::where('url', 'not like', '%/api/%')->where('url', 'not like', '%/admin/%')->count(),
            ];
            try {
                if (Schema::hasColumn('web_activity_logs', 'is_bot')) {
                    $botStats['human'] = WebActivityLog::where('is_bot', false)->where('url', 'not like', '%/api/%')->where('url', 'not like', '%/admin/%')->count();
                    $botStats['bot'] = WebActivityLog::where('is_bot', true)->where('url', 'not like', '%/api/%')->where('url', 'not like', '%/admin/%')->count();
                }
            } catch (\Exception $e) {
                Log::warning('Bot stats failed: ' . $e->getMessage());
            }

            // 9. Scroll Depth Distribution
            $scrollDepthStats = WebActivityLog::where('url', 'not like', '%/api/%')
                ->where('url', 'not like', '%/admin/%')
                ->selectRaw('
                    CASE 
                        WHEN scroll_depth_percent >= 90 THEN "90-100%"
                        WHEN scroll_depth_percent >= 75 THEN "75-89%"
                        WHEN scroll_depth_percent >= 50 THEN "50-74%"
                        WHEN scroll_depth_percent >= 25 THEN "25-49%"
                        WHEN scroll_depth_percent > 0 THEN "1-24%"
                        ELSE "No Data"
                    END as depth_range,
                    count(*) as count
                ')
                ->whereNotNull('scroll_depth_percent')
                ->groupBy('depth_range')
                ->orderByRaw('
                    CASE depth_range
                        WHEN "90-100%" THEN 1
                        WHEN "75-89%" THEN 2
                        WHEN "50-74%" THEN 3
                        WHEN "25-49%" THEN 4
                        WHEN "1-24%" THEN 5
                        ELSE 6
                    END
                ')
                ->get()
                ->map(function($item) {
                    return [
                        'range' => $item->depth_range,
                        'count' => $item->count
                    ];
                });

            // 10. Click Tracking - Top Interactions
            $clickTrackingStats = WebActivityLog::where('url', 'not like', '%/api/%')
                ->where('url', 'not like', '%/admin/%')
                ->whereNotNull('interactions')
                ->get()
                ->flatMap(function($log) {
                    $interactions = is_string($log->interactions) 
                        ? json_decode($log->interactions, true) 
                        : $log->interactions;
                    return $interactions ?? [];
                })
                ->groupBy('element')
                ->map(function($group, $element) {
                    return [
                        'element' => $element,
                        'count' => $group->count()
                    ];
                })
                ->sortByDesc('count')
                ->take(15)
                ->values();

            // 11. Time on Page Distribution
            $timeOnPageStats = WebActivityLog::where('url', 'not like', '%/api/%')
                ->where('url', 'not like', '%/admin/%')
                ->selectRaw('
                    CASE 
                        WHEN time_spent_seconds >= 300 THEN "5+ min"
                        WHEN time_spent_seconds >= 180 THEN "3-5 min"
                        WHEN time_spent_seconds >= 60 THEN "1-3 min"
                        WHEN time_spent_seconds >= 30 THEN "30-60 sec"
                        WHEN time_spent_seconds >= 10 THEN "10-30 sec"
                        WHEN time_spent_seconds > 0 THEN "0-10 sec"
                        ELSE "No Data"
                    END as time_range,
                    count(*) as count,
                    avg(time_spent_seconds) as avg_seconds
                ')
                ->whereNotNull('time_spent_seconds')
                ->where('time_spent_seconds', '>', 0)
                ->groupBy('time_range')
                ->orderByRaw('
                    CASE time_range
                        WHEN "5+ min" THEN 1
                        WHEN "3-5 min" THEN 2
                        WHEN "1-3 min" THEN 3
                        WHEN "30-60 sec" THEN 4
                        WHEN "10-30 sec" THEN 5
                        WHEN "0-10 sec" THEN 6
                        ELSE 7
                    END
                ')
                ->get()
                ->map(function($item) {
                    return [
                        'range' => $item->time_range,
                        'count' => $item->count,
                        'avg_seconds' => round($item->avg_seconds, 1)
                    ];
                });

            // 12. Exit Pages - Where users leave
            $exitPagesStats = WebActivityLog::where('url', 'not like', '%/api/%')
                ->where('url', 'not like', '%/admin/%')
                ->select('url', 'route_name')
                ->selectRaw('count(*) as exit_count')
                ->where('is_exit', true)
                ->groupBy('url', 'route_name')
                ->orderByDesc('exit_count')
                ->limit(15)
                ->get()
                ->map(function($page) {
                    return [
                        'url' => $page->url,
                        'route_name' => $page->route_name ? ucwords(str_replace('.', ' > ', $page->route_name)) : 'Unknown Route',
                        'exit_count' => $page->exit_count
                    ];
                });
            
            Log::info('Exit Pages count: ' . $exitPagesStats->count());
            Log::info('Exit Pages data: ' . json_encode($exitPagesStats->take(3)));

            // 13. Conversion Funnel - Landing → Job View → Application (simplified for now)
            $funnelStats = [
                'landing_pages' => 0,
                'job_views' => WebActivityLog::where('url', 'not like', '%/api/%')->where('url', 'not like', '%/admin/%')->where(function($q) {
                    $q->where('route_name', 'like', '%job%')->orWhere('url', 'like', '%/jobs/%');
                })->count(),
                'applications' => WebActivityLog::where('url', 'not like', '%/api/%')->where('url', 'not like', '%/admin/%')->where(function($q) {
                    $q->where('route_name', 'like', '%apply%')->orWhere('url', 'like', '%/apply%');
                })->count(),
            ];
            try {
                if (Schema::hasColumn('web_activity_logs', 'is_landing_page')) {
                    $funnelStats['landing_pages'] = WebActivityLog::where('is_landing_page', true)->where('url', 'not like', '%/api/%')->where('url', 'not like', '%/admin/%')->count();
                }
            } catch (\Exception $e) {
                Log::warning('Landing pages count failed: ' . $e->getMessage());
            }
            $funnelStats['landing_to_job_rate'] = $funnelStats['landing_pages'] > 0 
                ? round(($funnelStats['job_views'] / $funnelStats['landing_pages']) * 100, 1) 
                : 0;
            $funnelStats['job_to_application_rate'] = $funnelStats['job_views'] > 0 
                ? round(($funnelStats['applications'] / $funnelStats['job_views']) * 100, 1) 
                : 0;

            // 14. Real-time Activity - Last 10 minutes (excluding admin/current user IPs)
            $tenMinutesAgo = now()->subMinutes(10);
            $liveActivity = WebActivityLog::where('created_at', '>=', $tenMinutesAgo)
                ->whereNotIn('ip_address', $excludeIPs)
                ->where('url', 'not like', '%/api/%')
                ->where('url', 'not like', '%/admin/%')
                ->orderBy('created_at', 'desc')
                ->limit(100)
                ->get()
                ->map(function($log) {
                    return [
                        'id' => $log->id,
                        'ip_address' => $log->ip_address,
                        'url' => $log->url,
                        'route_name' => $log->route_name,
                        'device_type' => $log->device_type,
                        'country' => $log->country,
                        'city' => $log->city,
                        'is_mobile' => stripos($log->user_agent, 'mobile') !== false,
                        'created_at' => $log->created_at->toIso8601String(),
                        'time_ago' => $log->created_at->diffForHumans(),
                    ];
                });

            // 15. User Journey Visualization - Last 10 minutes (excluding admin/current user IPs)
            $tenMinutesAgo = now()->subMinutes(10);
            
            // Landing pages (entry points)
            $landingPages = collect();
            $browsingFlow = collect();
            $conversions = collect();
            
            try {
                if (Schema::hasColumn('web_activity_logs', 'is_landing_page')) {
                    $landingPages = WebActivityLog::where('created_at', '>=', $tenMinutesAgo)
                        ->whereNotIn('ip_address', $excludeIPs)
                        ->where('is_landing_page', true)
                        ->where('url', 'not like', '%/api/%')
                        ->where('url', 'not like', '%/admin/%')
                        ->select(
                            'route_name', 
                            'url', 
                            \DB::raw('COUNT(*) as count'),
                            \DB::raw('GROUP_CONCAT(DISTINCT ip_address SEPARATOR ", ") as ip_addresses')
                        )
                        ->groupBy('route_name', 'url')
                        ->orderBy('count', 'desc')
                        ->limit(10)
                        ->get();

                    $browsingFlow = WebActivityLog::where('created_at', '>=', $tenMinutesAgo)
                        ->whereNotIn('ip_address', $excludeIPs)
                        ->where('is_landing_page', false)
                        ->where('url', 'not like', '%/api/%')
                        ->where('url', 'not like', '%/admin/%')
                        ->select(
                            'route_name', 
                            'url', 
                            \DB::raw('COUNT(*) as count'),
                            \DB::raw('AVG(time_spent_seconds) as avg_time_spent'),
                            \DB::raw('GROUP_CONCAT(DISTINCT ip_address SEPARATOR ", ") as ip_addresses')
                        )
                        ->groupBy('route_name', 'url')
                        ->orderBy('count', 'desc')
                        ->limit(10)
                        ->get();
                }
            } catch (\Exception $e) {
                Log::warning('Landing/browsing flow failed: ' . $e->getMessage());
            }

            // Conversions (users completing key actions)
            try {
                if (Schema::hasColumn('web_activity_logs', 'conversion_type')) {
                    $conversions = WebActivityLog::where('created_at', '>=', $tenMinutesAgo)
                        ->whereNotIn('ip_address', $excludeIPs)
                        ->whereNotNull('conversion_type')
                        ->select(
                            'conversion_type', 
                            'funnel_stage',
                            \DB::raw('COUNT(*) as count'),
                            \DB::raw('GROUP_CONCAT(DISTINCT ip_address SEPARATOR ", ") as ip_addresses')
                        )
                        ->groupBy('conversion_type', 'funnel_stage')
                        ->orderBy('count', 'desc')
                        ->limit(10)
                        ->get();
                }
            } catch (\Exception $e) {
                Log::warning('Conversions failed: ' . $e->getMessage());
            }

            $userJourney = [
                'landingPages' => $landingPages,
                'browsingFlow' => $browsingFlow,
                'conversions' => $conversions,
            ];

            // 16. Active Users Right Now (last 5 minutes) - excluding admin/API routes for consistency
            $activeUsersCount = WebActivityLog::where('created_at', '>=', now()->subMinutes(5))
                ->where('url', 'not like', '%/api/%')
                ->where('url', 'not like', '%/admin/%')
                ->distinct('ip_address')
                ->count('ip_address');

            // 16. Week-over-Week Comparison
            $thisWeekStart = now()->startOfWeek();
            $lastWeekStart = now()->subWeek()->startOfWeek();
            $lastWeekEnd = now()->subWeek()->endOfWeek();
            
            $thisWeekVisits = WebActivityLog::where('created_at', '>=', $thisWeekStart)->where('url', 'not like', '%/api/%')->where('url', 'not like', '%/admin/%')->count();
            $lastWeekVisits = WebActivityLog::whereBetween('created_at', [$lastWeekStart, $lastWeekEnd])->where('url', 'not like', '%/api/%')->where('url', 'not like', '%/admin/%')->count();
            
            $weekOverWeekChange = $lastWeekVisits > 0 
                ? round((($thisWeekVisits - $lastWeekVisits) / $lastWeekVisits) * 100, 1) 
                : 0;

            // 17. Month-over-Month Comparison
            $thisMonthStart = now()->startOfMonth();
            $lastMonthStart = now()->subMonth()->startOfMonth();
            $lastMonthEnd = now()->subMonth()->endOfMonth();
            
            $thisMonthVisits = WebActivityLog::where('created_at', '>=', $thisMonthStart)->where('url', 'not like', '%/api/%')->where('url', 'not like', '%/admin/%')->count();
            $lastMonthVisits = WebActivityLog::whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->where('url', 'not like', '%/api/%')->where('url', 'not like', '%/admin/%')->count();
            
            $monthOverMonthChange = $lastMonthVisits > 0 
                ? round((($thisMonthVisits - $lastMonthVisits) / $lastMonthVisits) * 100, 1) 
                : 0;

            // 18. Growth Trends - Last 8 weeks
            $weeklyGrowthTrends = collect(range(7, 0))->map(function($weeksAgo) {
                $weekStart = now()->subWeeks($weeksAgo)->startOfWeek();
                $weekEnd = now()->subWeeks($weeksAgo)->endOfWeek();
                
                return [
                    'week_label' => $weekStart->format('M d'),
                    'total_visits' => WebActivityLog::whereBetween('created_at', [$weekStart, $weekEnd])->where('url', 'not like', '%/api/%')->where('url', 'not like', '%/admin/%')->count(),
                    'unique_visitors' => WebActivityLog::whereBetween('created_at', [$weekStart, $weekEnd])
                        ->where('url', 'not like', '%/api/%')
                        ->where('url', 'not like', '%/admin/%')
                        ->distinct('ip_address')
                        ->count('ip_address'),
                ];
            });

            // 19. Day of Week Patterns
            $dayOfWeekStats = WebActivityLog::where('url', 'not like', '%/api/%')
                ->where('url', 'not like', '%/admin/%')
                ->selectRaw('
                    DAYNAME(created_at) as day_name,
                    DAYOFWEEK(created_at) as day_number,
                    count(*) as count
                ')
                ->groupBy('day_name', 'day_number')
                ->orderBy('day_number')
                ->get()
                ->map(function($item) {
                    return [
                        'day' => $item->day_name,
                        'count' => $item->count
                    ];
                });

            // 20. Bounce Rate per Page - Pages with only one visit in session
            $bounceRateStats = collect();
            try {
                if (Schema::hasColumn('web_activity_logs', 'is_exit') && Schema::hasColumn('web_activity_logs', 'is_landing_page')) {
                    $bounceRateStats = WebActivityLog::selectRaw('
                            route_name,
                            url,
                            count(*) as total_visits,
                            SUM(CASE WHEN is_exit = 1 AND is_landing_page = 1 THEN 1 ELSE 0 END) as bounces
                        ')
                        ->groupBy('route_name', 'url')
                        ->havingRaw('total_visits > 5') // Only show pages with at least 5 visits
                        ->orderByDesc('bounces')
                        ->limit(15)
                        ->get()
                        ->map(function($page) {
                            $bounceRate = $page->total_visits > 0 ? round(($page->bounces / $page->total_visits) * 100, 1) : 0;
                            return [
                                'route_name' => $page->route_name ? ucwords(str_replace('.', ' > ', $page->route_name)) : 'Unknown Route',
                                'url' => $page->url,
                                'total_visits' => $page->total_visits,
                                'bounces' => $page->bounces,
                                'bounce_rate' => $bounceRate
                            ];
                        });
                }
            } catch (\Exception $e) {
                Log::warning('Bounce rate stats failed: ' . $e->getMessage());
            }

            // 21. Return Visitor Rate
            $totalUniqueIps = WebActivityLog::distinct('ip_address')->count('ip_address');
            $returningIps = WebActivityLog::selectRaw('ip_address, count(*) as visit_count')
                ->groupBy('ip_address')
                ->havingRaw('visit_count > 1')
                ->count();
            
            $returnVisitorRate = $totalUniqueIps > 0 ? round(($returningIps / $totalUniqueIps) * 100, 1) : 0;

            // 22. Page Performance - Average response times per route
            $pagePerformanceStats = collect();
            try {
                if (Schema::hasColumn('web_activity_logs', 'response_time_ms')) {
                    $pagePerformanceStats = WebActivityLog::selectRaw('
                            route_name,
                            url,
                            count(*) as request_count,
                            avg(response_time_ms) as avg_response_time,
                            min(response_time_ms) as min_response_time,
                            max(response_time_ms) as max_response_time
                        ')
                        ->whereNotNull('response_time_ms')
                        ->where('response_time_ms', '>', 0)
                        ->groupBy('route_name', 'url')
                        ->orderByDesc('avg_response_time')
                        ->limit(15)
                        ->get()
                        ->map(function($page) {
                            return [
                                'route_name' => $page->route_name ? ucwords(str_replace('.', ' > ', $page->route_name)) : 'Unknown Route',
                                'url' => $page->url,
                                'request_count' => $page->request_count,
                                'avg_response_time' => round($page->avg_response_time, 0),
                                'min_response_time' => round($page->min_response_time, 0),
                                'max_response_time' => round($page->max_response_time, 0),
                            ];
                        });
                }
            } catch (\Exception $e) {
                Log::warning('Page performance stats failed: ' . $e->getMessage());
            }

            // 23. Geographic Data - Top Cities
            $topCities = WebActivityLog::selectRaw('
                    city,
                    region,
                    country,
                    count(*) as visit_count
                ')
                ->whereNotNull('city')
                ->groupBy('city', 'region', 'country')
                ->orderByDesc('visit_count')
                ->limit(20)
                ->get()
                ->map(function($item) {
                    return [
                        'city' => $item->city,
                        'region' => $item->region,
                        'country' => $item->country,
                        'visit_count' => $item->visit_count,
                        'display' => $item->city . ($item->region ? ', ' . $item->region : '') . ($item->country ? ' (' . $item->country . ')' : '')
                    ];
                });

            // 24. Top ISPs/Organizations
            $topIsps = WebActivityLog::selectRaw('
                    isp,
                    count(*) as visit_count
                ')
                ->whereNotNull('isp')
                ->groupBy('isp')
                ->orderByDesc('visit_count')
                ->limit(15)
                ->get()
                ->map(function($item) {
                    return [
                        'isp' => $item->isp,
                        'visit_count' => $item->visit_count
                    ];
                });

            // 25. Timezone Distribution
            $timezoneStats = WebActivityLog::selectRaw('
                    timezone,
                    count(*) as visit_count
                ')
                ->whereNotNull('timezone')
                ->groupBy('timezone')
                ->orderByDesc('visit_count')
                ->limit(10)
                ->get()
                ->map(function($item) {
                    return [
                        'timezone' => $item->timezone,
                        'visit_count' => $item->visit_count
                    ];
                });
            
            $logs[] = 'All data fetched successfully!';
            
            return response()->json([
                'debug_logs' => $logs,
                'currentUserIp' => $request->ip(),
                'latestLogs' => $latestLogs,
                'trafficByUserAgent' => $trafficByUserAgent,
                'visitsPerDay' => $visitsPerDay,
                'uniqueIps' => $uniqueIps,
                'osStats' => $osStats,
                'deviceStats' => $deviceStats,
                'peakHours' => $completePeakHours,
                'topPages' => $topPages,
                'referrerSources' => $referrerSources,
                'statusCodes' => $statusCodes,
                'timeSpentStats' => $timeSpentStats,
                'utmStats' => $utmStats,
                'botStats' => $botStats,
                'scrollDepthStats' => $scrollDepthStats,
                'clickTrackingStats' => $clickTrackingStats,
                'timeOnPageStats' => $timeOnPageStats,
                'exitPagesStats' => $exitPagesStats,
                'funnelStats' => $funnelStats,
                'liveActivity' => $liveActivity,
                'userJourney' => $userJourney,
                'activeUsersCount' => $activeUsersCount,
                'weekOverWeekChange' => $weekOverWeekChange,
                'monthOverMonthChange' => $monthOverMonthChange,
                'thisWeekVisits' => $thisWeekVisits,
                'lastWeekVisits' => $lastWeekVisits,
                'thisMonthVisits' => $thisMonthVisits,
                'lastMonthVisits' => $lastMonthVisits,
                'weeklyGrowthTrends' => $weeklyGrowthTrends,
                'dayOfWeekStats' => $dayOfWeekStats,
                'bounceRateStats' => $bounceRateStats,
                'returnVisitorRate' => $returnVisitorRate,
                'totalUniqueIps' => $totalUniqueIps,
                'returningIps' => $returningIps,
                'pagePerformanceStats' => $pagePerformanceStats,
                'topCities' => $topCities,
                'topIsps' => $topIsps,
                'timezoneStats' => $timezoneStats,
                
                // Search Analytics
                'searchStats' => $this->getSearchAnalytics(),
            ]);
        } catch (\Exception $e) {
            $logs[] = 'ERROR OCCURRED: ' . $e->getMessage();
            $logs[] = 'Error at line: ' . $e->getLine();
            $logs[] = 'Error in file: ' . $e->getFile();
            
            Log::error('WebActivityLog data error: ' . $e->getMessage());
            Log::error('WebActivityLog data error trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'error' => 'Error fetching data',
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'debug_logs' => $logs,
                'trace' => explode("\n", $e->getTraceAsString())
            ], 500);
        }
    }

    /**
     * Get comprehensive search analytics
     */
    private function getSearchAnalytics()
    {
        try {
            // Debug: Check total count
            $totalCount = \App\Models\Search::count();
            Log::info("Search table total records: " . $totalCount);
            
            // Top Search Terms
            $topSearchTerms = \App\Models\Search::selectRaw('
                    search_text,
                    COUNT(*) as count,
                    ROUND(AVG(result_num), 0) as avg_results
                ')
                ->whereNotNull('search_text')
                ->where('search_text', '!=', '')
                ->groupBy('search_text')
                ->orderByDesc('count')
                ->limit(20)
                ->get();
            
            Log::info("Top search terms count: " . $topSearchTerms->count());

            // Zero Result Searches
            $zeroResultSearches = \App\Models\Search::selectRaw('
                    search_text,
                    COUNT(*) as count
                ')
                ->where('result_num', 0)
                ->whereNotNull('search_text')
                ->where('search_text', '!=', '')
                ->groupBy('search_text')
                ->orderByDesc('count')
                ->limit(15)
                ->get();

            // Filter Usage Statistics
            $filterUsage = \App\Models\Search::whereNotNull('filter')
                ->where('filter', '!=', '')
                ->where('filter', '!=', '[]')
                ->get()
                ->flatMap(function($search) {
                    $filters = $search->filter;
                    
                    // If it's still a string, decode it manually
                    if (is_string($filters)) {
                        $filters = json_decode($filters, true);
                    }
                    
                    // Make sure it's an array and not empty
                    if (!is_array($filters) || empty($filters)) {
                        return [];
                    }
                    
                    return array_keys($filters);
                })
                ->countBy()
                ->map(function($count, $filterType) {
                    return [
                        'filter_type' => $filterType,
                        'count' => $count
                    ];
                })
                ->sortByDesc('count')
                ->values();

            // Search Types Distribution
            $searchTypes = \App\Models\Search::selectRaw('
                    type,
                    COUNT(*) as count
                ')
                ->groupBy('type')
                ->get();

            // Searches Over Time (last 30 days)
            $searchesOverTime = collect();
            $today = \Carbon\Carbon::now();
            
            // Debug: Check date range of searches
            $oldestSearch = \App\Models\Search::orderBy('created_at', 'asc')->first();
            $newestSearch = \App\Models\Search::orderBy('created_at', 'desc')->first();
            Log::info("Search date range - Oldest: " . ($oldestSearch ? $oldestSearch->created_at : 'none') . ", Newest: " . ($newestSearch ? $newestSearch->created_at : 'none'));
            
            for ($i = 29; $i >= 0; $i--) {
                $date = $today->copy()->subDays($i);
                $dateString = $date->format('Y-m-d');
                
                $count = \App\Models\Search::whereDate('created_at', $dateString)->count();
                
                Log::info("Date: $dateString - Count: $count");
                
                $searchesOverTime->push([
                    'date' => $dateString,
                    'count' => $count
                ]);
            }
            
            Log::info("SearchesOverTime total entries: " . $searchesOverTime->count());

            $totalSearches = \App\Models\Search::count();
            $totalUniqueSearchTerms = \App\Models\Search::distinct('search_text')->count('search_text');
            $avgResultsPerSearch = round(\App\Models\Search::avg('result_num'), 1);
            
            // Recent Searches (last 50)
            $recentSearches = \App\Models\Search::latest()
                ->limit(50)
                ->get()
                ->map(function($search) {
                    // Decode filters if they're stored as JSON
                    $filters = $search->filter;
                    if (is_string($filters)) {
                        $filters = json_decode($filters, true);
                    }
                    
                    return [
                        'search_text' => $search->search_text,
                        'type' => $search->type,
                        'result_num' => $search->result_num,
                        'ip' => $search->ip,
                        'filters' => $filters && is_array($filters) ? $filters : [],
                        'created_at' => $search->created_at?->toISOString(),
                        'user_id' => $search->user_id,
                    ];
                });
            
            Log::info("Search Analytics Summary - Total: $totalSearches, Unique: $totalUniqueSearchTerms, Avg: $avgResultsPerSearch, Recent: " . $recentSearches->count());
            
            return [
                'topSearchTerms' => $topSearchTerms,
                'zeroResultSearches' => $zeroResultSearches,
                'filterUsage' => $filterUsage,
                'searchTypes' => $searchTypes,
                'searchesOverTime' => $searchesOverTime,
                'totalSearches' => $totalSearches,
                'totalUniqueSearchTerms' => $totalUniqueSearchTerms,
                'avgResultsPerSearch' => $avgResultsPerSearch,
                'recentSearches' => $recentSearches,
            ];
        } catch (\Exception $e) {
            Log::error('Search analytics error: ' . $e->getMessage());
            return [
                'topSearchTerms' => collect([]),
                'zeroResultSearches' => collect([]),
                'filterUsage' => collect([]),
                'searchTypes' => collect([]),
                'searchesOverTime' => collect([]),
                'totalSearches' => 0,
                'totalUniqueSearchTerms' => 0,
                'avgResultsPerSearch' => 0,
            ];
        }
    }

    /**
     * Ban an IP address
     */
    public function banIp(Request $request)
    {
        try {
            $validated = $request->validate([
                'ip_address' => 'required|string'
            ]);

            $affected = WebActivityLog::where('ip_address', $validated['ip_address'])
                ->update(['is_banned' => 1]);

            Log::info('IP banned', [
                'ip_address' => $validated['ip_address'],
                'rows_affected' => $affected,
                'admin_id' => auth()->id()
            ]);

            return response()->json([
                'message' => 'IP banned successfully',
                'ip_address' => $validated['ip_address'],
                'rows_affected' => $affected
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Ban IP validation error', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            return response()->json([
                'error' => 'Validation failed',
                'details' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Ban IP error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            return response()->json([
                'error' => 'Error banning IP',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Unban an IP address
     */
    public function unbanIp(Request $request)
    {
        try {
            $validated = $request->validate([
                'ip_address' => 'required|string'
            ]);

            $affected = WebActivityLog::where('ip_address', $validated['ip_address'])
                ->update(['is_banned' => 0]);

            Log::info('IP unbanned', [
                'ip_address' => $validated['ip_address'],
                'rows_affected' => $affected,
                'admin_id' => auth()->id()
            ]);

            return response()->json([
                'message' => 'IP unbanned successfully',
                'ip_address' => $validated['ip_address'],
                'rows_affected' => $affected
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Unban IP validation error', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            return response()->json([
                'error' => 'Validation failed',
                'details' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Unban IP error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            return response()->json([
                'error' => 'Error unbanning IP',
                'message' => $e->getMessage()
            ], 500);
        }
    }



    public function grabFirstCharacter($str)
    {
        return $str[0];
    }

    /**
     * Handle unsubscribe email POST request
    */
    public function unsubscribeEmail(Request $request)
    {
        // Here you would mark the user/email as unsubscribed in your database or mailing list provider.
        // For demo, just return success. You can extend this to actually update a DB table if needed.

        // Optionally, you can get the email from the request or session if you want to track who unsubscribed.
        // $email = $request->input('email');

        return response()->json(['success' => true]);
    }

    public function randomColor()
    {
        $colors = array("red",  "yellow", "green", "blue",  "purple", "pink", "indigo");
        $num = array_rand($colors);
        return $colors[$num];
    }

    public function sendSiteEmail($toEmail, $title, $body, $firstname)
    {
        $details = [
            'title' => $title,
            'body' => $body,
            'firstname' => $firstname,
        ];

       // while testing, always send to static email, 
       // in prod change to $toEmail
       //Mail::to($toEmail)->send(new SiteMailServer($details, $title));
       Mail::to('codnerkj@gmail.com')->send(new SiteMailServer($details, $title));
    }

    public function createAlphaNumericId(){
        return uniqid().'-'.uniqid().'-'.uniqid().'-'.now()->timestamp;
    }

    public function isEndDateBeforeStartDate($startDate, $endDate) {
        // Convert the date strings to DateTime objects
        $start = new DateTime($startDate);
        $end = new DateTime($endDate);
    
        // Compare the two dates
        if ($end < $start) {
            return true; // End date is before start date
        } else {
            return false; // End date is not before start date
        }
    }

    function isDateInThePast($startDate, $endDate) {
        // Get the current date
        $currentDate = new DateTime();
        
        // Convert the date strings to DateTime objects
        $start = new DateTime($startDate);
        $end = new DateTime($endDate);

        // If both the start and end dates are before the current date, return true
        if ($start < $currentDate && $end < $currentDate) {
            return true;
        }
        
        // Otherwise, return false if either start or end date is in the future
        return false;
    }

    // *********PRONETWORK HELPERS***********
    //returns next number of the current order
    function orderDeterminerForProNetworkProfile($id, $type) { 
        // Get the current date
        $obj = '';
        $result = '';

        if($type === 'education'){
            if(ProNetworkUserProfileEducation::where('user_id', $id)->exists()){
                $obj = ProNetworkUserProfileEducation::where('user_id', $id)->orderBy('order', 'desc')->first();
                if($obj->order===null){$result = '0';}else{$result = $obj->order + 1;}
            }else{
                $result = '0';
            }
        }else if($type === 'experience'){
            if(ProNetworkUserProfileExperience::where('user_id', $id)->exists()){
                $obj = ProNetworkUserProfileExperience::where('user_id', $id)->orderBy('order', 'desc')->first();
                if($obj->order===null){$result = '0';}else{$result = $obj->order + 1;}
                
            }else{
                $result = '0';
            }
        }else if($type === 'honour'){
            if(ProNetworkUserProfileHonour::where('user_id', $id)->exists()){
                $obj = ProNetworkUserProfileHonour::where('user_id', $id)->orderBy('order', 'desc')->first();
                if($obj->order===null){$result = '0';}else{$result = $obj->order + 1;}
            }else{
                $result = '0';
            }
        }else if($type === 'interest'){
            if(ProNetworkUserProfileInterest::where('user_id', $id)->exists()){
                $obj = ProNetworkUserProfileInterest::where('user_id', $id)->orderBy('order', 'desc')->first();
                if($obj->order===null){$result = '0';}else{$result = $obj->order + 1;}
            }else{
                $result = '0';
            }
        }else if($type === 'skill'){
            if(ProNetworkUserProfileSkill::where('user_id', $id)->exists()){
                $obj = ProNetworkUserProfileSkill::where('user_id', $id)->orderBy('order', 'desc')->first();
                if($obj->order===null){$result = '0';}else{$result = $obj->order + 1;}
            }else{
                $result = '0';
            }
        }else if($type === 'volunteer'){
            if(ProNetworkUserProfileVolunteering::where('user_id', $id)->exists()){
                $obj = ProNetworkUserProfileVolunteering::where('user_id', $id)->orderBy('order', 'desc')->first();
                if($obj->order===null){$result = '0';}else{$result = $obj->order + 1;}
            }else{
                $result = '0';
            }
        }
        
        return $result;
    }

    function getRandomProNetworkBackgroundProfileImage() {
        $images = array("background_1.png", "background_2.png", 
                        "background_3.png", "background_4.png",  
                        "background_5.png", "background_6.png", 
                        "background_7.png", "background_8.png",
                        "background_9.png",
                        "background_10.png"
                        ,"background_11.png"
                        ,"background_12.png"
                        ,"background_13.png"
                        ,"background_14.png"
                        ,"background_15.png");
        $num = array_rand($images);
        return $images[$num];
    }



    
}
