<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password'),
            'is_admin' => true,
            'phone' => '+1234567890',
        ]);

        // Create regular user
        User::create([
            'name' => 'Regular User',
            'email' => 'user@gmail.com',
            'password' => Hash::make('password'),
            'is_admin' => false,
            'phone' => '+1987654321',
        ]);

        // Create test user
        User::create([
            'name' => 'Test User',
            'email' => 'test@gmail.com',
            'password' => Hash::make('password'),
            'is_admin' => false,
        ]);

        $this->command->info('Users created successfully!');
        $this->command->info('Admin: admin@gmail.com / password');
        $this->command->info('User: user@gmail.com / password');
        $this->command->info('Test: test@gmail.com / password');
    }
}
