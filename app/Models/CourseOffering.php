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
        'program_id', // 💡 កែតម្រូវ: នេះក៏ត្រូវបានបន្ថែមទៅ migration ដែរ
        'course_id',
        'lecturer_user_id',
        'academic_year',
        'semester',
        'section',
        'capacity',
        'room_number',
        'generation', // 💡 បានបន្ថែម field នេះ
        'is_open_for_self_enrollment', // 💡 បានបន្ថែម field នេះ
        'start_date', // 💡 បានបន្ថែម field នេះ
        'end_date', // 💡 បានបន្ថែម field នេះ
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

    /**
     * Get the program through the course.
     * 💡 នេះគឺជាការកែសម្រួលដ៏សំខាន់
     */
    // public function program(): BelongsTo
    // {
    //     return $this->course->program(); // ចូលប្រើ Program តាមរយៈ Course
    // }
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
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

    public function targetPrograms()
    {
        return $this->belongsToMany(Program::class, 'course_offering_program')
            ->withPivot('generation') // យកទិន្នន័យ generation មកប្រើ
            ->withTimestamps();
    }

    /**
     * Get the academic year record that matches this offering's academic_year string.
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year', 'name');
    }
}
