<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pagesData = [
            [
                'slug' => 'about',
                'title' => 'About JB Fresh Chicken',
                'content' => '<p>Founded in 2023, JB Fresh Chicken was born from a simple idea: to create the most delicious, high-quality chicken and frozen food products that are both convenient and satisfying. We believe that quality food doesn\'t have to mean compromising on taste or freshness.</p>
                    <p>Our journey started with a commitment to providing the freshest chicken and frozen food products. We use only the finest ingredients to ensure every product meets our high standards of quality.</p>
                    <p>We\'re more than just a food supplier; we\'re a part of the community. We\'re dedicated to providing quick, friendly service and quality products that bring families and friends together. Thank you for choosing JB Fresh Chicken!</p>',
                'meta_title' => 'About Us - JB Fresh Chicken',
                'meta_description' => 'Learn about JB Fresh Chicken, our story, and our commitment to quality chicken and frozen food products.',
                'lastUpdated' => '2023-11-01',
            ],
            [
                'slug' => 'privacy',
                'title' => 'Privacy Policy',
                'content' => '<h2>1. Information We Collect</h2>
                    <p>We collect information you provide directly to us, such as when you create an account, place an order, or contact customer support. This may include your name, email address, shipping address, and payment information.</p>
                    <h2>2. How We Use Your Information</h2>
                    <p>We use the information we collect to process your orders, communicate with you, personalize your shopping experience, and improve our services. We will not share your personal information with third parties except as necessary to fulfill your order (e.g., with shipping carriers).</p>
                    <h2>3. Data Security</h2>
                    <p>We implement a variety of security measures to maintain the safety of your personal information. Your personal information is contained behind secured networks and is only accessible by a limited number of persons who have special access rights to such systems.</p>',
                'meta_title' => 'Privacy Policy - JB Fresh Chicken',
                'meta_description' => 'Read our privacy policy to understand how we collect and use your data.',
                'lastUpdated' => '2023-10-15',
            ],
            [
                'slug' => 'terms',
                'title' => 'Terms of Service',
                'content' => '<h2>1. Agreement to Terms</h2>
                    <p>By accessing or using our services, you agree to be bound by these Terms. If you disagree with any part of the terms, then you may not access the service.</p>
                    <h2>2. Orders</h2>
                    <p>When you place an order, you agree that all information you provide is accurate and complete. All orders are subject to acceptance and availability. We reserve the right to refuse or cancel an order for any reason.</p>
                    <h2>3. Intellectual Property</h2>
                    <p>The Service and its original content, features, and functionality are and will remain the exclusive property of JB Fresh Chicken and its licensors.</p>',
                'meta_title' => 'Terms of Service - JB Fresh Chicken',
                'meta_description' => 'Read our terms of service before using our website.',
                'lastUpdated' => '2023-10-15',
            ],
        ];

        foreach ($pagesData as $pageData) {
            Page::create([
                'slug' => $pageData['slug'],
                'title' => $pageData['title'],
                'content' => $pageData['content'],
                'meta_title' => $pageData['meta_title'],
                'meta_description' => $pageData['meta_description'],
                'is_published' => true,
                'updated_at' => $pageData['lastUpdated'],
            ]);
        }
    }
}

