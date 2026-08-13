<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Announcement;
use App\Models\Assignment;
use App\Models\AttendanceProfessor;
use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\Department;
use App\Models\Exam;
use App\Models\ExamResult;
use App\Models\Faculty;
use App\Models\GradingCategory;
use App\Models\Notification;
use App\Models\Program;
use App\Models\Quiz;
use App\Models\Room;
use App\Models\Schedule;
use App\Models\StudentCourseEnrollment;
use App\Models\StudentProgramEnrollment;
use App\Models\User;
use App\Models\UserProfile;
use App\Services\StudentIdGeneratorService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ClassManagementDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding Class Management Data...');

        // ---------- Faculties ----------
        $faculties = [
            ['id' => 1, 'name_km' => 'មហាវិទ្យាល័យវិទ្យាសាស្ត្រនិងបច្ចេកវិទ្យា', 'name_en' => 'Faculty Science and Technology', 'dean_user_id' => null],
            ['id' => 2, 'name_km' => 'មហាវិទ្យាល័យកសិកម្ម និងកែច្នៃអាហារ', 'name_en' => 'Faculty of Agriculture and Food Processing', 'dean_user_id' => null],
        ];
        foreach ($faculties as $f) {
            Faculty::updateOrCreate(['id' => $f['id']], $f);
        }
        $this->command->info('  Faculties: ' . count($faculties));

        // ---------- Departments ----------
        $departments = [
            ['id' => 1, 'faculty_id' => 1, 'name_km' => 'ព័ត៌មានវិទ្យា', 'name_en' => 'Science and technology', 'head_user_id' => null],
            ['id' => 2, 'faculty_id' => 2, 'name_km' => 'កសិកម្ម', 'name_en' => 'Agriculture', 'head_user_id' => null],
        ];
        foreach ($departments as $d) {
            Department::updateOrCreate(['id' => $d['id']], $d);
        }
        $this->command->info('  Departments: ' . count($departments));

        // ---------- Programs ----------
        $programs = [
            ['id' => 2, 'department_id' => 1, 'name_km' => 'គ្រប់គ្រងបណ្ដាញកុំព្យូទ័រ', 'name_en' => 'Computer Networking', 'degree_level' => 'បរិញ្ញាបត្រ', 'duration_years' => 4],
            ['id' => 3, 'department_id' => 1, 'name_km' => 'អភិវឌ្ឍន៍កម្មវិធីកុំព្យូទ័រនិងទូរស័ព្ទដៃ', 'name_en' => 'Computer and mobile application development', 'degree_level' => 'បរិញ្ញាបត្រ', 'duration_years' => 4],
            ['id' => 4, 'department_id' => 2, 'name_km' => 'បសុពេទ្យ', 'name_en' => 'Veterinarian', 'degree_level' => 'បរិញ្ញាបត្រ', 'duration_years' => 4],
        ];
        foreach ($programs as $p) {
            Program::updateOrCreate(['id' => $p['id']], $p);
        }
        $this->command->info('  Programs: ' . count($programs));

        // ---------- Users (Professors) ----------
        $professors = [
            ['id' => 31, 'name' => 'លោក សង សុីយូ', 'email' => 'siyou@gmail.com', 'department_id' => 1],
            ['id' => 32, 'name' => 'លោក ហេង ប៊ុនឌឿន', 'email' => 'bundern@gmail.com', 'department_id' => 1],
            ['id' => 33, 'name' => 'លោក ញឹក វិបុល', 'email' => 'vibol@gmail.com', 'department_id' => 1],
            ['id' => 34, 'name' => 'លោក ពេញ សុខភាព', 'email' => 'penhsopheab@gmail.com', 'department_id' => 1],
            ['id' => 35, 'name' => 'លោក វ៉ាង​ សុវណ្ណ', 'email' => 'psovan@gmail.com', 'department_id' => 1],
            ['id' => 37, 'name' => 'teacher007', 'email' => 'teacher007@gmail.com', 'department_id' => 1],
        ];
        foreach ($professors as $prof) {
            User::updateOrCreate(
                ['email' => $prof['email']],
                [
                    'id' => $prof['id'],
                    'name' => $prof['name'],
                    'password' => Hash::make('password'),
                    'role' => 'professor',
                    'department_id' => $prof['department_id'],
                    'email_verified_at' => now(),
                    // 'is_verified' => true,
                ]
            );
        }
        $this->command->info('  Professors: ' . count($professors));

        // ---------- Users (Students) ----------
        $students = [
            ['id' => 16, 'name' => 'ញិល គង់', 'email' => 'kong@gmail.com', 'program_id' => 3, 'generation' => '16'],
            ['id' => 17, 'name' => 'អង ច័ន្ទសីហា', 'email' => null, 'program_id' => 3, 'generation' => '16'],
            ['id' => 18, 'name' => 'ប៉េង ស្រីនិច', 'email' => null, 'program_id' => 3, 'generation' => '16'],
            ['id' => 19, 'name' => 'ស្មី សៃលិក', 'email' => null, 'program_id' => 2, 'generation' => '16'],
            ['id' => 20, 'name' => 'ស្រួច ស្រេងស្រុី', 'email' => null, 'program_id' => 2, 'generation' => '16'],
            ['id' => 21, 'name' => 'វណ្ណា លីហួ', 'email' => 'lyhou@gmail.com', 'program_id' => 2, 'generation' => '16'],
            ['id' => 24, 'name' => 'លី ស្រីនាថ', 'email' => null, 'program_id' => 4, 'generation' => '17'],
            ['id' => 27, 'name' => 'អៀន ថងឌី', 'email' => null, 'program_id' => 3, 'generation' => '18'],
            ['id' => 28, 'name' => 'សិទ្ធ គិមសាន', 'email' => null, 'program_id' => 2, 'generation' => '16'],
            ['id' => 29, 'name' => 'រូ ភារុណ', 'email' => 'ronlove3344@gmail.com', 'program_id' => 3, 'generation' => '16'],
            ['id' => 36, 'name' => 'ហេង ខេមរិន្ទ', 'email' => 'hengkhemrin1@gmail.com', 'program_id' => 3, 'generation' => '16'],
        ];
        foreach ($students as $s) {
            User::updateOrCreate(
                ['id' => $s['id']],
                [
                    'name' => $s['name'],
                    'email' => $s['email'],
                    'password' => Hash::make('password'),
                    'role' => 'student',
                    'program_id' => $s['program_id'],
                    'generation' => $s['generation'],
                    'email_verified_at' => now(),
                ]
            );
        }

        // Auto-generate student_id_code for all students
        $generator = app(StudentIdGeneratorService::class);
        foreach ($students as $s) {
            $user = User::find($s['id']);
            if ($user && ! $user->student_id_code) {
                $studentId = $generator->generate((int) $s['program_id'], $s['generation']);
                $user->student_id_code = $studentId;
                $user->save();
            }
        }
        $this->command->info('  Students: ' . count($students));

        // ---------- User Profiles ----------
        $profiles = [
            ['user_id' => 16, 'full_name_km' => 'ញិល គង់', 'full_name_en' => 'NHEL KONG', 'gender' => 'male'],
            ['user_id' => 31, 'full_name_km' => 'សង សុីយូ', 'full_name_en' => 'sorng siyou', 'gender' => 'male', 'telegram_user' => 'botsiyou'],
            ['user_id' => 32, 'full_name_km' => 'ហេង​ ប៊ុនឌឿន', 'full_name_en' => 'heng bundern', 'gender' => 'male'],
            ['user_id' => 33, 'full_name_km' => 'ញឹក វិបុល', 'full_name_en' => 'nherk vibol', 'gender' => 'male'],
            ['user_id' => 34, 'full_name_km' => 'ពេញ សុខភាព', 'full_name_en' => 'penh sopheab', 'gender' => 'male'],
            ['user_id' => 35, 'full_name_km' => 'វ៉ាង សុវណ្ណ', 'full_name_en' => 'van sovan', 'gender' => 'male'],
        ];
        foreach ($profiles as $p) {
            UserProfile::updateOrCreate(['user_id' => $p['user_id']], $p);
        }
        $this->command->info('  User Profiles: ' . count($profiles));

        // ---------- Academic Year ----------
        AcademicYear::updateOrCreate(
            ['name' => '2025-2026'],
            ['start_date' => '2025-10-01', 'end_date' => '2026-08-30', 'is_current' => true, 'description' => 'Academic Year 2025-2026']
        );
        $this->command->info('  Academic Year: 2025-2026');

        // ---------- Rooms ----------
        $rooms = [
            ['id' => 1, 'room_number' => 'B10', 'capacity' => 30, 'wifi_qr_code' => 'https://ik.imagekit.io/x1cuwrrgc/room_wifi/wifi_qr_1769181566_cak3_tEQZ', 'location_of_room' => 'ជាន់ទី២', 'type_of_room' => 'បន្ទប់កុំព្យូទ័រ'],
            ['id' => 2, 'room_number' => 'A5', 'capacity' => 30, 'wifi_qr_code' => 'https://ik.imagekit.io/x1cuwrrgc/room_wifi/wifi_qr_1769181578_4799iIceZ', 'location_of_room' => 'ជាន់ទី១', 'type_of_room' => 'បន្ទប់កុំព្យូទ័រ'],
        ];
        foreach ($rooms as $r) {
            Room::updateOrCreate(['id' => $r['id']], $r);
        }
        $this->command->info('  Rooms: ' . count($rooms));

        // ---------- Courses ----------
        $courses = [
            ['id' => 9, 'department_id' => 1, 'generation' => '16', 'title_km' => 'Cyber Security', 'title_en' => 'Cyber Security', 'credits' => 3],
            ['id' => 10, 'department_id' => 1, 'generation' => '16', 'title_km' => 'Machine Learning(AI)', 'title_en' => 'Machine Learning(AI)', 'credits' => 3],
            ['id' => 14, 'department_id' => 1, 'generation' => '16', 'title_km' => 'Advance Data Communication', 'title_en' => 'Advance Data Communication', 'credits' => 3],
            ['id' => 15, 'department_id' => 1, 'generation' => '16', 'title_km' => 'test2', 'title_en' => 'test2', 'credits' => 3],
            ['id' => 17, 'department_id' => 1, 'generation' => '16', 'title_km' => 'dark', 'title_en' => 'dark', 'credits' => 3],
        ];
        foreach ($courses as $c) {
            Course::updateOrCreate(['id' => $c['id']], $c);
        }
        $this->command->info('  Courses: ' . count($courses));

        // ---------- Course-Program mapping ----------
        $coursePrograms = [
            ['course_id' => 17, 'program_id' => 2],
            ['course_id' => 9, 'program_id' => 2],
            ['course_id' => 9, 'program_id' => 3],
            ['course_id' => 10, 'program_id' => 2],
            ['course_id' => 10, 'program_id' => 3],
            ['course_id' => 14, 'program_id' => 3],
            ['course_id' => 15, 'program_id' => 2],
            ['course_id' => 15, 'program_id' => 3],
            ['course_id' => 17, 'program_id' => 3],
        ];
        foreach ($coursePrograms as $cp) {
            \DB::table('course_program')->updateOrInsert(
                ['course_id' => $cp['course_id'], 'program_id' => $cp['program_id']],
                $cp
            );
        }
        $this->command->info('  Course-Program mappings: ' . count($coursePrograms));

        // ---------- Course Offerings ----------
        $offerings = [
            ['id' => 13, 'course_id' => 9, 'lecturer_user_id' => 35, 'academic_year' => '2025-2026', 'semester' => 'ឆមាសទី២', 'capacity' => 20, 'start_date' => '2026-02-23', 'end_date' => '2026-05-23'],
            ['id' => 16, 'course_id' => 15, 'lecturer_user_id' => 32, 'academic_year' => '2025-2026', 'semester' => 'ឆមាសទី១', 'capacity' => 30, 'start_date' => '2026-02-23', 'end_date' => '2026-05-23'],
            ['id' => 18, 'course_id' => 17, 'lecturer_user_id' => 37, 'academic_year' => '2025-2026', 'semester' => 'ឆមាសទី២', 'capacity' => 20, 'start_date' => '2026-04-20', 'end_date' => '2026-05-20'],
            ['id' => 19, 'course_id' => 10, 'lecturer_user_id' => 31, 'academic_year' => '2025-2026', 'semester' => 'ឆមាសទី២', 'capacity' => 30, 'start_date' => '2026-04-23', 'end_date' => '2026-05-23'],
        ];
        foreach ($offerings as $o) {
            CourseOffering::updateOrCreate(['id' => $o['id']], $o);
        }
        $this->command->info('  Course Offerings: ' . count($offerings));

        // ---------- Course Offering-Program mapping ----------
        $offeringPrograms = [
            ['course_offering_id' => 13, 'program_id' => 3, 'generation' => '16'],
            ['course_offering_id' => 13, 'program_id' => 2, 'generation' => '16'],
            ['course_offering_id' => 16, 'program_id' => 3, 'generation' => '16'],
            ['course_offering_id' => 18, 'program_id' => 2, 'generation' => '17'],
            ['course_offering_id' => 19, 'program_id' => 2, 'generation' => '16'],
            ['course_offering_id' => 19, 'program_id' => 3, 'generation' => '16'],
        ];
        foreach ($offeringPrograms as $op) {
            \DB::table('course_offering_program')->updateOrInsert(
                ['course_offering_id' => $op['course_offering_id'], 'program_id' => $op['program_id'], 'generation' => $op['generation']],
                $op
            );
        }
        $this->command->info('  Offering-Program mappings: ' . count($offeringPrograms));

        // ---------- Schedules ----------
        $schedules = [
            ['id' => 71, 'course_offering_id' => 16, 'day_of_week' => 'Friday', 'room_id' => 1, 'start_time' => '07:00:00', 'end_time' => '08:30:00'],
            ['id' => 77, 'course_offering_id' => 18, 'day_of_week' => 'Friday', 'room_id' => 2, 'start_time' => '07:00:00', 'end_time' => '08:30:00'],
            ['id' => 78, 'course_offering_id' => 19, 'day_of_week' => 'Monday', 'room_id' => 1, 'start_time' => '10:30:00', 'end_time' => '12:00:00'],
            ['id' => 79, 'course_offering_id' => 19, 'day_of_week' => 'Tuesday', 'room_id' => 1, 'start_time' => '10:30:00', 'end_time' => '12:00:00'],
            ['id' => 80, 'course_offering_id' => 13, 'day_of_week' => 'Monday', 'room_id' => 1, 'start_time' => '07:00:00', 'end_time' => '08:30:00'],
            ['id' => 81, 'course_offering_id' => 13, 'day_of_week' => 'Monday', 'room_id' => 1, 'start_time' => '08:30:00', 'end_time' => '10:00:00'],
            ['id' => 82, 'course_offering_id' => 13, 'day_of_week' => 'Tuesday', 'room_id' => 1, 'start_time' => '07:00:00', 'end_time' => '08:30:00'],
            ['id' => 83, 'course_offering_id' => 13, 'day_of_week' => 'Tuesday', 'room_id' => 1, 'start_time' => '08:30:00', 'end_time' => '10:00:00'],
        ];
        foreach ($schedules as $s) {
            Schedule::updateOrCreate(['id' => $s['id']], $s);
        }
        $this->command->info('  Schedules: ' . count($schedules));

        // ---------- Student Program Enrollments ----------
        $studentIds = [16, 17, 18, 19, 20, 21, 24, 27, 28, 29, 36];
        $studentProgramMap = [
            16 => 3, 17 => 3, 18 => 3, 19 => 2, 20 => 2,
            21 => 2, 24 => 4, 27 => 3, 28 => 2, 29 => 3, 36 => 3,
        ];
        foreach ($studentIds as $sid) {
            StudentProgramEnrollment::updateOrCreate(
                ['student_user_id' => $sid],
                ['program_id' => $studentProgramMap[$sid], 'enrollment_date' => '2026-01-23', 'status' => 'active']
            );
        }
        $this->command->info('  Student Program Enrollments: ' . count($studentIds));

        // ---------- Student Course Enrollments ----------
        $enrollmentData = [
            // Offering 13 (Cyber Security)
            [16, 13, false], [17, 13, false], [18, 13, false], [29, 13, false], [36, 13, false],
            [19, 13, false], [20, 13, false], [21, 13, false], [28, 13, false],
            // Offering 16 (test2)
            [16, 16, false], [17, 16, false], [18, 16, false], [29, 16, false], [36, 16, false],
            // Offering 19 (Machine Learning)
            [19, 19, false], [20, 19, false], [21, 19, false], [28, 19, false],
            [16, 19, true], [17, 19, false], [18, 19, false], [29, 19, false], [36, 19, true],
        ];
        foreach ($enrollmentData as $e) {
            StudentCourseEnrollment::updateOrCreate(
                ['student_user_id' => $e[0], 'course_offering_id' => $e[1]],
                ['student_id' => $e[0], 'is_class_leader' => $e[2], 'enrollment_date' => '2026-02-24', 'status' => 'enrolled']
            );
        }
        $this->command->info('  Student Course Enrollments: ' . count($enrollmentData));

        // ---------- Grading Categories ----------
        $gradingCategories = [
            // Course 9
            ['course_id' => 9, 'name_km' => 'ការប្រឡងឆមាសកណ្តាល', 'name_en' => 'Midterm Exam', 'weight_percentage' => 15],
            ['course_id' => 9, 'name_km' => 'ការប្រឡងចុងឆមាស', 'name_en' => 'Final Exam', 'weight_percentage' => 50],
            ['course_id' => 9, 'name_km' => 'វត្តមាន', 'name_en' => 'Attendance', 'weight_percentage' => 15],
            ['course_id' => 9, 'name_km' => 'កិច្ចការស្រាវជ្រាវ', 'name_en' => 'Assignment', 'weight_percentage' => 20],
            ['course_id' => 9, 'name_km' => 'កិច្ចការ', 'name_en' => 'Quiz', 'weight_percentage' => 10],
            // Course 10
            ['course_id' => 10, 'name_km' => 'ការប្រឡងឆមាសកណ្តាល', 'name_en' => 'Midterm Exam', 'weight_percentage' => 15],
            ['course_id' => 10, 'name_km' => 'ការប្រឡងចុងឆមាស', 'name_en' => 'Final Exam', 'weight_percentage' => 50],
            ['course_id' => 10, 'name_km' => 'វត្តមាន', 'name_en' => 'Attendance', 'weight_percentage' => 15],
            ['course_id' => 10, 'name_km' => 'កិច្ចការស្រាវជ្រាវ', 'name_en' => 'Assignment', 'weight_percentage' => 20],
            ['course_id' => 10, 'name_km' => 'កិច្ចការ', 'name_en' => 'Quiz', 'weight_percentage' => 10],
            // Course 14
            ['course_id' => 14, 'name_km' => 'ការប្រឡងឆមាសកណ្តាល', 'name_en' => 'Midterm Exam', 'weight_percentage' => 15],
            ['course_id' => 14, 'name_km' => 'ការប្រឡងចុងឆមាស', 'name_en' => 'Final Exam', 'weight_percentage' => 50],
            ['course_id' => 14, 'name_km' => 'វត្តមាន', 'name_en' => 'Attendance', 'weight_percentage' => 15],
            ['course_id' => 14, 'name_km' => 'កិច្ចការស្រាវជ្រាវ', 'name_en' => 'Assignment', 'weight_percentage' => 20],
            ['course_id' => 14, 'name_km' => 'កិច្ចការ', 'name_en' => 'Quiz', 'weight_percentage' => 10],
            // Course 15
            ['course_id' => 15, 'name_km' => 'ការប្រឡងឆមាសកណ្តាល', 'name_en' => 'Midterm Exam', 'weight_percentage' => 15],
            ['course_id' => 15, 'name_km' => 'ការប្រឡងចុងឆមាស', 'name_en' => 'Final Exam', 'weight_percentage' => 50],
            ['course_id' => 15, 'name_km' => 'វត្តមាន', 'name_en' => 'Attendance', 'weight_percentage' => 15],
            ['course_id' => 15, 'name_km' => 'កិច្ចការស្រាវជ្រាវ', 'name_en' => 'Assignment', 'weight_percentage' => 20],
            ['course_id' => 15, 'name_km' => 'កិច្ចការ', 'name_en' => 'Quiz', 'weight_percentage' => 10],
            // Course 17
            ['course_id' => 17, 'name_km' => 'ការប្រឡងឆមាសកណ្តាល', 'name_en' => 'Midterm Exam', 'weight_percentage' => 15],
            ['course_id' => 17, 'name_km' => 'ការប្រឡងចុងឆមាស', 'name_en' => 'Final Exam', 'weight_percentage' => 50],
            ['course_id' => 17, 'name_km' => 'វត្តមាន', 'name_en' => 'Attendance', 'weight_percentage' => 15],
            ['course_id' => 17, 'name_km' => 'កិច្ចការស្រាវជ្រាវ', 'name_en' => 'Assignment', 'weight_percentage' => 20],
            ['course_id' => 17, 'name_km' => 'កិច្ចការ', 'name_en' => 'Quiz', 'weight_percentage' => 10],
        ];
        foreach ($gradingCategories as $gc) {
            GradingCategory::updateOrCreate(
                ['course_id' => $gc['course_id'], 'name_en' => $gc['name_en']],
                $gc
            );
        }
        $this->command->info('  Grading Categories: ' . count($gradingCategories));

        // ---------- Exams ----------
        $exams = [
            ['id' => 1, 'course_offering_id' => 13, 'title_km' => 'ប្រឡងពាក់កណ្ដាលឆមាសទី២', 'title_en' => 'Midterm', 'exam_date' => '2026-02-25', 'duration_minutes' => 120, 'max_score' => 15],
            ['id' => 2, 'course_offering_id' => 19, 'title_km' => 'ប្រឡងពាក់កណ្ដាលឆមាស', 'title_en' => 'Miterm exam', 'exam_date' => '2026-04-23', 'duration_minutes' => 120, 'max_score' => 15],
            ['id' => 3, 'course_offering_id' => 19, 'title_km' => 'ប្រឡងប្រចាំឆមាសទី២', 'title_en' => 'Final Exam', 'exam_date' => '2026-04-23', 'duration_minutes' => 120, 'max_score' => 50],
        ];
        foreach ($exams as $e) {
            Exam::updateOrCreate(['id' => $e['id']], $e);
        }
        $this->command->info('  Exams: ' . count($exams));

        // ---------- Assignments ----------
        $assignmentCategory = GradingCategory::where('course_id', 10)->where('name_en', 'Assignment')->first();
        if ($assignmentCategory) {
            Assignment::updateOrCreate(
                ['id' => 4],
                ['course_offering_id' => 19, 'grading_category_id' => $assignmentCategory->id, 'title_km' => 'កិច្ចការស្រាវជ្រាវ', 'title_en' => 'Assignment', 'due_date' => '2026-04-23', 'max_score' => 20]
            );
            $this->command->info('  Assignments: 1');
        }

        // ---------- Quizzes ----------
        $quizCategory = GradingCategory::where('course_id', 10)->where('name_en', 'Quiz')->first();
        if ($quizCategory) {
            Quiz::updateOrCreate(
                ['id' => 1],
                ['course_offering_id' => 19, 'grading_category_id' => $quizCategory->id, 'title_km' => 'កិច្ចការ', 'title_en' => 'Homework', 'max_score' => 5.00, 'quiz_date' => '2026-04-23']
            );
            $this->command->info('  Quizzes: 1');
        }

        // ---------- Exam Results ----------
        $examResults = [
            // Midterm exam (id=2) for offering 19 — all students got 15/15
            ['assessment_id' => 2, 'assessment_type' => 'exam', 'student_user_id' => 19, 'score_obtained' => 15, 'recorded_at' => '2026-04-23 14:57:08'],
            ['assessment_id' => 2, 'assessment_type' => 'exam', 'student_user_id' => 20, 'score_obtained' => 15, 'recorded_at' => '2026-04-23 14:57:08'],
            ['assessment_id' => 2, 'assessment_type' => 'exam', 'student_user_id' => 21, 'score_obtained' => 15, 'recorded_at' => '2026-04-23 14:57:09'],
            ['assessment_id' => 2, 'assessment_type' => 'exam', 'student_user_id' => 28, 'score_obtained' => 15, 'recorded_at' => '2026-04-23 14:57:09'],
            ['assessment_id' => 2, 'assessment_type' => 'exam', 'student_user_id' => 16, 'score_obtained' => 15, 'recorded_at' => '2026-04-23 14:57:09'],
            ['assessment_id' => 2, 'assessment_type' => 'exam', 'student_user_id' => 17, 'score_obtained' => 15, 'recorded_at' => '2026-04-23 14:57:10'],
            ['assessment_id' => 2, 'assessment_type' => 'exam', 'student_user_id' => 18, 'score_obtained' => 15, 'recorded_at' => '2026-04-23 14:57:10'],
            ['assessment_id' => 2, 'assessment_type' => 'exam', 'student_user_id' => 29, 'score_obtained' => 15, 'recorded_at' => '2026-04-23 14:57:10'],
            ['assessment_id' => 2, 'assessment_type' => 'exam', 'student_user_id' => 36, 'score_obtained' => 15, 'recorded_at' => '2026-04-23 14:57:11'],
            // Quiz (id=1) for offering 19 — student 16 got 5/5
            ['assessment_id' => 1, 'assessment_type' => 'quiz', 'student_user_id' => 16, 'score_obtained' => 5, 'recorded_at' => '2026-04-23 15:08:02'],
            // Assignment (id=4) for offering 19
            ['assessment_id' => 4, 'assessment_type' => 'assignment', 'student_user_id' => 19, 'score_obtained' => 14, 'recorded_at' => '2026-05-06 15:12:34'],
            ['assessment_id' => 4, 'assessment_type' => 'assignment', 'student_user_id' => 20, 'score_obtained' => 10, 'recorded_at' => '2026-05-06 15:12:34'],
            ['assessment_id' => 4, 'assessment_type' => 'assignment', 'student_user_id' => 21, 'score_obtained' => 12, 'recorded_at' => '2026-05-06 15:12:34'],
            ['assessment_id' => 4, 'assessment_type' => 'assignment', 'student_user_id' => 28, 'score_obtained' => 15, 'recorded_at' => '2026-05-06 15:12:34'],
            ['assessment_id' => 4, 'assessment_type' => 'assignment', 'student_user_id' => 16, 'score_obtained' => 15, 'recorded_at' => '2026-05-06 15:12:34'],
            ['assessment_id' => 4, 'assessment_type' => 'assignment', 'student_user_id' => 17, 'score_obtained' => 12, 'recorded_at' => '2026-05-06 15:12:34'],
            ['assessment_id' => 4, 'assessment_type' => 'assignment', 'student_user_id' => 18, 'score_obtained' => 15, 'recorded_at' => '2026-05-06 15:12:34'],
            ['assessment_id' => 4, 'assessment_type' => 'assignment', 'student_user_id' => 29, 'score_obtained' => 14, 'recorded_at' => '2026-05-06 15:12:34'],
            ['assessment_id' => 4, 'assessment_type' => 'assignment', 'student_user_id' => 36, 'score_obtained' => 15, 'recorded_at' => '2026-05-06 15:12:34'],
            // Final exam (id=3) for offering 19
            ['assessment_id' => 3, 'assessment_type' => 'exam', 'student_user_id' => 19, 'score_obtained' => 45, 'recorded_at' => '2026-04-23 15:57:40'],
            ['assessment_id' => 3, 'assessment_type' => 'exam', 'student_user_id' => 20, 'score_obtained' => 35, 'recorded_at' => '2026-04-23 15:57:40'],
            ['assessment_id' => 3, 'assessment_type' => 'exam', 'student_user_id' => 21, 'score_obtained' => 34, 'recorded_at' => '2026-04-23 15:57:40'],
            ['assessment_id' => 3, 'assessment_type' => 'exam', 'student_user_id' => 28, 'score_obtained' => 45, 'recorded_at' => '2026-04-23 15:57:40'],
            ['assessment_id' => 3, 'assessment_type' => 'exam', 'student_user_id' => 16, 'score_obtained' => 43, 'recorded_at' => '2026-04-23 15:57:40'],
            ['assessment_id' => 3, 'assessment_type' => 'exam', 'student_user_id' => 17, 'score_obtained' => 50, 'recorded_at' => '2026-04-23 15:57:40'],
            ['assessment_id' => 3, 'assessment_type' => 'exam', 'student_user_id' => 18, 'score_obtained' => 45, 'recorded_at' => '2026-04-23 15:57:40'],
            ['assessment_id' => 3, 'assessment_type' => 'exam', 'student_user_id' => 29, 'score_obtained' => 45, 'recorded_at' => '2026-04-23 15:57:40'],
            ['assessment_id' => 3, 'assessment_type' => 'exam', 'student_user_id' => 36, 'score_obtained' => 45, 'recorded_at' => '2026-04-23 15:57:40'],
        ];
        foreach ($examResults as $er) {
            ExamResult::updateOrCreate(
                ['assessment_id' => $er['assessment_id'], 'assessment_type' => $er['assessment_type'], 'student_user_id' => $er['student_user_id']],
                $er
            );
        }
        $this->command->info('  Exam Results: ' . count($examResults));

        $this->command->info('Class Management Data seeded successfully!');
    }
}
