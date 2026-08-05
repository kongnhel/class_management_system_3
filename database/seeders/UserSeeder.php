<?php

namespace Database\Seeders;

use App\Models\ProfessorProfile;
use App\Models\StudentProfile;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make(env('SEEDER_PASSWORD', 'password'));

        foreach ($this->adminUsers() as $userData) {
            $this->seedUser($userData, $password);
        }

        foreach ($this->professorUsers() as $userData) {
            $this->seedUser($userData, $password);
        }

        foreach ($this->studentUsers() as $userData) {
            $this->seedUser($userData, $password);
        }
    }

    private function seedUser(array $userData, string $password): void
    {
        $profileData = $userData['profile'];
        $role = $userData['role'];
        $userAttributes = $userData;

        unset($userAttributes['profile'], $userAttributes['staff_id']);

        $user = User::updateOrCreate(
            ['email' => $userAttributes['email']],
            array_merge($userAttributes, ['password' => $password])
        );

        $user->email_verified_at = now();
        $user->save();

        UserProfile::updateOrCreate(
            ['user_id' => $user->id],
            $profileData
        );

        if ($role === 'professor') {
            ProfessorProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'staff_id' => $userData['staff_id'],
                    'full_name_en' => $profileData['full_name_en'],
                    'position' => 'Lecturer',
                ]
            );
        }

        if ($role === 'student') {
            StudentProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'full_name_en' => $profileData['full_name_en'],
                    'gender' => $profileData['gender'],
                ]
            );
        }
    }

    private function adminUsers(): array
    {
        return [
            [
                'name' => 'Admin One',
                'email' => 'admin1@example.com',
                'role' => 'admin',
                'student_id_code' => null,
                'department_id' => null,
                'program_id' => null,
                'profile' => $this->profile('Admin One', 'other'),
            ],
            [
                'name' => 'Admin Two',
                'email' => 'admin2@example.com',
                'role' => 'admin',
                'student_id_code' => null,
                'department_id' => null,
                'program_id' => null,
                'profile' => $this->profile('Admin Two', 'other'),
            ],
        ];
    }

    private function professorUsers(): array
    {
        $users = [];

        for ($number = 1; $number <= 5; $number++) {
            $name = "Professor {$number}";

            $users[] = [
                'name' => $name,
                'email' => "professor{$number}@example.com",
                'role' => 'professor',
                'student_id_code' => null,
                'department_id' => null,
                'program_id' => null,
                'staff_id' => sprintf('PROF-%03d', $number),
                'profile' => $this->profile($name, $number % 2 === 0 ? 'female' : 'male'),
            ];
        }

        return $users;
    }

    private function studentUsers(): array
    {
        $users = [];

        for ($number = 1; $number <= 20; $number++) {
            $name = "Student {$number}";

            $users[] = [
                'name' => $name,
                'email' => "student{$number}@example.com",
                'role' => 'student',
                'student_id_code' => sprintf('STU-%03d', $number),
                'department_id' => null,
                'program_id' => null,
                'profile' => $this->profile($name, $number % 2 === 0 ? 'female' : 'male'),
            ];
        }

        return $users;
    }

    private function profile(string $name, string $gender): array
    {
        return [
            'full_name_en' => $name,
            'full_name_km' => $name,
            'gender' => $gender,
        ];
    }
}
