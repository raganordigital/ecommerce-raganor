<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $categories = Category::withCount('products')
            ->orderBy('name')
            ->limit(12)
            ->get();

        $featuredProducts = Product::with(['primaryImage', 'reviews'])
            ->where('is_active', true)
            ->where('is_featured', true)
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->latest()
            ->limit(8)
            ->get();

        $latestProducts = Product::with(['primaryImage', 'reviews'])
            ->where('is_active', true)
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->latest()
            ->limit(8)
            ->get();

        $totalProducts = Product::where('is_active', true)->count();

        return view('public.home.index', compact(
            'categories',
            'featuredProducts',
            'latestProducts',
            'totalProducts',
        ));
    }
}