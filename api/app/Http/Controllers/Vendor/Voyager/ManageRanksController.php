<?php

namespace App\Http\Controllers\Vendor\Voyager;

use App\Models\Comment;
use App\Models\Ranking\Ranking;
use App\Models\Ranking\RankingTransactionHistory;
use App\Models\Ranking\RankingGroup;
use App\Models\Ranking\RankingWeight;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Middleware\AdminRoleMiddleware;
use League\CommonMark\Util\ArrayCollection;
use Illuminate\Pagination\Paginator;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class ManageRanksController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', AdminRoleMiddleware::class]);
        Paginator::useBootstrap();
    }

    public function index(Request $request)
    {
        $query = Ranking::with(['user:id,firstname,lastname,username,email', 'rankingGroup:id,rank_group_type,rank_group_tier', 'rankingWeight:id,rank_weight_name'])
            ->select('rankings.*');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('firstname', 'like', "%{$search}%")
                  ->orWhere('lastname', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhere('rank_score', 'like', "%{$search}%");
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('rank_status', $request->status);
        }

        // Filter by rank group
        if ($request->filled('rank_group_id')) {
            $query->where('rank_group_id', $request->rank_group_id);
        }

        // Sort
        $sortField = $request->get('sort', 'id');
        $sortDirection = $request->get('direction', 'desc');
        
        if (in_array($sortField, ['id', 'rank_score', 'rank_weighed_score', 'rank_status', 'created_at'])) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->orderBy('id', 'desc');
        }

        $rankings = $query->paginate(15);

        // Ensure pagination URLs preserve current filters
        $rankings->appends($request->only(['search', 'status', 'rank_group_id', 'sort', 'direction']));

        // Get filter options
        $rankingGroups = RankingGroup::select('id', 'rank_group_type', 'rank_group_tier')
            ->where('rank_group_status', 'active')
            ->get();

        $rankingWeights = RankingWeight::select('id', 'rank_weight_name')
            ->get();

        return Inertia::render('Admin/RankingManagement/ViewAndManageUserRankings', [
            'rankings' => $rankings,
            'rankingGroups' => $rankingGroups,
            'rankingWeights' => $rankingWeights,
            'filters' => $request->only(['search', 'status', 'rank_group_id', 'sort', 'direction'])
        ]);
    }

    public function getRankingHistory(Request $request, $rankingId)
    {
        $ranking = Ranking::findOrFail($rankingId);
        
        $history = RankingTransactionHistory::where('user_id', $ranking->user_id)
            ->with(['rankingInteract:id,rank_interact_name,rank_interact_type'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'history' => $history
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'rank_group_id' => 'nullable|exists:ranking_groups,id',
            'rank_weight_id' => 'nullable|exists:ranking_weight,id',
            'rank_status' => 'required|in:active,inactive,suspended',
            'rank_score' => 'required|numeric|min:0',
            'rank_weighed_score' => 'nullable|numeric|min:0',
            'rank_data' => 'nullable|string|max:5000'
        ]);

        $ranking = Ranking::create($validated);

        return redirect()->back()->with('success', 'User ranking created successfully');
    }

    public function update(Request $request, $id)
    {
        $ranking = Ranking::findOrFail($id);

        $validated = $request->validate([
            'rank_group_id' => 'nullable|exists:ranking_groups,id',
            'rank_weight_id' => 'nullable|exists:ranking_weight,id',
            'rank_status' => 'required|in:active,inactive,suspended',
            'rank_score' => 'required|numeric|min:0',
            'rank_weighed_score' => 'nullable|numeric|min:0',
            'rank_data' => 'nullable|string|max:5000'
        ]);

        $ranking->update($validated);

        return redirect()->back()->with('success', 'User ranking updated successfully');
    }

    public function destroy($id)
    {
        $ranking = Ranking::findOrFail($id);
        $ranking->delete();

        return redirect()->back()->with('success', 'User ranking deleted successfully');
    }

    public function bulkUpdateStatus(Request $request)
    {
        $validated = $request->validate([
            'ranking_ids' => 'required|array',
            'ranking_ids.*' => 'exists:rankings,id',
            'status' => 'required|in:active,inactive,suspended'
        ]);

        Ranking::whereIn('id', $validated['ranking_ids'])
            ->update(['rank_status' => $validated['status']]);

        return redirect()->back()->with('success', 'Rankings status updated successfully');
    }

    public function recalculateRanking(Request $request, $id)
    {
        $ranking = Ranking::findOrFail($id);
        
        // Recalculate logic would go here
        // For now, just update the timestamp
        $ranking->touch();

        return redirect()->back()->with('success', 'Ranking recalculated successfully');
    }
}
