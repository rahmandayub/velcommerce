<?php

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property int|null $category_id
 * @property string $name
 * @property string $slug
 * @property string $description
 * @property string|null $short_description
 * @property string $price
 * @property string|null $compare_price
 * @property string|null $cost
 * @property string $sku
 * @property string|null $barcode
 * @property int $stock
 * @property bool $is_active
 * @property bool $is_featured
 * @property int|null $weight
 * @property array<string, mixed>|null $dimensions
 */
class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'short_description',
        'price',
        'compare_price',
        'cost',
        'sku',
        'barcode',
        'stock',
        'is_active',
        'is_featured',
        'weight',
        'dimensions',
        'meta_title',
        'meta_description',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'compare_price' => 'decimal:2',
            'cost' => 'decimal:2',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'dimensions' => 'array',
        ];
    }

    public function getSeoTitleAttribute(): string
    {
        $title = $this->meta_title ?: $this->name;

        return Str::limit(trim((string) $title), 60, '');
    }

    public function getSeoDescriptionAttribute(): string
    {
        $desc = $this->meta_description
            ?: $this->short_description
            ?: Str::limit(strip_tags((string) $this->description), 160, '');

        $desc = trim(preg_replace('/\s+/', ' ', (string) $desc) ?? '');

        return Str::limit($desc, 160, '');
    }

    protected static function booted(): void
    {
        static::creating(function (Product $product): void {
            if (empty($product->slug) && ! empty($product->name)) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    /**
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return HasMany<ProductVariant, $this>
     */
    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    /**
     * @return HasMany<ProductImage, $this>
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    /**
     * @return HasMany<Review, $this>
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * @return HasMany<Wishlist, $this>
     */
    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    /**
     * @return HasMany<CartItem, $this>
     */
    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * @return HasMany<OrderItem, $this>
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getTotalStockAttribute(): int
    {
        if ($this->variants()->exists()) {
            return (int) $this->variants()->sum('stock');
        }

        return (int) $this->stock;
    }

    /**
     * Average rating across approved reviews (0 when none).
     */
    public function getAverageRatingAttribute(): float
    {
        return round((float) $this->reviews()->approved()->avg('rating') ?? 0, 2);
    }

    /**
     * Number of approved reviews.
     */
    public function getApprovedReviewsCountAttribute(): int
    {
        return $this->reviews()->approved()->count();
    }

    /**
     * Scope products for the public catalog.
     *
     * @param  array{q?: string|null, category?: string|null, min_price?: mixed, max_price?: mixed, sort?: string|null}  $filters
     */
    public function scopeFiltered(Builder $query, array $filters): Builder
    {
        $query->where('is_active', true);

        if (! empty($filters['q'])) {
            $q = (string) $filters['q'];
            $query->where(function (Builder $inner) use ($q): void {
                $inner->where('name', 'like', "%{$q}%")
                    ->orWhere('sku', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            });
        }

        if (! empty($filters['category'])) {
            $slug = (string) $filters['category'];
            $category = Category::query()->where('slug', $slug)->first();

            if ($category) {
                $ids = Category::query()
                    ->where('id', $category->id)
                    ->orWhere('parent_id', $category->id)
                    ->pluck('id')
                    ->all();

                $query->whereIn('category_id', $ids);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        if (isset($filters['min_price']) && $filters['min_price'] !== '' && $filters['min_price'] !== null) {
            $query->where('price', '>=', (float) $filters['min_price']);
        }

        if (isset($filters['max_price']) && $filters['max_price'] !== '' && $filters['max_price'] !== null) {
            $query->where('price', '<=', (float) $filters['max_price']);
        }

        $sort = $filters['sort'] ?? 'latest';

        match ($sort) {
            'price_asc' => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            default => $query->latest('id'),
        };

        return $query;
    }
}
