<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add e-commerce specific fields
            $table->string('phone')->nullable()->after('email');
            $table->boolean('is_admin')->default(false)->after('password');
            $table->boolean('is_active')->default(true)->after('is_admin');
            $table->string('profile_image')->nullable()->after('is_active');
            
            // Shipping address fields
            $table->text('shipping_address')->nullable()->after('profile_image');
            $table->string('shipping_city')->nullable()->after('shipping_address');
            $table->string('shipping_state')->nullable()->after('shipping_city');
            $table->string('shipping_country')->nullable()->after('shipping_state');
            $table->string('shipping_zip')->nullable()->after('shipping_country');
            
            // Billing address fields
            $table->text('billing_address')->nullable()->after('shipping_zip');
            $table->string('billing_city')->nullable()->after('billing_address');
            $table->string('billing_state')->nullable()->after('billing_city');
            $table->string('billing_country')->nullable()->after('billing_state');
            $table->string('billing_zip')->nullable()->after('billing_country');
            
            $table->softDeletes();
            
            $table->index('is_admin');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'is_admin',
                'is_active',
                'profile_image',
                'shipping_address',
                'shipping_city',
                'shipping_state',
                'shipping_country',
                'shipping_zip',
                'billing_address',
                'billing_city',
                'billing_state',
                'billing_country',
                'billing_zip',
            ]);
            $table->dropSoftDeletes();
        });
    }
};

