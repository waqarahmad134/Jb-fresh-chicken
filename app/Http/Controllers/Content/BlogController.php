<?php

namespace App\Http\Controllers\Content;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use Illuminate\View\View;

class BlogController extends Controller
{
    /**
     * Display the blog listing page.
     */
    public function index(): View
    {
        $posts = BlogPost::query()
            ->where('is_published', true)
            ->where('published_at', '<=', now())
            ->with(['blogCategory', 'author'])
            ->orderByDesc('published_at')
            ->paginate(12);

        return view('pages.blog.index', compact('posts'));
    }

    /**
     * Display a single blog post.
     */
    public function show(string $slug): View
    {
        $post = BlogPost::where('slug', $slug)
            ->where('is_published', true)
            ->where('published_at', '<=', now())
            ->with(['blogCategory', 'author'])
            ->firstOrFail();

        return view('pages.blog.show', compact('post'));
    }
}

