<?php

namespace App\Http\Controllers\Vendor\Voyager;

use App\Models\Ranking\RankingPermission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class ManageRankingPermissionsController extends Controller
{
    public function index()
    {
        $permissions = RankingPermission::orderBy('rank_perm_order', 'asc')
                                     ->orderBy('rank_perm_threshold', 'asc')
                                     ->get();

        return Inertia::render('Admin/RankingManagement/ManageRankingPermissions', [
            'permissions' => $permissions,
            'success' => session('success'),
            'error' => session('error')
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'rank_perm_name' => 'required|string|max:355|unique:ranking_permissions',
            'rank_perm_threshold' => 'required|integer|min:0',
            'rank_perm_value' => 'required|integer|min:0',
            'rank_perm_order' => 'required|integer|min:0',
            'rank_perm_type1' => 'required|string|in:post,item,trade,comment,message',
            'rank_perm_type2' => 'nullable|string|max:255',
            'rank_perm_status' => 'required|in:active,inactive',
            'rank_perm_limit_duration' => 'required|string|in:1hour,1day,1week,1month,1year,unlimited',
            'rank_perm_op2' => 'nullable|string|max:500',
            'rank_perm_op3' => 'nullable|string|max:500',
        ]);

        try {
            RankingPermission::create($request->all());
            return back()->with('success', 'Ranking permission created successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create ranking permission: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $permission = RankingPermission::findOrFail($id);

        $request->validate([
            'rank_perm_name' => [
                'required',
                'string',
                'max:355',
                Rule::unique('ranking_permissions')->ignore($id)
            ],
            'rank_perm_threshold' => 'required|integer|min:0',
            'rank_perm_value' => 'required|integer|min:0',
            'rank_perm_order' => 'required|integer|min:0',
            'rank_perm_type1' => 'required|string|in:post,item,trade,comment,message',
            'rank_perm_type2' => 'nullable|string|max:255',
            'rank_perm_status' => 'required|in:active,inactive',
            'rank_perm_limit_duration' => 'required|string|in:1hour,1day,1week,1month,1year,unlimited',
            'rank_perm_op2' => 'nullable|string|max:500',
            'rank_perm_op3' => 'nullable|string|max:500',
        ]);

        try {
            $permission->update($request->all());
            return back()->with('success', 'Ranking permission updated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update ranking permission: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $permission = RankingPermission::findOrFail($id);
            $permission->delete();
            return back()->with('success', 'Ranking permission deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete ranking permission: ' . $e->getMessage());
        }
    }

    public function toggleStatus($id)
    {
        try {
            $permission = RankingPermission::findOrFail($id);
            $permission->rank_perm_status = $permission->rank_perm_status === 'active' ? 'inactive' : 'active';
            $permission->save();
            
            return back()->with('success', 'Status updated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update status: ' . $e->getMessage());
        }
    }
}
