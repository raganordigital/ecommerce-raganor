<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductRequest;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use App\Services\ImageUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    /**
     * @var ImageUploadService
     */
    protected ImageUploadService $imageService;

    /**
     * Constructor
     *
     * @param ImageUploadService $imageService
     */
    public function __construct(ImageUploadService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Display a listing of the products.
     *
     * @param Request $request
     * @return View
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
                case 'low_stock':
                    $query->where('manage_stock', true)
                          ->where('stock_quantity', '<', 10)
                          ->where('stock_quantity', '>', 0);
                    break;
                case 'out_of_stock':
                    $query->where('manage_stock', true)
                          ->where('stock_quantity', 0);
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
     *
     * @return View
     */
    public function create(): View
    {
        $categories = Category::active()->orderBy('name')->get();
        return view('admin.products.create', compact('categories'));
    }

    /**
     * Store a newly created product in storage.
     *
     * @param ProductRequest $request
     * @return RedirectResponse
     */
    public function store(ProductRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();
            
            // Create product
            $product = Product::create($request->except(['categories', 'images']));
            
            // Attach categories
            if ($request->has('categories')) {
                $product->categories()->attach($request->categories);
            }
            
            // Handle image uploads
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $image) {
                    try {
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
                            'is_primary' => $index === 0, // First image is primary
                        ]);
                        
                    } catch (\Exception $e) {
                        Log::error('Failed to upload product image', [
                            'product_id' => $product->id,
                            'error' => $e->getMessage()
                        ]);
                        throw $e;
                    }
                }
            }
            
            DB::commit();
            
            return redirect()
                ->route('admin.products.index')
                ->with('success', "Product '{$product->name}' created successfully.");
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create product', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create product. Please try again.');
        }
    }

    /**
     * Display the specified product.
     *
     * @param Product $product
     * @return View
     */
    public function show(Product $product): View
    {
        $product->load(['categories', 'images', 'orderItems' => function ($query) {
            $query->latest()->limit(10);
        }]);
        
        return view('admin.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified product.
     *
     * @param Product $product
     * @return View
     */
    public function edit(Product $product): View
    {
        $categories = Category::active()->orderBy('name')->get();
        $product->load('categories', 'images');
        
        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified product in storage.
     *
     * @param ProductRequest $request
     * @param Product $product
     * @return RedirectResponse
     */
    public function update(ProductRequest $request, Product $product): RedirectResponse
    {
        try {
            DB::beginTransaction();
            
            // Update product
            $product->update($request->except(['categories', 'images']));
            
            // Sync categories
            if ($request->has('categories')) {
                $product->categories()->sync($request->categories);
            } else {
                $product->categories()->detach();
            }
            
            // Handle new image uploads
            if ($request->hasFile('images')) {
                // Get current max sort order
                $maxSortOrder = $product->images()->max('sort_order') ?? -1;
                
                foreach ($request->file('images') as $index => $image) {
                    try {
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
                        
                    } catch (\Exception $e) {
                        Log::error('Failed to upload product image during update', [
                            'product_id' => $product->id,
                            'error' => $e->getMessage()
                        ]);
                        throw $e;
                    }
                }
            }
            
            DB::commit();
            
            return redirect()
                ->route('admin.products.index')
                ->with('success', "Product '{$product->name}' updated successfully.");
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update product', [
                'product_id' => $product->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update product. Please try again.');
        }
    }

    /**
     * Remove the specified product from storage.
     *
     * @param Product $product
     * @return RedirectResponse
     */
    public function destroy(Product $product): RedirectResponse
    {
        try {
            DB::beginTransaction();
            
            // Delete associated images from storage
            foreach ($product->images as $image) {
                $this->imageService->deleteImages([
                    $image->path,
                    $image->thumbnail_path
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
            Log::error('Failed to delete product', [
                'product_id' => $product->id,
                'error' => $e->getMessage()
            ]);
            
            return redirect()
                ->back()
                ->with('error', 'Failed to delete product. Please try again.');
        }
    }

    /**
     * Delete a specific product image.
     *
     * @param Request $request
     * @param Product $product
     * @param ProductImage $image
     * @return RedirectResponse
     */
    public function deleteImage(Request $request, Product $product, ProductImage $image): RedirectResponse
    {
        // Verify image belongs to product
        if ($image->product_id !== $product->id) {
            abort(404);
        }
        
        try {
            DB::beginTransaction();
            
            // Delete files from storage
            $this->imageService->deleteImages([
                $image->path,
                $image->thumbnail_path
            ]);
            
            // If this was primary image, assign new primary
            if ($image->is_primary) {
                $newPrimary = $product->images()
                    ->where('id', '!=', $image->id)
                    ->orderBy('sort_order')
                    ->first();
                    
                if ($newPrimary) {
                    $newPrimary->update(['is_primary' => true]);
                }
            }
            
            // Delete database record
            $image->delete();
            
            DB::commit();
            
            return redirect()
                ->back()
                ->with('success', 'Image deleted successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete product image', [
                'image_id' => $image->id,
                'error' => $e->getMessage()
            ]);
            
            return redirect()
                ->back()
                ->with('error', 'Failed to delete image. Please try again.');
        }
    }

    /**
     * Update image sorting order.
     *
     * @param Request $request
     * @param Product $product
     * @return RedirectResponse
     */
    public function reorderImages(Request $request, Product $product): RedirectResponse
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
            Log::error('Failed to reorder images', [
                'product_id' => $product->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json(['success' => false], 500);
        }
    }

    /**
     * Toggle product status.
     *
     * @param Product $product
     * @return RedirectResponse
     */
    public function toggleStatus(Product $product): RedirectResponse
    {
        try {
            $product->update([
                'is_active' => !$product->is_active
            ]);
            
            $status = $product->is_active ? 'activated' : 'deactivated';
            
            return redirect()
                ->back()
                ->with('success', "Product '{$product->name}' {$status} successfully.");
                
        } catch (\Exception $e) {
            Log::error('Failed to toggle product status', [
                'product_id' => $product->id,
                'error' => $e->getMessage()
            ]);
            
            return redirect()
                ->back()
                ->with('error', 'Failed to update product status.');
        }
    }

    /**
     * Toggle featured status.
     *
     * @param Product $product
     * @return RedirectResponse
     */
    public function toggleFeatured(Product $product): RedirectResponse
    {
        try {
            $product->update([
                'is_featured' => !$product->is_featured
            ]);
            
            $status = $product->is_featured ? 'added to' : 'removed from';
            
            return redirect()
                ->back()
                ->with('success', "Product '{$product->name}' {$status} featured successfully.");
                
        } catch (\Exception $e) {
            Log::error('Failed to toggle featured status', [
                'product_id' => $product->id,
                'error' => $e->getMessage()
            ]);
            
            return redirect()
                ->back()
                ->with('error', 'Failed to update featured status.');
        }
    }
}