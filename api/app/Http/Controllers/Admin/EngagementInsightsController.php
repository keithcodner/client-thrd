<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Posts\JobPost;
use App\Models\Articles\Article;
use Carbon\Carbon;

class EngagementInsightsController extends Controller
{
    public function getData()
    {
        // 1. Overview Stats - Use the views and clicks fields from job_posts table
        $totalViews = JobPost::sum('views') ?? 0;
        $totalClicks = JobPost::sum('clicks') ?? 0;

        $activeJobs = JobPost::where('status', 'COMMITTED')
            ->where(function($query) {
                $query->whereNull('expires_at')
                      ->orWhere('expires_at', '>', now());
            })
            ->count();

        $engagementRate = $totalViews > 0 
            ? round(($totalClicks / $totalViews) * 100, 1) 
            : 0;

        // 2. Most Viewed Jobs - Use the views field directly
        $mostViewedJobs = JobPost::select(
                'id',
                'title as job_title',
                'company_name',
                DB::raw('CONCAT(location_city, ", ", location_state_province) as location'),
                'views as view_count'
            )
            ->where('status', 'COMMITTED')
            ->where('views', '>', 0)
            ->orderByDesc('views')
            ->limit(15)
            ->get();

        // 3. CTR Stats
        $ctrStats = [
            'total_views' => $totalViews,
            'total_clicks' => $totalClicks,
            'overall_ctr' => $engagementRate
        ];

        // 4. Trending Jobs - Jobs posted in last 7 days with views
        $trendingJobs = JobPost::select(
                'id',
                'title as job_title',
                'company_name',
                'views as recent_views',
                'created_at'
            )
            ->where('status', 'COMMITTED')
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->where('views', '>', 0)
            ->orderByDesc('views')
            ->limit(10)
            ->get()
            ->map(function($job) {
                return [
                    'id' => $job->id,
                    'job_title' => $job->job_title,
                    'company_name' => $job->company_name,
                    'recent_views' => $job->recent_views,
                    'days_ago' => Carbon::parse($job->created_at)->diffInDays(Carbon::now())
                ];
            });

        // 5. Zero View Jobs
        $zeroViewJobs = JobPost::select(
                'id',
                'title as job_title',
                'company_name',
                DB::raw('CONCAT(location_city, ", ", location_state_province) as location'),
                'status',
                'created_at',
                DB::raw('DATEDIFF(NOW(), created_at) as days_since_posted')
            )
            ->where('status', 'COMMITTED')
            ->where(function($query) {
                $query->where('views', 0)
                      ->orWhereNull('views');
            })
            ->orderByDesc('created_at')
            ->limit(15)
            ->get();

        // 6. Category Performance - Using primary_tag as category
        $categoryPerformance = JobPost::select(
                'primary_tag as category',
                DB::raw('COUNT(id) as job_count'),
                DB::raw('SUM(COALESCE(views, 0)) as total_views'),
                DB::raw('SUM(COALESCE(clicks, 0)) as total_clicks'),
                DB::raw('ROUND((SUM(COALESCE(clicks, 0)) / NULLIF(SUM(COALESCE(views, 0)), 0) * 100), 2) as ctr')
            )
            ->where('status', 'COMMITTED')
            ->whereNotNull('primary_tag')
            ->groupBy('primary_tag')
            ->orderByDesc('total_views')
            ->limit(10)
            ->get();

        // === NEWS ARTICLES ANALYTICS ===

        // 7. Article Overview Stats
        $totalArticles = Article::where('status', 'published')->count();
        $totalArticleViews = Article::sum('views') ?? 0;
        $articlesThisMonth = Article::where('status', 'published')
            ->where('created_at', '>=', Carbon::now()->startOfMonth())
            ->count();

        // 8. Most Viewed Articles
        $mostViewedArticles = Article::select(
                'id',
                'subject',
                'views',
                'created_at',
                'link'
            )
            ->where('status', 'published')
            ->where('views', '>', 0)
            ->orderByDesc('views')
            ->limit(10)
            ->get()
            ->map(function($article) {
                return [
                    'id' => $article->id,
                    'title' => $article->subject,
                    'views' => $article->views,
                    'published_date' => Carbon::parse($article->created_at)->format('M d, Y'),
                    'days_ago' => Carbon::parse($article->created_at)->diffInDays(Carbon::now())
                ];
            });

        // 9. Recent Articles Performance (last 7 days)
        $recentArticles = Article::select(
                'id',
                'subject',
                'views',
                'created_at'
            )
            ->where('status', 'published')
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->orderByDesc('created_at')
            ->limit(10)
            ->get()
            ->map(function($article) {
                return [
                    'id' => $article->id,
                    'title' => $article->subject,
                    'views' => $article->views ?? 0,
                    'published_date' => Carbon::parse($article->created_at)->format('M d, Y'),
                    'days_ago' => Carbon::parse($article->created_at)->diffInDays(Carbon::now())
                ];
            });

        // 10. Articles with Zero Views
        $zeroViewArticles = Article::select(
                'id',
                'subject',
                'views',
                'created_at'
            )
            ->where('status', 'published')
            ->where(function($query) {
                $query->where('views', 0)
                      ->orWhereNull('views');
            })
            ->orderByDesc('created_at')
            ->limit(10)
            ->get()
            ->map(function($article) {
                return [
                    'id' => $article->id,
                    'title' => $article->subject,
                    'views' => $article->views ?? 0,
                    'days_ago' => Carbon::parse($article->created_at)->diffInDays(Carbon::now())
                ];
            });

        // 11. Article Engagement Trends (last 30 days)
        $articleTrends = Article::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(id) as articles_published'),
                DB::raw('SUM(COALESCE(views, 0)) as total_views')
            )
            ->where('status', 'published')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date', 'asc')
            ->get();

        return response()->json([
            'overview' => [
                'total_views' => $totalViews,
                'total_clicks' => $totalClicks,
                'engagement_rate' => $engagementRate,
                'active_jobs' => $activeJobs
            ],
            'mostViewedJobs' => $mostViewedJobs,
            'ctrStats' => $ctrStats,
            'trendingJobs' => $trendingJobs,
            'zeroViewJobs' => $zeroViewJobs,
            'categoryPerformance' => $categoryPerformance,
            // News Articles Data
            'articleOverview' => [
                'total_articles' => $totalArticles,
                'total_article_views' => $totalArticleViews,
                'articles_this_month' => $articlesThisMonth,
                'avg_views_per_article' => $totalArticles > 0 ? round($totalArticleViews / $totalArticles, 1) : 0
            ],
            'mostViewedArticles' => $mostViewedArticles,
            'recentArticles' => $recentArticles,
            'zeroViewArticles' => $zeroViewArticles,
            'articleTrends' => $articleTrends
        ]);
    }
}
