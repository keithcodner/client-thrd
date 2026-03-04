<?php

namespace App\Http\Controllers\Vendor\Voyager;

use App\Http\Controllers\Controller;
use App\Models\Posts\JobPost;
use App\Models\AdminActivityLog;
use App\Models\User;
use App\Jobs\PopulateSalaryEstimatesFromJobPostsJob;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ManageJobPostsController extends Controller
{
    /**
     * Display a listing of job posts.
     *
     * @param Request $request
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'all'); // all, PUBLISHED, DRAFT, PENDING, REMOVED
        $search = $request->get('search', '');
        $perPage = $request->get('per_page', 25);

        $query = JobPost::with(['author:id,name,email'])
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        // Search by title, company name, or author email
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%")
                    ->orWhereHas('author', function ($authorQuery) use ($search) {
                        $authorQuery->where('email', 'like', "%{$search}%")
                            ->orWhere('name', 'like', "%{$search}%");
                    });
            });
        }

        $jobPosts = $query->paginate($perPage);

        // Get counts for each status
        $statusCounts = [
            'all' => JobPost::count(),
            'COMMITTED' => JobPost::where('status', 'COMMITTED')->count(),
            'UNPAID' => JobPost::where('status', 'UNPAID')->count(),
            'DRAFT' => JobPost::where('status', 'DRAFT')->count(),
            'ARCHIVED' => JobPost::where('status', 'ARCHIVED')->count(),
            'REMOVED' => JobPost::where('status', 'REMOVED')->count(),
        ];

        return Inertia::render('Admin/ContentManagement/ManageJobPosts', [
            'jobPosts' => $jobPosts,
            'statusCounts' => $statusCounts,
            'filters' => [
                'status' => $status,
                'search' => $search,
                'per_page' => $perPage,
            ]
        ]);
    }

    /**
     * Get a specific job post details.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getJobPost(Request $request)
    {
        $request->validate([
            'job_post_id' => 'required|exists:job_posts,id'
        ]);

        $jobPost = JobPost::with(['author:id,name,email'])
            ->findOrFail($request->job_post_id);

        return response()->json($jobPost);
    }

    /**
     * Update the status of a job post (PUBLISHED, REMOVED, etc.)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatus(Request $request)
    {
        $request->validate([
            'job_post_id' => 'required|exists:job_posts,id',
            'status' => 'required|in:COMMITTED,UNPAID,UNPAID_TO_BE_PAID_DRAFT,UNPAID_TO_BE_PAID_DRAFT_REMOVED,DRAFT,ARCHIVED,REMOVED'
        ]);

        $jobPost = JobPost::findOrFail($request->job_post_id);
        $oldStatus = $jobPost->status;
        $newStatus = $request->status;

        $jobPost->status = $newStatus;
        $jobPost->save();

        // Log the status change
        AdminActivityLog::create([
            'admin_id' => auth()->id(),
            'action' => 'job_post_status_update',
            'target_type' => 'JobPost',
            'target_id' => $jobPost->id,
            'description' => "Changed job post #{$jobPost->id} status from {$oldStatus} to {$newStatus}",
        ]);

        return response()->json([
            'success' => true,
            'message' => "Job post status updated from {$oldStatus} to {$newStatus}",
            'jobPost' => $jobPost
        ]);
    }

    /**
     * Hide/Remove a job post (set status to REMOVED).
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function hidePost(Request $request)
    {
        $request->validate([
            'job_post_id' => 'required|exists:job_posts,id',
            'reason' => 'nullable|string|max:500'
        ]);

        $jobPost = JobPost::findOrFail($request->job_post_id);
        $oldStatus = $jobPost->status;

        // Store original status to potentially restore later
        $jobPost->status = 'REMOVED';
        $jobPost->save();

        // Log the action
        $reason = $request->reason ? " Reason: {$request->reason}" : '';
        AdminActivityLog::create([
            'admin_id' => auth()->id(),
            'action' => 'job_post_hidden',
            'target_type' => 'JobPost',
            'target_id' => $jobPost->id,
            'description' => "Hid job post #{$jobPost->id} (was {$oldStatus}).{$reason}",
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Job post has been hidden successfully',
            'jobPost' => $jobPost
        ]);
    }

    /**
     * Restore/Reactivate a hidden job post (set status back to PUBLISHED).
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function restorePost(Request $request)
    {
        $request->validate([
            'job_post_id' => 'required|exists:job_posts,id'
        ]);

        $jobPost = JobPost::findOrFail($request->job_post_id);
        $oldStatus = $jobPost->status;

        // Restore to COMMITTED status (the main visible status)
        $jobPost->status = 'COMMITTED';
        $jobPost->save();

        // Log the action
        AdminActivityLog::create([
            'admin_id' => auth()->id(),
            'action' => 'job_post_restored',
            'target_type' => 'JobPost',
            'target_id' => $jobPost->id,
            'description' => "Restored job post #{$jobPost->id} (was {$oldStatus})",
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Job post has been restored successfully',
            'jobPost' => $jobPost
        ]);
    }

    /**
     * Bulk update job post statuses.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'job_post_ids' => 'required|array|min:1',
            'job_post_ids.*' => 'exists:job_posts,id',
            'status' => 'required|in:COMMITTED,UNPAID,UNPAID_TO_BE_PAID_DRAFT,UNPAID_TO_BE_PAID_DRAFT_REMOVED,DRAFT,ARCHIVED,REMOVED'
        ]);

        $updatedCount = JobPost::whereIn('id', $request->job_post_ids)
            ->update(['status' => $request->status]);

        // Log bulk action
        AdminActivityLog::create([
            'admin_id' => auth()->id(),
            'action' => 'job_post_bulk_status_update',
            'target_type' => 'JobPost',
            'target_id' => null,
            'description' => "Bulk updated {$updatedCount} job posts to status: {$request->status}",
        ]);

        return response()->json([
            'success' => true,
            'message' => "{$updatedCount} job post(s) updated successfully",
            'updated_count' => $updatedCount
        ]);
    }

    /**
     * Generate slugs for all job posts that don't have one or need regeneration
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateSlugs(Request $request)
    {
        try {
            // Only get job posts where slug_trans is 0 (not yet transformed)
            $jobPosts = JobPost::where('slug_trans', 0)->get();
            $updatedCount = 0;
            $skippedCount = 0;
            $errors = [];

            foreach ($jobPosts as $jobPost) {
                try {
                    $slug = $this->createSlugFromTitle($jobPost->title);
                    $jobPost->slug = $slug;
                    $jobPost->slug_trans = 1; // Mark as transformed
                    $jobPost->save();
                    $updatedCount++;
                } catch (\Exception $e) {
                    $errors[] = "Failed to update job post ID {$jobPost->id}: " . $e->getMessage();
                }
            }

            // Count how many were already transformed
            $totalJobPosts = JobPost::count();
            $skippedCount = $totalJobPosts - $updatedCount;

            // Log the action
            AdminActivityLog::create([
                'admin_id' => auth()->id(),
                'action' => 'job_post_slugs_generated',
                'target_type' => 'JobPost',
                'target_id' => null,
                'description' => "Generated slugs for {$updatedCount} job posts (skipped {$skippedCount} already transformed)",
            ]);

            return response()->json([
                'success' => true,
                'message' => "Successfully generated slugs for {$updatedCount} job post(s). Skipped {$skippedCount} already transformed post(s).",
                'updated_count' => $updatedCount,
                'skipped_count' => $skippedCount,
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate slugs: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Populate salary estimates from all job posts
     */
    public function populateSalaryEstimates()
    {
        try {
            // Dispatch the job to run in background
            PopulateSalaryEstimatesFromJobPostsJob::dispatch();
            
            return response()->json([
                'success' => true,
                'message' => 'Salary estimates population job has been queued. This may take several minutes to complete.'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to queue salary estimates population: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a slug from a job title
     * Format: title-words-123456789012 (12 random digits)
     *
     * @param string $title
     * @return string
     */
    private function createSlugFromTitle($title)
    {
        // Convert to lowercase and replace spaces/special chars with hyphens
        $slug = strtolower($title);
        
        // Remove special characters except spaces and hyphens
        $slug = preg_replace('/[^a-z0-9\s\-]/', '', $slug);
        
        // Replace multiple spaces or hyphens with single hyphen
        $slug = preg_replace('/[\s\-]+/', '-', $slug);
        
        // Trim hyphens from start and end
        $slug = trim($slug, '-');
        
        // Generate 12 random digits
        $randomDigits = '';
        for ($i = 0; $i < 12; $i++) {
            $randomDigits .= rand(0, 9);
        }
        
        // Append random digits
        $slug = $slug . '-' . $randomDigits;
        
        return $slug;
    }
}
