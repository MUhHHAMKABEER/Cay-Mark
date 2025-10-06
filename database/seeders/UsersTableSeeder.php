<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        // Admin
User::create([
    'name' => 'Admin User',
    'email' => 'admin@gmail.com',
    'username' => 'admin',
    'password' => Hash::make('1234567890'),
    'role' => 'admin',
    'nationality' => 'Bahamian',
    'dob' => '1990-01-01', // <-- add a date of birth
    'email_verified_at' => now(),
]);

User::create([
    'name' => 'Seller User',
    'email' => 'seller@gmail.com',
    'username' => 'seller',
    'password' => Hash::make('1234567890'),
    'role' => 'seller',
    'nationality' => 'Bahamian',
    'dob' => '1990-01-01', // <-- add a date of birth
    'email_verified_at' => now(),
]);

User::create([
    'name' => 'Buyer User',
    'email' => 'buyer@gmail.com',
    'username' => 'buyer',
    'password' => Hash::make('1234567890'),
    'role' => 'buyer',
    'nationality' => 'Bahamian',
    'dob' => '1990-01-01', // <-- add a date of birth
    'email_verified_at' => now(),
]);

    }
}

