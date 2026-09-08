<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;           // ← important!

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Super Administrator
        User::create([
            'username'       => 'superadmin',
            'first_name'     => 'Jureen',
            'last_name'      => 'Altoveros',
            'email'          => 'superadmin@gmail.com',
            'password'       => Hash::make('super12345'),
            'contact_number' => '09171234567',
            'position'       => 'System Owner',
            'user_level'     => 'superadmin',
            'active'         => true,
            'email_verified_at' => now(),
        ]);

        // Normal Administrator
        User::create([
            'username'       => 'admin',
            'first_name'     => 'Jureen',
            'last_name'      => 'Altoveros',
            'email'          => 'admin@gmail.com',
            'password'       => Hash::make('admin12345'),
            'contact_number' => '09181234567',
            'position'       => 'IT Administrator',
            'user_level'     => 'admin',
            'active'         => true,
            'email_verified_at' => now(),
        ]);

        // Regular test user
        User::create([
            'username'       => 'user1',
            'first_name'     => 'Jureen',
            'last_name'      => 'Altoveros',
            'email'          => 'user@gmail.com',
            'password'       => Hash::make('user12345'),
            'contact_number' => '09191234567',
            'position'       => 'IT Staff',
            'user_level'     => 'user',
            'active'         => true,
            'email_verified_at' => now(),
        ]);

        // Optional: create 5 more random users (good for testing)
        // User::factory()->count(5)->create();
    }
}