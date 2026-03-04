<?php

namespace App\Http\Controllers\Vendor\Voyager;

use App\Models\Comment\Comment;
use App\Models\User;
use App\Models\Posts\JobPost;
use App\Models\PaymentTransactions\PaymentTransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use League\CommonMark\Util\ArrayCollection;
use Illuminate\Pagination\Paginator;
use Inertia\Inertia;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function __construct()
    {
        //$this->middleware([\App\Http\Middleware\AdminRoleMiddleware::class]);
    }

    public function index()
    {
        // Get dashboard statistics
        $stats = [
            'totalUsers' => User::count(),
            'newUsersThisWeek' => User::where('created_at', '>=', Carbon::now()->subWeek())->count(),
            'activeUsers' => User::where('status', 'active')->count(),
            'totalJobPosts' => JobPost::count(),
            'newJobPostsThisWeek' => JobPost::where('created_at', '>=', Carbon::now()->subWeek())->count(),
            'totalContactComments' => Comment::where('comm_type', 'contact_message')->where('comm_status', '!=', 'deleted')->count(),
            'paymentsPastWeek' => PaymentTransaction::where('created_at', '>=', Carbon::now()->subWeek())->sum('amount'),
        ];

        // Recent activities (last 5 of each)
        $recentUsers = User::latest()->take(5)->get(['id', 'name', 'firstname', 'lastname', 'email', 'created_at']);
        $recentJobPosts = JobPost::with('user:id,firstname,lastname,email,name')
            ->latest()
            ->take(5)
            ->get(['id', 'title', 'company_name', 'status', 'created_at', 'author_id', 'budget', 'currency']);
        $recentContactComments = Comment::where('comm_type', 'contact_message')
            ->where('comm_status', '!=', 'deleted')
            ->latest()
            ->take(5)
            ->get(['id', 'comm_name', 'comm_email', 'comm_comment', 'created_at']);

        // Weekly growth data for charts (last 7 days)
        $weeklyData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $weeklyData[] = [
                'date' => $date->format('M j'),
                'users' => User::whereDate('created_at', $date)->count(),
                'jobPosts' => JobPost::whereDate('created_at', $date)->count(),
                'payments' => PaymentTransaction::whereDate('created_at', $date)->sum('amount'),
            ];
        }

        return Inertia::render('Admin/Dashboard/Dashboard', [
            'stats' => $stats,
            'recentUsers' => $recentUsers,
            'recentJobPosts' => $recentJobPosts,
            'recentContactComments' => $recentContactComments,
            'weeklyData' => $weeklyData,
        ]);
    }

}
