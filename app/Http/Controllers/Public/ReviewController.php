<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    /**
     * Show form to create a review.
     */
    public function create(Product $product): View
    {
        // Check if user has already reviewed this product
        $existingReview = Review::where('user_id', auth()->id())
            ->where('product_id', $product->id)
            ->first();

        // Check if user has purchased this product
        $hasPurchased = Order::where('user_id', auth()->id())
            ->whereHas('items', function ($query) use ($product) {
                $query->where('product_id', $product->id);
            })
            ->where('status', 'delivered')
            ->exists();

        return view('public.reviews.create', compact('product', 'existingReview', 'hasPurchased'));
    }

    /**
     * Store a newly created review.
     */
    public function store(Request $request, Product $product): RedirectResponse
    {
        $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'title' => ['nullable', 'string', 'max:255'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        // Check if user has already reviewed
        $existingReview = Review::where('user_id', auth()->id())
            ->where('product_id', $product->id)
            ->first();

        if ($existingReview) {
            return redirect()->back()
                ->with('error', 'You have already reviewed this product.');
        }

        // Check if user has purchased this product
        $isVerifiedPurchase = Order::where('user_id', auth()->id())
            ->whereHas('items', function ($query) use ($product) {
                $query->where('product_id', $product->id);
            })
            ->where('status', 'delivered')
            ->exists();

        Review::create([
            'user_id' => auth()->id(),
            'product_id' => $product->id,
            'rating' => $request->rating,
            'title' => $request->title,
            'comment' => $request->comment,
            'is_verified_purchase' => $isVerifiedPurchase,
            'is_approved' => false, // Requires admin approval
        ]);

        return redirect()->route('products.show', $product)
            ->with('success', 'Thank you for your review! It will be visible after admin approval.');
    }

    /**
     * Mark review as helpful.
     */
    public function helpful(Review $review): RedirectResponse
    {
        $review->increment('helpful_count');

        return redirect()->back()
            ->with('success', 'Thank you for your feedback!');
    }

    /**
     * Mark review as unhelpful.
     */
    public function unhelpful(Review $review): RedirectResponse
    {
        $review->increment('unhelpful_count');

        return redirect()->back()
            ->with('success', 'Thank you for your feedback!');
    }
}
