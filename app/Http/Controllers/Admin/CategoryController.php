<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories.
     */
    public function index(Request $request): View
    {
        $query = Category::withCount('products')
            ->with('parent');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by parent
        if ($request->filled('parent')) {
            if ($request->get('parent') === 'none') {
                $query->whereNull('parent_id');
            } else {
                $query->where('parent_id', $request->get('parent'));
            }
        }

        $categories = $query->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(15);

        $parentCategories = Category::whereNull('parent_id')
            ->orderBy('name')
            ->get();

        return view('admin.categories.index', compact('categories', 'parentCategories'));
    }

    /**
     * Show form for creating new category.
     */
    public function create(): View
    {
        $categories = Category::whereNull('parent_id')
            ->orderBy('name')
            ->get();

        return view('admin.categories.create', compact('categories'));
    }

    /**
     * Store a newly created category.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('categories')],
            'description' => ['nullable', 'string'],
            'parent_id' => ['nullable', 'exists:categories,id'],
            'is_active' => ['boolean'],
            'sort_order' => ['integer'],
        ]);

        try {
            DB::beginTransaction();

            $category = Category::create($validated);

            DB::commit();

            return redirect()
                ->route('admin.categories.index')
                ->with('success', "Category '{$category->name}' created successfully.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create category', ['error' => $e->getMessage()]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create category. Please try again.');
        }
    }

    /**
     * Display the specified category.
     */
    public function show(Category $category): View
    {
        $category->load(['parent', 'children', 'products' => function ($query) {
            $query->latest()->limit(10);
        }]);

        return view('admin.categories.show', compact('category'));
    }

    /**
     * Show form for editing category.
     */
    public function edit(Category $category): View
    {
        $categories = Category::whereNull('parent_id')
            ->where('id', '!=', $category->id)
            ->orderBy('name')
            ->get();

        return view('admin.categories.edit', compact('category', 'categories'));
    }

    /**
     * Update the specified category.
     */
    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('categories')->ignore($category->id)],
            'description' => ['nullable', 'string'],
            'parent_id' => ['nullable', 'exists:categories,id', 'not_in:'.$category->id],
            'is_active' => ['boolean'],
            'sort_order' => ['integer'],
        ]);

        try {
            DB::beginTransaction();

            $category->update($validated);

            DB::commit();

            return redirect()
                ->route('admin.categories.index')
                ->with('success', "Category '{$category->name}' updated successfully.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update category', ['error' => $e->getMessage()]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update category. Please try again.');
        }
    }

    /**
     * Remove the specified category.
     */
    public function destroy(Category $category): RedirectResponse
    {
        try {
            DB::beginTransaction();

            // Check if category has products
            if ($category->products()->count() > 0) {
                return redirect()
                    ->back()
                    ->with('error', 'Cannot delete category that has products.');
            }

            // Reassign children to parent if any
            if ($category->children()->count() > 0) {
                $category->children()->update(['parent_id' => $category->parent_id]);
            }

            $category->delete();

            DB::commit();

            return redirect()
                ->route('admin.categories.index')
                ->with('success', "Category '{$category->name}' deleted successfully.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete category', ['error' => $e->getMessage()]);

            return redirect()
                ->back()
                ->with('error', 'Failed to delete category. Please try again.');
        }
    }
}
