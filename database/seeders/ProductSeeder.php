<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $productsData = [
            [
                'name' => 'Classic Crispy Nuggets',
                'description' => '12 pieces of our signature golden-brown, all-white meat chicken nuggets.',
                'price' => 9.99,
                'imageUrls' => [
                    'https://picsum.photos/id/1080/800/600',
                    'https://picsum.photos/id/1074/800/600',
                    'https://picsum.photos/id/1069/800/600',
                    'https://picsum.photos/id/1062/800/600',
                ],
                'category' => 'Nuggets & Poppers',
                'tags' => ['classic', 'shareable'],
            ],
            [
                'name' => 'Spicy Chicken Strips',
                'description' => '5 tender, juicy chicken strips with a kick of our secret spicy seasoning.',
                'price' => 12.99,
                'imageUrls' => [
                    'https://picsum.photos/id/1060/800/600',
                    'https://picsum.photos/id/1059/800/600',
                    'https://picsum.photos/id/1058/800/600',
                ],
                'category' => 'Strips & Sandwiches',
                'tags' => ['spicy', 'classic'],
            ],
            [
                'name' => 'Family Feast Bucket',
                'description' => 'A bucket full of 30 crispy nuggets, perfect for sharing with family and friends.',
                'price' => 24.99,
                'imageUrls' => [
                    'https://picsum.photos/id/219/800/600',
                    'https://picsum.photos/id/220/800/600',
                    'https://picsum.photos/id/221/800/600',
                ],
                'category' => 'Family Meals',
                'tags' => ['shareable', 'best-value'],
            ],
            [
                'name' => 'BBQ Glazed Wings',
                'description' => '10 succulent chicken wings tossed in a sweet and smoky BBQ sauce.',
                'price' => 14.99,
                'imageUrls' => ['https://picsum.photos/id/1025/500/500'],
                'category' => 'Wings',
                'tags' => ['bbq', 'shareable'],
            ],
            [
                'name' => 'Chicken Poppers Share Box',
                'description' => 'Bite-sized chicken poppers that are big on flavor. Great for dipping!',
                'price' => 8.49,
                'imageUrls' => ['https://picsum.photos/id/312/500/500'],
                'category' => 'Nuggets & Poppers',
                'tags' => ['shareable', 'classic'],
            ],
            [
                'name' => 'The Ultimate Strip Sandwich',
                'description' => 'Three crispy chicken strips on a toasted brioche bun with lettuce and mayo.',
                'price' => 11.99,
                'imageUrls' => ['https://picsum.photos/id/202/500/500'],
                'category' => 'Strips & Sandwiches',
                'tags' => ['classic', 'meal'],
            ],
            [
                'name' => 'Buffalo Wings',
                'description' => '10 fiery buffalo wings, served with a side of cool ranch dressing. Not for the faint of heart!',
                'price' => 15.99,
                'imageUrls' => ['https://picsum.photos/id/103/500/500'],
                'category' => 'Wings',
                'tags' => ['spicy', 'shareable', 'hot'],
            ],
            [
                'name' => 'Crispy Chicken Sandwich',
                'description' => 'A perfectly fried chicken breast on a buttered brioche bun with pickles and our signature sauce.',
                'price' => 10.99,
                'imageUrls' => ['https://picsum.photos/id/211/500/500'],
                'category' => 'Strips & Sandwiches',
                'tags' => ['classic', 'meal', 'best-seller'],
            ],
            [
                'name' => 'Honey Garlic Poppers',
                'description' => 'Our classic chicken poppers tossed in a sweet and savory honey garlic sauce.',
                'price' => 9.49,
                'imageUrls' => ['https://picsum.photos/id/431/500/500'],
                'category' => 'Nuggets & Poppers',
                'tags' => ['sweet', 'shareable'],
            ],
            [
                'name' => 'Mega Share Platter',
                'description' => 'The ultimate party starter: 15 nuggets, 10 strips, and a mountain of poppers.',
                'price' => 39.99,
                'imageUrls' => ['https://picsum.photos/id/122/500/500'],
                'category' => 'Family Meals',
                'tags' => ['shareable', 'best-value', 'party'],
            ],
        ];

        foreach ($productsData as $index => $productData) {
            $category = Category::where('name', $productData['category'])->first();

            $product = Product::create([
                'name' => $productData['name'],
                'slug' => Str::slug($productData['name']),
                'description' => $productData['description'],
                'price' => $productData['price'],
                'sku' => 'SKU-' . str_pad($index + 1, 5, '0', STR_PAD_LEFT),
                'stock_quantity' => 100,
                'track_inventory' => true,
                'category_id' => $category ? $category->id : null,
                'is_active' => true,
                'is_featured' => in_array('best-seller', $productData['tags']),
                'sort_order' => $index,
            ]);

            // Add images
            foreach ($productData['imageUrls'] as $imageIndex => $imageUrl) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_url' => $imageUrl,
                    'alt_text' => $productData['name'],
                    'sort_order' => $imageIndex,
                    'is_primary' => $imageIndex === 0,
                ]);
            }

            // Attach tags
            foreach ($productData['tags'] as $tagName) {
                $tag = Tag::where('slug', Str::slug($tagName))->first();
                if ($tag) {
                    $product->tags()->attach($tag->id);
                }
            }
        }
    }
}

