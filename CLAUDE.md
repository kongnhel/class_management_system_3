# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with this repository.

## Project

This is a Laravel 12 class-management application for an educational institution. It has separate admin, professor, and student workflows for academic setup, course offerings, enrollment, grading, attendance, schedules, announcements, and notifications.

The repository's README is the stock Laravel README, so the application-specific guidance is documented here and in the source code.

## Commands

### Initial setup

```bash
composer install
npm install
```

If `.env` does not exist, copy `.env.example` to `.env` and generate the application key:

```bash
# PowerShell
Copy-Item .env.example .env
php artisan key:generate

# Then create/update the configured database
php artisan migrate
```

The example environment uses SQLite at `database/database.sqlite`, with database-backed sessions, cache, and queues. It also contains configuration points for MySQL, Redis, mail, broadcasting, and external integrations.

### Development

```bash
# Run the HTTP server, queue worker, log viewer, and Vite together
composer run dev

# Or run individual processes
php artisan serve
php artisan queue:listen --tries=1
php artisan pail --timeout=0
npm run dev
```

Useful application commands:

```bash
php artisan route:list
php artisan optimize:clear
php artisan migrate
php artisan db:seed

# Destructive: replace the database and run all seeders
php artisan migrate:fresh --seed
```

### Tests

Tests use Pest through Laravel's test runner. PHPUnit's test suites are `tests/Unit` and `tests/Feature`.

```bash
# All tests
php artisan test

# One test file
php artisan test tests/Feature/Auth/AuthenticationTest.php

# Filter by test or method name
php artisan test --filter="user can authenticate"

# Direct Pest invocation, if needed
php vendor/bin/pest
```

The test configuration uses array cache/session and synchronous queues. Database test overrides are commented out in `phpunit.xml`, so enable an appropriate test database in the environment when a test needs database isolation.

### Frontend and formatting

```bash
# Production asset build
npm run build

# Format PHP with Laravel Pint
php vendor/bin/pint

# Check formatting without changing files
php vendor/bin/pint --test
```

There is no PHPStan/Psalm script configured in `composer.json`; do not use the old `php artisan code:analyse` command from earlier documentation.

## Architecture

### Application entry points and routing

- `bootstrap/app.php` configures the Laravel 12 application, loads `routes/web.php`, `routes/api.php`, `routes/console.php`, and `routes/channels.php`, aliases the custom `role` middleware, and appends security headers.
- `routes/web.php` is the main application map. It contains shared authenticated routes, the admin/professor/student route groups, QR attendance endpoints, AI chat endpoints, locale switching, Google account routes, and the inclusion of `routes/auth.php`.
- `routes/auth.php` contains the Breeze-style guest and authenticated login, registration, password, email verification, and logout routes.
- Admin routes use the `admin` prefix/name group and `auth`, `role:admin`, and throttling. Professor and student routes use corresponding prefixes/name groups and `role:professor` or `role:student`.
- `app/Http/Middleware/CheckUserRole.php` implements the `role` alias. Roles are stored as strings in `users.role`; `User::isAdmin()`, `isProfessor()`, and `isStudent()` are commonly used by redirects and views.

When changing a feature, trace its route, role middleware, controller, model/query, and Blade/JavaScript caller together. Many endpoints are conventional web routes rather than a separately versioned API.

### Domain model

The central academic flow is:

```text
Faculty -> Department -> Program -> Course -> CourseOffering
                                                  |
                           lecturer, students/enrollments, schedules,
                           room, assignments, exams, quizzes, attendance,
                           announcements, and grading categories
```

The exact relationships are represented by Eloquent models and migrations; `CourseOffering` is the main aggregate for professor and student course work. Student membership is stored through the `student_course_enrollments` table, and the offering also has a many-to-many `students` relationship.

`User` is both the authentication model and the link to role-specific data. It has student/professor profiles, department/program links, taught offerings, enrollments, attendance records, submissions, exam results, notifications, and soft deletes.

### Grading and attendance

- Assessment data is split across assignments, exams, quizzes, submissions/results, and configurable `GradingCategory` records.
- `app/Services/GradingService.php` maps total scores out of 100 to the application's letter-grade scale. The documented model is attendance out of 15 plus other assessments out of 85.
- `User::getAttendanceScoreByCourse()` calculates the automatic attendance score from `AttendanceRecord` rows: one point is deducted for each two absences and each four permissions, with a floor of zero from a maximum of 15.
- Professor attendance routes create/manage sessions and records; student QR scanning is handled by `AttendanceController`, which validates the token, expiry, enrollment, and duplicate scan before creating a present record. Closing attendance can create absent records for enrolled students who did not scan.
- Attendance and grade changes often affect exports and notification flows, so check the related professor/student controllers and export classes when changing scoring behavior.

### Controllers, views, and frontend

Controllers are grouped by responsibility under `app/Http/Controllers/`:

- `admin/` manages users and academic configuration such as faculties, departments, programs, courses, offerings, rooms, years, imports, grades, and attendance dashboards.
- `professor/` manages assigned offerings, assessments, grade entry/import/export, attendance, submissions, notifications, profiles, and Telegram actions.
- `Student/` provides enrollment, grades, schedules, rooms, attendance, notifications, and profile pages.
- `Auth/` contains authentication-specific controllers, including Google/phone/QR-related flows.
- Root-level controllers contain shared flows such as profile, attendance scanning, student registration, Telegram, and the AI assistant.

Blade views are under `resources/views/` with role-specific directories (`admin`, `professor`, `student`) plus `auth`, `layouts`, `components`, and Livewire views. `resources/views/layouts/app.blade.php` and `navigation.blade.php` are the main authenticated shell.

Vite builds `resources/css/app.css` and `resources/js/app.js` from `vite.config.js`. The frontend uses Tailwind CSS, Alpine.js, Axios, Laravel Echo/Pusher, and small feature scripts such as `resources/js/ai-chat.js` and `resources/js/echo.js`. Livewire is a Composer dependency and has a custom JavaScript route in `routes/web.php`.

### Integrations and supporting code

Integration code is spread between controllers, events, and services in `app/Services/`. Relevant dependencies/features include:

- Firebase authentication/messaging, Google account linking, and QR login.
- Pusher/Echo broadcasting and database notifications.
- Telegram grade/notification delivery through `TelegramClientService` and related controllers.
- Cloudinary/ImageKit media storage.
- Excel import/export, PDF generation, DOCX generation, and QR code generation.
- `StudentProgressionService`, `ActivityLogger`, `OtpService`, and `AIContextService` for cross-cutting workflows.

These features depend on environment credentials and external services; local core development can use the defaults in `.env.example`, while integration-specific tests need their corresponding configuration.

## Where to look first

- `routes/web.php` — route names, role boundaries, and feature entry points.
- `bootstrap/app.php` and `app/Http/Middleware/CheckUserRole.php` — middleware registration and authorization behavior.
- `app/Models/User.php` — roles and user-centered relationships.
- `app/Models/CourseOffering.php` — course offering relationships and enrollment access.
- `app/Services/GradingService.php` — letter-grade thresholds.
- `app/Http/Controllers/AttendanceController.php` and `app/Http/Controllers/professor/` — QR/professor attendance flows.
- `database/migrations/` and `database/seeders/` — schema and initial data.
- `app/Exports/` — grade, enrollment, and user export implementations.
- `resources/views/layouts/`, `resources/views/{admin,professor,student}/`, and `resources/js/` — UI and browser-side behavior.
- `tests/Feature/` and `tests/Unit/` — current Pest coverage, primarily authentication/profile scaffolding.
