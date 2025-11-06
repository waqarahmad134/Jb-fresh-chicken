<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            'classic',
            'shareable',
            'spicy',
            'best-value',
            'bbq',
            'sweet',
            'hot',
            'best-seller',
            'meal',
            'party',
            'sauces',
            'diy',
            'flavor',
            'leftovers',
            'hacks',
            'recipes',
            'cooking',
            'tips',
            'crispy',
            'pairings',
            'sides',
            'wings',
        ];

        foreach ($tags as $tagName) {
            Tag::create([
                'name' => $tagName,
                'slug' => Str::slug($tagName),
            ]);
        }
    }
}

