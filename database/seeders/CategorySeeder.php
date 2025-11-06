<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Nuggets & Poppers',
            'Strips & Sandwiches',
            'Family Meals',
            'Wings',
        ];

        foreach ($categories as $index => $categoryName) {
            Category::create([
                'name' => $categoryName,
                'slug' => Str::slug($categoryName),
                'description' => 'Delicious ' . strtolower($categoryName) . ' for everyone',
                'image_url' => 'https://picsum.photos/id/' . (100 + $index) . '/400/300',
                'sort_order' => $index,
                'is_active' => true,
            ]);
        }
    }
}

