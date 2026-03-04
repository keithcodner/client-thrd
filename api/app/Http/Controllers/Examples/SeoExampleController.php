<?php

namespace App\Http\Controllers\Examples;

use App\Http\Controllers\Controller;
use App\Http\Traits\SeoMetaTrait;
use App\Models\Posts;
use App\Models\User;
use Inertia\Inertia;

/**
 * Example controller showing how to use SEO meta tags
 * Use this as a reference for implementing SEO in your controllers
 */
class SeoExampleController extends Controller
{
    use SeoMetaTrait;

    /**
     * Example: Homepage with custom SEO
     */
    public function home()
    {
        $this->setSeoMeta([
            'title' => 'GigBizness - Connect, Collaborate, Grow',
            'description' => 'The premier platform for freelancers and entrepreneurs to network, find opportunities, and build their business.',
            'keywords' => 'freelance jobs, gig economy, business networking, professional services',
            'og_image' => asset('images/homepage-og.jpg'),
        ]);

        return Inertia::render('Home');
    }

    /**
     * Example: Post/Article page with dynamic SEO
     */
    public function showPost($id)
    {
        $post = Posts::with('user')->findOrFail($id);
        
        // Generate SEO meta for this post
        $this->setSeoMeta($this->getPostSeoMeta($post));

        return Inertia::render('Post/Show', [
            'post' => $post,
            'articleSchema' => $this->getArticleSchema($post),
            'breadcrumbSchema' => $this->getBreadcrumbSchema([
                ['name' => 'Home', 'url' => url('/')],
                ['name' => 'Posts', 'url' => url('/posts')],
                ['name' => $post->title, 'url' => url('/post/' . $post->id)],
            ]),
        ]);
    }

    /**
     * Example: User profile with dynamic SEO
     */
    public function showProfile($id)
    {
        $user = User::findOrFail($id);
        
        // Generate SEO meta for this profile
        $this->setSeoMeta($this->getProfileSeoMeta($user));

        return Inertia::render('Profile/Show', [
            'user' => $user,
            'breadcrumbSchema' => $this->getBreadcrumbSchema([
                ['name' => 'Home', 'url' => url('/')],
                ['name' => 'Profiles', 'url' => url('/profiles')],
                ['name' => $user->name, 'url' => url('/profile/' . $user->id)],
            ]),
        ]);
    }

    /**
     * Example: Pages that should not be indexed
     */
    public function privateArea()
    {
        $this->setSeoMeta([
            'title' => 'Private Area - GigBizness',
            'robots' => 'noindex, nofollow', // Don't index this page
        ]);

        return Inertia::render('Private/Dashboard');
    }

    /**
     * Example: Category/Collection page
     */
    public function category($slug)
    {
        $this->setSeoMeta([
            'title' => ucfirst($slug) . ' Services - GigBizness',
            'description' => 'Find the best ' . $slug . ' professionals and services on GigBizness.',
            'keywords' => $slug . ', services, freelance, gig economy',
            'canonical' => url('/category/' . $slug),
        ]);

        return Inertia::render('Category/Show', [
            'category' => $slug,
        ]);
    }
}
