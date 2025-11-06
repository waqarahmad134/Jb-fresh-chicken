<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Page;

class PageController extends Controller
{
    /**
     * Get all published pages.
     */
    public function index()
    {
        $pages = Page::where('is_published', true)->get();
        return response()->json($pages);
    }

    /**
     * Get a specific page by slug.
     */
    public function show($slug)
    {
        $page = Page::where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        return response()->json($page);
    }
}

