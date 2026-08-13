<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions
        $permissions = [
            // User Management
            ['name' => 'users.view', 'description' => 'View users', 'group' => 'users'],
            ['name' => 'users.create', 'description' => 'Create users', 'group' => 'users'],
            ['name' => 'users.edit', 'description' => 'Edit users', 'group' => 'users'],
            ['name' => 'users.delete', 'description' => 'Delete users', 'group' => 'users'],
            ['name' => 'users.export', 'description' => 'Export users', 'group' => 'users'],

            // Course Management
            ['name' => 'courses.view', 'description' => 'View courses', 'group' => 'courses'],
            ['name' => 'courses.create', 'description' => 'Create courses', 'group' => 'courses'],
            ['name' => 'courses.edit', 'description' => 'Edit courses', 'group' => 'courses'],
            ['name' => 'courses.delete', 'description' => 'Delete courses', 'group' => 'courses'],

            // Course Offering Management
            ['name' => 'course_offerings.view', 'description' => 'View course offerings', 'group' => 'course_offerings'],
            ['name' => 'course_offerings.create', 'description' => 'Create course offerings', 'group' => 'course_offerings'],
            ['name' => 'course_offerings.edit', 'description' => 'Edit course offerings', 'group' => 'course_offerings'],
            ['name' => 'course_offerings.delete', 'description' => 'Delete course offerings', 'group' => 'course_offerings'],

            // Grade Management
            ['name' => 'grades.view', 'description' => 'View grades', 'group' => 'grades'],
            ['name' => 'grades.edit', 'description' => 'Edit grades', 'group' => 'grades'],
            ['name' => 'grades.export', 'description' => 'Export grades', 'group' => 'grades'],

            // Attendance Management
            ['name' => 'attendance.view', 'description' => 'View attendance', 'group' => 'attendance'],
            ['name' => 'attendance.edit', 'description' => 'Edit attendance', 'group' => 'attendance'],

            // System Settings
            ['name' => 'settings.view', 'description' => 'View settings', 'group' => 'settings'],
            ['name' => 'settings.edit', 'description' => 'Edit settings', 'group' => 'settings'],

            // Announcements
            ['name' => 'announcements.view', 'description' => 'View announcements', 'group' => 'announcements'],
            ['name' => 'announcements.create', 'description' => 'Create announcements', 'group' => 'announcements'],
            ['name' => 'announcements.edit', 'description' => 'Edit announcements', 'group' => 'announcements'],
            ['name' => 'announcements.delete', 'description' => 'Delete announcements', 'group' => 'announcements'],
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }

        // Create roles
        $adminRole = Role::create(['name' => 'admin', 'description' => 'System Administrator']);
        $professorRole = Role::create(['name' => 'professor', 'description' => 'Professor/Lecturer']);
        $studentRole = Role::create(['name' => 'student', 'description' => 'Student']);

        // Assign all permissions to admin
        $adminRole->permissions()->attach(Permission::all());

        // Assign permissions to professor
        $professorPermissions = Permission::whereIn('name', [
            'courses.view',
            'course_offerings.view',
            'grades.view',
            'grades.edit',
            'attendance.view',
            'attendance.edit',
            'announcements.view',
            'announcements.create',
        ])->get();
        $professorRole->permissions()->attach($professorPermissions);

        // Assign permissions to student
        $studentPermissions = Permission::whereIn('name', [
            'courses.view',
            'course_offerings.view',
            'grades.view',
            'attendance.view',
            'announcements.view',
        ])->get();
        $studentRole->permissions()->attach($studentPermissions);
    }
}
