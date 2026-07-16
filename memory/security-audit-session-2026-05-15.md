---
name: security-audit-session-2026-05-15
description: Laravel class management system security audit — fixes applied and remaining work
metadata:
  type: project
---

## Security Audit Session — 2026-05-15

User did a security audit of the codebase with me. We went through several files and fixed security issues.

### Files Fixed (by me):

1. **`api/index.php`** — Vercel entry point
   - Fixed `$_SERVER['SERVER_NAME']` user-controlled HTTP_HOST (safer fallback chain)
   - Fixed forced `HTTPS=on` / `SERVER_PORT=443` — now conditional (only set if not already set)

2. **`routes/api.php`** — API routes
   - Deleted `/api/debug` and `/api/debug-test` (critical: leaked APP_KEY, DB creds, Redis, Telegram tokens)
   - Added `throttle:120,1` to `/api/user` route

3. **`app/Http/Controllers/Auth/QrLoginController.php`** — QR login
   - Removed debug logging that wrote raw UUID token to disk (`Log::info('QR Scan Attempt', ['token' => $token, ...])`)
   - `finalizeLogin` route got `throttle:5,1` added in routes/web.php

4. **`app/Exceptions/Handler.php`** — Exception handling
   - Added `reportable()` hook with logging of all exceptions
   - Added `dontFlash` array (password fields never logged)
   - CSRF mismatches logged with IP, user_id, url
   - Removed duplicate 403 handling (already in bootstrap/app.php)

5. **`app/Exports/CourseStudentsExport.php`** — Student data export
   - Added nullsafety (`?->` throughout) for `$profile`, `$program`, `$student->email`
   - Fixed gender detection (handles M/Male/F/Female properly)
   - Added authorization inside export class itself (professor's own courses only, student's own enrollments, admins bypass)
   - Constructor updated to accept `$requestingUserId`
   - Controller updated to pass `auth()->id()`

6. **`app/Exports/StudentsGradeExport.php`** — Grade data export
   - Completely rewrote from skeleton (headings mismatch with map output)
   - Same authorization pattern as CourseStudentsExport
   - Added grade notes breakdown (attendance/midterm/final)
   - **Note: not yet wired to any route** — ready to use but unused

### Files Checked (no fixes needed):

- **`app/Events/QrLoginSuccessful.php`** — Token in channel name is safe (UUID v4 unguessable)
- **`public/index.php`** — Clean Bootstrap entry point
- **`bootstrap/app.php`** — Properly configured
- **QR login flow** — Cryptographically sound token generation with Str::uuid()

### Remaining Work:

1. **`app/Exports/StudentsGradeExport`** — needs a route wired up (e.g., `grades.export-excel`) in `ProfessorController`
2. **API routes** in `routes/api.php` — only one route left (`/api/user`), no public API consumers
3. **`.env` full credential exposure** — user was reminded to rotate exposed SECRETs (DB password, Redis password, Telegram tokens, etc.) in production
4. **Security headers middleware** (`AddSecurityHeaders.php`) — was registered globally, worth verifying it covers all edge cases

### How to Continue Next Session:

- Continue file-by-file audit (ask "go to [filename]" and wait for command before each fix)
- User prefers: 1. see analysis first, 2. "proceed" or "yes" to apply fixes, 3. work in order
- For Exports — always check if wired to routes, always add authorization + nullsafety
- Check `app/Models/ChatMessage.php` — had encryption added per security fixes, verify it's working

### Codebase Context:

- Laravel 12 + PHP 8.2
- Vercel deployment
- Roles: admin, professor, student (isAdmin/isProfessor/isStudent methods on User model)
- Integrations: Firebase, Pusher, Redis (Upstash), Telegram, ImageKit, Gemini AI
- `CHECK_USER_ROLE` middleware for route protection

### User Preferences:

- 300 concurrent users expected (rate limits set accordingly)
- Prefers proceeding fix-by-fix with confirmation
- Remembers session context for next time
- Wants to complete full codebase audit eventually