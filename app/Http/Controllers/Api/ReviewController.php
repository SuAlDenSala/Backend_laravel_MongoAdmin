<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Get reviews for a specific product.
     */
    public function index(string $productId)
    {
        $reviews = Review::where('product_id', $productId)->get();

        return response()->json([
            'success' => true,
            'data' => $reviews
        ]);
    }

    /**
     * Submit a review for a product.
     */
    public function store(Request $request, string $productId)
    {
        $product = Product::find($productId);
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found.'
            ], 404);
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string',
        ]);

        $token = $request->bearerToken() ?? $request->header('X-API-TOKEN');
        $user = User::where('api_token', $token)->first();

        $review = Review::create([
            'product_id' => $productId,
            'user_id' => $user ? $user->_id : null,
            'user_name' => $user ? $user->name : 'Anonymous Customer',
            'rating' => (int) $validated['rating'],
            'comment' => $validated['comment'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Review submitted successfully.',
            'data' => $review
        ], 201);
    }
}
