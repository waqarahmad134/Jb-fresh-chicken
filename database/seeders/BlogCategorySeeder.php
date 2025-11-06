<?php

namespace Database\Seeders;

use App\Models\BlogCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BlogCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Recipes & Ideas',
            'Cooking Tips',
            'Behind the Scenes',
        ];

        foreach ($categories as $categoryName) {
            BlogCategory::create([
                'name' => $categoryName,
                'slug' => Str::slug($categoryName),
                'description' => 'Articles about ' . strtolower($categoryName),
            ]);
        }
    }
}

