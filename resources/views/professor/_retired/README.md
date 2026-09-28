# Retired professor pages

Moved here 2026-09-27. No route, controller method, or `@include` renders anything
in this folder any more. They are kept, not deleted, because most of them are a
complete UI whose controller code went missing — reviving a page should be a
matter of writing the method, not rebuilding the view.

Restore one by moving it back to its original path (paths are preserved) and
re-adding the route + method listed below.

| File here | Move back to | Route + method needed |
|---|---|---|
| `manage-exams.blade.php` | `professor/manage-exams.blade.php` | `GET /course-offering/{id}/exams` → `ProfessorController@manageExams` |
| `manage-assignments.blade.php` | `professor/manage-assignments.blade.php` | `GET /course-offering/{id}/assignments` → `ProfessorController@manageAssignments` |
| `exams/index.blade.php` | `professor/exams/index.blade.php` | same as `manage-exams` (newer generation) |
| `exams/edit.blade.php` | `professor/exams/edit.blade.php` | `GET .../exams/{exam}/edit` → `@editExam`, `PUT .../exams/{exam}` → `@updateExam` |
| `assignments/edit.blade.php` | `professor/assignments/edit.blade.php` | `GET .../assignments/{a}/edit` → `@editAssignment`, `PUT` → `@updateAssignment` |
| `manage-grades.blade.php` | `professor/manage-grades.blade.php` | not needed — the route `professor.manage-grades` is alive and now renders `professor.grades.index`. This file is the older version of that same page. |
| `gradebook/index.blade.php` | `professor/gradebook/index.blade.php` | `ProfessorController@showGradebook` was deleted; it returned `view('professor.gradebook')`, a name that matched no file. Recreate the method against `professor.gradebook.index` and add a route. The working `export-gradebook` route is untouched. |
| `grading-categories/index.blade.php` | `professor/grading-categories/index.blade.php` | `@manageGradingCategories`, `@storeGradingCategory`, `@destroyGradingCategory` |
| `notifications/edit.blade.php` | `professor/notifications/edit.blade.php` | `ProfessorNotificationController@notificationsEdit`, `@notificationsUpdate` |
| `all-course-offerings/index.blade.php` | `professor/all-course-offerings/index.blade.php` | `ProfessorController@viewAllCourseOfferings` |
| `all-exams.blade.php` | `professor/all-exams.blade.php` | no route ever existed; would need a new one |
| `all-assignments.blade.php` | `professor/all-assignments.blade.php` | no route ever existed; would need a new one |
| `attendance/attendance-modal.blade.php` | `professor/attendance/attendance-modal.blade.php` | still referenced by `app/Livewire/Teacher/AttendanceModal.php:198`. That component is never mounted (no `@livewire`/`livewire:` tag for it anywhere), so the view is unreachable — but the reference is real. The dashboard uses the Alpine twin, `professor.attendance.attendance-modal-alpine`. |

## Route registrations that were removed along with these

All 13 pointed at methods that did not exist, so every one returned a 500:

`manage-exams`, `store-exam`, `exams.edit`, `exams.update`, `exams.destroy`,
`manage-assignments`, `assignments.store`, `assignments.edit`, `assignments.update`,
`assignments.destroy`, `grading-categories.index`, `grading-categories.store`,
`grading-categories.destroy`.
