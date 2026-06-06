<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $count = 500;
        $programIds = [1, 2];
        $generations = ['16', '17', '18', '19', '20'];
        $password = Hash::make('password123');

        $khmerFirstNames = [
            'សុខ', 'ប៉ា', 'ចាន់', 'សុន', 'ហ៊ុន', 'រិទ្ធ', 'វិសាល', 'គង់', 'ឈិត', 'លី',
            'ចិន', 'អ៊ុក', 'ផាន', 'ឈួន', 'ភួង', 'អូន', 'ស្រី', 'រតនា', 'ពិសិដ្ឋ', 'សុផល',
            'ម៉ៅ', 'ទី', 'ជី', 'អាន', 'ថៅ', 'ស៊ី', 'ឌី', 'ម៉ែត', 'រស្មី', 'សុភ័ណ្ឌ',
        ];

        $khmerLastNames = [
            'វ៉ាន់', 'សារិន', 'ពេជ្រ', 'លាង', 'សុខ', 'ថៃ', 'វីរៈ', 'ភាព', 'សំណាង', 'អុល',
            'ចិន', 'សេង', 'ផល', 'សុង', 'ឈុំ', 'ហួរ', 'សុជាតិ', 'សុធារ៉ា', 'កែវ', 'គុយ',
        ];

        $phonePrefixes = ['012', '015', '016', '017', '068', '069', '081', '085', '086', '087', '088', '089', '092', '093', '095', '096', '097'];

        $maxUserId = (int) User::max('id');
        $startCode = $maxUserId + 10001;
        $now = now()->toDateTimeString();

        $this->command->info("Seeding {$count} students...");

        $users = [];
        $userProfiles = [];
        $studentProfiles = [];

        for ($i = 0; $i < $count; $i++) {
            $userId = $maxUserId + $i + 1;
            $studentCode = str_pad($startCode + $i, 5, '0', STR_PAD_LEFT);
            $programId = $programIds[array_rand($programIds)];
            $generation = $generations[array_rand($generations)];
            $firstName = $khmerFirstNames[array_rand($khmerFirstNames)];
            $lastName = $khmerLastNames[array_rand($khmerLastNames)];
            $fullName = $firstName.' '.$lastName;
            $email = 'student'.($startCode + $i).'@example.com';
            $phone = $phonePrefixes[array_rand($phonePrefixes)].str_pad(rand(0, 9999999), 7, '0', STR_PAD_LEFT);
            $gender = rand(0, 1) ? 'male' : 'female';
            $dob = now()->subYears(rand(18, 25))->subDays(rand(0, 364))->toDateString();

            $users[] = [
                'id' => $userId,
                'name' => $fullName,
                'email' => $email,
                'password' => $password,
                'role' => 'student',
                'student_id_code' => $studentCode,
                'program_id' => $programId,
                'generation' => $generation,
                'email_verified_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            $userProfiles[] = [
                'user_id' => $userId,
                'full_name_km' => $fullName,
                'full_name_en' => $fullName,
                'gender' => $gender,
                'phone_number' => $phone,
                'date_of_birth' => $dob,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            $studentProfiles[] = [
                'user_id' => $userId,
                'student_code_id' => $studentCode,
                'full_name_km' => $fullName,
                'full_name_en' => $fullName,
                'gender' => $gender,
                'generation' => $generation,
                'date_of_birth' => $dob,
                'phone_number' => $phone,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        $chunks = array_chunk($users, 100);
        foreach ($chunks as $chunk) {
            DB::table('users')->insert($chunk);
        }
        $this->command->info('  Inserted '.count($users).' users.');

        $chunks = array_chunk($userProfiles, 100);
        foreach ($chunks as $chunk) {
            DB::table('user_profiles')->insert($chunk);
        }
        $this->command->info('  Inserted '.count($userProfiles).' user_profiles.');

        $chunks = array_chunk($studentProfiles, 100);
        foreach ($chunks as $chunk) {
            DB::table('student_profiles')->insert($chunk);
        }
        $this->command->info('  Inserted '.count($studentProfiles).' student_profiles.');

        $this->command->info("Successfully created {$count} students!");
        $this->command->info("Login with: student{$startCode}@example.com / password123");
    }
}
