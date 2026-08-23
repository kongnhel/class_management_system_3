<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Assignment;
use App\Models\AttendanceRecord;
use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\Department;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\Faculty;
use App\Models\Generation;
use App\Models\Program;
use App\Models\Quiz;
use App\Models\Room;
use App\Models\Schedule;
use App\Models\StudentCourseEnrollment;
use App\Models\StudentProgramEnrollment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding demo data...');

        $generation = 16;

        // ---------- Generations ----------
        foreach ([14, 15, 16, 17, 18] as $gen) {
            Generation::updateOrCreate(['name' => (string) $gen], ['join_year' => 2006 + $gen]);
        }

        // ---------- Academic Year ----------
        AcademicYear::updateOrCreate(
            ['name' => '2025-2026'],
            ['start_date' => '2025-10-01', 'end_date' => '2026-08-30', 'is_current' => true]
        );

        // ---------- Faculties ----------
        $facultyBlocks = [
            ['name_en' => 'Faculty of Science', 'name_km' => 'មហាវិទ្យាល័យវិទ្យាសាស្ត្រ'],
            ['name_en' => 'Faculty of Business', 'name_km' => 'មហាវិទ្យាល័យអាជីវកម្ម'],
            ['name_en' => 'Faculty of Engineering', 'name_km' => 'មហាវិទ្យាល័យវិស្វកម្ម'],
        ];
        $faculties = collect();
        foreach ($facultyBlocks as $fb) {
            $faculties->push(Faculty::firstOrCreate(['name_en' => $fb['name_en']], $fb));
        }
        $this->command->info('Faculties: '.$faculties->count());

        // ---------- Departments ----------
        $depBlocks = [
            ['name_en' => 'Information Technology', 'name_km' => 'ព័ត៌មានវិទ្យា'],
            ['name_en' => 'Computer Science', 'name_km' => 'វិទ្យាសាស្ត្រកុំព្យូទ័រ'],
            ['name_en' => 'Business Administration', 'name_km' => 'គ្រប់គ្រងពាណិជ្ជកម្ម'],
            ['name_en' => 'Accounting', 'name_km' => 'គណនេយ្យ'],
            ['name_en' => 'Civil Engineering', 'name_km' => 'សំណង់ស៊ីវិល'],
        ];
        $departments = collect();
        foreach ($depBlocks as $i => $db) {
            $departments->push(Department::firstOrCreate(
                ['faculty_id' => $faculties[$i % 3]->id, 'name_en' => $db['name_en']],
                $db
            ));
        }
        $this->command->info('Departments: '.$departments->count());

        // ---------- Programs ----------
        $progBlocks = [
            ['name_en' => 'Software Engineering', 'name_km' => 'វិស្វកម្មកម្មវិធី'],
            ['name_en' => 'Networking', 'name_km' => 'បណ្តាញកុំព្យូទ័រ'],
            ['name_en' => 'Management', 'name_km' => 'គ្រប់គ្រង'],
            ['name_en' => 'Finance & Banking', 'name_km' => 'ហិរញ្ញវត្ថុ និងធនាគារ'],
        ];
        $programs = collect();
        foreach ($progBlocks as $i => $pb) {
            $programs->push(Program::firstOrCreate(
                ['department_id' => $departments[$i]->id, 'name_en' => $pb['name_en']],
                array_merge($pb, ['duration_years' => 4])
            ));
        }
        $this->command->info('Programs: '.$programs->count());

        // ---------- Rooms ----------
        $rooms = collect();
        foreach (['A101', 'A102', 'B201', 'B202', 'C301'] as $roomNumber) {
            $rooms->push(Room::firstOrCreate(['room_number' => $roomNumber], ['capacity' => 40, 'type_of_room' => 'Classroom']));
        }

        // ---------- Admin ----------
        $admin = User::updateOrCreate(
            ['email' => 'demo.admin@example.com'],
            ['name' => 'Demo Admin', 'role' => 'admin', 'email_verified_at' => now(), 'password' => Hash::make('password')]
        );
        \App\Models\UserProfile::updateOrCreate(['user_id' => $admin->id], ['full_name_en' => 'Demo Admin']);
        $this->command->info('Admin login: demo.admin@example.com / password');

        // ---------- Professors ----------
        $professorNames = ['Dara', 'Sokha', 'Rithy', 'Piseth', 'Sovann', 'Kimleng', 'Bora', 'Chenda', 'Vicheka', 'Meng'];
        $professors = collect();
        foreach ($professorNames as $i => $name) {
            $user = User::firstOrCreate(
                ['email' => "prof.{$name}.demo@example.com"],
                [
                    'name' => $name,
                    'role' => 'professor',
                    'email_verified_at' => now(),
                    'department_id' => $departments[$i % 5]->id,
                    'password' => Hash::make('password'),
                ]
            );
            \App\Models\ProfessorProfile::updateOrCreate(['user_id' => $user->id], [
                'staff_id' => 'P-'.$user->id,
                'full_name_en' => $name,
                'position' => 'Lecturer',
                'department_id' => $departments[$i % 5]->id,
            ]);
            $professors->push($user);
        }
        $this->command->info('Professors: '.$professors->count().' (password)');

        // ---------- Students ----------
        $khmerNames = ['ញិល', 'អង', 'ប៉េង', 'ស្មី', 'ស្រួច', 'វណ្ណា', 'លី', 'អៀន', 'រូ', 'ហេង', 'សុខ', 'ផល'];
        $students = collect();
        for ($i = 0; $i < 30; $i++) {
            $programId = $programs[$i % 4]->id;
            $user = User::firstOrCreate(
                ['email' => "demo.stu{$i}@example.com"],
                [
                    'name' => $khmerNames[$i % count($khmerNames)],
                    'role' => 'student',
                    'email_verified_at' => now(),
                    'program_id' => $programId,
                    'generation' => (string) $generation,
                    'student_id_code' => 'B-XVI-'.str_pad((string) ($i + 1), 6, '0', STR_PAD_LEFT),
                    'password' => Hash::make('password'),
                ]
            );
            \App\Models\StudentProfile::updateOrCreate(['user_id' => $user->id], [
                'full_name_en' => $user->name.' '.rand(1, 99),
                'full_name_km' => $user->name,
                'gender' => $i % 2 ? 'female' : 'male',
                'date_of_birth' => now()->subYears(20 + ($i % 6))->toDateString(),
            ]);
            StudentProgramEnrollment::updateOrCreate(
                ['student_user_id' => $user->id],
                ['program_id' => $programId, 'degree_level' => 'បរិញ្ញាបត្រ', 'starting_year_level' => 1, 'enrollment_date' => now()->startOfYear(), 'status' => 'active']
            );
            $students->push($user);
        }
        $this->command->info('Students: '.$students->count().' (demo.stu0..demo.stu29 / password)');

        // ---------- Courses ----------
        $courseTitles = [
            'Web Development', 'Database Systems', 'Operating Systems', 'Networking', 'Algorithms',
            'Machine Learning', 'Cyber Security', 'Mobile Development', 'Project Management',
            'Financial Accounting', 'Microeconomics', 'Data Structures',
        ];
        $courses = collect();
        foreach ($courseTitles as $i => $title) {
            $course = Course::firstOrCreate(
                ['department_id' => $departments[$i % 5]->id, 'title_en' => $title],
                ['title_km' => $title, 'credits' => 3, 'generation' => (string) $generation]
            );
            DB::table('course_program')->updateOrInsert(
                ['course_id' => $course->id, 'program_id' => $programs[$i % 4]->id]
            );
            $courses->push($course);
        }
        $this->command->info('Courses: '.$courses->count());

        // ---------- Course Offerings + Schedules + Assessments ----------
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];

        // Canonical teaching sessions generated from config/school.php
        // so seeded demo timetables follow the school's real time grid.
        $sessionMinutes = (int) config('school.schedule.session_minutes', 90);
        $sessionSlots = [];
        foreach (config('school.schedule.windows', []) as $window) {
            [$sh, $sm] = array_map('intval', explode(':', $window['start']));
            [$eh, $em] = array_map('intval', explode(':', $window['end']));
            $cursor = $sh * 60 + $sm;
            $windowEnd = $eh * 60 + $em;
            while ($cursor + $sessionMinutes <= $windowEnd) {
                $sessionSlots[] = [
                    sprintf('%02d:%02d', intdiv($cursor, 60), $cursor % 60),
                    sprintf('%02d:%02d', intdiv($cursor + $sessionMinutes, 60), ($cursor + $sessionMinutes) % 60),
                ];
                $cursor += $sessionMinutes;
            }
        }
        if (empty($sessionSlots)) {
            $sessionSlots = [['07:00', '08:30']];
        }

        // Deterministic rotation guarantees no two offerings of the same
        // semester share a room/day/time slot (mirrors the app's conflict rules).
        $semesterCounters = [];

        $offerings = collect();
        foreach (array_chunk($courses->all(), 4) as $chunkIdx => $chunkCourses) {
            foreach ($chunkCourses as $ci => $course) {
                $prof = $professors[($chunkIdx + $ci) % $professors->count()];
                $semester = ($chunkIdx % 2 === 0) ? 'ឆមាសទី១' : 'ឆមាសទី២';

                $k = $semesterCounters[$semester] ?? 0;
                $semesterCounters[$semester] = $k + 1;

                $room = $rooms[$k % $rooms->count()];
                [$startTime, $endTime] = $sessionSlots[intdiv($k, $rooms->count()) % count($sessionSlots)];
                $day = $days[$k % count($days)];

                $offering = CourseOffering::firstOrCreate(
                    [
                        'course_id' => $course->id,
                        'academic_year' => '2025-2026',
                        'semester' => $semester,
                        'lecturer_user_id' => $prof->id,
                    ],
                    [
                        'capacity' => 8,
                        'generation' => (string) $generation,
                        'section' => 'A',
                        'room_number' => $room->room_number,
                        'start_date' => now()->subMonths(2)->toDateString(),
                        'end_date' => now()->addMonths(2)->toDateString(),
                    ]
                );

                $program = $programs[$ci % 4];
                DB::table('course_offering_program')->updateOrInsert(
                    ['course_offering_id' => $offering->id, 'program_id' => $program->id],
                    ['generation' => (string) $generation]
                );

                Schedule::updateOrCreate(
                    ['course_offering_id' => $offering->id],
                    [
                        'day_of_week' => $day,
                        'room_id' => $room->id,
                        'start_time' => $startTime.':00',
                        'end_time' => $endTime.':00',
                    ]
                );

                foreach ($students as $s) {
                    StudentCourseEnrollment::updateOrInsert(
                        ['student_user_id' => $s->id, 'course_offering_id' => $offering->id],
                        ['student_id' => $s->id, 'enrollment_date' => now()->subMonths(2)->toDateString(), 'status' => 'enrolled']
                    );
                }

                $offerings->push($offering);
            }
        }
        $this->command->info('Course Offerings: '.$offerings->count());

        // ---------- Assessments + results + attendance ----------
        foreach ($offerings as $offering) {
            $ass1 = Assignment::firstOrCreate(
                ['course_offering_id' => $offering->id, 'title_en' => 'Assignment 1'],
                ['title_km' => 'ជំពូកទី១', 'due_date' => now()->addDays(5)->toDateTimeString(), 'max_score' => 20]
            );
            $midterm = Exam::firstOrCreate(
                ['course_offering_id' => $offering->id, 'title_en' => 'Midterm Exam'],
                ['title_km' => 'ប្រឡងពាក់កណ្ដាល់ឆមាស', 'exam_date' => now()->toDateTimeString(), 'duration_minutes' => 60, 'max_score' => 15]
            );
            $final = Exam::firstOrCreate(
                ['course_offering_id' => $offering->id, 'title_en' => 'Final Exam'],
                ['title_km' => 'ប្រឡងប្រចាំឆមាស', 'exam_date' => now()->addDays(30)->toDateTimeString(), 'duration_minutes' => 90, 'max_score' => 50]
            );
            $quiz = Quiz::firstOrCreate(
                ['course_offering_id' => $offering->id, 'title_en' => 'Quiz 1'],
                ['title_km' => 'កិច្ចការ', 'max_score' => 5, 'quiz_date' => now()->subDays(3)->toDateString()]
            );

            $enrolled = StudentCourseEnrollment::where('course_offering_id', $offering->id)->get();
            foreach ($enrolled as $enrol) {
                $studentId = $enrol->student_user_id;

                $this->recordResult($studentId, $ass1->id, 'assignment', rand(8, 20));
                $this->recordResult($studentId, $midterm->id, 'exam', rand(5, 15));
                $this->recordResult($studentId, $final->id, 'exam', rand(20, 50));
                $this->recordResult($studentId, $quiz->id, 'quiz', rand(1, 5));

                $start = now()->subDays(10);
                for ($d = 0; $d < 6; $d++) {
                    $date = $start->copy()->addDays($d * 2);
                    $roll = rand(1, 10);
                    $status = $roll > 3 ? 'present' : ($roll === 1 ? 'absent' : 'permission');
                    AttendanceRecord::updateOrInsert(
                        ['course_offering_id' => $offering->id, 'user_id' => $studentId, 'date' => $date->toDateString()],
                        ['student_user_id' => $studentId, 'status' => $status, 'created_at' => now(), 'updated_at' => now()]
                    );
                }
            }
        }
        $this->command->info('Assessments, grades and attendance seeded.');

        $this->command->info('Demo data seeded successfully!');
    }

    private function recordResult(int $studentUserId, int $assessmentId, string $type, int $score): void
    {
        ExamResult::updateOrCreate(
            ['assessment_id' => $assessmentId, 'assessment_type' => $type, 'student_user_id' => $studentUserId],
            ['score_obtained' => $score, 'recorded_at' => now()]
        );
    }
}
