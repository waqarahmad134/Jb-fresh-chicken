<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'key' => 'site_name',
                'value' => 'JB Fresh Chicken',
                'type' => 'string',
                'description' => 'Website name',
                'group' => 'general',
            ],
            [
                'key' => 'logo_url',
                'value' => '/logo.png',
                'type' => 'string',
                'description' => 'Logo URL',
                'group' => 'general',
            ],
            [
                'key' => 'meta_description',
                'value' => 'Order the crispiest chicken nuggets, strips, and wings online. Fast delivery!',
                'type' => 'string',
                'description' => 'Meta description for SEO',
                'group' => 'general',
            ],
            [
                'key' => 'blog_enabled',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Enable blog functionality',
                'group' => 'blog',
            ],
            [
                'key' => 'newsletter_enabled',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Enable newsletter subscription',
                'group' => 'general',
            ],
            [
                'key' => 'maintenance_mode',
                'value' => '0',
                'type' => 'boolean',
                'description' => 'Enable maintenance mode',
                'group' => 'general',
            ],
            [
                'key' => 'product_card_style',
                'value' => 'style1',
                'type' => 'string',
                'description' => 'Product card display style',
                'group' => 'products',
            ],
            [
                'key' => 'quick_view_enabled',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Enable quick view modal for products',
                'group' => 'products',
            ],
            [
                'key' => 'add_to_cart_behavior',
                'value' => 'page',
                'type' => 'string',
                'description' => 'Behavior after adding to cart (drawer or page)',
                'group' => 'products',
            ],
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }
    }
}

