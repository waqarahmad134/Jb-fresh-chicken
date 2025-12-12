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
                'name' => 'zinger piece with skin',
                'description' => 'Enjoy the bold, crispy goodness of our zinger piece with skin, marinated with signature spices and coated in a crunchy, golden breading. The skin adds an extra layer of flavorful crisp, while the inside stays tender, juicy, and perfectly cooked. Ideal for fried-chicken lovers, this zinger piece brings the perfect balance of heat, crunch, and mouth-watering freshness—great as a snack or part of any meal.',
                'price' => 9.99,
                'imageUrls' => [
                    'https://picsum.photos/id/1080/800/600',
                ],
                'category' => 'Chicken Items',
                'tags' => ['crispy', 'zinger'],
            ],
            [
                'name' => 'Whole leg zinger',
                'description' => 'A spicy and crispy whole leg zinger packed with bold flavors and an extra-crunchy coating. Marinated to the bone and fried to perfection, this juicy whole leg delivers a satisfying bite with every mouthful. Perfect for those who love deep, rich taste with plenty of crunch.',
                'price' => 12.99,
                'imageUrls' => [
                    'https://picsum.photos/id/1060/800/600',
                ],
                'category' => 'Chicken Items',
                'tags' => ['spicy', 'crispy'],
            ],
            [
                'name' => 'Bonless',
                'description' => 'Enjoy tender and juicy boneless chicken pieces, perfectly seasoned and cooked for maximum flavor. Soft, easy to eat, and full of taste, these boneless cuts are ideal for quick meals, snacks, or recipes that demand premium-quality chicken without the hassle of bones.',
                'price' => 10.49,
                'imageUrls' => [
                    'https://picsum.photos/id/1059/800/600',
                ],
                'category' => 'Chicken Items',
                'tags' => ['boneless', 'tender'],
            ],
            [
                'name' => 'Skinless Bonless',
                'description' => 'Our skinless boneless chicken offers pure, lean, high-quality meat with zero skin and bones. Clean, healthy, and easy to cook, it is perfect for grilling, frying, baking, or adding to everyday meals. A top choice for fitness lovers and families seeking fresh, tender chicken.',
                'price' => 11.99,
                'imageUrls' => [
                    'https://picsum.photos/id/1058/800/600',
                ],
                'category' => 'Chicken Items',
                'tags' => ['healthy', 'lean'],
            ],
            [
                'name' => 'Drumstick',
                'description' => 'A juicy, flavorful drumstick seasoned with rich spices and cooked until perfectly tender. With a naturally succulent texture, this drumstick delivers a satisfying mix of tenderness and delicious taste—perfect for frying, grilling, or oven roasting.',
                'price' => 8.99,
                'imageUrls' => [
                    'https://picsum.photos/id/1062/800/600',
                ],
                'category' => 'Chicken Items',
                'tags' => ['classic', 'drumstick'],
            ],
            [
                'name' => 'Fresh Chicken',
                'description' => 'Premium-quality fresh chicken, cleaned and prepared for all types of cooking. Naturally tender, hormone-free, and full of flavor, it’s ideal for curries, roasts, BBQ, and everyday meals. Freshness you can taste in every bite.',
                'price' => 7.99,
                'imageUrls' => [
                    'https://picsum.photos/id/312/800/600',
                ],
                'category' => 'Raw Items',
                'tags' => ['fresh', 'raw'],
            ],
            [
                'name' => 'Topping',
                'description' => 'Add extra flavor to your dishes with our premium topping, crafted to enhance pizzas, burgers, fries, sandwiches, and more. Rich, tasty, and full of fresh ingredients—perfect for boosting the aroma and taste of any meal.',
                'price' => 3.99,
                'imageUrls' => [
                    'https://picsum.photos/id/202/800/600',
                ],
                'category' => 'Add-ons',
                'tags' => ['extra', 'flavor'],
            ],
            [
                'name' => 'Hot Wings',
                'description' => 'Crispy, fiery, and full of bold flavor—our hot wings are marinated in spicy seasonings and fried to golden perfection. Each bite delivers crunchy texture on the outside and juicy tenderness inside. Perfect for spice lovers and snack cravings.',
                'price' => 14.99,
                'imageUrls' => [
                    'https://picsum.photos/id/103/800/600',
                ],
                'category' => 'Wings',
                'tags' => ['hot', 'spicy'],
            ],
            [
                'name' => 'Sajji',
                'description' => 'Authentic Sajji prepared with traditional spices and slow-cooked to perfection. Tender, smoky, and rich in aroma, this Sajji offers true regional flavor with every bite. Ideal for gatherings, events, or a flavorful family meal.',
                'price' => 18.99,
                'imageUrls' => [
                    'https://picsum.photos/id/211/800/600',
                ],
                'category' => 'Special Items',
                'tags' => ['traditional', 'smoky'],
            ],
            [
                'name' => 'Chargha',
                'description' => 'A whole Chargha marinated in classic Lahori spices and steamed or fried until juicy and flavorful. Crispy on the outside and tender inside, this Chargha brings authentic street-style taste right to your plate.',
                'price' => 22.99,
                'imageUrls' => [
                    'https://picsum.photos/id/431/800/600',
                ],
                'category' => 'Special Items',
                'tags' => ['lahori', 'crispy'],
            ],
            [
                'name' => 'Nine Cut',
                'description' => 'Fresh and perfectly portioned nine cut chicken, offering all essential pieces for balanced meals. Includes breast, thigh, wings, and more—ideal for families, meal prep, BBQs, or everyday home cooking.',
                'price' => 16.49,
                'imageUrls' => [
                    'https://picsum.photos/id/122/800/600',
                ],
                'category' => 'Raw Items',
                'tags' => ['raw', 'family'],
            ],
            [
                'name' => 'Qeema',
                'description' => 'Fine-quality qeema made from fresh chicken, offering soft texture and rich flavor. Perfect for kebabs, burgers, curries, fillings, and homemade recipes. Clean, fresh, and ready to cook for any dish.',
                'price' => 13.99,
                'imageUrls' => [
                    'https://picsum.photos/id/220/800/600',
                ],
                'category' => 'Raw Items',
                'tags' => ['minced', 'fresh'],
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

            foreach ($productData['imageUrls'] as $imageIndex => $imageUrl) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_url' => $imageUrl,
                    'alt_text' => $productData['name'],
                    'sort_order' => $imageIndex,
                    'is_primary' => $imageIndex === 0,
                ]);
            }

            foreach ($productData['tags'] as $tagName) {
                $tag = Tag::where('slug', Str::slug($tagName))->first();
                if ($tag) {
                    $product->tags()->attach($tag->id);
                }
            }
        }
    }
}
