<?php

namespace App\Http\Controllers\Vendor\Voyager;

use App\Models\Ranking\RankingInteractCatalog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class ManageRankingCatalogController extends Controller
{
    public function index()
    {
        $interactions = RankingInteractCatalog::orderBy('created_at', 'desc')->get();

        return Inertia::render('Admin/RankingManagement/ManageRankingCatalog', [
            'interactions' => $interactions,
            'success' => session('success'),
            'error' => session('error')
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'rank_interact_name' => 'required|string|max:1000|unique:ranking_interaction_catalog',
            'rank_interact_rate' => 'required|numeric|min:0',
            'rank_interact_type' => 'required|in:ACTION,PASSIVE',
            'rank_interact_status' => 'required|in:active,inactive',
            'rank_interact_op_1' => 'required|in:gain,loss',
            'rank_interact_op_2' => 'nullable|string|max:500',
            'rank_interact_passive_threshold' => 'nullable|integer|min:0',
            'rank_interact_passive_reward' => 'nullable|integer|min:0',
        ]);

        try {
            RankingInteractCatalog::create($request->all());
            return back()->with('success', 'Ranking interaction created successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create ranking interaction: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $interaction = RankingInteractCatalog::findOrFail($id);

        $request->validate([
            'rank_interact_name' => [
                'required',
                'string',
                'max:1000',
                Rule::unique('ranking_interaction_catalog')->ignore($id)
            ],
            'rank_interact_rate' => 'required|numeric|min:0',
            'rank_interact_type' => 'required|in:ACTION,PASSIVE',
            'rank_interact_status' => 'required|in:active,inactive',
            'rank_interact_op_1' => 'required|in:gain,loss',
            'rank_interact_op_2' => 'nullable|string|max:500',
            'rank_interact_passive_threshold' => 'nullable|integer|min:0',
            'rank_interact_passive_reward' => 'nullable|integer|min:0',
        ]);

        try {
            $interaction->update($request->all());
            return back()->with('success', 'Ranking interaction updated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update ranking interaction: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $interaction = RankingInteractCatalog::findOrFail($id);
            $interaction->delete();
            return back()->with('success', 'Ranking interaction deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete ranking interaction: ' . $e->getMessage());
        }
    }

    public function toggleStatus($id)
    {
        try {
            $interaction = RankingInteractCatalog::findOrFail($id);
            $interaction->rank_interact_status = $interaction->rank_interact_status === 'active' ? 'inactive' : 'active';
            $interaction->save();
            
            return back()->with('success', 'Status updated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update status: ' . $e->getMessage());
        }
    }
}
