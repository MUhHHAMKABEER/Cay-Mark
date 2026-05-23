<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name'                  => 'Admin User',
                'username'              => 'admin',
                'password'              => Hash::make('1234567890'),
                'role'                  => 'admin',
                'nationality'           => 'Bahamian',
                'dob'                   => '1990-01-01',
                'email_verified_at'     => now(),
                'registration_complete' => true,
            ]
        );
    }
}
