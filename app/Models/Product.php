<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

/**
 * Class Product
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $sku
 * @property string $description
 * @property string|null $short_description
 * @property float $price
 * @property float|null $sale_price
 * @property \DateTime|null $sale_price_from
 * @property \DateTime|null $sale_price_to
 * @property int $stock_quantity
 * @property bool $manage_stock
 * @property bool $is_active
 * @property bool $is_featured
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property string|null $meta_keywords
 * @property int $views_count
 * @property int $sales_count
 * @property-read float $current_price
 * @property-read bool $on_sale
 * @property-read string $formatted_price
 */
class Product extends Model
{
    use HasFactory;
    use HasSlug;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'sku',
        'description',
        'short_description',
        'price',
        'sale_price',
        'sale_price_from',
        'sale_price_to',
        'stock_quantity',
        'manage_stock',
        'is_active',
        'is_featured',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'views_count',
        'sales_count',
        'allow_cod',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'sale_price_from' => 'datetime',
        'sale_price_to' => 'datetime',
        'stock_quantity' => 'integer',
        'manage_stock' => 'boolean',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'views_count' => 'integer',
        'sales_count' => 'integer',
        'allow_cod' => 'boolean',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<string>
     */
    protected $appends = [
        'current_price',
        'on_sale',
        'formatted_price',
    ];

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate();
    }

    /**
     * Get the categories for this product.
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_product')
            ->withTimestamps();
    }

    /**
     * Get the images for this product.
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    /**
     * Get the primary image for this product.
     */
    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    /**
     * Get the order items for this product.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the wishlists for this product.
     */
    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    /**
     * Get the current price (considering sale).
     */
    public function getCurrentPriceAttribute(): float
    {
        if (! $this->on_sale) {
            return (float) $this->price;
        }

        return (float) $this->sale_price;
    }

    /**
     * Check if product is on sale.
     */
    public function getOnSaleAttribute(): bool
    {
        if (! $this->sale_price) {
            return false;
        }

        $now = now();

        if ($this->sale_price_from && $this->sale_price_from > $now) {
            return false;
        }

        if ($this->sale_price_to && $this->sale_price_to < $now) {
            return false;
        }

        return true;
    }

    /**
     * Get formatted price.
     */
    public function getFormattedPriceAttribute(): string
    {
        return '$'.number_format($this->current_price, 2);
    }

    /**
     * Check if product is in stock.
     */
    public function inStock(int $quantity = 1): bool
    {
        if (! $this->manage_stock) {
            return true;
        }

        return $this->stock_quantity >= $quantity;
    }

    /**
     * Decrease stock quantity.
     */
    public function decreaseStock(int $quantity): bool
    {
        if (! $this->manage_stock) {
            return true;
        }

        if ($this->stock_quantity < $quantity) {
            return false;
        }

        $this->decrement('stock_quantity', $quantity);
        $this->increment('sales_count', $quantity);

        return true;
    }

    /**
     * Increase stock quantity.
     */
    public function increaseStock(int $quantity): void
    {
        if ($this->manage_stock) {
            $this->increment('stock_quantity', $quantity);
        }
    }

    /**
     * Scope a query to only active products.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only featured products.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to products on sale.
     */
    public function scopeOnSale($query)
    {
        return $query->whereNotNull('sale_price')
            ->where(function ($q) {
                $q->whereNull('sale_price_from')
                    ->orWhere('sale_price_from', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('sale_price_to')
                    ->orWhere('sale_price_to', '>=', now());
            });
    }

    /**
     * Scope a query to products in stock.
     */
    public function scopeInStock($query)
    {
        return $query->where(function ($q) {
            $q->where('manage_stock', false)
                ->orWhere('stock_quantity', '>', 0);
        });
    }

    /**
     * Increment view count.
     */
    public function recordView(): void
    {
        $this->increment('views_count');
    }

    /**
     * Get the reviews for this product.
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get approved reviews for this product.
     */
    public function approvedReviews()
    {
        return $this->hasMany(Review::class)->where('is_approved', true);
    }

    /**
     * Get average rating for the product.
     */
    public function getAverageRatingAttribute(): float
    {
        return (float) $this->approvedReviews()->avg('rating') ?? 0;
    }

    /**
     * Get total reviews count.
     */
    public function getReviewsCountAttribute(): int
    {
        return $this->approvedReviews()->count();
    }

    /**
     * Get rating distribution.
     */
    public function getRatingDistributionAttribute(): array
    {
        $distribution = [];

        for ($i = 1; $i <= 5; $i++) {
            $distribution[$i] = $this->approvedReviews()
                ->where('rating', $i)
                ->count();
        }

        return $distribution;
    }

    
}
