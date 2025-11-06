<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use App\Models\BlogCategory;
use App\Models\User;
use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BlogPostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $blogPostsData = [
            [
                'slug' => 'the-ultimate-guide-to-chicken-nugget-dipping-sauces',
                'title' => 'The Ultimate Guide to Chicken Nugget Dipping Sauces',
                'excerpt' => 'Tired of the same old ketchup? We explore a world of flavor with ten incredible dipping sauces that will take your nugget game to the next level.',
                'content' => "Let's face it, a chicken nugget is only as good as its dipping sauce. While ketchup is a classic, the world of sauces is vast and delicious...",
                'imageUrl' => 'https://picsum.photos/id/102/1200/800',
                'category' => 'Recipes & Ideas',
                'tags' => ['sauces', 'diy', 'flavor'],
                'author' => 'Chris P. Chicken',
                'authorImageUrl' => 'https://i.pravatar.cc/150?u=chris',
                'date' => '2023-11-05',
            ],
            [
                'slug' => '5-creative-ways-to-use-leftover-chicken-strips',
                'title' => '5 Creative Ways to Use Leftover Chicken Strips',
                'excerpt' => 'Don\'t let those delicious chicken strips go to waste! We share five fun and easy meal ideas to transform your leftovers into a brand new dish.',
                'content' => "Got a few extra Spicy Chicken Strips from your last JB Fresh Chicken order? Don't just reheat them—reinvent them!",
                'imageUrl' => 'https://picsum.photos/id/202/1200/800',
                'category' => 'Cooking Tips',
                'tags' => ['leftovers', 'hacks', 'recipes'],
                'author' => 'Fry Fanatic',
                'authorImageUrl' => 'https://i.pravatar.cc/150?u=fryfanatic',
                'date' => '2023-11-01',
            ],
            [
                'slug' => 'the-secret-to-perfectly-crispy-chicken-at-home',
                'title' => 'The Secret to Perfectly Crispy Chicken at Home',
                'excerpt' => 'Ever wonder how we get our chicken so crispy? We\'re spilling a few secrets to help you achieve that perfect golden-brown crunch in your own kitchen.',
                'content' => 'The quest for perfectly crispy chicken is a noble one. While our JB Fresh Chicken kitchen has professional-grade equipment, you can still achieve a fantastic crunch at home.',
                'imageUrl' => 'https://picsum.photos/id/1080/1200/800',
                'category' => 'Behind the Scenes',
                'tags' => ['cooking', 'tips', 'crispy'],
                'author' => 'Chef Cluckington',
                'authorImageUrl' => 'https://i.pravatar.cc/150?u=chef',
                'date' => '2023-10-28',
            ],
            [
                'slug' => 'perfect-pairings-what-to-serve-with-chicken-wings',
                'title' => 'Perfect Pairings: What to Serve with Chicken Wings',
                'excerpt' => 'Wings are the star, but every star needs a supporting cast. Discover the best side dishes to complement your BBQ or Buffalo wings for a complete and satisfying meal.',
                'content' => 'You\'ve got a platter of delicious, saucy wings from JB Fresh Chicken. What do you serve alongside them? The right side dishes can balance the heat, complement the flavor...',
                'imageUrl' => 'https://picsum.photos/id/1025/1200/800',
                'category' => 'Recipes & Ideas',
                'tags' => ['pairings', 'sides', 'wings'],
                'author' => 'Chris P. Chicken',
                'authorImageUrl' => 'https://i.pravatar.cc/150?u=chris',
                'date' => '2023-10-22',
            ],
        ];

        foreach ($blogPostsData as $postData) {
            $category = BlogCategory::where('name', $postData['category'])->first();
            $user = User::where('name', $postData['author'])->first();

            if (!$user) {
                $user = User::where('is_admin', true)->first();
            }

            $blogPost = BlogPost::create([
                'slug' => $postData['slug'],
                'title' => $postData['title'],
                'excerpt' => $postData['excerpt'],
                'content' => $postData['content'],
                'image_url' => $postData['imageUrl'],
                'blog_category_id' => $category ? $category->id : null,
                'author_id' => $user->id,
                'author_name' => $postData['author'],
                'author_image_url' => $postData['authorImageUrl'],
                'is_published' => true,
                'published_at' => $postData['date'],
                'views_count' => 0,
                'created_at' => $postData['date'],
            ]);

            // Attach tags
            foreach ($postData['tags'] as $tagName) {
                $tag = Tag::where('slug', Str::slug($tagName))->first();
                if ($tag) {
                    $blogPost->tags()->attach($tag->id);
                }
            }
        }
    }
}

