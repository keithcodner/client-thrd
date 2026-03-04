<?php

namespace App\Http\Controllers\Posts;

use App\Http\Controllers\Controller;
use App\Models\Posts\JobPost;
use App\Models\Core\FilePostStored;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Carbon\Carbon;

class ViewPostController extends Controller
{
    /**
     * Show a single job post by slug and increment view count
     */
    public function show(Request $request, $slug)
    {
        try {
            // Find the job post by slug
            $job = JobPost::where('slug', $slug)
                ->where('status', 'COMMITTED') // Only show committed (published) posts
                ->firstOrFail();

            // Increment view count
            if ($job->views === null) {
                $job->views = 0;
            }
            $job->views = $job->views + 1;
            $job->save();

            Log::info('Job post viewed', ['slug' => $slug, 'views' => $job->views]);

            // Get uploaded image if exists (use url() for absolute URLs needed by Twitter/Reddit)
            $uploadedImage = null;
            $imageFile = FilePostStored::where('post_id', $job->id)
                ->where('type', 'job_post')
                ->where('status', 'active')
                ->first();
            
            if ($imageFile) {
                $uploadedImage = url('storage/store_data/posts/draft/' . $imageFile->foldername . '/' . $imageFile->filename);
            }

            // Parse JSON fields if they are strings
            $benefits = $job->benefits;
            if (is_string($benefits)) {
                $benefits = json_decode($benefits, true) ?: [];
            }
            $benefits = is_array($benefits) ? array_slice($benefits, 0, 10) : [];

            $tags = $job->secondary_tags;
            if (is_string($tags)) {
                $tags = json_decode($tags, true) ?: [];
            }
            $tags = is_array($tags) ? $tags : [];

            $skills = $job->skills;
            if (is_string($skills)) {
                $skills = json_decode($skills, true) ?: [];
            }
            $skills = is_array($skills) ? $skills : [];

            // Calculate posted days ago
            $postedDaysAgo = $job->created_at ? $job->created_at->diffForHumans() : 'Recently';

            // Format location type
            $locationType = $job->location_type ? ucfirst(strtolower($job->location_type)) : null;

            // Format payment frequency
            $paymentFrequency = $job->payment_frequency;
            if ($paymentFrequency) {
                $paymentFrequency = ucfirst(str_replace('_', ' ', strtolower($paymentFrequency)));
            }

            // Fetch similar jobs based on primary_tag
            $similarJobs = [];
            if ($job->primary_tag) {
                $similarJobs = JobPost::where('status', 'COMMITTED')
                    ->where('primary_tag', $job->primary_tag)
                    ->where('id', '!=', $job->id) // Exclude current job
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get()
                    ->map(function ($similarJob) {
                        // Get uploaded image for similar job
                        $similarUploadedImage = null;
                        $similarImageFile = FilePostStored::where('post_id', $similarJob->id)
                            ->where('type', 'job_post')
                            ->where('status', 'active')
                            ->first();
                        
                        if ($similarImageFile) {
                            $similarUploadedImage = asset('storage/store_data/posts/draft/' . $similarImageFile->foldername . '/' . $similarImageFile->filename);
                        }

                        return [
                            'id' => $similarJob->id,
                            'slug' => $similarJob->slug,
                            'title' => $similarJob->title,
                            'company_name' => $similarJob->company_name,
                            'company_logo' => $similarJob->company_logo,
                            'uploaded_image' => $similarUploadedImage,
                            'location' => $similarJob->location,
                            'location_type' => $similarJob->location_type ? ucfirst(strtolower($similarJob->location_type)) : null,
                            'employer_type' => $similarJob->employer_type,
                            'posted_ago' => $similarJob->created_at ? $similarJob->created_at->diffForHumans() : 'Recently',
                        ];
                    });
            }

            // Prepare job data for frontend
            $jobData = [
                'id' => $job->id,
                'slug' => $job->slug,
                'title' => $job->title,
                'company_name' => $job->company_name,
                'company_url' => $job->company_website,
                'uploaded_image' => $uploadedImage,
                'company_logo' => $job->company_logo,
                'location' => $job->location,
                'location_type' => $locationType,
                'employer_type' => $job->employer_type,
                'salary' => $job->salary_min && $job->salary_max 
                    ? '$' . number_format($job->salary_min) . ' - $' . number_format($job->salary_max)
                    : ($job->budget ? '$' . number_format($job->budget) : null),
                'payment_frequency' => $paymentFrequency,
                'job_description' => $job->job_description,
                'description' => $job->job_description, // Alias for structured data
                'apply_url' => $job->apply_url,
                'apply_email' => $job->apply_email_address,
                'tags' => array_merge($tags, $skills),
                'benefits' => $benefits,
                'posted_ago' => $postedDaysAgo,
                'created_at' => $job->created_at ? $job->created_at->toISOString() : null,
                'expires_at' => $job->expires_at ? $job->expires_at->toISOString() : null,
                'views' => $job->views ?? 0,
                'clicks' => $job->clicks ?? 0,
                'create_qr_code' => $job->create_qr_code ?? false,
                'highlight_post' => $job->highlight_post ?? false,
                'sticky_note_24_hour' => $job->sticky_note_24_hour ?? false,
                'sticky_note_week' => $job->sticky_note_week ?? false,
                'sticky_note_month' => $job->sticky_note_month ?? false,
                'primary_tag' => $job->primary_tag,
            ];

            // Prepare Open Graph image with absolute URL for social media (Reddit, LinkedIn, Twitter)
            $ogImageUrl = $uploadedImage ?: ($job->company_logo ? url($job->company_logo) : url('images/og-default.svg'));

            return Inertia::render('Views/ViewPost', [
                'job' => $jobData,
                'similarJobs' => $similarJobs,
            ])
            ->withViewData([
                'metaTitle' => $job->title . ' at ' . $job->company_name . ' | GigBizness Jobs',
                'metaDescription' => strip_tags(substr($job->job_description, 0, 155)) . '...',
                'ogTitle' => $job->title . ' at ' . $job->company_name,
                'ogDescription' => strip_tags(substr($job->job_description, 0, 155)) . '...',
                'ogImage' => $ogImageUrl,
                'ogType' => 'article',
                'ogUrl' => url()->current(),
                'twitterImage' => $ogImageUrl,
                'twitterCard' => 'summary_large_image',
            ]);

        } catch (\Exception $e) {
            Log::error('Error displaying job post', [
                'slug' => $slug,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            abort(404, 'Job post not found');
        }
    }

    /**
     * Record a click on the apply button
     */
    public function recordClick(Request $request, $slug)
    {
        try {
            $job = JobPost::where('slug', $slug)
                ->where('status', 'COMMITTED')
                ->firstOrFail();
            
            // Handle NULL values explicitly
            if ($job->clicks === null) {
                $job->clicks = 0;
            }
            $job->clicks = $job->clicks + 1;
            $job->save();
            
            Log::info('Click recorded for job post', ['slug' => $slug, 'clicks' => $job->clicks]);
            
            return response()->json([
                'success' => true,
                'clicks' => $job->clicks
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to record click', [
                'slug' => $slug,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
