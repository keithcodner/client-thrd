<?php

namespace App\Http\Controllers\Vendor\Voyager;

use App\Models\TradeTransactions\TradeTransaction;
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

class ManageJobPostController extends Controller
{
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
            'totalTrades' => TradeTransaction::count(),
            'completedTrades' => TradeTransaction::where('trade_status', 'completed')->count(),
            'successfulTradeRate' => $this->calculateTradeSuccessRate(),
            'averageTradeCompletionTime' => $this->calculateAverageTradeTime(),
            'tradesThisMonth' => TradeTransaction::whereMonth('created_at', Carbon::now()->month)->count(),
            'disputedTrades' => TradeTransaction::where('trade_isInDispute', 'true')->count(),
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
            'tradeFlowAnalysis' => $this->getTradeFlowAnalysis(),
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

    private function calculateTradeSuccessRate()
    {
        $totalTrades = TradeTransaction::count();
        if ($totalTrades === 0) return 0;
        
        $completedTrades = TradeTransaction::where('trade_status', 'completed')->count();
        return round(($completedTrades / $totalTrades) * 100, 2);
    }

    private function calculateAverageTradeTime()
    {
        $completedTrades = TradeTransaction::where('trade_status', 'completed')
            ->whereNotNull('trade_initiation_date')
            ->whereNotNull('trade_completion_date')
            ->get();

        if ($completedTrades->count() === 0) return 0;

        $totalDays = 0;
        foreach ($completedTrades as $trade) {
            $initiation = Carbon::parse($trade->trade_initiation_date);
            $completion = Carbon::parse($trade->trade_completion_date);
            $totalDays += $initiation->diffInDays($completion);
        }

        return round($totalDays / $completedTrades->count(), 1);
    }

    private function calculateDisputeRate()
    {
        $totalTrades = TradeTransaction::count();
        if ($totalTrades === 0) return 0;
        
        $disputedTrades = TradeTransaction::where('trade_isInDispute', 'true')->count();
        return round(($disputedTrades / $totalTrades) * 100, 2);
    }

    private function getTopTradingUsers()
    {
        return User::select('users.*', DB::raw('COUNT(trade_transaction.id) as trade_count'))
            ->leftJoin('trade_transaction', function($join) {
                $join->on('users.id', '=', 'trade_transaction.trade_id_initiator')
                     ->orOn('users.id', '=', 'trade_transaction.trade_id_prospect');
            })
            ->groupBy('users.id')
            ->orderByDesc('trade_count')
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
                'newTrades' => TradeTransaction::whereYear('created_at', $date->year)->whereMonth('created_at', $date->month)->count(),
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
                'trades' => TradeTransaction::whereRaw('HOUR(created_at) = ?', [$hour])->count(),
            ];
        }
        return $patterns;
    }

    private function getTradeFlowAnalysis()
    {
        return [
            'statusDistribution' => TradeTransaction::select('trade_status', DB::raw('COUNT(*) as count'))
                ->whereNotNull('trade_status')
                ->groupBy('trade_status')
                ->get(),
            'averageNegotiationTime' => $this->calculateAverageTradeTime(),
            'topTradedCategories' => Item::select('ip_category', DB::raw('COUNT(*) as count'))
                ->whereHas('tradeTransactionProspect')
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

    /**
     * Shift job post dates to keep older posts fresh
     * 
     * This method redistributes job post dates for posts older than 1 week:
     * - Jobs posted within last 7 days: No changes
     * - Jobs older than 7 days: Split into 5 groups and redistributed across 3-7 days ago
     * - Sets expires_at to 30 days from new created_at
     * - Records the shift in updated_at
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function shiftDates()
    {
        try {
            $now = Carbon::now();
            $oneWeekAgo = $now->copy()->subDays(7);

            // Get all COMMITTED job posts older than 1 week from admin users (role_id = 1), ordered by ID (oldest first)
            $oldJobs = \App\Models\Posts\JobPost::where('status', 'COMMITTED')
                ->where('created_at', '<', $oneWeekAgo)
                ->whereHas('user', function($query) {
                    $query->where('role_id', 1);
                })
                ->orderBy('id', 'asc')
                ->get();

            // Count jobs that will be skipped (recent ones from admin users only)
            $recentJobs = \App\Models\Posts\JobPost::where('status', 'COMMITTED')
                ->where('created_at', '>=', $oneWeekAgo)
                ->whereHas('user', function($query) {
                    $query->where('role_id', 1);
                })
                ->count();

            if ($oldJobs->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'message' => 'No jobs older than 1 week found. All jobs are recent!',
                    'updated_count' => 0,
                    'skipped_count' => $recentJobs,
                    'distribution' => [
                        '3_days' => 0,
                        '4_days' => 0,
                        '5_days' => 0,
                        '6_days' => 0,
                        '7_days' => 0,
                    ],
                ]);
            }

            // Split old jobs into 5 equal groups
            $totalOldJobs = $oldJobs->count();
            $groupSize = ceil($totalOldJobs / 5);

            $distribution = [
                '3_days' => 0,
                '4_days' => 0,
                '5_days' => 0,
                '6_days' => 0,
                '7_days' => 0,
            ];

            $updatedCount = 0;

            // Process each job in groups
            foreach ($oldJobs as $index => $job) {
                // Determine which group this job belongs to (0-4)
                $groupIndex = floor($index / $groupSize);
                
                // Clamp group index to 0-4 to handle any rounding edge cases
                $groupIndex = min($groupIndex, 4);

                // Map group index to days ago (3, 4, 5, 6, 7)
                $daysAgo = 3 + $groupIndex;

                // Calculate new dates
                $newCreatedAt = $now->copy()->subDays($daysAgo);
                $newExpiresAt = $newCreatedAt->copy()->addDays(30);

                // Update the job post
                $job->created_at = $newCreatedAt;
                $job->expires_at = $newExpiresAt;
                $job->updated_at = $now; // Record when this shift occurred
                $job->save();

                $updatedCount++;
                $distribution[$daysAgo . '_days']++;
            }

            return response()->json([
                'success' => true,
                'message' => "Successfully shifted dates for {$updatedCount} job post(s).",
                'updated_count' => $updatedCount,
                'skipped_count' => $recentJobs,
                'distribution' => $distribution,
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error shifting job post dates: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while shifting dates: ' . $e->getMessage(),
            ], 500);
        }
    }
}
