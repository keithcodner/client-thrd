<?php

namespace App\Http\Controllers\Vendor\Voyager;

use App\Models\TradeTransactions\TradeTransaction;
use App\Models\PaymentTransactions\PaymentTransaction;
use App\Models\Event\Event;
use App\Models\Ranking\Ranking;
use App\Models\MyNetwork\MyNetworkUserProfileAnalytics;
use App\Models\User;
use App\Models\Posts;
use App\Models\Item;
use App\Models\Support\SupportRequest;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AdminSupportController extends Controller
{
    public function __construct()
    {
        $this->middleware([\App\Http\Middleware\AdminRoleMiddleware::class]);
    }

    public function index()
    {
        // Trade Transaction Analytics
        $tradeStats = [
            'total' => TradeTransaction::count(),
            'completed' => TradeTransaction::where('trade_status', 'completed')->count(),
            'pending' => TradeTransaction::whereIn('trade_status', ['pending', 'prospect_incoming', 'initiator_incoming'])->count(),
            'disputed' => TradeTransaction::where('trade_isInDispute', 'true')->count(),
            'thisMonth' => TradeTransaction::whereMonth('created_at', Carbon::now()->month)->count(),
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

        // Event Analytics
        $eventStats = [
            'total' => Event::count(),
            'thisMonth' => Event::whereMonth('created_at', Carbon::now()->month)->count(),
            'upcoming' => Event::where('event_date_time', '>', Carbon::now())->count(),
            'past' => Event::where('event_date_time', '<', Carbon::now())->count(),
        ];

        // Profile Views Analytics
        $profileViewStats = [
            'totalViews' => MyNetworkUserProfileAnalytics::sum('profile_views_count'),
            'averageViewsPerProfile' => MyNetworkUserProfileAnalytics::avg('profile_views_count'),
            'mostViewedProfiles' => MyNetworkUserProfileAnalytics::orderBy('profile_views_count', 'desc')->take(5)->get(),
            'profilesWithAnalytics' => MyNetworkUserProfileAnalytics::count(),
        ];

        // Ranking Analytics
        $rankingStats = [
            'totalRankings' => Ranking::count(),
            'activeRankings' => Ranking::where('rank_status', 'active')->count(),
            'averageScore' => Ranking::avg('rank_score'),
            'topRankedUsers' => Ranking::with('user:id,name,firstname,lastname,email')
                ->orderBy('rank_score', 'desc')
                ->take(10)
                ->get(),
        ];

        // Monthly trends for charts (last 12 months)
        $monthlyTrends = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthlyTrends[] = [
                'month' => $date->format('M Y'),
                'trades' => TradeTransaction::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)->count(),
                'payments' => PaymentTransaction::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)->count(),
                'events' => Event::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)->count(),
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
                'trades' => TradeTransaction::whereBetween('created_at', [$startOfWeek, $endOfWeek])->count(),
                'payments' => PaymentTransaction::whereBetween('created_at', [$startOfWeek, $endOfWeek])->count(),
                'profileViews' => MyNetworkUserProfileAnalytics::whereBetween('updated_at', [$startOfWeek, $endOfWeek])
                    ->sum('profile_views_count'),
            ];
        }

        // Recent trade transactions
        $recentTrades = TradeTransaction::with(['initiator:id,name,firstname,lastname', 'prospect:id,name,firstname,lastname'])
            ->latest()
            ->take(10)
            ->get();

        // Recent payment transactions
        $recentPayments = PaymentTransaction::with('user:id,name,firstname,lastname,email')
            ->latest()
            ->take(10)
            ->get();

        // Trade status distribution
        $tradeStatusDistribution = TradeTransaction::select('trade_status', DB::raw('count(*) as count'))
            ->groupBy('trade_status')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->trade_status => $item->count];
            });

        // Payment status distribution
        $paymentStatusDistribution = PaymentTransaction::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->status => $item->count];
            });

        return Inertia::render('Admin/Analytics/Analytics', [
            'tradeStats' => $tradeStats,
            'paymentStats' => $paymentStats,
            'eventStats' => $eventStats,
            'profileViewStats' => $profileViewStats,
            'rankingStats' => $rankingStats,
            'monthlyTrends' => $monthlyTrends,
            'weeklyActivity' => $weeklyActivity,
            'recentTrades' => $recentTrades,
            'recentPayments' => $recentPayments,
            'tradeStatusDistribution' => $tradeStatusDistribution,
            'paymentStatusDistribution' => $paymentStatusDistribution,
        ]);
    }

    /**
     * Admin Support Requests Management
     */

    /**
     * Display admin support requests page.
     */
    public function supportRequestsIndex()
    {
        $supportRequests = SupportRequest::with(['user:id,firstname,lastname,email'])
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        // Get tech users for assignment dropdown
        $techUsers = User::where('role_id', '1') // admin role
            ->orWhere('role_id', '3') // tech role
            ->select('id', 'firstname', 'lastname', 'email')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->firstname . ' ' . $user->lastname,
                    'email' => $user->email,
                ];
            });

        return Inertia::render('Admin/Support/SupportRequest/SupportRequest', [
            'supportRequests' => $supportRequests,
            'techUsers' => $techUsers,
        ]);
    }

    /**
     * Admin reply to support request.
     */
    public function reply(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ticket_id' => 'required|exists:support_requests,id',
            'reply_message' => 'required|string|min:1|max:1000',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator->errors());
        }

        try {
            $supportRequest = SupportRequest::findOrFail($request->ticket_id);
            $admin = Auth::user();
            
            // Generate unique IDs using the SiteHelperController pattern
            $comm_an_id = app(\App\Http\Controllers\Core\SiteHelperController::class)->createAlphaNumericId();
            $comm_comment_unique_an_id = app(\App\Http\Controllers\Core\SiteHelperController::class)->createAlphaNumericId();
            
            Comment::create([
                "user_id" => $admin->id, 
                "support_id" => $request->ticket_id, 
                "comm_an_id" => $comm_an_id, 
                "comm_comment_unique_an_id" => $comm_comment_unique_an_id, 
                "comm_comment" => $request->reply_message, 
                "comm_is_reply" => 'No', 
                "comm_status" => 'Active', 
                "comm_type" => 'help_request_comment', 
                "comm_name" => 'Admin: ' . $admin->firstname . ' ' . $admin->lastname, 
            ]);

            // Update support request timestamp
            $supportRequest->touch();

            Log::info('Admin replied to support request', [
                'support_request_id' => $request->ticket_id,
                'admin_id' => Auth::id(),
                'comment_id' => $comm_an_id,
            ]);

            return back()->with('success', 'Reply sent successfully.');

        } catch (\Exception $e) {
            Log::error('Error sending admin reply', [
                'error' => $e->getMessage(),
                'support_request_id' => $request->ticket_id,
                'admin_id' => Auth::id(),
            ]);

            return back()->withErrors(['error' => 'An error occurred while sending your reply.']);
        }
    }

    /**
     * Update support request status.
     */
    public function updateStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ticket_id' => 'required|exists:support_requests,id',
            'status' => 'required|in:open,in_progress,resolved,closed',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator->errors());
        }

        try {
            $supportRequest = SupportRequest::findOrFail($request->ticket_id);
            $admin = Auth::user();

            $supportRequest->update([
                'status' => $request->status,
                'updated_by' => $admin->id,
            ]);

            // Add admin notes as a comment if provided
            if ($request->admin_notes) {
                $comm_an_id = app(\App\Http\Controllers\Core\SiteHelperController::class)->createAlphaNumericId();
                $comm_comment_unique_an_id = app(\App\Http\Controllers\Core\SiteHelperController::class)->createAlphaNumericId();
                
                Comment::create([
                    "user_id" => $admin->id, 
                    "support_id" => $request->ticket_id, 
                    "comm_an_id" => $comm_an_id, 
                    "comm_comment_unique_an_id" => $comm_comment_unique_an_id, 
                    "comm_comment" => "Status updated to: " . ucfirst($request->status) . "\n\nAdmin Notes: " . $request->admin_notes, 
                    "comm_is_reply" => 'No', 
                    "comm_status" => 'Active', 
                    "comm_type" => 'help_request_comment', 
                    "comm_name" => 'Admin: ' . $admin->firstname . ' ' . $admin->lastname, 
                ]);
            }

            Log::info('Admin updated support request status', [
                'support_request_id' => $request->ticket_id,
                'admin_id' => Auth::id(),
                'new_status' => $request->status,
            ]);

            return back()->with('success', 'Status updated successfully.');

        } catch (\Exception $e) {
            Log::error('Error updating support request status', [
                'error' => $e->getMessage(),
                'support_request_id' => $request->ticket_id,
                'admin_id' => Auth::id(),
            ]);

            return back()->withErrors(['error' => 'An error occurred while updating the status.']);
        }
    }

    /**
     * Assign support request to technician.
     */
    public function assign(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ticket_id' => 'required|exists:support_requests,id',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator->errors());
        }

        try {
            $supportRequest = SupportRequest::findOrFail($request->ticket_id);
            $admin = Auth::user();

            $supportRequest->update([
                'assigned_to' => $request->assigned_to,
                'updated_by' => $admin->id,
            ]);

            // Add assignment note as a comment
            $assignedUser = $request->assigned_to ? User::find($request->assigned_to) : null;
            $assignmentNote = $assignedUser 
                ? "Ticket assigned to: " . $assignedUser->firstname . ' ' . $assignedUser->lastname 
                : "Ticket unassigned";

            $comm_an_id = app(\App\Http\Controllers\Core\SiteHelperController::class)->createAlphaNumericId();
            $comm_comment_unique_an_id = app(\App\Http\Controllers\Core\SiteHelperController::class)->createAlphaNumericId();
            
            Comment::create([
                "user_id" => $admin->id, 
                "support_id" => $request->ticket_id, 
                "comm_an_id" => $comm_an_id, 
                "comm_comment_unique_an_id" => $comm_comment_unique_an_id, 
                "comm_comment" => $assignmentNote, 
                "comm_is_reply" => 'No', 
                "comm_status" => 'Active', 
                "comm_type" => 'help_request_comment', 
                "comm_name" => 'Admin: ' . $admin->firstname . ' ' . $admin->lastname, 
            ]);

            Log::info('Admin assigned support request', [
                'support_request_id' => $request->ticket_id,
                'admin_id' => Auth::id(),
                'assigned_to' => $request->assigned_to,
            ]);

            return back()->with('success', 'Ticket assigned successfully.');

        } catch (\Exception $e) {
            Log::error('Error assigning support request', [
                'error' => $e->getMessage(),
                'support_request_id' => $request->ticket_id,
                'admin_id' => Auth::id(),
            ]);

            return back()->withErrors(['error' => 'An error occurred while assigning the ticket.']);
        }
    }

    /**
     * Create new support request (admin).
     */
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'subject_type' => 'required|in:issue,error,bug,help,feature',
            'title' => 'required|string|max:500',
            'description' => 'required|string|min:10',
            'priority' => 'required|in:low,medium,high,critical',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator->errors());
        }

        try {
            $user = User::findOrFail($request->user_id);
            $admin = Auth::user();
            
            $supportRequest = SupportRequest::create([
                'user_id' => $user->id,
                'first_name' => $user->firstname,
                'last_name' => $user->lastname,
                'email' => $user->email,
                'subject_type' => $request->subject_type,
                'title' => $request->title,
                'description' => $request->description,
                'priority' => $request->priority,
                'status' => 'open',
                'created_by' => $admin->id,
            ]);

            Log::info('Admin created support request', [
                'support_request_id' => $supportRequest->id,
                'admin_id' => Auth::id(),
                'user_id' => $request->user_id,
            ]);

            return back()->with('success', 'Support ticket created successfully.');

        } catch (\Exception $e) {
            Log::error('Error creating support request', [
                'error' => $e->getMessage(),
                'admin_id' => Auth::id(),
                'request_data' => $request->all(),
            ]);

            return back()->withErrors(['error' => 'An error occurred while creating the ticket.']);
        }
    }

    /**
     * Delete support request.
     */
    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ticket_id' => 'required|exists:support_requests,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator->errors());
        }

        try {
            $supportRequest = SupportRequest::findOrFail($request->ticket_id);

            // Delete associated comments
            Comment::where('support_id', $request->ticket_id)
                ->where('comm_type', 'help_request_comment')
                ->delete();

            // Delete the support request
            $supportRequest->delete();

            Log::info('Admin deleted support request', [
                'support_request_id' => $request->ticket_id,
                'admin_id' => Auth::id(),
            ]);

            return back()->with('success', 'Support ticket deleted successfully.');

        } catch (\Exception $e) {
            Log::error('Error deleting support request', [
                'error' => $e->getMessage(),
                'support_request_id' => $request->ticket_id,
                'admin_id' => Auth::id(),
            ]);

            return back()->withErrors(['error' => 'An error occurred while deleting the ticket.']);
        }
    }

    /**
     * Get comments for a support request (admin view).
     */
    public function getComments($id)
    {
        try {
            $supportRequest = SupportRequest::findOrFail($id);

            $comments = Comment::where('support_id', $id)
                ->where('comm_type', 'help_request_comment')
                ->orderBy('created_at', 'asc')
                ->get();

            return response()->json([
                'comments' => $comments,
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error fetching support request comments (admin)', [
                'error' => $e->getMessage(),
                'support_request_id' => $id,
                'admin_id' => Auth::id(),
            ]);

            return response()->json([
                'message' => 'Failed to load comments.',
                'comments' => [],
            ], 500);
        }
    }
}
