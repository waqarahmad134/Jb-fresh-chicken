<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tag) {
            if (empty($tag->slug)) {
                $tag->slug = Str::slug($tag->name);
            }
        });

        static::updating(function ($tag) {
            if ($tag->isDirty('name') && empty($tag->slug)) {
                $tag->slug = Str::slug($tag->name);
            }
        });
    }

    /**
     * The products that belong to the tag.
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_tag')
            ->withTimestamps();
    }

    /**
     * The blog posts that belong to the tag.
     */
    public function blogPosts()
    {
        return $this->belongsToMany(BlogPost::class, 'blog_post_tag')
            ->withTimestamps();
    }

    /**
     * Scope a query to filter by name.
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('slug', 'like', "%{$search}%");
        });
    }

    /**
     * Scope a query to get tags with products count.
     */
    public function scopeWithProductsCount($query)
    {
        return $query->withCount('products');
    }

    /**
     * Scope a query to get tags with blog posts count.
     */
    public function scopeWithBlogPostsCount($query)
    {
        return $query->withCount('blogPosts');
    }

    /**
     * Get the total usage count (products + blog posts).
     */
    public function getUsageCountAttribute(): int
    {
        return $this->products_count + $this->blog_posts_count;
    }
}

