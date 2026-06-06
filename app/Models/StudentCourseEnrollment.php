<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentCourseEnrollment extends Model
{
    use HasFactory;

    // ឈ្មោះតារាងដែល Model នេះប្រើ
    protected $table = 'student_course_enrollments';

    // Field ដែលអាចបញ្ចូល ឬកែប្រែដោយ Mass Assignment
    protected $fillable = [
        'student_id',
        'student_user_id',
        'course_offering_id',
        'enrollment_date',
        'final_grade',
        'attendance_score_manual',
        'status',
    ];

    // កំណត់ប្រភេទទិន្នន័យសម្រាប់ Field ជាក់លាក់
    protected $casts = [
        'enrollment_date' => 'date', // នឹងបំប្លែងទៅជា Carbon Object ដោយស្វ័យប្រវត្តិ
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Get the student (user) that is enrolled in the course offering.
     * កំណត់ទំនាក់ទំនងថាការចុះឈ្មោះនេះជារបស់និស្សិតណា (User Model)។
     * ដោយសារ foreign key មិនមែន 'user_id' តាមលំនាំដើម យើងត្រូវបញ្ជាក់ 'student_user_id'។
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'student_user_id');
    }

    /**
     * Get the course offering that the student is enrolled in.
     * កំណត់ទំនាក់ទំនងថាការចុះឈ្មោះនេះគឺសម្រាប់មុខវិជ្ជាអ្វី (CourseOffering Model)។
     */
    public function courseOffering()
    {
        return $this->belongsTo(CourseOffering::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'student_user_id');
    }

    // នៅក្នុង Model StudentCourseEnrollment
    public function studentUser()
    {
        // សន្មតថា column ក្នុង table enrollments ដែលភ្ជាប់ទៅ user គឺ student_user_id
        return $this->belongsTo(\App\Models\User::class, 'student_user_id');
    }
    /**
     * It's also good practice to have a relationship named 'student'.
     * This is an alias for the 'user' relationship.
     */
}
