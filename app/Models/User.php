<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'student_id_code', // Added for students
        'department_id',   // Added for professors
        'program_id',      // Added for students
        'generation',  
            // Added for students
        'google_id',
        'avatar',

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_login_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }
    
    
    // Added for role check in authorization
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isProfessor(): bool
    {
        return $this->role === 'professor';
    }

    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    /**
     * Get the user profile associated with the user.
     */
    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }
    
    // In App\Models\User.php

// public function professorProfile()
// {
//     return $this->hasOne(ProfessorProfile::class);
// }

    /**
     * Get the department that the professor user belongs to.
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the program that the student user belongs to.
     */
    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * Get the faculties where this user is the dean.
     */
    public function facultiesAsDean()
    {
        return $this->hasMany(Faculty::class, 'dean_user_id');
    }

    /**
     * Get the departments where this user is the head.
     */
    public function departmentsAsHead()
    {
        return $this->hasMany(Department::class, 'head_user_id');
    }

    /**
     * Get the course offerings where this user is the lecturer.
     */
    public function courseOfferingsAsLecturer()
    {
        return $this->hasMany(CourseOffering::class, 'lecturer_user_id');
    }

    /**
     * Get the program enrollments for this student user.
     */
    public function studentProgramEnrollments()
    {
        return $this->hasMany(StudentProgramEnrollment::class, 'student_user_id');
    }

    /**
     * Get the course enrollments for this student user.
     */
    public function studentCourseEnrollments()
    {
        return $this->hasMany(StudentCourseEnrollment::class, 'student_user_id');
    }

    /**
     * Get the attendance records for this student user.
     */
    public function attendanceRecords()
    {
        return $this->hasMany(AttendanceRecord::class, 'student_user_id');
    }

    /**
     * Get the assignment submissions for this student user.
     */
    public function submissions()
    {
        return $this->hasMany(Submission::class, 'student_user_id');
    }

    /**
     * Get the exam results for this student user.
     */
    public function examResults()
    {
        return $this->hasMany(ExamResult::class, 'student_user_id');
    }

    /**
     * Get the quiz responses for this student user.
     */
    public function quizResponses()
    {
        return $this->hasMany(StudentQuizResponse::class, 'student_user_id');
    }

    /**
     * Get the announcements posted by this user.
     */
    public function announcementsPosted()
    {
        return $this->hasMany(Announcement::class, 'poster_user_id');
    }

    /**
     * Get the notifications for this user (if using custom notification table or morph relation).
     * If using default Laravel notifications, the Notifiable trait already handles this.
     */
    public function sentNotifications()
    {
        return $this->morphMany(Notification::class, 'notifiable');
    }

       public function studentEnrollments()
    {
        return $this->hasMany(StudentCourseEnrollment::class, 'student_user_id');
    }
        public function programs()
    {
        return $this->hasManyThrough(
            Program::class, // The final model we want to access
            CourseOffering::class, // The intermediate model
            'program_id', // Foreign key on the intermediate table...
            'id', // Foreign key on the User table...
            'id', // Local key on the User table...
            'course_offering_id', // Local key on the intermediate table...
        );
    }
    public function userProfile()
{
    return $this->hasOne(UserProfile::class);
}

    public function studentProfile()
{
    return $this->hasOne(StudentProfile::class);
}

    /**
     * Get the course offerings taught by the user (if they are a professor).
     */
    public function taughtCourseOfferings()
    {
        return $this->hasMany(CourseOffering::class, 'lecturer_user_id');
    }

    public function hasRole($role)
{
    // ឧបមាថាអ្នកមាន column ឈ្មោះ 'role' នៅក្នុង table 'users'
    // បើអ្នកប្រើតារាងផ្សេង សូមកែសម្រួលលក្ខខណ្ឌខាងក្រោម
    return $this->role === $role;
}
public function enrolledCourses()
{
    return $this->belongsToMany(
        \App\Models\CourseOffering::class, 
        'student_course_enrollments', 
        'student_user_id', 
        'course_offering_id'
    );
}

/**
 * Relationship ទៅកាន់តារាងវត្តមាន
 * ប្រើដើម្បីរាប់ចំនួនវត្តមានក្នុងរបាយការណ៍
 */
public function attendances()
{
    return $this->hasMany(\App\Models\AttendanceRecord::class, 'student_user_id');
}
    // You may also have other relationships here, like department() or program()
    // You can also add the studentCourseEnrollments relationship here for consistency
    
// app/Models/User.php



/**
 * គណនាពិន្ទុវត្តមាន (១៥%)
 */
// នៅក្នុង User.php Model
// public function getAttendanceScoreByCourse($course_id)
// {
//     $maxScore = 15;

//     // រាប់អវត្តមាន តែរាប់ចំពោះតែមុខវិជ្ជាដែលយើងចង់ដឹងប៉ុណ្ណោះ
//     $absentCount = $this->attendanceRecords()
//                         ->where('course_offering_id', $course_id) 
//                         ->where('status', 'absent')
//                         ->count();

//     $deduction = floor($absentCount / 2); 
//     $score = $maxScore - $deduction;

//     return max(0, $score);
// }

// public function getFinalAttendanceScore($course_id)
// {
//     $enrollment = \App\Models\StudentCourseEnrollment::where('student_user_id', $this->id)
//                     ->where('course_offering_id', $course_id)
//                     ->first();

//     // បើមានពិន្ទុដែលគ្រូបញ្ចូលដោយដៃ យកពិន្ទុដៃ
//     if ($enrollment && $enrollment->attendance_score_manual !== null) {
//         return $enrollment->attendance_score_manual;
//     }

//     // បើគ្មានទេ ប្រើ System គណនា
//     return $this->getAttendanceScoreByCourse($course_id);
// }

/**
 * គណនាពិន្ទុវត្តមានស្វ័យប្រវត្តិ (Auto Calculation)
 */
public function getAttendanceScoreByCourse($course_id)
{
    $maxScore = 15; // ពិន្ទុពេញ ១៥

    // ១. រាប់ចំនួនអវត្តមាន (Absent)
    $absentCount = $this->attendanceRecords()
                        ->where('course_offering_id', $course_id) 
                        ->where('status', 'absent')
                        ->count();

    // ២. រាប់ចំនួនសុំច្បាប់ (Permission)
    $permissionCount = $this->attendanceRecords()
                            ->where('course_offering_id', $course_id)
                            ->where('status', 'permission')
                            ->count();

    // --- រូបមន្តដកពិន្ទុ ---
    // អវត្តមាន ២ ដង ដក ១ ពិន្ទុ
    $absentDeduction = floor($absentCount / 2);
    
    // ច្បាប់ ៤ ដង ដក ១ ពិន្ទុ (បន្ថែមថ្មី)
    $permissionDeduction = floor($permissionCount / 4);

    // គណនាពិន្ទុចុងក្រោយ
    $score = $maxScore - ($absentDeduction + $permissionDeduction);

    return max(0, $score);
}
// // នៅក្នុង Student Model
// public function calculateAutoAttendanceScore($courseOfferingId) {
//     $totalSessions = Attendance::where('course_offering_id', $courseOfferingId)->count();
//     $presentSessions = Attendance::where('course_offering_id', $courseOfferingId)
//                         ->where('student_id', $this->id)
//                         ->where('status', 'present')
//                         ->count();
                        
//     if($totalSessions == 0) return 0;
    
//     $attendanceWeight = 15; // ឬទាញពី $courseOffering->attendance_weight
//     return ($presentSessions / $totalSessions) * $attendanceWeight;
// }
/**
 * យកពិន្ទុចុងក្រោយ (Manual Override vs Auto)
 */

/**
 * បន្ថែម Relationship ទៅកាន់តារាង StudentProgramEnrollment
 */
// public function programEnrollments()
// {
//     return $this->hasMany(\App\Models\StudentProgramEnrollment::class, 'student_user_id');
// }

}
