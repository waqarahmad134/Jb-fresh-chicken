<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories.
     */
    public function index()
    {
        $categories = Category::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return response()->json($categories);
    }

    /**
     * Display the specified category with products.
     */
    public function show($slug)
    {
        $category = Category::where('slug', $slug)
            ->with(['products' => function ($query) {
                $query->where('is_active', true)
                    ->with(['images', 'tags'])
                    ->orderBy('sort_order');
            }])
            ->firstOrFail();

        return response()->json($category);
    }
}

