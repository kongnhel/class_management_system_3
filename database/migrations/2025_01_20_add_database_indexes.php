<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add indexes to frequently queried columns
     * Improves performance on SELECT queries
     * Note: Using explicit index names to avoid MySQL 64-char limit
     */
    public function up(): void
    {
        // Index on attendance_records for fast queries by course and user
        if (Schema::hasTable('attendance_records')) {
            Schema::table('attendance_records', function (Blueprint $table) {
                if (!Schema::hasColumn('attendance_records', 'course_offering_id')) {
                    return; // Table might not have this column
                }
                $table->index('course_offering_id', 'idx_attend_course');
                $table->index('student_user_id', 'idx_attend_student');
                $table->index(['course_offering_id', 'student_user_id'], 'idx_attend_course_student');
            });
        }

        // Index on student_course_enrollments for fast enrollment lookups
        if (Schema::hasTable('student_course_enrollments')) {
            Schema::table('student_course_enrollments', function (Blueprint $table) {
                $table->index('student_user_id', 'idx_enroll_student');
                $table->index('course_offering_id', 'idx_enroll_course');
                $table->index(['student_user_id', 'course_offering_id'], 'idx_enroll_comp');
            });
        }

        // Index on exam_results for fast grade lookups
        if (Schema::hasTable('exam_results')) {
            Schema::table('exam_results', function (Blueprint $table) {
                $table->index('student_user_id', 'idx_exam_student');
                $table->index('assessment_id', 'idx_exam_assess');
                $table->index('assessment_type', 'idx_exam_type');
            });
        }

        // Index on course_offerings for finding lecturer's courses
        if (Schema::hasTable('course_offerings')) {
            Schema::table('course_offerings', function (Blueprint $table) {
                $table->index('lecturer_user_id', 'idx_co_lecturer');
                $table->index('program_id', 'idx_co_program');
                $table->index('academic_year', 'idx_co_year');
            });
        }

        // Index on schedules for finding rooms and times
        if (Schema::hasTable('schedules')) {
            Schema::table('schedules', function (Blueprint $table) {
                $table->index('course_offering_id', 'idx_sched_course');
                $table->index('room_id', 'idx_sched_room');
                $table->index('day_of_week', 'idx_sched_day');
            });
        }

        // Index on submissions for finding student work
        if (Schema::hasTable('submissions')) {
            Schema::table('submissions', function (Blueprint $table) {
                $table->index('student_user_id', 'idx_submit_student');
                $table->index('assignment_id', 'idx_submit_assign');
            });
        }

        // Index on announcements for role-based queries
        if (Schema::hasTable('announcements')) {
            Schema::table('announcements', function (Blueprint $table) {
                $table->index('poster_user_id', 'idx_announ_poster');
                $table->index('target_role', 'idx_announ_role');
                $table->index('created_at', 'idx_announ_created');
            });
        }

        // Index on users for role-based lookups
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                // if (Schema::hasColumn('users', 'email')) {
                //     $table->unique('email');
                // }
                if (Schema::hasColumn('users', 'role')) {
                    $table->index('role', 'idx_user_role');
                }
                if (Schema::hasColumn('users', 'deleted_at')) {
                    $table->index('deleted_at', 'idx_user_deleted');
                }
            });
        }

        // Index on chat_messages for conversation history
        if (Schema::hasTable('chat_messages')) {
            Schema::table('chat_messages', function (Blueprint $table) {
                $table->index('user_id', 'idx_chat_user');
                $table->index('created_at', 'idx_chat_created');
            });
        }

        // Index on notifications for notification queries
        if (Schema::hasTable('notifications')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->index('notifiable_type', 'idx_notif_type');
                $table->index('notifiable_id', 'idx_notif_id');
                $table->index('read_at', 'idx_notif_read');
                $table->index('created_at', 'idx_notif_created');
            });
        }

        // Index on grading_categories
        if (Schema::hasTable('grading_categories')) {
            Schema::table('grading_categories', function (Blueprint $table) {
                $table->index('course_id', 'idx_grade_course');
            });
        }
    }

    /**
     * Revert the indexes
     */
    public function down(): void
    {
        // Indexes are typically dropped automatically when rolling back
    }
};
