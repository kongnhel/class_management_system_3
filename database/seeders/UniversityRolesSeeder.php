<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon; // Import Hash facade
use Illuminate\Support\Facades\Hash; // Import Carbon for now()

class UniversityRolesSeeder extends Seeder
{
    /**
     * រត់ Database Seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'University Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('Admin1234!@#$'), // Password គឺ 'password'
            'role' => 'admin',
            'email_verified_at' => Carbon::now(), // Verify email immediately for testing
        ]);

        // // បង្កើត Professor User
        User::create([
            'name' => 'វ៉ាង សុវណ្ណ',
            'email' => 'psovann@gmail.com',
            'password' => Hash::make('Psovann1234!@#$'), // Password គឺ 'password'
            'role' => 'professor',
            'email_verified_at' => Carbon::now(),
        ]);

    }
}
