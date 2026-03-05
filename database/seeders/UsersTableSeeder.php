<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        // Admin – updateOrCreate so existing admin@gmail.com always gets role=admin and registration_complete
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin User',
                'username' => 'admin',
                'password' => Hash::make('1234567890'),
                'role' => 'admin',
                'nationality' => 'Bahamian',
                'dob' => '1990-01-01',
                'email_verified_at' => now(),
                'registration_complete' => true,
            ]
        );

        // Seller
        User::updateOrCreate(
            ['email' => 'seller@gmail.com'],
            [
                'name' => 'Seller User',
                'username' => 'seller',
                'password' => Hash::make('1234567890'),
                'role' => 'seller',
                'nationality' => 'Bahamian',
                'dob' => '1990-01-01',
                'email_verified_at' => now(),
                'registration_complete' => true,
            ]
        );

        // Buyer
        User::updateOrCreate(
            ['email' => 'buyer@gmail.com'],
            [
                'name' => 'Buyer User',
                'username' => 'buyer',
                'password' => Hash::make('1234567890'),
                'role' => 'buyer',
                'nationality' => 'Bahamian',
                'dob' => '1990-01-01',
                'email_verified_at' => now(),
                'registration_complete' => true,
            ]
        );
    }
}

