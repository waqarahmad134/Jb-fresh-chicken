<?php

namespace Database\Seeders;

use App\Models\Review;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $reviewsData = [
            [
                'productId' => 1,
                'userName' => 'Chris P. Chicken',
                'rating' => 5,
                'comment' => "Absolutely the crispiest nuggets I've ever had. My kids love them! A staple in our house now.",
                'date' => '2023-10-20',
            ],
            [
                'productId' => 1,
                'userName' => 'Fry Fanatic',
                'rating' => 4,
                'comment' => 'Great taste and very convenient for a quick meal. A little bit greasy, but what do you expect? Still delicious.',
                'date' => '2023-10-18',
            ],
            [
                'productId' => 2,
                'userName' => 'SpiceQueen',
                'rating' => 5,
                'comment' => "Finally, some chicken strips with a REAL kick! They're not kidding about the spicy. Perfect heat level and so juicy.",
                'date' => '2023-10-25',
            ],
            [
                'productId' => 3,
                'userName' => 'Chris P. Chicken',
                'rating' => 5,
                'comment' => 'The Family Feast Bucket is a lifesaver for game nights. Plenty to go around and everyone loves it. Great value.',
                'date' => '2023-09-15',
            ],
            [
                'productId' => 2,
                'userName' => 'Mild Mike',
                'rating' => 3,
                'comment' => 'Too spicy for me, but my friends who like heat said they were amazing. The chicken quality itself was good.',
                'date' => '2023-10-22',
            ],
            [
                'productId' => 6,
                'userName' => 'Sandwich Sam',
                'rating' => 5,
                'comment' => 'This is not just a sandwich, it is a masterpiece. The bun is soft, the chicken is crispy, the balance is perfect.',
                'date' => '2023-10-28',
            ],
        ];

        foreach ($reviewsData as $reviewData) {
            $product = Product::find($reviewData['productId']);
            $user = User::where('name', $reviewData['userName'])->first();

            if ($product && $user) {
                Review::create([
                    'product_id' => $product->id,
                    'user_id' => $user->id,
                    'rating' => $reviewData['rating'],
                    'comment' => $reviewData['comment'],
                    'is_verified_purchase' => false,
                    'is_approved' => true,
                    'created_at' => $reviewData['date'],
                ]);
            }
        }
    }
}

