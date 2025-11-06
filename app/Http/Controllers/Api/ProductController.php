<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of products.
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'images', 'tags'])
            ->where('is_active', true);

        // Filter by category
        if ($request->has('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Filter by tags
        if ($request->has('tag')) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->where('slug', $request->tag);
            });
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Featured only
        if ($request->has('featured') && $request->featured) {
            $query->where('is_featured', true);
        }

        // Sort
        $sortBy = $request->input('sort_by', 'sort_order');
        $sortOrder = $request->input('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        $products = $query->get();

        return response()->json($products);
    }

    /**
     * Display the specified product.
     */
    public function show($id)
    {
        $product = Product::with(['category', 'images', 'tags', 'reviews.user'])
            ->findOrFail($id);

        // Calculate average rating
        $averageRating = $product->averageRating();

        $data = $product->toArray();
        $data['average_rating'] = round($averageRating, 1);
        $data['reviews_count'] = $product->reviews()->where('is_approved', true)->count();

        return response()->json($data);
    }

    /**
     * Store a newly created product (Admin only).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'image_urls' => 'nullable|array',
            'tags' => 'nullable|array',
        ]);

        $product = Product::create([
            'name' => $validated['name'],
            'slug' => \Str::slug($validated['name']),
            'description' => $validated['description'],
            'price' => $validated['price'],
            'category_id' => $validated['category_id'] ?? null,
            'is_active' => true,
        ]);

        // Add images
        if (!empty($validated['image_urls'])) {
            foreach ($validated['image_urls'] as $index => $imageUrl) {
                $product->images()->create([
                    'image_url' => $imageUrl,
                    'sort_order' => $index,
                    'is_primary' => $index === 0,
                ]);
            }
        }

        // Attach tags
        if (!empty($validated['tags'])) {
            $product->tags()->sync($validated['tags']);
        }

        return response()->json($product->load(['category', 'images', 'tags']), 201);
    }

    /**
     * Update the specified product (Admin only).
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'price' => 'sometimes|numeric|min:0',
            'category_id' => 'nullable|exists:categories,id',
            'is_active' => 'sometimes|boolean',
            'is_featured' => 'sometimes|boolean',
        ]);

        $product->update($validated);

        return response()->json($product->load(['category', 'images', 'tags']));
    }

    /**
     * Remove the specified product (Admin only).
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return response()->json(['message' => 'Product deleted successfully']);
    }
}

