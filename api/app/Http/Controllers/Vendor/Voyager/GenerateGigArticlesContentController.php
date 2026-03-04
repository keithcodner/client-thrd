<?php

namespace App\Http\Controllers\Vendor\Voyager;

use App\Models\Articles\Article;
use App\Models\Articles\FileArticle;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class GenerateGigArticlesContentController extends Controller
{
    public function __construct()
    {
        //$this->middleware(['auth']);
    }

    /**
     * Display a listing of articles with statistics.
     */
    public function index(Request $request)
    {
        $query = Article::with(['user', 'files']);

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by type
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        // Sort
        $sortBy = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        $articles = $query->paginate(15)->withQueryString();

        // Statistics
        $statistics = [
            'total_articles' => Article::count(),
            'published_articles' => Article::where('status', 'published')->count(),
            'draft_articles' => Article::where('status', 'draft')->count(),
            'total_views' => Article::sum('views'),
            'articles_this_month' => Article::whereMonth('created_at', now()->month)
                                           ->whereYear('created_at', now()->year)
                                           ->count(),
        ];

        // Get available statuses and types for filters
        $statuses = Article::select('status')
                           ->distinct()
                           ->whereNotNull('status')
                           ->pluck('status');

        $types = Article::select('type')
                       ->distinct()
                       ->whereNotNull('type')
                       ->pluck('type');

        return Inertia::render('Admin/ManageArticles/ManageArticles', [
            'articles' => $articles,
            'statistics' => $statistics,
            'filters' => [
                'search' => $request->search ?? '',
                'status' => $request->status ?? '',
                'type' => $request->type ?? '',
                'sort' => $sortBy,
                'direction' => $sortDirection,
            ],
            'availableStatuses' => $statuses,
            'availableTypes' => $types,
        ]);
    }

    /**
     * Store a newly created article.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:2000',
            'description' => 'required|string',
            'link' => 'nullable|url|max:65535',
            'status' => 'required|string|in:draft,published,archived',
            'type' => 'required|string|max:50',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:10240',
        ]);

        DB::beginTransaction();
        try {
            $article = Article::create([
                'user_id' => Auth::id(),
                'subject' => $validated['subject'],
                'description' => $validated['description'],
                'link' => $validated['link'] ?? null,
                'status' => $validated['status'],
                'type' => $validated['type'],
                'views' => 0,
            ]);

            // Handle image uploads
            if ($request->hasFile('images')) {
                $this->uploadArticleImages($article, $request->file('images'));
            }

            DB::commit();

            return redirect()->back()->with('success', 'Article created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to create article: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified article.
     */
    public function update(Request $request, $id)
    {
        $article = Article::findOrFail($id);

        $validated = $request->validate([
            'subject' => 'required|string|max:2000',
            'description' => 'required|string',
            'link' => 'nullable|url|max:65535',
            'status' => 'required|string|in:draft,published,archived',
            'type' => 'required|string|max:50',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'deleted_images' => 'nullable|array',
        ]);

        DB::beginTransaction();
        try {
            $article->update([
                'subject' => $validated['subject'],
                'description' => $validated['description'],
                'link' => $validated['link'] ?? null,
                'status' => $validated['status'],
                'type' => $validated['type'],
            ]);

            // Handle deleted images
            if ($request->has('deleted_images') && is_array($request->deleted_images)) {
                $this->deleteArticleImages($article, $request->deleted_images);
            }

            // Handle new image uploads
            if ($request->hasFile('images')) {
                $this->uploadArticleImages($article, $request->file('images'));
            }

            DB::commit();

            return redirect()->back()->with('success', 'Article updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to update article: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified article.
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $article = Article::findOrFail($id);

            // Delete all associated images
            $files = $article->files;
            foreach ($files as $file) {
                $this->deleteFile($file);
            }

            $article->delete();

            DB::commit();

            return redirect()->back()->with('success', 'Article deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to delete article: ' . $e->getMessage());
        }
    }

    /**
     * Bulk delete articles.
     */
    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:articles,id',
        ]);

        DB::beginTransaction();
        try {
            foreach ($validated['ids'] as $id) {
                $article = Article::find($id);
                if ($article) {
                    // Delete all associated images
                    $files = $article->files;
                    foreach ($files as $file) {
                        $this->deleteFile($file);
                    }
                    $article->delete();
                }
            }

            DB::commit();

            return redirect()->back()->with('success', count($validated['ids']) . ' article(s) deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to delete articles: ' . $e->getMessage());
        }
    }

    /**
     * Bulk update status.
     */
    public function bulkUpdateStatus(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:articles,id',
            'status' => 'required|string|in:draft,published,archived',
        ]);

        Article::whereIn('id', $validated['ids'])->update(['status' => $validated['status']]);

        return redirect()->back()->with('success', count($validated['ids']) . ' article(s) updated successfully.');
    }

    /**
     * Delete a specific image from an article.
     */
    public function deleteImage(Request $request, $articleId, $imageId)
    {
        try {
            $article = Article::findOrFail($articleId);
            
            $file = FileArticle::where('id', $imageId)
                ->where('reference_id', $article->id)
                ->where('table_reference_name', 'articles')
                ->first();

            if (!$file) {
                return response()->json(['error' => 'Image not found'], 404);
            }

            $this->deleteFile($file);

            return response()->json(['success' => true, 'message' => 'Image deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete image: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Upload images for an article.
     */
    protected function uploadArticleImages(Article $article, array $images): void
    {
        $maxOrder = $article->files()->max('file_order') ?? 0;

        foreach ($images as $index => $image) {
            $filename = Str::uuid() . '.' . $image->getClientOriginalExtension();
            $foldername = 'articles/' . $article->id;
            
            $path = $image->storeAs($foldername, $filename, 'public');
            
            if ($path) {
                FileArticle::create([
                    'reference_id' => $article->id,
                    'table_reference_name' => 'articles',
                    'file_store_an_id' => Str::uuid(),
                    'filename' => $filename,
                    'foldername' => $foldername,
                    'status' => 'active',
                    'verify_status' => 'verified',
                    'type' => 'image',
                    'file_order' => $maxOrder + $index + 1,
                ]);
            }
        }
    }

    /**
     * Delete specific images from an article.
     */
    protected function deleteArticleImages(Article $article, array $imageIds): void
    {
        $files = FileArticle::whereIn('id', $imageIds)
            ->where('reference_id', $article->id)
            ->where('table_reference_name', 'articles')
            ->get();

        foreach ($files as $file) {
            $this->deleteFile($file);
        }
    }

    /**
     * Delete a single file and its database record.
     */
    protected function deleteFile(FileArticle $file): void
    {
        if ($file->foldername && $file->filename) {
            $filePath = $file->foldername . '/' . $file->filename;
            Storage::disk('public')->delete($filePath);
        }

        $file->delete();
    }
}
