<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Services\ImageUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class ProductController extends Controller
{
    protected ImageUploadService $imageService;

    public function __construct(ImageUploadService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Display a listing of the products.
     */
    public function index(Request $request): View
    {
        $query = Product::with(['categories', 'primaryImage']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('categories.id', $request->get('category'));
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            switch ($request->get('status')) {
                case 'active':
                    $query->where('is_active', true);
                    break;
                case 'inactive':
                    $query->where('is_active', false);
                    break;
                case 'featured':
                    $query->where('is_featured', true);
                    break;
                case 'on_sale':
                    $query->onSale();
                    break;
            }
        }

        // Sort
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $products = $query->paginate(15);
        $categories = Category::active()->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create(): View
    {
        $categories = Category::active()->orderBy('name')->get();

        return view('admin.products.create', compact('categories'));
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(ProductRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();

            // Prepare data – convert checkbox to boolean
            $data = $request->except(['categories', 'images']);
            $data['allow_cod'] = $request->has('allow_cod');

            $product = Product::create($data);

            // Attach categories
            if ($request->has('categories')) {
                $product->categories()->attach($request->categories);
            }

            // Handle image uploads
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $image) {
                    $uploadedImages = $this->imageService->uploadProductImage(
                        $image,
                        $product->sku,
                        $index
                    );

                    ProductImage::create([
                        'product_id' => $product->id,
                        'path' => $uploadedImages['original'],
                        'thumbnail_path' => $uploadedImages['thumbnail'],
                        'sort_order' => $index,
                        'is_primary' => $index === 0,
                    ]);
                }
            }

            DB::commit();

            return redirect()
                ->route('admin.products.index')
                ->with('success', "Product '{$product->name}' created successfully.");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create product', ['error' => $e->getMessage()]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create product. Please try again.');
        }
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product): View
{
    if (! $product->is_active) {
        abort(404);
    }

    $product->increment('views_count');

    $product->load([
        'categories',
        'images',
        'reviews' => function ($q) {
            $q->with('user')->where('is_approved', true)->latest();
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

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product): View
    {
        $categories = Category::active()->orderBy('name')->get();
        $product->load('categories', 'images');

        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(ProductRequest $request, Product $product): RedirectResponse
    {
        try {
            DB::beginTransaction();

            // Update product – convert checkbox to boolean
            $data = $request->except(['categories', 'images']);
            $data['allow_cod'] = $request->has('allow_cod');
            $product->update($data);

            // Sync categories
            if ($request->has('categories')) {
                $product->categories()->sync($request->categories);
            } else {
                $product->categories()->detach();
            }

            // Delete images marked for removal
            if ($request->has('delete_images')) {
                $imagesToDelete = ProductImage::whereIn('id', $request->delete_images)
                    ->where('product_id', $product->id)
                    ->get();

                foreach ($imagesToDelete as $image) {
                    $this->imageService->deleteImages([
                        $image->path,
                        $image->thumbnail_path,
                    ]);

                    if ($image->is_primary) {
                        // Set another image as primary if available
                        $newPrimary = $product->images()
                            ->where('id', '!=', $image->id)
                            ->orderBy('sort_order')
                            ->first();
                        if ($newPrimary) {
                            $newPrimary->update(['is_primary' => true]);
                        }
                    }

                    $image->delete();
                }
            }

            // Handle new image uploads
            if ($request->hasFile('images')) {
                $maxSortOrder = $product->images()->max('sort_order') ?? -1;

                foreach ($request->file('images') as $index => $image) {
                    $uploadedImages = $this->imageService->uploadProductImage(
                        $image,
                        $product->sku,
                        $maxSortOrder + $index + 1
                    );

                    ProductImage::create([
                        'product_id' => $product->id,
                        'path' => $uploadedImages['original'],
                        'thumbnail_path' => $uploadedImages['thumbnail'],
                        'sort_order' => $maxSortOrder + $index + 1,
                        'is_primary' => !$product->primaryImage()->exists() && $index === 0,
                    ]);
                }
            }

            DB::commit();

            return redirect()
                ->route('admin.products.index')
                ->with('success', "Product '{$product->name}' updated successfully.");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update product', ['error' => $e->getMessage()]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update product. Please try again.');
        }
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Product $product): RedirectResponse
    {
        try {
            DB::beginTransaction();

            // Delete associated images from storage
            foreach ($product->images as $image) {
                $this->imageService->deleteImages([
                    $image->path,
                    $image->thumbnail_path,
                ]);
                $image->delete();
            }

            // Detach categories
            $product->categories()->detach();

            // Delete product
            $product->delete();

            DB::commit();

            return redirect()
                ->route('admin.products.index')
                ->with('success', "Product '{$product->name}' deleted successfully.");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete product', ['error' => $e->getMessage()]);

            return redirect()
                ->back()
                ->with('error', 'Failed to delete product. Please try again.');
        }
    }

    /**
     * Toggle product status.
     */
    public function toggleStatus(Product $product): RedirectResponse
    {
        try {
            $product->update([
                'is_active' => ! $product->is_active,
            ]);

            $status = $product->is_active ? 'activated' : 'deactivated';

            return redirect()
                ->back()
                ->with('success', "Product '{$product->name}' {$status} successfully.");
        } catch (\Exception $e) {
            Log::error('Failed to toggle product status', ['error' => $e->getMessage()]);

            return redirect()
                ->back()
                ->with('error', 'Failed to update product status.');
        }
    }

    /**
     * Toggle featured status.
     */
    public function toggleFeatured(Product $product): RedirectResponse
    {
        try {
            $product->update([
                'is_featured' => ! $product->is_featured,
            ]);

            $status = $product->is_featured ? 'added to' : 'removed from';

            return redirect()
                ->back()
                ->with('success', "Product '{$product->name}' {$status} featured successfully.");
        } catch (\Exception $e) {
            Log::error('Failed to toggle featured status', ['error' => $e->getMessage()]);

            return redirect()
                ->back()
                ->with('error', 'Failed to update featured status.');
        }
    }

    /**
     * Delete a specific product image.
     */
    public function deleteImage(Product $product, ProductImage $image): \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
    {
        if ($image->product_id !== $product->id) {
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Image does not belong to this product.'], 404);
            }
            abort(404);
        }

        try {
            DB::beginTransaction();

            $this->imageService->deleteImages([
                $image->path,
                $image->thumbnail_path,
            ]);

            if ($image->is_primary) {
                $newPrimary = $product->images()
                    ->where('id', '!=', $image->id)
                    ->orderBy('sort_order')
                    ->first();

                if ($newPrimary) {
                    $newPrimary->update(['is_primary' => true]);
                }
            }

            $image->delete();

            DB::commit();

            if (request()->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Image deleted.']);
            }

            return redirect()
                ->back()
                ->with('success', 'Image deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete image', ['error' => $e->getMessage()]);

            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Failed to delete image.'], 500);
            }

            return redirect()
                ->back()
                ->with('error', 'Failed to delete image.');
        }
    }

    /**
     * Reorder images.
     */
    public function reorderImages(Request $request, Product $product): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'images' => 'required|array',
            'images.*' => 'exists:product_images,id',
        ]);

        try {
            DB::beginTransaction();

            foreach ($request->images as $index => $imageId) {
                ProductImage::where('id', $imageId)
                    ->where('product_id', $product->id)
                    ->update(['sort_order' => $index]);
            }

            DB::commit();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to reorder images', ['error' => $e->getMessage()]);

            return response()->json(['success' => false], 500);
        }
    }
}
