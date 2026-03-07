<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    /**
     * Display a listing of reviews.
     */
    public function index(Request $request): View
    {
        $query = Review::with(['user', 'product']);

        // Filter by approval status
        if ($request->filled('approved')) {
            $query->where('is_approved', $request->approved === 'yes');
        }

        // Filter by verified purchase
        if ($request->filled('verified')) {
            $query->where('is_verified_purchase', $request->verified === 'yes');
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($user) use ($search) {
                    $user->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })->orWhereHas('product', function ($product) use ($search) {
                    $product->where('name', 'like', "%{$search}%");
                })->orWhere('comment', 'like', "%{$search}%");
            });
        }

        $reviews = $query->latest()->paginate(20);

        return view('admin.reviews.index', compact('reviews'));
    }

    /**
     * Show the specified review.
     */
    public function show(Review $review): View
    {
        $review->load(['user', 'product']);

        return view('admin.reviews.show', compact('review'));
    }

    /**
     * Approve the specified review.
     */
    public function approve(Review $review): RedirectResponse
    {
        $review->update(['is_approved' => true]);

        return redirect()->back()
            ->with('success', 'Review approved successfully.');
    }

    /**
     * Reject the specified review.
     */
    public function reject(Review $review): RedirectResponse
    {
        $review->update(['is_approved' => false]);

        return redirect()->back()
            ->with('success', 'Review rejected successfully.');
    }

    /**
     * Remove the specified review.
     */
    public function destroy(Review $review): RedirectResponse
    {
        $review->delete();

        return redirect()->route('admin.reviews.index')
            ->with('success', 'Review deleted successfully.');
    }

    /**
     * Bulk approve reviews.
     */
    public function bulkApprove(Request $request): RedirectResponse
    {
        $request->validate([
            'reviews' => ['required', 'array'],
            'reviews.*' => ['exists:reviews,id'],
        ]);

        Review::whereIn('id', $request->reviews)
            ->update(['is_approved' => true]);

        return redirect()->back()
            ->with('success', count($request->reviews).' reviews approved successfully.');
    }
}
