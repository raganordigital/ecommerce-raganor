<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Display a listing of products.
     */
    public function index(Request $request): View
    {
        $query = Product::with(['categories', 'primaryImage'])
            ->where('is_active', true);

        // Category filter
        if ($request->filled('category')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('categories.slug', $request->get('category'));
            });
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Sort
        switch ($request->get('sort', 'newest')) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            default: // newest
                $query->latest();
                break;
        }

        $products = $query->paginate(12);

        $categories = Category::whereNull('parent_id')
            ->with('children')
            ->where('is_active', true)
            ->get();

        return view('public.products.index', compact('products', 'categories'));
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product): View
    {
        if (! $product->is_active) {
            abort(404);
        }

        // Increment view count
        $product->increment('views_count');

        // Load relationships
        $product->load(['categories', 'images']);

        // Get related products (same categories)
        $relatedProducts = Product::with('primaryImage')
            ->where('is_active', true)
            ->where('id', '!=', $product->id)
            ->whereHas('categories', function ($query) use ($product) {
                $query->whereIn('categories.id', $product->categories->pluck('id'));
            })
            ->limit(4)
            ->get();

        return view('public.products.show', compact('product', 'relatedProducts'));
    }
}
