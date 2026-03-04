<?php

namespace App\Http\Controllers\Article;

use App\Http\Controllers\Controller;
use App\Models\Articles\Article;
use App\Models\Posts\JobPost;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\DB;

class ArticleController extends Controller
{
    /**
     * Display a listing of news articles (browse).
     */
    public function index(Request $request): Response
    {
        $query = Article::with(['user', 'files'])
            ->where('status', 'published')
            ->where('type', 'news');

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Sort by most recent
        $query->orderBy('created_at', 'desc');

        // Get articles for keyword extraction
        $allArticles = $query->get();
        
        // Paginate results
        $articles = $query->paginate(12)->through(function ($article) {
            return [
                'id' => $article->id,
                'subject' => $article->subject,
                'description' => $article->description,
                'link' => $article->link,
                'excerpt' => strip_tags(substr($article->description, 0, 150)) . '...',
                'views' => $article->views,
                'created_at' => $article->created_at->format('M d, Y'),
                'author' => $article->user ? $article->user->name : 'Anonymous',
                'image' => $article->files->first() ? '/storage/' . $article->files->first()->foldername . '/' . $article->files->first()->filename : null,
                'slug' => \Str::slug($article->subject) . '-' . $article->id,
            ];
        });

        // Extract keywords from all articles on the page for job matching
        $combinedContent = '';
        foreach ($allArticles->take(12) as $article) {
            $combinedContent .= ' ' . $article->subject . ' ' . strip_tags($article->description);
        }
        $keywords = $this->extractKeywords($combinedContent);

        // Get similar jobs based on article keywords
        $similarJobs = collect([]);
        if (!empty($keywords)) {
            $similarJobs = JobPost::where('status', 'COMMITTED')
                ->where(function($query) use ($keywords) {
                    foreach ($keywords as $keyword) {
                        $query->orWhere('title', 'like', "%{$keyword}%")
                              ->orWhere('job_description', 'like', "%{$keyword}%")
                              ->orWhere('primary_tag', 'like', "%{$keyword}%");
                    }
                })
                ->where('expires_at', '>', now())
                ->orderBy('created_at', 'desc')
                ->limit(6)
                ->get()
                ->map(function($job) {
                    return [
                        'id' => $job->id,
                        'title' => $job->title,
                        'company_name' => $job->company_name,
                        'employer_type' => $job->employer_type,
                        'location_type' => $job->location_type,
                        'primary_tag' => $job->primary_tag,
                        'salary_min' => $job->salary_min,
                        'salary_max' => $job->salary_max,
                        'currency' => $job->currency,
                        'slug' => $job->slug,
                        'created_at' => $job->created_at->format('M d, Y'),
                    ];
                });
        }
        
        // If no keyword matches found, get recent jobs as fallback
        if ($similarJobs->isEmpty()) {
            $similarJobs = JobPost::where('status', 'COMMITTED')
                ->where('expires_at', '>', now())
                ->orderBy('created_at', 'desc')
                ->limit(6)
                ->get()
                ->map(function($job) {
                    return [
                        'id' => $job->id,
                        'title' => $job->title,
                        'company_name' => $job->company_name,
                        'employer_type' => $job->employer_type,
                        'location_type' => $job->location_type,
                        'primary_tag' => $job->primary_tag,
                        'salary_min' => $job->salary_min,
                        'salary_max' => $job->salary_max,
                        'currency' => $job->currency,
                        'slug' => $job->slug,
                        'created_at' => $job->created_at->format('M d, Y'),
                    ];
                });
        }

        return Inertia::render('Articles/ArticleBrowse', [
            'articles' => $articles,
            'similarJobs' => $similarJobs,
            'filters' => [
                'search' => $request->search ?? '',
            ],
        ]);
    }

    /**
     * Search news articles.
     */
    public function search(Request $request): Response
    {
        $search = $request->input('q', '');

        $query = Article::with(['user', 'files'])
            ->where('status', 'published')
            ->where('type', 'news');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $query->orderBy('created_at', 'desc');

        $articles = $query->paginate(12)->through(function ($article) {
            return [
                'id' => $article->id,
                'subject' => $article->subject,
                'description' => $article->description,
                'link' => $article->link,
                'excerpt' => strip_tags(substr($article->description, 0, 150)) . '...',
                'views' => $article->views,
                'created_at' => $article->created_at->format('M d, Y'),
                'author' => $article->user ? $article->user->name : 'Anonymous',
                'image' => $article->files->first() ? '/storage/' . $article->files->first()->foldername . '/' . $article->files->first()->filename : null,
                'slug' => \Str::slug($article->subject) . '-' . $article->id,
            ];
        });

        return Inertia::render('Articles/ArticleSearch', [
            'articles' => $articles,
            'searchTerm' => $search,
        ]);
    }

    /**
     * Display a specific article and related jobs.
     */
    public function show(Request $request, $slug): Response
    {
        // Extract article ID from slug
        $id = (int) substr($slug, strrpos($slug, '-') + 1);

        $article = Article::with(['user', 'files'])->findOrFail($id);

        // Increment views
        $article->incrementViews();

        // Get related jobs based on keywords from article title only
        $keywords = $this->extractKeywords($article->subject);
        
        $relatedJobs = collect([]);
        
        // Try to find jobs matching keywords
        if (!empty($keywords)) {
            $relatedJobs = JobPost::where('status', 'COMMITTED')
                ->where(function($query) use ($keywords) {
                    foreach ($keywords as $keyword) {
                        $query->orWhere('title', 'like', "%{$keyword}%")
                              ->orWhere('job_description', 'like', "%{$keyword}%")
                              ->orWhere('primary_tag', 'like', "%{$keyword}%");
                    }
                })
                ->where('expires_at', '>', now())
                ->orderBy('created_at', 'desc')
                ->limit(6)
                ->get();
        }
        
        // If no keyword matches found, get recent jobs as fallback
        if ($relatedJobs->isEmpty()) {
            $relatedJobs = JobPost::where('status', 'COMMITTED')
                ->where('expires_at', '>', now())
                ->orderBy('created_at', 'desc')
                ->limit(6)
                ->get();
        }
        
        $relatedJobs = $relatedJobs->map(function($job) {
            return [
                'id' => $job->id,
                'title' => $job->title,
                'company_name' => $job->company_name,
                'employer_type' => $job->employer_type,
                'location_type' => $job->location_type,
                'primary_tag' => $job->primary_tag,
                'salary_min' => $job->salary_min,
                'salary_max' => $job->salary_max,
                'currency' => $job->currency,
                'slug' => $job->slug,
                'created_at' => $job->created_at->format('M d, Y'),
            ];
        });

        // Determine og:image with absolute URL (article image or default)
        $ogImage = $article->files->count() > 0
            ? url('storage/' . $article->files->first()->foldername . '/' . $article->files->first()->filename)
            : url('images/og-default.svg');

        return Inertia::render('Articles/ArticleView', [
            'article' => [
                'id' => $article->id,
                'subject' => $article->subject,
                'description' => $article->description,
                'link' => $article->link,
                'views' => $article->views,
                'created_at' => $article->created_at->format('M d, Y'),
                'author' => $article->user ? [
                    'name' => $article->user->name,
                    'id' => $article->user->id,
                ] : null,
                'images' => $article->files->map(function($file) {
                    return [
                        'id' => $file->id,
                        'url' => asset('storage/' . $file->foldername . '/' . $file->filename),
                        'filename' => $file->filename,
                    ];
                }),
            ],
            'relatedJobs' => $relatedJobs,
        ])
        ->withViewData([
            'metaTitle' => $article->subject . ' | GigBizness',
            'metaDescription' => \Illuminate\Support\Str::limit(strip_tags($article->description), 155),
            'ogTitle' => $article->subject,
            'ogDescription' => \Illuminate\Support\Str::limit(strip_tags($article->description), 155),
            'ogImage' => $ogImage,
            'ogType' => 'article',
            'ogUrl' => url()->current(),
            'twitterCard' => 'summary_large_image',
            'twitterImage' => $ogImage,
            'twitterTitle' => $article->subject,
            'twitterDescription' => \Illuminate\Support\Str::limit(strip_tags($article->description), 155),
            'articlePublishedTime' => $article->created_at ? $article->created_at->toIso8601String() : null,
            'articleAuthor' => $article->user->name ?? 'Gigbizness',
        ]);
    }

    /**
     * Extract keywords from text for finding related jobs.
     */
    private function extractKeywords($text): array
    {
        // Remove HTML tags and special characters
        $text = strip_tags($text);
        $text = preg_replace('/[^a-zA-Z0-9\s]/', '', $text);
        
        // Convert to lowercase and split into words
        $words = str_word_count(strtolower($text), 1);
        
        // Remove common words (stop words)
        $stopWords = ['the', 'a', 'an', 'and', 'or', 'but', 'in', 'on', 'at', 'to', 'for', 'of', 'with', 'by', 'from', 'as', 'is', 'was', 'are', 'were', 'been', 'be', 'have', 'has', 'had', 'do', 'does', 'did', 'will', 'would', 'could', 'should', 'may', 'might', 'must', 'can', 'this', 'that', 'these', 'those'];
        
        $keywords = array_filter($words, function($word) use ($stopWords) {
            return strlen($word) > 3 && !in_array($word, $stopWords);
        });
        
        // Get word frequency
        $frequency = array_count_values($keywords);
        arsort($frequency);
        
        // Return top 10 keywords
        return array_slice(array_keys($frequency), 0, 10);
    }
}
