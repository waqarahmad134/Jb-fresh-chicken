<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Get reviews for a product.
     */
    public function index($productId)
    {
        $reviews = Review::where('product_id', $productId)
            ->where('is_approved', true)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($reviews);
    }

    /**
     * Create a new review.
     */
    public function store(Request $request, $productId)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        $product = Product::findOrFail($productId);

        // Check if user already reviewed this product
        $existingReview = Review::where('product_id', $product->id)
            ->where('user_id', $request->user()->id)
            ->first();

        if ($existingReview) {
            return response()->json(['message' => 'You have already reviewed this product'], 400);
        }

        $review = Review::create([
            'product_id' => $product->id,
            'user_id' => $request->user()->id,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
            'is_approved' => true, // Auto-approve for now
        ]);

        return response()->json([
            'message' => 'Review submitted successfully',
            'review' => $review->load('user'),
        ], 201);
    }

    /**
     * Update a review.
     */
    public function update(Request $request, $productId, $reviewId)
    {
        $validated = $request->validate([
            'rating' => 'sometimes|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        $review = Review::where('id', $reviewId)
            ->where('product_id', $productId)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $review->update($validated);

        return response()->json([
            'message' => 'Review updated successfully',
            'review' => $review->load('user'),
        ]);
    }

    /**
     * Delete a review.
     */
    public function destroy(Request $request, $productId, $reviewId)
    {
        $review = Review::where('id', $reviewId)
            ->where('product_id', $productId)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $review->delete();

        return response()->json(['message' => 'Review deleted successfully']);
    }
}

