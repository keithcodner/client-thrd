<?php

namespace App\Http\Controllers\Support;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class EngagementInsightsController extends Controller
{
    public function getData(Request $request)
    {
        try {
            // Get date range from request or default to last 30 days
            $startDate = $request->input('start_date') 
                ? Carbon::parse($request->input('start_date'))->startOfDay()
                : Carbon::now()->subDays(30)->startOfDay();
            
            $endDate = $request->input('end_date')
                ? Carbon::parse($request->input('end_date'))->endOfDay()
                : Carbon::now()->endOfDay();

            Log::info("Engagement Insights Date Range: " . $startDate . " to " . $endDate);

            // 1. Total Views and Clicks
            $totalViews = DB::table('web_activity_logs')
                ->where('event_type', 'view')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();

            $totalClicks = DB::table('web_activity_logs')
                ->where('event_type', 'click')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();

            Log::info("Total Views: $totalViews, Total Clicks: $totalClicks");

            // 2. Engagement Rate
            $engagementRate = $totalViews > 0 ? round(($totalClicks / $totalViews) * 100, 2) : 0;

            // 3. Active Jobs (jobs with at least 1 view)
            $activeJobs = DB::table('web_activity_logs')
                ->where('event_type', 'view')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->whereNotNull('related_id')
                ->where('related_type', 'job')
                ->distinct('related_id')
                ->count('related_id');

            // 4. Click-Through Rate by Job
            $ctrData = DB::table('web_activity_logs as views')
                ->select(
                    'views.related_id',
                    DB::raw('COUNT(DISTINCT CASE WHEN views.event_type = "view" THEN views.id END) as view_count'),
                    DB::raw('COUNT(DISTINCT CASE WHEN clicks.event_type = "click" THEN clicks.id END) as click_count'),
                    DB::raw('ROUND((COUNT(DISTINCT CASE WHEN clicks.event_type = "click" THEN clicks.id END) / COUNT(DISTINCT CASE WHEN views.event_type = "view" THEN views.id END) * 100), 2) as ctr')
                )
                ->leftJoin('web_activity_logs as clicks', function($join) use ($startDate, $endDate) {
                    $join->on('views.related_id', '=', 'clicks.related_id')
                         ->where('clicks.event_type', '=', 'click')
                         ->whereBetween('clicks.created_at', [$startDate, $endDate]);
                })
                ->where('views.event_type', 'view')
                ->whereNotNull('views.related_id')
                ->where('views.related_type', 'job')
                ->whereBetween('views.created_at', [$startDate, $endDate])
                ->groupBy('views.related_id')
                ->having('view_count', '>', 0)
                ->orderByDesc('ctr')
                ->limit(10)
                ->get();

            // 5. Most Viewed Jobs
            $mostViewedJobs = DB::table('web_activity_logs')
                ->select(
                    'related_id as job_id',
                    DB::raw('COUNT(*) as view_count'),
                    'url',
                    'route_name'
                )
                ->where('event_type', 'view')
                ->whereNotNull('related_id')
                ->where('related_type', 'job')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->groupBy('related_id', 'url', 'route_name')
                ->orderByDesc('view_count')
                ->limit(15)
                ->get()
                ->map(function($job) {
                    // Try to get job details
                    $jobPost = DB::table('job_posts')->find($job->job_id);
                    return [
                        'job_id' => $job->job_id,
                        'title' => $jobPost->title ?? 'Unknown Job',
                        'company' => $jobPost->company_name ?? 'Unknown Company',
                        'view_count' => $job->view_count,
                        'url' => $job->url
                    ];
                });

            Log::info("Most Viewed Jobs count: " . $mostViewedJobs->count());

            // 6. Trending Jobs (gaining views rapidly in last 7 days vs previous 7 days)
            $last7Days = Carbon::now()->subDays(7);
            $previous7Days = Carbon::now()->subDays(14);

            $trendingJobs = DB::table('web_activity_logs')
                ->select(
                    'related_id as job_id',
                    DB::raw('SUM(CASE WHEN created_at >= "' . $last7Days . '" THEN 1 ELSE 0 END) as recent_views'),
                    DB::raw('SUM(CASE WHEN created_at < "' . $last7Days . '" AND created_at >= "' . $previous7Days . '" THEN 1 ELSE 0 END) as previous_views'),
                    DB::raw('((SUM(CASE WHEN created_at >= "' . $last7Days . '" THEN 1 ELSE 0 END) - SUM(CASE WHEN created_at < "' . $last7Days . '" AND created_at >= "' . $previous7Days . '" THEN 1 ELSE 0 END)) / GREATEST(SUM(CASE WHEN created_at < "' . $last7Days . '" AND created_at >= "' . $previous7Days . '" THEN 1 ELSE 0 END), 1) * 100) as growth_rate')
                )
                ->where('event_type', 'view')
                ->whereNotNull('related_id')
                ->where('related_type', 'job')
                ->whereBetween('created_at', [$previous7Days, Carbon::now()])
                ->groupBy('related_id')
                ->having('recent_views', '>', 0)
                ->orderByDesc('growth_rate')
                ->limit(10)
                ->get()
                ->map(function($job) {
                    $jobPost = DB::table('job_posts')->find($job->job_id);
                    return [
                        'job_id' => $job->job_id,
                        'title' => $jobPost->title ?? 'Unknown Job',
                        'company' => $jobPost->company_name ?? 'Unknown Company',
                        'recent_views' => $job->recent_views,
                        'previous_views' => $job->previous_views,
                        'growth_rate' => round($job->growth_rate, 1)
                    ];
                });

            // 7. Zero View Jobs
            $allJobIds = DB::table('job_posts')
                ->where('status', 'active')
                ->pluck('id');

            $viewedJobIds = DB::table('web_activity_logs')
                ->where('event_type', 'view')
                ->where('related_type', 'job')
                ->whereNotNull('related_id')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->distinct()
                ->pluck('related_id');

            $zeroViewJobIds = $allJobIds->diff($viewedJobIds)->take(15);

            $zeroViewJobs = DB::table('job_posts')
                ->whereIn('id', $zeroViewJobIds)
                ->select('id', 'title', 'company_name', 'created_at')
                ->orderByDesc('created_at')
                ->get()
                ->map(function($job) {
                    return [
                        'job_id' => $job->id,
                        'title' => $job->title,
                        'company' => $job->company_name,
                        'posted_at' => Carbon::parse($job->created_at)->diffForHumans()
                    ];
                });

            // 8. Job Category Performance
            $categoryPerformance = DB::table('web_activity_logs')
                ->join('job_posts', 'web_activity_logs.related_id', '=', 'job_posts.id')
                ->select(
                    'job_posts.job_type as category',
                    DB::raw('COUNT(DISTINCT CASE WHEN web_activity_logs.event_type = "view" THEN web_activity_logs.id END) as views'),
                    DB::raw('COUNT(DISTINCT CASE WHEN web_activity_logs.event_type = "click" THEN web_activity_logs.id END) as clicks'),
                    DB::raw('ROUND((COUNT(DISTINCT CASE WHEN web_activity_logs.event_type = "click" THEN web_activity_logs.id END) / GREATEST(COUNT(DISTINCT CASE WHEN web_activity_logs.event_type = "view" THEN web_activity_logs.id END), 1) * 100), 2) as engagement_rate')
                )
                ->where('web_activity_logs.related_type', 'job')
                ->whereBetween('web_activity_logs.created_at', [$startDate, $endDate])
                ->groupBy('job_posts.job_type')
                ->orderByDesc('views')
                ->get();

            // 9. High Views Low Clicks (needs optimization)
            $highViewsLowClicks = DB::table('web_activity_logs')
                ->select(
                    'related_id as job_id',
                    DB::raw('COUNT(CASE WHEN event_type = "view" THEN 1 END) as views'),
                    DB::raw('COUNT(CASE WHEN event_type = "click" THEN 1 END) as clicks'),
                    DB::raw('ROUND((COUNT(CASE WHEN event_type = "click" THEN 1 END) / GREATEST(COUNT(CASE WHEN event_type = "view" THEN 1 END), 1) * 100), 2) as ctr')
                )
                ->whereNotNull('related_id')
                ->where('related_type', 'job')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->groupBy('related_id')
                ->having('views', '>=', 10)
                ->having('ctr', '<', 5)
                ->orderBy('views', 'desc')
                ->limit(10)
                ->get()
                ->map(function($job) {
                    $jobPost = DB::table('job_posts')->find($job->job_id);
                    return [
                        'job_id' => $job->job_id,
                        'title' => $jobPost->title ?? 'Unknown Job',
                        'company' => $jobPost->company_name ?? 'Unknown Company',
                        'views' => $job->views,
                        'clicks' => $job->clicks,
                        'ctr' => $job->ctr
                    ];
                });

            return response()->json([
                'overview' => [
                    'total_views' => $totalViews,
                    'total_clicks' => $totalClicks,
                    'engagement_rate' => $engagementRate,
                    'active_jobs' => $activeJobs
                ],
                'ctr_data' => $ctrData,
                'most_viewed_jobs' => $mostViewedJobs,
                'trending_jobs' => $trendingJobs,
                'zero_view_jobs' => $zeroViewJobs,
                'category_performance' => $categoryPerformance,
                'high_views_low_clicks' => $highViewsLowClicks,
                'date_range' => [
                    'start' => $startDate->toDateString(),
                    'end' => $endDate->toDateString()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Engagement Insights Error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return response()->json([
                'error' => 'Failed to fetch engagement data',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
