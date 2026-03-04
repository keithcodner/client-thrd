<?php

namespace App\Http\Controllers\Vendor\Voyager;

use App\Models\Ranking\RankingGroup;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class ManageRankingGroupsController extends Controller
{
    public function index()
    {
        $groups = RankingGroup::orderBy('rank_group_order', 'asc')
                            ->orderBy('rank_group_weighted_score_threshold', 'desc')
                            ->get();

        return Inertia::render('Admin/RankingManagement/ManageRankingGroups', [
            'groups' => $groups,
            'success' => session('success'),
            'error' => session('error')
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'rank_group_type' => 'required|string|in:rank,badge,achievement',
            'rank_group_tier' => 'required|string|max:500',
            'rank_group_order' => 'required|integer|min:0',
            'rank_group_weighted_score_threshold' => 'required|numeric|min:0',
            'rank_group_status' => 'required|in:active,in-active',
            'rank_group_data' => 'nullable|string|max:255',
        ]);

        try {
            RankingGroup::create($request->all());
            return back()->with('success', 'Ranking group created successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create ranking group: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $group = RankingGroup::findOrFail($id);

        $request->validate([
            'rank_group_type' => 'required|string|in:rank,badge,achievement',
            'rank_group_tier' => 'required|string|max:500',
            'rank_group_order' => 'required|integer|min:0',
            'rank_group_weighted_score_threshold' => 'required|numeric|min:0',
            'rank_group_status' => 'required|in:active,in-active',
            'rank_group_data' => 'nullable|string|max:255',
        ]);

        try {
            $group->update($request->all());
            return back()->with('success', 'Ranking group updated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update ranking group: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $group = RankingGroup::findOrFail($id);
            $group->delete();
            return back()->with('success', 'Ranking group deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete ranking group: ' . $e->getMessage());
        }
    }

    public function toggleStatus($id)
    {
        try {
            $group = RankingGroup::findOrFail($id);
            $group->rank_group_status = $group->rank_group_status === 'active' ? 'in-active' : 'active';
            $group->save();
            
            return back()->with('success', 'Status updated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update status: ' . $e->getMessage());
        }
    }
}
