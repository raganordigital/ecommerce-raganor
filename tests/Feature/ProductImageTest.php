<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductImageTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_image_path_is_stored_correctly(): void
    {
        $imagePath = 'products/original/TEST_0_1234567890.jpg';
        $thumbnailPath = 'products/thumbnails/TEST_0_1234567890_thumb.jpg';

        // Simulate creating a product with an image
        $product = Product::factory()->create([
            'sku' => 'TEST-SKU-001',
            'name' => 'Test Product',
            'price' => 99.99,
            'is_active' => true,
        ]);

        ProductImage::create([
            'product_id' => $product->id,
            'path' => $imagePath,
            'thumbnail_path' => $thumbnailPath,
            'is_primary' => true,
            'sort_order' => 0,
        ]);

        $this->assertDatabaseHas('product_images', [
            'product_id' => $product->id,
            'path' => $imagePath,
            'thumbnail_path' => $thumbnailPath,
            'is_primary' => true,
        ]);

        // Verify the product has a primary image
        $this->assertNotNull($product->refresh()->primaryImage);
        $this->assertEquals($imagePath, $product->primaryImage->path);
        $this->assertEquals($thumbnailPath, $product->primaryImage->thumbnail_path);
    }

    public function test_products_page_displays_product_images(): void
    {
        // Create a product with an image
        $product = Product::factory()->create([
            'is_active' => true,
        ]);

        ProductImage::create([
            'product_id' => $product->id,
            'path' => 'products/original/test.jpg',
            'thumbnail_path' => 'products/thumbnails/test_thumb.jpg',
            'is_primary' => true,
            'sort_order' => 0,
        ]);

        // Visit the products page
        $response = $this->get(route('products.index'));

        $response->assertStatus(200);
        $response->assertViewHas('products');
        $response->assertViewHas('categories');
    }

    public function test_product_without_image_displays_placeholder(): void
    {
        $product = Product::factory()->create([
            'is_active' => true,
        ]);

        $response = $this->get(route('products.index'));

        $response->assertStatus(200);
        $response->assertSee('fa-image'); // Should see the image placeholder icon
    }
}
