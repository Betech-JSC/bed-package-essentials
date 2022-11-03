<?php

namespace Jamstackvietnam\Blog\Controllers;

use Inertia\Inertia;
use Illuminate\Routing\Controller;
use Jamstackvietnam\Blog\Models\Post;
use Jamstackvietnam\Blog\Models\PostCategory;

class PostController extends Controller
{
    public $model = Post::class;

    public function index()
    {
        $posts = $this->model::query()
            ->active()
            ->where('posted_at', '<=', now())
            ->orderByDesc('is_featured')
            ->orderByDesc('posted_at')
            ->orderByDesc('id')
            ->take(5)
            ->get();

        $categories = PostCategory::query()
            ->active()
            ->orderByDesc('position')
            ->orderByDesc('id')
            ->get();

        $data = [
            'posts' => $posts->map(fn ($item) => $item->transform()),
            'categories' => $categories->map(fn ($item) => $item->transformPosts()),
        ];

        if (request()->wantsJson()) {
            return response()->json($data);
        }

        return Inertia::render('Posts/Index', $data);
    }

    public function category($slug)
    {
        $category = PostCategory::query()
            ->active()
            ->whereSlug($slug)
            ->firstOrFail();

        $query = $this->model::query()
            ->active()
            ->where('posted_at', '<=', now())
            ->orderByDesc('is_featured')
            ->orderByDesc('posted_at')
            ->orderByDesc('id')
            ->whereHas('categories', function ($query) use ($category) {
                $query->where('post_categories.id', $category->id);
            });

        $posts = $query->paginate(18)
            ->through(function ($item) {
                return $item->transform();
            })->withQueryString();

        $data = [
            'category' => $category->transform(),
            'posts' => $posts,
        ];

        if (request()->wantsJson()) {
            return response()->json($data);
        }

        return Inertia::render('Posts/Category', $data);
    }

    public function show($slug, $id)
    {
        $post = $this->model::query()
            ->active()
            ->with('translations')
            ->findOrFail($id);

        $post->increment('view_count');

        $relatedPosts = $post->related();

        $data = [
            'post' => $post->transformDetails(),
            'related_posts' => $relatedPosts
        ];

        if (request()->wantsJson()) {
            return response()->json($data);
        }

        return Inertia::render('Posts/Show', $data);
    }

    public function getCategories()
    {
        return PostCategory::active()->get()->map(fn($item) => $item->transform());
    }
}
