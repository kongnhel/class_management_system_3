<?php

namespace Database\Seeders;

use App\Models\ProfessorProfile;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Sets the actual login password to Pnp@123456$
        $password = Hash::make('Pnp@123456$');

        // Admin
        $admin = User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin',
                'password' => $password,
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        UserProfile::updateOrCreate(
            ['user_id' => $admin->id],
            [
                'full_name_km' => 'អ្នកគ្រប់គ្រង',
                'full_name_en' => 'Admin',
                'gender' => 'male',
            ]
        );

        // Professor
        $professor = User::updateOrCreate(
            ['email' => 'professor@gmail.com'],
            [
                'name' => 'Professor',
                'password' => $password,
                'role' => 'professor',
                'email_verified_at' => now(),
            ]
        );

        UserProfile::updateOrCreate(
            ['user_id' => $professor->id],
            [
                'full_name_km' => 'សាស្រ្តាចារ្យ',
                'full_name_en' => 'Professor',
                'gender' => 'male',
            ]
        );

        ProfessorProfile::updateOrCreate(
            ['user_id' => $professor->id],
            [
                'staff_id' => 'PROF-001',
            ]
        );
    }
}