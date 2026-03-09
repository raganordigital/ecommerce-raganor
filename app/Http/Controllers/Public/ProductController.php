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
    public function index(Request $request): View
    {
        $query = Product::with(['categories', 'primaryImage'])
            ->where('is_active', true);

        if ($request->filled('category')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('categories.slug', $request->get('category'));
            });
        }

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

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
            default:
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

    public function show(Product $product): View
    {
        if (! $product->is_active) {
            abort(404);
        }

        $product->increment('views_count');

        // Load all needed relationships — reviews with user for display
        $product->load([
            'categories',
            'images',
            'reviews' => function ($q) {
                $q->with('user')->latest();
            },
        ]);

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