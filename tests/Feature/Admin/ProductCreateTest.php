<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductCreateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        // Create admin role and user
        $this->setupRoles();
    }

    protected function setupRoles(): void
    {
        \Spatie\Permission\Models\Role::create(['name' => 'admin', 'guard_name' => 'web']);
    }

    public function test_product_form_loads(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get(route('admin.products.create'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.products.create');
    }

    public function test_can_create_product_with_valid_data(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $category = Category::factory()->create();

        $response = $this->actingAs($admin)->post(route('admin.products.store'), [
            'name' => 'Test Product',
            'sku' => 'TEST-SKU-001',
            'description' => 'This is a test product description',
            'short_description' => 'Short description',
            'price' => 99.99,
            'stock_quantity' => 10,
            'manage_stock' => true,
            'is_active' => true,
            'categories' => [$category->id],
        ]);

        $this->assertDatabaseHas('products', [
            'name' => 'Test Product',
            'sku' => 'TEST-SKU-001',
            'price' => 99.99,
        ]);

        $response->assertRedirect(route('admin.products.index'));
    }

    public function test_product_creation_requires_name(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->post(route('admin.products.store'), [
            'sku' => 'TEST-SKU-001',
            'description' => 'This is a test product description',
            'price' => 99.99,
            'stock_quantity' => 10,
            'manage_stock' => true,
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_product_creation_with_image(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $category = Category::factory()->create();

        $file = UploadedFile::fake()->image('product.jpg', 100, 100)->size(512);

        $response = $this->actingAs($admin)->post(route('admin.products.store'), [
            'name' => 'Test Product with Image',
            'sku' => 'TEST-SKU-002',
            'description' => 'This is a test product with image',
            'short_description' => 'Short description',
            'price' => 49.99,
            'stock_quantity' => 5,
            'manage_stock' => true,
            'is_active' => true,
            'categories' => [$category->id],
            'images' => [$file],
        ]);

        $this->assertDatabaseHas('products', [
            'name' => 'Test Product with Image',
            'sku' => 'TEST-SKU-002',
        ]);

        $response->assertRedirect(route('admin.products.index'));
    }

    public function test_product_creation_rejects_oversized_image(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $category = Category::factory()->create();

        // Create a fake file that's over 2MB
        $file = UploadedFile::fake()->image('large.jpg')->size(3000); // 3000 KB = 3 MB

        $response = $this->actingAs($admin)->post(route('admin.products.store'), [
            'name' => 'Test Product',
            'sku' => 'TEST-SKU-003',
            'description' => 'This is a test product description',
            'price' => 99.99,
            'stock_quantity' => 10,
            'manage_stock' => true,
            'categories' => [$category->id],
            'images' => [$file],
        ]);

        $response->assertSessionHasErrors('images.*');
    }

    public function test_stock_quantity_not_required_when_manage_stock_unchecked(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $category = Category::factory()->create();

        $response = $this->actingAs($admin)->post(route('admin.products.store'), [
            'name' => 'Test Product',
            'sku' => 'TEST-SKU-004',
            'description' => 'This is a test product description',
            'price' => 99.99,
            'manage_stock' => false,
            'is_active' => true,
            'categories' => [$category->id],
        ]);

        $this->assertDatabaseHas('products', [
            'sku' => 'TEST-SKU-004',
            'manage_stock' => false,
        ]);

        $response->assertRedirect(route('admin.products.index'));
    }
}
