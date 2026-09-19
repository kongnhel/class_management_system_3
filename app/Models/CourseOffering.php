<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany; // 💡 បានបន្ថែមការ import នេះ
use Illuminate\Database\Eloquent\SoftDeletes; // 💡 បានបន្ថែមការ import នេះ (សម្រាប់ schedules, attendanceRecords, etc.)

class CourseOffering extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'course_id',
        'department_id',
        'lecturer_user_id',
        'academic_year',
        'semester',
        'section',
        'capacity',
        'room_number',
        'generation',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'start_date' => 'date', // 💡 បានបន្ថែម cast នេះ
        'end_date' => 'date',   // 💡 បានបន្ថែម cast នេះ
        'deleted_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Get the course that this offering belongs to.
     */
    public function course(): BelongsTo // 💡 បញ្ជាក់ return type
    {
        // return $this->belongsTo(Course::class);
        return $this->belongsTo(Course::class, 'course_id');
    }

    /**
     * Get the lecturer (user) for this course offering.
     */
    public function lecturer(): BelongsTo // 💡 បញ្ជាក់ return type
    {
        return $this->belongsTo(User::class, 'lecturer_user_id');
    }

    /**
     * Get the student enrollments for this course offering.
     */
    public function studentCourseEnrollments(): HasMany // 💡 បញ្ជាក់ return type
    {
        return $this->hasMany(StudentCourseEnrollment::class, 'course_offering_id');
    }

    /**
     * Get the schedules for this course offering.
     */
    public function schedules(): HasMany // 💡 បញ្ជាក់ return type
    {
        return $this->hasMany(Schedule::class);
    }

    /**
     * Get the attendance records for this course offering.
     */
    public function attendanceRecords(): HasMany // 💡 បញ្ជាក់ return type
    {
        return $this->hasMany(AttendanceRecord::class);
    }

    /**
     * Get the assignments for this course offering.
     */
    public function assignments(): HasMany // 💡 បញ្ជាក់ return type
    {
        return $this->hasMany(Assignment::class);
    }

    /**
     * Get the exams for this course offering.
     */
    public function exams(): HasMany // 💡 បញ្ជាក់ return type
    {
        return $this->hasMany(Exam::class);
    }

    /**
     * Get the quizzes for this course offering.
     */
    public function quizzes(): HasMany // 💡 បញ្ជាក់ return type
    {
        return $this->hasMany(Quiz::class);
    }

    /**
     * Get the announcements for this course offering.
     */
    public function announcements(): HasMany // 💡 បញ្ជាក់ return type
    {
        return $this->hasMany(Announcement::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'room_number', 'room_number');
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'student_course_enrollments', 'course_offering_id', 'student_user_id')
            ->withPivot('is_class_leader');
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year', 'name');
    }
}
