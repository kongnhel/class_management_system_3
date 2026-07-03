<?php

use App\Http\Controllers\admin\AcademicYearController;
use App\Http\Controllers\admin\AdminAttendanceController;
use App\Http\Controllers\admin\AdminController;
use App\Http\Controllers\admin\AdminGradeController;
use App\Http\Controllers\admin\AnnouncementController;
use App\Http\Controllers\admin\BulkImportController;
use App\Http\Controllers\admin\CourseController;
use App\Http\Controllers\admin\CourseOfferingController;
use App\Http\Controllers\admin\DepartmentController;
use App\Http\Controllers\admin\FacultyController;
use App\Http\Controllers\admin\ProgramController;
use App\Http\Controllers\admin\RoomController;
use App\Http\Controllers\admin\StudentProgressionController;
use App\Http\Controllers\admin\SystemSettingController;
use App\Http\Controllers\admin\TransitionController;
use App\Http\Controllers\admin\UserController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\professor\ProfessorAttendanceController;
use App\Http\Controllers\professor\ProfessorController;
use App\Http\Controllers\professor\ProfessorCourseOfferingController;
use App\Http\Controllers\professor\ProfessorGradeController;
use App\Http\Controllers\professor\ProfessorNotificationController;
use App\Http\Controllers\professor\ProfessorProfileController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentRegistrationController;
use App\Http\Controllers\SmartAssistantController;
use App\Http\Controllers\Student\NotificationController;
use App\Http\Controllers\Student\StudentAttendanceController;
use App\Http\Controllers\Student\StudentController;
use App\Http\Controllers\Student\StudentGradeController;
use App\Http\Controllers\Student\StudentRoomController;
use App\Http\Controllers\StudentProfileController;
use App\Http\Controllers\TelegramController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// ========================================================
// AUTHENTICATED ROUTES - AI Chat (All Authenticated Users)
// ========================================================
Route::middleware(['auth', 'throttle:60,1'])->group(function () {
    Route::post('/ai/send', [SmartAssistantController::class, 'generateResponse'])->name('ai.send');
    Route::get('/ai/history', [SmartAssistantController::class, 'getHistory'])->name('ai.history');
    Route::post('/ai/clear-history', [SmartAssistantController::class, 'clearHistory'])->name('ai.clear-history');
});

Route::get('/locale/{locale}', function (string $locale) {
    if (! in_array($locale, ['km', 'en'])) {
        abort(400);
    }
    session(['locale' => $locale]);
    app()->setLocale($locale);
    return redirect()->back();
})->name('locale.switch');

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    if (Auth::check()) {
        $user = Auth::user();
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        if ($user->isProfessor()) {
            return redirect()->route('professor.dashboard');
        }
        if ($user->isStudent()) {
            return redirect()->route('student.dashboard');
        }

        return redirect()->route('auth.login');
    }

    return redirect()->route('login');
});

Route::get('/api/check-student/{code}', [StudentRegistrationController::class, 'checkStudent']);
/*
|--------------------------------------------------------------------------
| Authenticated & Verified Routes (Shared for all authenticated users)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        if (Auth::user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif (Auth::user()->isProfessor()) {
            return redirect()->route('professor.dashboard');
        } else { // Default to student role
            return redirect()->route('student.dashboard');
        }
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/update-picture', [ProfileController::class, 'updateProfilePicture'])->name('profile.update-picture');
});

Route::middleware(['auth', 'role:admin', 'throttle:120,1'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/get-courses-by-program-and-generation', [CourseOfferingController::class, 'getCoursesByProgramAndGeneration'])->name('get-courses-by-program-and-generation');
    Route::get('/users', [UserController::class, 'manageUsers'])->name('manage-users');
    Route::get('/users/create', [UserController::class, 'createUser'])->name('create-user');
    Route::post('/users', [UserController::class, 'storeUser'])->name('store-user');
    Route::get('/users/{user}/edit', [UserController::class, 'editUser'])->name('edit-user');
    Route::put('/users/{user}', [UserController::class, 'updateUser'])->name('update-user');
    Route::delete('/users/{user}', [UserController::class, 'deleteUser'])->name('delete-user');
    Route::get('/users/show/{user}', [UserController::class, 'showUser'])->name('show-user');
    Route::get('/users/export', [UserController::class, 'exportUsers'])->name('users.export');

    Route::get('/students/{student}/transition', [TransitionController::class, 'create'])->name('students.transition');
    Route::post('/students/{student}/transition', [TransitionController::class, 'store'])->name('students.transition.store');

    Route::get('/faculties', [FacultyController::class, 'index'])->name('manage-faculties');
    Route::get('/faculties/create', [FacultyController::class, 'create'])->name('create-faculty');
    Route::post('/faculties', [FacultyController::class, 'store'])->name('store-faculty');
    Route::get('/faculties/{faculty}/edit', [FacultyController::class, 'edit'])->name('edit-faculty');
    Route::put('/faculties/{faculty}', [FacultyController::class, 'update'])->name('update-faculty');
    Route::delete('/faculties/{faculty}', [FacultyController::class, 'destroy'])->name('delete-faculty');
    Route::get('/faculties/{faculty}/delete', [FacultyController::class, 'deleteFaculty'])->name('delete-faculty-get');
    // Route::get('/get-departments-by-faculty/{faculty}', [FacultyController::class, 'getDepartmentsByFaculty'])->name('get-departments-by-faculty');
    // Route::get('/get-departments-by-faculty/{faculty}', [AdminController::class, 'getDepartmentsByFaculty'])->name('get-departments-by-faculty');

    Route::get('/departments', [DepartmentController::class, 'index'])->name('manage-departments');
    Route::get('/departments/create', [DepartmentController::class, 'create'])->name('create-department');
    Route::post('/departments', [DepartmentController::class, 'store'])->name('store-department');
    Route::get('/departments/{department}/edit', [DepartmentController::class, 'edit'])->name('edit-department');
    Route::put('/departments/{department}', [DepartmentController::class, 'update'])->name('update-department');
    Route::delete('/departments/{department}', [DepartmentController::class, 'destroy'])->name('delete-department');
    Route::get('/get-departments-by-faculty/{faculty}', [DepartmentController::class, 'getDepartmentsByFaculty'])->name('get-departments-by-faculty');

    Route::get('/programs', [ProgramController::class, 'index'])->name('manage-programs');
    Route::get('/programs/create', [ProgramController::class, 'create'])->name('create-program');
    Route::post('/programs', [ProgramController::class, 'store'])->name('store-program');
    Route::get('/programs/{program}/edit', [ProgramController::class, 'edit'])->name('edit-program');
    Route::put('/programs/{program}', [ProgramController::class, 'update'])->name('update-program');
    Route::delete('/programs/{program}', [ProgramController::class, 'destroy'])->name('delete-program');

    Route::get('/courses', [CourseController::class, 'index'])->name('manage-courses');
    Route::get('/courses/create', [CourseController::class, 'create'])->name('create-course');
    Route::post('/courses', [CourseController::class, 'store'])->name('store-course');
    Route::get('/courses/{course}/edit', [CourseController::class, 'edit'])->name('edit-course');
    Route::put('/courses/{course}', [CourseController::class, 'update'])->name('update-course');
    Route::delete('/courses/{course}', [CourseController::class, 'destroy'])->name('delete-course');

    // Route::resource('course-offerings', CourseOfferingController::class);
    Route::get('/course-offerings', [CourseOfferingController::class, 'index'])->name('manage-course-offerings');
    Route::get('/course-offerings/create', [CourseOfferingController::class, 'create'])->name('create-course-offering');
    Route::post('/course-offerings', [CourseOfferingController::class, 'store'])->name('store-course-offering');
    Route::get('/course-offerings/{courseOffering}/edit', [CourseOfferingController::class, 'edit'])->name('edit-course-offering');
    // Route::put('/course-offerings/{courseOffering}', [CourseOfferingController::class, 'update'])->name('update-course-offering');
    Route::put('/course-offerings/{courseOffering}', [CourseOfferingController::class, 'update'])->name('course-offerings.update');
    Route::delete('/course-offerings/{courseOffering}', [CourseOfferingController::class, 'destroy'])->name('course-offerings.destroy');
    Route::get('/enroll-student', [CourseOfferingController::class, 'enrollStudentForm'])->name('enroll_student_form');
    Route::post('/perform-enrollment', [CourseOfferingController::class, 'performEnrollment'])->name('perform_enrollment');

    // Route::resource('rooms', RoomController::class);
    Route::get('/rooms', [RoomController::class, 'index'])->name('rooms.index');
    Route::get('/rooms/create', [RoomController::class, 'create'])->name('rooms.create');
    Route::post('/rooms', [RoomController::class, 'store'])->name('rooms.store');
    Route::get('/rooms/{room}', [RoomController::class, 'show'])->name('rooms.show');
    Route::get('/rooms/{room}/edit', [RoomController::class, 'edit'])->name('rooms.edit');
    Route::put('/rooms/{room}', [RoomController::class, 'update'])->name('rooms.update');
    Route::delete('/rooms/{room}', [RoomController::class, 'destroy'])->name('rooms.destroy');

    Route::get('/announcements', [AnnouncementController::class, 'index'])->name('announcements.index');
    Route::get('/announcements/create', [AnnouncementController::class, 'create'])->name('announcements.create');
    Route::post('/announcements/store', [AnnouncementController::class, 'store'])->name('announcements.store');
    Route::get('/announcements/{announcement}/edit', [AnnouncementController::class, 'edit'])->name('announcements.edit');
    Route::put('/announcements/{announcement}/update', [AnnouncementController::class, 'update'])->name('announcements.update');
    Route::delete('/announcements/{announcement}', [AnnouncementController::class, 'destroy'])->name('announcements.destroy');

    Route::get('/users/search', [UserController::class, 'searchUsers'])->name('users.search');
    Route::get('/get-courses-by-program/{program}', [AdminController::class, 'getCoursesByProgram'])->name('get-courses-by-program');
    Route::get('/course-offerings/{courseOffering}', [CourseOfferingController::class, 'show'])->name('show-course-offering');

    // Academic Year Management
    Route::get('/academic-years', [AcademicYearController::class, 'index'])->name('academic-years.index');
    Route::get('/academic-years/create', [AcademicYearController::class, 'create'])->name('academic-years.create');
    Route::post('/academic-years', [AcademicYearController::class, 'store'])->name('academic-years.store');
    Route::get('/academic-years/{academicYear}/edit', [AcademicYearController::class, 'edit'])->name('academic-years.edit');
    Route::put('/academic-years/{academicYear}', [AcademicYearController::class, 'update'])->name('academic-years.update');
    Route::delete('/academic-years/{academicYear}', [AcademicYearController::class, 'destroy'])->name('academic-years.destroy');
    Route::post('/academic-years/{academicYear}/set-current', [AcademicYearController::class, 'setCurrent'])->name('academic-years.set-current');

    // System Settings
    Route::get('/settings', [SystemSettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SystemSettingController::class, 'update'])->name('settings.update');

    // Grade Management
    Route::get('/grades', [AdminGradeController::class, 'index'])->name('grades.index');
    Route::get('/grades/{courseOffering}', [AdminGradeController::class, 'show'])->name('grades.show');
    Route::get('/grades/{courseOffering}/export', [AdminGradeController::class, 'exportGrades'])->name('grades.export');

    // Attendance Dashboard
    Route::get('/attendance', [AdminAttendanceController::class, 'index'])->name('attendance.index');
    Route::get('/attendance/{courseOffering}', [AdminAttendanceController::class, 'show'])->name('attendance.show');

    // // Bulk Import (hidden)
    // Route::get('/import', [BulkImportController::class, 'index'])->name('import.index');
    // Route::post('/import/users', [BulkImportController::class, 'importUsers'])->name('import.users');
    // Route::get('/import/template', [BulkImportController::class, 'downloadTemplate'])->name('import.template');

    // // Audit Logs (hidden)
    // Route::get('/audit-logs', [\App\Http\Controllers\admin\AuditLogController::class, 'index'])->name('audit-logs.index');
    // Route::get('/audit-logs/{auditLog}', [\App\Http\Controllers\admin\AuditLogController::class, 'show'])->name('audit-logs.show');

    // Student Progression
    Route::get('/progression', [StudentProgressionController::class, 'index'])->name('progression.index');
    Route::get('/progression/advance', [StudentProgressionController::class, 'advance'])->name('progression.advance');
    Route::post('/progression/advance', [StudentProgressionController::class, 'executeAdvance'])->name('progression.executeAdvance');
    Route::post('/progression/auto-graduate', [StudentProgressionController::class, 'autoGraduate'])->name('progression.autoGraduate');

});

/*
|--------------------------------------------------------------------------
| Professor Routes (Protected by 'role:professor' middleware)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:professor'])->prefix('professor')->name('professor.')->group(function () {

    Route::get('/dashboard', [ProfessorController::class, 'dashboard'])->name('dashboard');
    Route::get('/view-departments', [ProfessorController::class, 'viewDepartments'])->name('view-departments');
    Route::get('/view-programs', [ProfessorController::class, 'viewPrograms'])->name('view-programs');
    Route::get('/view-courses', [ProfessorController::class, 'viewCourses'])->name('view-courses');
    Route::get('/view-all-course-offerings', [ProfessorController::class, 'viewAllCourseOfferings'])->name('view-all-course-offerings');
    Route::get('/all-students', [ProfessorController::class, 'allStudents'])->name('all-students');
    Route::get('/my-course-offerings', [ProfessorCourseOfferingController::class, 'myCourseOfferings'])->name('my-course-offerings');
    Route::get('/course-offering/{offering_id}/grades', [ProfessorGradeController::class, 'manageGrades'])->name('manage-grades');
    Route::get('/course-offering/{offering_id}/attendance', [ProfessorController::class, 'manageAttendance'])->name('manage-attendance');
    Route::get('/course-offering/{offering_id}/assignments', [ProfessorController::class, 'manageAssignments'])->name('manage-assignments');
    Route::get('/course-offering/{offering_id}/exams', [ProfessorController::class, 'manageExams'])->name('manage-exams');
    Route::post('/course-offering/{offering_id}/exams', [ProfessorController::class, 'storeExam'])->name('store-exam');

    Route::get('/all-grades', [ProfessorGradeController::class, 'allGrades'])->name('grades.all');
    Route::get('/courses', [ProfessorGradeController::class, 'professorCourses'])->name('professor.courses');

    Route::get('/course-offerings/{offering_id}/assignments/{assignment}/edit', [ProfessorController::class, 'editAssignment'])->name('assignments.edit');
    Route::put('/course-offerings/{offering_id}/assignments/{assignment}', [ProfessorController::class, 'updateAssignment'])->name('assignments.update');
    Route::delete('/course-offerings/{offering_id}/assignments/{assignment}', [ProfessorController::class, 'destroyAssignment'])->name('assignments.destroy');
    Route::get('/course-offering/{offering_id}/exams/{exam}/edit', [ProfessorController::class, 'editExam'])->name('exams.edit');
    Route::put('/course-offering/{offering_id}/exams/{exam}', [ProfessorController::class, 'updateExam'])->name('exams.update');
    Route::delete('/course-offering/{offering_id}/exams/{exam}', [ProfessorController::class, 'destroyExam'])->name('exams.destroy');
    Route::get('/all-attendance', [ProfessorController::class, 'allAttendance'])->name('all-attendance');
    Route::post('/attendances', [ProfessorAttendanceController::class, 'storeAttendance'])->name('attendances.store');
    Route::put('/attendances/{attendance}', [ProfessorAttendanceController::class, 'updateAttendance'])->name('attendances.update');
    Route::delete('/attendances/{attendance}', [ProfessorAttendanceController::class, 'destroyAttendance'])->name('attendances.destroy');
    Route::get('/my-schedule', [ProfessorController::class, 'mySchedule'])->name('my-schedule');
    Route::get('/api/course-offerings-with-students', [ProfessorController::class, 'getCourseOfferingsWithStudents']);
    Route::get('/all-data', [ProfessorController::class, 'allDataView'])->name('all-data-view');
    Route::get('/course-offering/{offering_id}/students', [ProfessorController::class, 'getStudentsInCourseOffering'])->name('students.in-course-offering');
    Route::get('/course-offerings/{courseOffering}/students', [ProfessorController::class, 'showStudentsInCourse'])->name('professor.course-offerings.students.index');
    Route::get('/course-offerings/{courseOffering}/students/{student}', [ProfessorController::class, 'showStudentProfile'])->name('students.show');
    Route::get('/students/{student}', [ProfessorController::class, 'showStudentProfile'])->name('professor.students.show');
    Route::get('/profile/create', [ProfessorProfileController::class, 'create'])->name('profile.create');

    Route::get('/notifications', [ProfessorNotificationController::class, 'notificationsIndex'])->name('notifications.index');
    Route::get('/course-offerings/{courseOffering}/students', [ProfessorNotificationController::class, 'getStudentsForCourseOffering'])->name('course_offerings.students');
    Route::get('/notifications/create', [ProfessorNotificationController::class, 'createNotificationForm'])->name('notifications.create');
    Route::post('/notifications/store', [ProfessorNotificationController::class, 'notificationsStore'])->name('notifications.store');
    Route::get('/notifications/{id}/edit', [ProfessorNotificationController::class, 'notificationsEdit'])->name('notifications.edit');
    Route::put('/notifications/{id}', [ProfessorNotificationController::class, 'notificationsUpdate'])->name('notifications.update');
    Route::delete('/notifications/{id}', [ProfessorNotificationController::class, 'notificationsDestroy'])->name('notifications.destroy');
    Route::post('/notifications/{id}/mark-as-read', [ProfessorNotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::post('/notifications/mark-all-as-read', [ProfessorNotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
    Route::get('/course/{course}/grading-categories', [ProfessorController::class, 'manageGradingCategories'])->name('grading-categories.index');
    Route::post('/course/{course}/grading-categories', [ProfessorController::class, 'storeGradingCategory'])->name('grading-categories.store');
    Route::delete('/grading-categories/{category}', [ProfessorController::class, 'destroyGradingCategory'])->name('grading-categories.destroy');
    Route::get('/course-offerings/{offering_id}/assessments/create', [ProfessorGradeController::class, 'createAssessmentForm'])->name('assessments.create');
    Route::post('/course-offerings/{offering_id}/assessments', [ProfessorGradeController::class, 'storeAssessment'])->name('assessments.store');
    Route::get('/api/check-duplicate', [ProfessorGradeController::class, 'checkDuplicate']);
    Route::get('/assessments/{assessment_id}/grades/edit', [ProfessorGradeController::class, 'showGradeEntryForm'])->name('grades.edit');
    // Route::delete('/assessments/{id}', [ProfessorGradeController::class, 'destroyAssessment'])->name('assessments.destroy');
    Route::post('/assessments/{assessment_id}/grades', [ProfessorGradeController::class, 'storeGradesForAssessment'])->name('grades.store');
    Route::post('/course-offering/{offering_id}/assignments', [ProfessorController::class, 'storeAssignment'])->name('assignments.store');

    Route::post('/announcements/{announcement}/mark-as-read', [ProfessorController::class, 'markAsRead'])->name('announcements.markAsRead');
    Route::get('/profile', [ProfessorProfileController::class, 'showProfile'])->name('profile.show');
    Route::get('/profile/edit', [ProfessorProfileController::class, 'editProfile'])->name('profile.edit');
    Route::put('/profile', [ProfessorProfileController::class, 'updateProfile'])->name('profile.update');

    Route::get('/quizzes/{quiz}/questions', [ProfessorController::class, 'manageQuizQuestions'])->name('quizzes.questions.index');
    Route::post('/quizzes/{quiz}/questions', [ProfessorController::class, 'storeQuizQuestion'])->name('quizzes.questions.store');

    Route::post('/telegram/webhook', [TelegramController::class, 'handleWebhook']);

    Route::post('/send-grade-telegram/{enrollment_id}', [ProfessorController::class, 'sendGradeTelegram'])
        ->name('send_grade_telegram');
    Route::post('/course-offering/{id}/send-all-telegram', [ProfessorController::class, 'sendAllTelegram'])->name('send_all_telegram');
    Route::post('/update-telegram', [ProfessorController::class, 'updateTelegram'])->name('update_telegram');
    Route::patch('/course-offering/{offering_id}/student/{student_user_id}/toggle-leader',
        [ProfessorController::class, 'toggleClassLeader']
    )->name('toggleClassLeader');

    Route::get('/course-offerings/{courseOffering}/attendance', [ProfessorController::class, 'attendanceIndex'])->name('attendance.index');
    Route::post('/course-offerings/{courseOffering}/attendance', [ProfessorController::class, 'attendanceStore'])->name('attendance.store');
    Route::get('/course-offerings/{courseOffering}/attendance-report', [ProfessorController::class, 'attendanceReport'])
        ->name('attendance.report');
    Route::post('/grades/store/{assessment_id}', [ProfessorController::class, 'updateGrades'])
        ->name('grades.update-grades');
    Route::delete('/assessments/{id}', [ProfessorGradeController::class, 'destroyAssessment'])->name('assessments.destroy');
    Route::get('/assessments/{id}/edit/{type}',
        [ProfessorGradeController::class, 'assessmentEdit']
    )->name('assessments.edit');

    Route::put('/assessments/{id}/{type}',
        [ProfessorGradeController::class, 'update']
    )->name('assessments.update');

    Route::get('/course-offerings/{offering_id}/export-docx', [ProfessorController::class, 'exportStudentsDocx'])
        ->name('students.export-docx');
    Route::get('/course-offerings/{offering_id}/export-gradebook', [ProfessorController::class, 'exportGradebookDocx'])
        ->name('grades.export-docx');

    Route::post('/verify-location', [App\Http\Controllers\professor\ProfessorAttendanceController::class, 'verifyLocation'])
        ->name('verify-location');

    Route::post('/attendance/precheck', [App\Http\Controllers\professor\ProfessorAttendanceController::class, 'precheck'])
        ->name('attendance.precheck');

    Route::get('/course-offerings/{offering_id}/assignments/{assignment_id}/submissions', [App\Http\Controllers\professor\ProfessorSubmissionController::class, 'index'])
        ->name('submissions.index');
    Route::get('/course-offerings/{offering_id}/assignments/{assignment_id}/submissions/{submission_id}', [App\Http\Controllers\professor\ProfessorSubmissionController::class, 'show'])
        ->name('submissions.show');
    Route::post('/course-offerings/{offering_id}/assignments/{assignment_id}/submissions/{submission_id}/grade', [App\Http\Controllers\professor\ProfessorSubmissionController::class, 'grade'])
        ->name('submissions.grade');
    Route::get('/course-offerings/{offering_id}/assignments/{assignment_id}/submissions/{submission_id}/download', [App\Http\Controllers\professor\ProfessorSubmissionController::class, 'download'])
        ->name('submissions.download');

});

Route::prefix('grades')->name('grades.')->middleware(['auth', 'role:professor'])->group(function () {
    Route::get('/edit/{student_id}/{course_id}', [ProfessorGradeController::class, 'editAttendance'])
        ->name('edit-attendance');
    Route::post('/attendance/update', [ProfessorGradeController::class, 'updateAttendanceScore'])
        ->name('update-attendance');
});

Route::get('/professor/course-offering/{offering_id}/export', [CourseOfferingController::class, 'exportStudents'])
    ->middleware(['auth', 'role:professor'])
    ->name('professor.course-offering.export');
Route::get('/professor/attendance/history', [ProfessorAttendanceController::class, 'history'])
    ->middleware(['auth', 'role:professor'])
    ->name('professor.attendance.history');

Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('dashboard');
    Route::get('/my-grades', [StudentGradeController::class, 'myGrades'])->name('my-grades');
    Route::get('/my-enrolled-courses', [StudentGradeController::class, 'myEnrolledCourses'])->name('my-enrolled-courses');
    Route::get('/my-schedule', [StudentGradeController::class, 'mySchedule'])->name('my-schedule');
    Route::get('/my-assignments', [StudentGradeController::class, 'myAssignments'])->name('my-assignments');
    Route::get('/my-exams', [StudentGradeController::class, 'myExams'])->name('my-exams');
    Route::get('/my-quizzes', [StudentGradeController::class, 'myQuizzes'])->name('my-quizzes');
    Route::get('/quizzes/{quiz_id}', [StudentGradeController::class, 'takeQuiz'])->name('take-quiz');
    Route::post('/quizzes/{quiz_id}/submit', [StudentGradeController::class, 'submitQuiz'])->name('submit-quiz');
    Route::get('/{studentId}/enrolled-courses', [StudentGradeController::class, 'enrolledCourses'])->name('enrolled_courses');
    Route::get('/available-programs', [StudentGradeController::class, 'availablePrograms'])->name('available_programs');
    Route::get('/available-courses', [StudentGradeController::class, 'availableCourses'])->name('available_courses');
    Route::post('/enroll-self', [StudentGradeController::class, 'enrollSelf'])->name('enroll_self');
    Route::post('/enroll-program', [StudentGradeController::class, 'enrollProgram'])->name('enroll-program');
    Route::get('profile', [StudentProfileController::class, 'show'])->name('profile.show');
    Route::get('profile/edit', [StudentProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [StudentProfileController::class, 'update'])->name('profile.update');
    Route::get('/rooms', [StudentRoomController::class, 'rooms'])->name('rooms.index');
    Route::get('/my-timetable', [StudentController::class, 'myTimetable'])->name('my-timetable');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::patch('/announcements/{id}/read', [NotificationController::class, 'markAnnouncementAsRead'])->name('announcements.read');
    Route::patch('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');
    Route::get('/my-attendance', [StudentAttendanceController::class, 'myAttendance'])->name('my-attendance');

    Route::get('/class-leader/course/{courseOffering}/attendance', [StudentAttendanceController::class, 'leaderAttendance'])
        ->name('leader.attendance');
    Route::post('/class-leader/course/{courseOffering}/attendance', [StudentAttendanceController::class, 'storeLeaderAttendance'])
        ->name('leader.attendance.store');
    Route::get('/leader/attendance-report/{courseOffering}', [StudentAttendanceController::class, 'leaderAttendanceReport'])
        ->name('leader.report');
    Route::post('/student/update-telegram', [StudentController::class, 'updateTelegram'])
        ->name('update_telegram');
    Route::get('/student/scan', function () {
        return view('student.scan');
    })->name('scan');

    // API សម្រាប់ទទួលទិន្នន័យស្កែន
    Route::post('/student/process-scan', [AttendanceController::class, 'processScan'])
        ->name('process-scan');

});

Route::middleware(['auth', 'role:professor'])->group(function () {
    Route::get('/check-time', function () {
        dd(now()->toDateTimeString(), config('app.timezone'));
    });

    Route::get('assessment/{id}/export-csv', [ProfessorGradeController::class, 'exportCSV'])->name('grades.export');
    Route::post('/assessment/{id}/import-csv', [ProfessorGradeController::class, 'importCSV'])->name('grades.import');
});

Route::post('/auth/google/callback', [GoogleAuthController::class, 'handleCallback'])
    ->name('auth.google.callback');

Route::post('/user/link-google', [App\Http\Controllers\Auth\GoogleAuthController::class, 'linkAccount'])->name('user.link-google');

require __DIR__.'/auth.php';
