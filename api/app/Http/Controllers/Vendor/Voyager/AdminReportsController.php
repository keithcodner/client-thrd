<?php

namespace App\Http\Controllers\Vendor\Voyager;

use App\Models\CircleTransactions\CircleTransaction;
use App\Models\PaymentTransactions\PaymentTransaction;
use App\Models\Event\Event;
use App\Models\Ranking\Ranking;
use App\Models\MyNetwork\MyNetworkUserProfileAnalytics;
use App\Models\MyNetwork\MyNetworkConnections;
use App\Models\User;
use App\Models\Posts;
use App\Models\Item;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Conversation\Conversation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminReportsController extends Controller
{
    public function __construct()
    {
        $this->middleware([\App\Http\Middleware\AdminRoleMiddleware::class]);
    }

    public function index()
    {
        // 1. USER ENGAGEMENT KPIs
        $userEngagementKpis = [
            'totalUsers' => User::count(),
            'activeUsers' => User::where('status', 'active')->count(),
            'verifiedUsers' => User::where('user_IsVerified', 'yes')->count(),
            'newUsersThisMonth' => User::whereMonth('created_at', Carbon::now()->month)->count(),
            'newUsersLastMonth' => User::whereMonth('created_at', Carbon::now()->subMonth()->month)->count(),
            'averageLoginFrequency' => User::whereNotNull('last_login')->count(),
            'usersWithProfiles' => DB::table('mynetwork_user_profile')->count(),
            'profileCompletionRate' => $this->calculateProfileCompletionRate(),
        ];

        // 2. CONTENT CREATION KPIs
        $contentKpis = [
            'totalPosts' => Posts::count(),
            'totalItems' => Item::count(),
            'totalComments' => Comment::count(),
            'totalLikes' => Like::count(),
            'postsThisMonth' => Posts::whereMonth('created_at', Carbon::now()->month)->count(),
            'itemsThisMonth' => Item::whereMonth('created_at', Carbon::now()->month)->count(),
            'averageCommentsPerPost' => $this->calculateAverageCommentsPerPost(),
            'averageLikesPerPost' => $this->calculateAverageLikesPerPost(),
            'contentEngagementRate' => $this->calculateContentEngagementRate(),
        ];

        // 3. TRADING ACTIVITY KPIs
        $tradingKpis = [
            'totalCircles' => CircleTransaction::count(),
            'completedCircles' => CircleTransaction::where('circle_status', 'completed')->count(),
            'successfulCircleRate' => $this->calculateCircleSuccessRate(),
            'averageCircleCompletionTime' => $this->calculateAverageCircleTime(),
            'circlesThisMonth' => CircleTransaction::whereMonth('created_at', Carbon::now()->month)->count(),
            'disputedCircles' => CircleTransaction::where('circle_isInDispute', 'true')->count(),
            'disputeRate' => $this->calculateDisputeRate(),
            'topTradingUsers' => $this->getTopTradingUsers(),
        ];

        // 4. FINANCIAL KPIs
        $financialKpis = [
            'totalRevenue' => PaymentTransaction::where('status', 'completed')->sum('amount'),
            'monthlyRevenue' => PaymentTransaction::where('status', 'completed')
                ->whereMonth('created_at', Carbon::now()->month)->sum('amount'),
            'averageTransactionValue' => PaymentTransaction::where('status', 'completed')->avg('amount'),
            'totalTransactions' => PaymentTransaction::count(),
            'successfulPayments' => PaymentTransaction::where('status', 'completed')->count(),
            'paymentSuccessRate' => $this->calculatePaymentSuccessRate(),
            'revenueGrowthRate' => $this->calculateRevenueGrowthRate(),
        ];

        // 5. SOCIAL NETWORK KPIs
        $socialKpis = [
            'totalConnections' => MyNetworkConnections::count(),
            'totalProfileViews' => MyNetworkUserProfileAnalytics::sum('profile_views_count'),
            'averageConnectionsPerUser' => $this->calculateAverageConnections(),
            'networkGrowthRate' => $this->calculateNetworkGrowthRate(),
            'topInfluencers' => $this->getTopInfluencers(),
        ];

        // 6. PLATFORM HEALTH KPIs
        $platformKpis = [
            'totalEvents' => Event::count(),
            'upcomingEvents' => Event::where('event_date_time', '>', Carbon::now())->count(),
            'systemRankings' => Ranking::count(),
            'averageUserRanking' => Ranking::avg('rank_score'),
            'totalConversations' => Conversation::count(),
            'activeConversations' => $this->getActiveConversations(),
        ];

        // 7. TIME-BASED TRENDS (Last 12 months)
        $monthlyTrends = $this->getMonthlyTrends();

        // 8. PERFORMANCE METRICS
        $performanceMetrics = [
            'userRetentionRate' => $this->calculateUserRetentionRate(),
            'dailyActiveUsers' => $this->getDailyActiveUsers(),
            'weeklyActiveUsers' => $this->getWeeklyActiveUsers(),
            'monthlyActiveUsers' => $this->getMonthlyActiveUsers(),
            'churnRate' => $this->calculateChurnRate(),
        ];

        // 9. GEOGRAPHIC DISTRIBUTION
        $geographicData = $this->getGeographicDistribution();

        // 10. DETAILED REPORTS DATA
        $detailedReports = [
            'topPerformingContent' => $this->getTopPerformingContent(),
            'userActivityPatterns' => $this->getUserActivityPatterns(),
            'circleFlowAnalysis' => $this->getCircleFlowAnalysis(),
            'revenueBreakdown' => $this->getRevenueBreakdown(),
        ];

        return Inertia::render('Admin/Reports/Reports', [
            'userEngagementKpis' => $userEngagementKpis,
            'contentKpis' => $contentKpis,
            'tradingKpis' => $tradingKpis,
            'financialKpis' => $financialKpis,
            'socialKpis' => $socialKpis,
            'platformKpis' => $platformKpis,
            'monthlyTrends' => $monthlyTrends,
            'performanceMetrics' => $performanceMetrics,
            'geographicData' => $geographicData,
            'detailedReports' => $detailedReports,
        ]);
    }

    private function calculateProfileCompletionRate()
    {
        $totalUsers = User::count();
        if ($totalUsers === 0) return 0;
        
        $usersWithProfiles = DB::table('mynetwork_user_profile')->count();
        return round(($usersWithProfiles / $totalUsers) * 100, 2);
    }

    private function calculateAverageCommentsPerPost()
    {
        $totalPosts = Posts::count();
        if ($totalPosts === 0) return 0;
        
        $totalComments = Comment::whereNotNull('post_id')->count();
        return round($totalComments / $totalPosts, 2);
    }

    private function calculateAverageLikesPerPost()
    {
        $totalPosts = Posts::count();
        if ($totalPosts === 0) return 0;
        
        $totalLikes = Like::whereNotNull('post_id')->count();
        return round($totalLikes / $totalPosts, 2);
    }

    private function calculateContentEngagementRate()
    {
        $totalPosts = Posts::count();
        if ($totalPosts === 0) return 0;
        
        $totalLikes = Like::whereNotNull('post_id')->count();
        $totalComments = Comment::whereNotNull('post_id')->count();
        $totalEngagements = $totalLikes + $totalComments;
        
        return round($totalEngagements / $totalPosts, 2);
    }

    private function calculateCircleSuccessRate()
    {
        $totalCircles = CircleTransaction::count();
        if ($totalCircles === 0) return 0;
        
        $completedCircles = CircleTransaction::where('circle_status', 'completed')->count();
        return round(($completedCircles / $totalCircles) * 100, 2);
    }

    private function calculateAverageCircleTime()
    {
        $completedCircles = CircleTransaction::where('circle_status', 'completed')
            ->whereNotNull('circle_initiation_date')
            ->whereNotNull('circle_completion_date')
            ->get();

        if ($completedCircles->count() === 0) return 0;

        $totalDays = 0;
        foreach ($completedCircles as $circle) {
            $initiation = Carbon::parse($circle->circle_initiation_date);
            $completion = Carbon::parse($circle->circle_completion_date);
            $totalDays += $initiation->diffInDays($completion);
        }

        return round($totalDays / $completedCircles->count(), 1);
    }

    private function calculateDisputeRate()
    {
        $totalCircles = CircleTransaction::count();
        if ($totalCircles === 0) return 0;
        
        $disputedCircles = CircleTransaction::where('circle_isInDispute', 'true')->count();
        return round(($disputedCircles / $totalCircles) * 100, 2);
    }

    private function getTopTradingUsers()
    {
        return User::select('users.*', DB::raw('COUNT(circle_transaction.id) as circle_count'))
            ->leftJoin('circle_transaction', function($join) {
                $join->on('users.id', '=', 'circle_transaction.circle_id_initiator')
                     ->orOn('users.id', '=', 'circle_transaction.circle_id_prospect');
            })
            ->groupBy('users.id')
            ->orderByDesc('circle_count')
            ->take(10)
            ->get();
    }

    private function calculatePaymentSuccessRate()
    {
        $totalPayments = PaymentTransaction::count();
        if ($totalPayments === 0) return 0;
        
        $successfulPayments = PaymentTransaction::where('status', 'completed')->count();
        return round(($successfulPayments / $totalPayments) * 100, 2);
    }

    private function calculateRevenueGrowthRate()
    {
        $thisMonth = PaymentTransaction::where('status', 'completed')
            ->whereMonth('created_at', Carbon::now()->month)->sum('amount');
        $lastMonth = PaymentTransaction::where('status', 'completed')
            ->whereMonth('created_at', Carbon::now()->subMonth()->month)->sum('amount');

        if ($lastMonth == 0) return 0;
        return round((($thisMonth - $lastMonth) / $lastMonth) * 100, 2);
    }

    private function calculateAverageConnections()
    {
        $totalUsers = User::count();
        if ($totalUsers === 0) return 0;
        
        $totalConnections = MyNetworkConnections::count();
        return round($totalConnections / $totalUsers, 2);
    }

    private function calculateNetworkGrowthRate()
    {
        $thisMonth = MyNetworkConnections::whereMonth('created_at', Carbon::now()->month)->count();
        $lastMonth = MyNetworkConnections::whereMonth('created_at', Carbon::now()->subMonth()->month)->count();

        if ($lastMonth == 0) return 0;
        return round((($thisMonth - $lastMonth) / $lastMonth) * 100, 2);
    }

    private function getTopInfluencers()
    {
        return MyNetworkUserProfileAnalytics::with('user:id,name,firstname,lastname,email')
            ->where('profile_views_count', '>', 0)
            ->orderByDesc('profile_views_count')
            ->take(10)
            ->get();
    }

    private function getActiveConversations()
    {
        return Conversation::where('updated_at', '>=', Carbon::now()->subDays(30))->count();
    }

    private function getMonthlyTrends()
    {
        $trends = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $trends[] = [
                'month' => $date->format('M Y'),
                'newUsers' => User::whereYear('created_at', $date->year)->whereMonth('created_at', $date->month)->count(),
                'newPosts' => Posts::whereYear('created_at', $date->year)->whereMonth('created_at', $date->month)->count(),
                'newCircles' => CircleTransaction::whereYear('created_at', $date->year)->whereMonth('created_at', $date->month)->count(),
                'revenue' => PaymentTransaction::where('status', 'completed')
                    ->whereYear('created_at', $date->year)->whereMonth('created_at', $date->month)->sum('amount'),
            ];
        }
        return $trends;
    }

    private function calculateUserRetentionRate()
    {
        $oneMonthAgo = Carbon::now()->subMonth();
        $usersOneMonthAgo = User::where('created_at', '<=', $oneMonthAgo)->count();
        
        if ($usersOneMonthAgo === 0) return 0;
        
        $activeUsersFromThatPeriod = User::where('created_at', '<=', $oneMonthAgo)
            ->where('last_login', '>=', Carbon::now()->subDays(30))
            ->count();
            
        return round(($activeUsersFromThatPeriod / $usersOneMonthAgo) * 100, 2);
    }

    private function getDailyActiveUsers()
    {
        return User::where('last_login', '>=', Carbon::now()->subDay())->count();
    }

    private function getWeeklyActiveUsers()
    {
        return User::where('last_login', '>=', Carbon::now()->subWeek())->count();
    }

    private function getMonthlyActiveUsers()
    {
        return User::where('last_login', '>=', Carbon::now()->subMonth())->count();
    }

    private function calculateChurnRate()
    {
        $totalUsers = User::count();
        if ($totalUsers === 0) return 0;
        
        $inactiveUsers = User::where('last_login', '<', Carbon::now()->subMonths(3))
            ->orWhere('last_login', null)
            ->count();
            
        return round(($inactiveUsers / $totalUsers) * 100, 2);
    }

    private function getGeographicDistribution()
    {
        // Get country data from address table since users table doesn't have user_country
        return DB::table('address')
            ->select('addr_country as country', DB::raw('COUNT(*) as user_count'))
            ->whereNotNull('addr_country')
            ->groupBy('addr_country')
            ->orderByDesc('user_count')
            ->take(10)
            ->get();
    }

    private function getTopPerformingContent()
    {
        return Posts::select('posts.*', DB::raw('COALESCE(COUNT(DISTINCT likes.id), 0) as like_count'), DB::raw('COALESCE(COUNT(DISTINCT comments.id), 0) as comment_count'))
            ->leftJoin('likes', 'posts.id', '=', 'likes.post_id')
            ->leftJoin('comments', 'posts.id', '=', 'comments.post_id')
            ->groupBy('posts.id')
            ->orderByDesc(DB::raw('COALESCE(COUNT(DISTINCT likes.id), 0) + COALESCE(COUNT(DISTINCT comments.id), 0)'))
            ->take(10)
            ->get();
    }

    private function getUserActivityPatterns()
    {
        $patterns = [];
        for ($hour = 0; $hour < 24; $hour++) {
            $patterns[] = [
                'hour' => $hour,
                'posts' => Posts::whereRaw('HOUR(created_at) = ?', [$hour])->count(),
                'circles' => CircleTransaction::whereRaw('HOUR(created_at) = ?', [$hour])->count(),
            ];
        }
        return $patterns;
    }

    private function getCircleFlowAnalysis()
    {
        return [
            'statusDistribution' => CircleTransaction::select('circle_status', DB::raw('COUNT(*) as count'))
                ->whereNotNull('circle_status')
                ->groupBy('circle_status')
                ->get(),
            'averageNegotiationTime' => $this->calculateAverageCircleTime(),
            'topCircledCategories' => Item::select('ip_category', DB::raw('COUNT(*) as count'))
                ->whereHas('circleTransactionProspect')
                ->whereNotNull('ip_category')
                ->groupBy('ip_category')
                ->orderByDesc('count')
                ->take(10)
                ->get(),
        ];
    }

    private function getRevenueBreakdown()
    {
        return [
            'byPaymentMethod' => PaymentTransaction::select('method', DB::raw('SUM(amount) as total'))
                ->where('status', 'completed')
                ->whereNotNull('method')
                ->groupBy('method')
                ->get(),
            'byTransactionType' => PaymentTransaction::select('type', DB::raw('SUM(amount) as total'))
                ->where('status', 'completed')
                ->whereNotNull('type')
                ->groupBy('type')
                ->get(),
            'dailyRevenue' => PaymentTransaction::select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('SUM(amount) as total')
                )
                ->where('status', 'completed')
                ->where('created_at', '>=', Carbon::now()->subDays(30))
                ->groupBy(DB::raw('DATE(created_at)'))
                ->orderBy('date')
                ->get(),
        ];
    }
}
