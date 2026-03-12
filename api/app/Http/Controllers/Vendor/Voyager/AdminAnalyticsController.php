<?php

namespace App\Http\Controllers\Vendor\Voyager;

use App\Models\CircleTransactions\CircleTransaction;
use App\Models\PaymentTransactions\PaymentTransaction;
use App\Models\Event\Event;
use App\Models\Ranking\Ranking;
use App\Models\MyNetwork\MyNetworkUserProfileAnalytics;
use App\Models\User;
use App\Models\Posts;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminAnalyticsController extends Controller
{
    public function __construct()
    {
        //$this->middleware([\App\Http\Middleware\AdminRoleMiddleware::class]);
    }

    public function index()
    {
        // Job Post Transaction Analytics
        $jobPostStats = [
            'total' => \App\Models\Posts\JobPost::count(),
            'live' => \App\Models\Posts\JobPost::where('status', 'COMMITTED')->count(),
            'draft' => \App\Models\Posts\JobPost::where('status', 'DRAFT')->count(),
            'pending' => \App\Models\Posts\JobPost::where('status', 'PENDING')->count(),
            'thisMonth' => \App\Models\Posts\JobPost::whereMonth('created_at', Carbon::now()->month)->count(),
        ];

        // Payment Transaction Analytics
        $paymentStats = [
            'total' => PaymentTransaction::count(),
            'successful' => PaymentTransaction::where('status', 'completed')->count(),
            'pending' => PaymentTransaction::where('status', 'pending')->count(),
            'failed' => PaymentTransaction::where('status', 'failed')->count(),
            'thisMonth' => PaymentTransaction::whereMonth('created_at', Carbon::now()->month)->count(),
            'totalRevenue' => PaymentTransaction::where('status', 'completed')->sum('amount'),
        ];

        // Job Views Analytics (replace events)
        $jobViewStats = [
            'total' => \App\Models\Posts\JobPost::sum('views'),
            'thisMonth' => \App\Models\Posts\JobPost::whereMonth('created_at', Carbon::now()->month)->sum('views'),
            'topViewed' => \App\Models\Posts\JobPost::orderBy('views', 'desc')->take(5)->get(['id','title','views']),
        ];

        // Profile Views Analytics
        $profileViewStats = [
            //'totalViews' => MyNetworkUserProfileAnalytics::sum('profile_views_count'),
            //'averageViewsPerProfile' => MyNetworkUserProfileAnalytics::avg('profile_views_count'),
            //'mostViewedProfiles' => MyNetworkUserProfileAnalytics::orderBy('profile_views_count', 'desc')->take(5)->get(),
            //'profilesWithAnalytics' => MyNetworkUserProfileAnalytics::count(),
        ];

        // Job Post Apply Clicks Analytics (replace ranking)
        $jobApplyStats = [
            'totalClicks' => \App\Models\Posts\JobPost::sum('clicks'),
            'averageClicks' => round(\App\Models\Posts\JobPost::avg('clicks'), 2),
            'topClicked' => \App\Models\Posts\JobPost::orderBy('clicks', 'desc')->take(5)->get(['id','title','clicks']),
        ];

        // Monthly trends for charts (last 12 months)
        $monthlyTrends = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthlyTrends[] = [
                'month' => $date->format('M Y'),
                //'circles' => CircleTransaction::whereYear('created_at', $date->year)
                //    ->whereMonth('created_at', $date->month)->count(),
                //'payments' => PaymentTransaction::whereYear('created_at', $date->year)
                //    ->whereMonth('created_at', $date->month)->count(),
                //'events' => Event::whereYear('created_at', $date->year)
                //    ->whereMonth('created_at', $date->month)->count(),
                'newUsers' => User::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)->count(),
            ];
        }

        // Weekly activity for the last 4 weeks
        $weeklyActivity = [];
        for ($i = 3; $i >= 0; $i--) {
            $startOfWeek = Carbon::now()->subWeeks($i)->startOfWeek();
            $endOfWeek = Carbon::now()->subWeeks($i)->endOfWeek();
            
            $weeklyActivity[] = [
                'week' => $startOfWeek->format('M j') . ' - ' . $endOfWeek->format('M j'),
                //'circles' => CircleTransaction::whereBetween('created_at', [$startOfWeek, $endOfWeek])->count(),
                'payments' => PaymentTransaction::whereBetween('created_at', [$startOfWeek, $endOfWeek])->count(),
                //'profileViews' => MyNetworkUserProfileAnalytics::whereBetween('updated_at', [$startOfWeek, $endOfWeek])
                //   ->sum('profile_views_count'),
            ];
        }

        // Top paying users (replaces recent circle transactions)
        $topPayingUsers = User::select('users.id', 'users.firstname', 'users.lastname', 'users.email', 'users.name')
            ->join('trxn_payment_transaction', 'users.id', '=', 'trxn_payment_transaction.user_id')
            ->where('trxn_payment_transaction.status', 'completed')
            ->selectRaw('SUM(trxn_payment_transaction.amount) as total_paid')
            ->groupBy('users.id', 'users.firstname', 'users.lastname', 'users.email', 'users.name')
            ->orderByDesc('total_paid')
            ->take(10)
            ->get();

        // Recent payment transactions with user information
        $recentPayments = PaymentTransaction::with(['user:id,firstname,lastname,email,name'])
            ->latest()
            ->take(10)
            ->get();

        // Circle status distribution
        // $circleStatusDistribution = CircleTransaction::select('circle_status', DB::raw('count(*) as count'))
        //     ->groupBy('circle_status')
        //     ->get()
        //     ->mapWithKeys(function ($item) {
        //         return [$item->circle_status => $item->count];
        //     });

        // Payment status distribution
        $paymentStatusDistribution = PaymentTransaction::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->status => $item->count];
            });

        return Inertia::render('Admin/AdminInsights/AdminInsights', [
            'jobPostStats' => $jobPostStats,
            'paymentStats' => $paymentStats,
            'jobViewStats' => $jobViewStats,
            'profileViewStats' => $profileViewStats,
            'jobApplyStats' => $jobApplyStats,
            'monthlyTrends' => $monthlyTrends,
            'weeklyActivity' => $weeklyActivity,
            'recentPayments' => $recentPayments,
            'paymentStatusDistribution' => $paymentStatusDistribution,
            'topPayingUsers' => $topPayingUsers,
        ]);
    }
}
