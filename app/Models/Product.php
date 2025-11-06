<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'compare_at_price',
        'sku',
        'stock_quantity',
        'track_inventory',
        'category_id',
        'is_active',
        'is_featured',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'compare_at_price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'track_inventory' => 'boolean',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected $appends = ['image_url', 'image_urls', 'average_rating', 'reviews_count'];

    /**
     * Get the category that owns the product.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the images for the product.
     */
    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    /**
     * Get the primary image URL.
     */
    public function getImageUrlAttribute()
    {
        $primaryImage = $this->images()->where('is_primary', true)->first();
        return $primaryImage ? $primaryImage->image_url : ($this->images()->first()->image_url ?? null);
    }

    /**
     * Get all image URLs as array.
     */
    public function getImageUrlsAttribute()
    {
        return $this->images->pluck('image_url')->toArray();
    }

    /**
     * The tags that belong to the product.
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'product_tag')
            ->withTimestamps();
    }

    /**
     * Get the reviews for the product.
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get the average rating for the product.
     */
    public function averageRating()
    {
        return $this->reviews()->where('is_approved', true)->avg('rating');
    }

    /**
     * Get the average rating attribute (accessor).
     */
    public function getAverageRatingAttribute()
    {
        // Use eager-loaded reviews if available, otherwise query
        if ($this->relationLoaded('reviews')) {
            $approvedReviews = $this->reviews->where('is_approved', true);
            if ($approvedReviews->isEmpty()) {
                return 0;
            }
            $avg = $approvedReviews->avg('rating');
            return $avg ? round($avg, 2) : 0;
        }
        
        $avg = $this->reviews()->where('is_approved', true)->avg('rating');
        return $avg ? round($avg, 2) : 0;
    }

    /**
     * Get the reviews count attribute (accessor).
     */
    public function getReviewsCountAttribute()
    {
        // Use eager-loaded reviews if available, otherwise query
        if ($this->relationLoaded('reviews')) {
            return $this->reviews->where('is_approved', true)->count();
        }
        
        return $this->reviews()->where('is_approved', true)->count();
    }

    /**
     * Get the wishlists that include the product.
     */
    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    /**
     * Get the cart items for the product.
     */
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }
}

