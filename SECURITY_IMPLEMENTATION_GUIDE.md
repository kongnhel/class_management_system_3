# 🛡️ SECURITY IMPLEMENTATION GUIDE

## Quick Reference for Developers

This guide explains how to use the new security features and best practices.

---

## 1️⃣ ACTIVITY LOGGING

### When to Log

Log all critical business logic operations:

```php
use App\Services\ActivityLogger;

// Log grade changes
ActivityLogger::logGradeUpdate(
    studentId: $student->id,
    courseOfferingId: $courseOffering->id,
    oldScore: $oldGrade,
    newScore: $newGrade
);

// Log attendance
ActivityLogger::logAttendanceRecord(
    studentId: $student->id,
    courseOfferingId: $course->id,
    status: 'present'
);

// Log user operations
ActivityLogger::logUserCreated($user->id, $user->email, 'student');
ActivityLogger::logUserDeleted($user->id, $user->email);

// Log unauthorized attempts
if ($user->role !== 'admin') {
    ActivityLogger::logUnauthorizedAccess("Attempted to access admin panel");
    abort(403);
}
```

### Viewing Logs

```bash
# All activity logs
tail -f storage/logs/laravel.log | grep "AUDIT:"

# Specific action
grep "grade_updated" storage/logs/laravel.log

# JSON format for analysis
grep "AUDIT:" storage/logs/laravel.log | grep grade_updated | jq .
```

---

## 2️⃣ INPUT VALIDATION

### Using Form Requests

Always use Form Request classes for validation:

```php
// ❌ WRONG - Validation in controller
public function store(Request $request) {
    $validated = $request->validate([...]);
}

// ✅ RIGHT - Use Form Request
public function store(SendAIChatMessageRequest $request) {
    $validated = $request->validated();
}
```

### Creating Custom Form Requests

```php
// app/Http/Requests/CustomRequest.php
class StoreGradeRequest extends FormRequest {
    public function authorize(): bool {
        return auth()->check();
    }

    public function rules(): array {
        return [
            'score' => 'required|numeric|min:0|max:100',
            'comment' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array {
        return [
            'score.required' => 'ពិន្ទុត្រូវតែផ្តល់ឱ្យ',
            'score.numeric' => 'ពិន្ទុត្រូវតែជាលេខ',
        ];
    }
}

// In controller
public function store(StoreGradeRequest $request) {
    $validated = $request->validated();
    // Use $validated - it's safe!
}
```

---

## 3️⃣ RATE LIMITING

### Apply to Routes

```php
// Limit to 60 requests per minute
Route::middleware(['throttle:60,1'])->group(function () {
    Route::post('/api/grades', [GradeController::class, 'store']);
});

// Custom rate limits
Route::post('/login', [LoginController::class, 'login'])
    ->middleware('throttle:5,1'); // 5 per minute

Route::post('/password/email', [PasswordResetLinkController::class, 'store'])
    ->middleware('throttle:3,60'); // 3 per hour
```

### Custom Rate Limiting in Controller

```php
use Illuminate\Support\Facades\RateLimiter;

public function store(Request $request) {
    $key = "endpoint:{$request->user()->id}";

    if (RateLimiter::tooManyAttempts($key, $limit = 5)) {
        return response()->json(['error' => 'Too many requests'], 429);
    }

    RateLimiter::hit($key, minutes: 1);

    // Process request
}
```

---

## 4️⃣ DATA ENCRYPTION

### Encrypted Fields

Sensitive data is automatically encrypted:

**ChatMessage:**

```php
$message = ChatMessage::create([
    'user_id' => auth()->id(),
    'message' => 'This is automatically encrypted', // 🔒
    'sender' => 'user',
]);

// When retrieved, it's automatically decrypted:
echo $message->message; // Original plaintext shown
```

### Add Encryption to Other Models

```php
class SensitiveData extends Model {
    public function setSensitiveFieldAttribute($value) {
        $this->attributes['sensitive_field'] = encrypt($value);
    }

    public function getSensitiveFieldAttribute($value) {
        try {
            return decrypt($value);
        } catch (\Exception $e) {
            Log::warning("Decryption failed");
            return $value;
        }
    }
}
```

---

## 5️⃣ ERROR HANDLING

### Proper Error Handling Pattern

```php
try {
    // Perform operation
    $result = ExternalAPI::call();
} catch (ValidationException $e) {
    // Log validation errors
    Log::warning("Validation failed", $e->errors());
    return response()->json(['errors' => $e->errors()], 422);

} catch (RateLimitException $e) {
    // Handle rate limiting
    ActivityLogger::logRateLimitExceeded('external-api');
    return response()->json(['error' => 'Service rate limited'], 429);

} catch (\Exception $e) {
    // Log full error server-side
    Log::error("Unexpected error", [
        'exception' => get_class($e),
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString(),
    ]);

    // Return generic message to user
    return response()->json([
        'error' => 'Something went wrong'
    ], 500);
}
```

### Never Expose Secrets

```php
// ❌ WRONG - API key might be in error message
$response = Http::post($url, $payload);
if ($response->failed()) {
    return response()->json(['error' => $response->json()], 500);
}

// ✅ RIGHT - Log details server-side, generic message to user
$response = Http::post($url, $payload);
if ($response->failed()) {
    Log::error("API call failed", [
        'status' => $response->status(),
        'response' => $response->json(),
    ]);
    return response()->json([
        'error' => 'Service temporarily unavailable'
    ], 500);
}
```

---

## 6️⃣ SOFT DELETES

### Using Soft Deletes

```php
// Delete (soft delete - record not removed)
$user->delete();

// View all (including soft deleted)
User::withTrashed()->get();

// View only soft deleted
User::onlyTrashed()->get();

// Restore
$user->restore();

// Force delete (permanent)
$user->forceDelete();

// Check if soft deleted
$user->trashed(); // Returns true/false
```

### In Queries

```php
// By default, soft deleted records are excluded
User::all(); // Doesn't include deleted users

// To include deleted records
User::withTrashed()->where('role', 'student')->get();

// Only deleted
User::onlyTrashed()->get();

// Query specific user (excluded from results if deleted)
$user = User::find(1); // null if user is soft deleted

// Query including deleted
$user = User::withTrashed()->find(1); // Returns user even if deleted
```

---

## 7️⃣ SECURITY HEADERS

Automatically added to all responses:

- ✅ `X-Content-Type-Options: nosniff` - Prevent MIME sniffing
- ✅ `X-Frame-Options: DENY` - Prevent clickjacking
- ✅ `X-XSS-Protection: 1; mode=block` - XSS protection
- ✅ `Strict-Transport-Security` - Force HTTPS
- ✅ `Content-Security-Policy` - Script/style restrictions
- ✅ `Referrer-Policy` - Control referrer info
- ✅ `Permissions-Policy` - Disable geolocation, camera, etc.

**Verify Headers:**

```bash
curl -I http://localhost:8000
# Check X-Content-Type-Options, X-Frame-Options, etc.
```

---

## 8️⃣ COMMON SECURITY PATTERNS

### Protecting Admin Routes

```php
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::post('/users/{user}', [UserController::class, 'update'])->name('users.update');
});

// In middleware (already implemented):
class CheckUserRole {
    public function handle($request, $next, $role) {
        if (auth()->user()?->role !== $role) {
            ActivityLogger::logUnauthorizedAccess("Role check failed");
            abort(403);
        }
        return $next($request);
    }
}
```

### Protecting Sensitive Data Access

```php
// ❌ WRONG - No authorization
$grade = Grade::find($id);

// ✅ RIGHT - Check authorization
$grade = Grade::find($id);
if ($grade->courseOffering->lecturer_id !== auth()->id()) {
    abort(403, 'Unauthorized');
}
```

### Checking Ownership

```php
$submission = Submission::find($id);

// Verify student owns submission
if ($submission->student_id !== auth()->id()) {
    ActivityLogger::logUnauthorizedAccess("Unauthorized submission access");
    abort(403);
}

// Verify professor teaches the course
if ($submission->assignment->courseOffering->lecturer_id !== auth()->id()) {
    abort(403);
}
```

---

## 9️⃣ ENVIRONMENT VARIABLES

### Required Security Variables

Create `.env` file:

```env
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:your-encryption-key

# API Keys (NEVER commit to git!)
GEMINI_API_KEY=your-api-key
FIREBASE_API_KEY=your-firebase-key
TELEGRAM_BOT_TOKEN=your-telegram-token

# Database
DB_CONNECTION=mysql
DB_HOST=localhost
DB_USERNAME=user
DB_PASSWORD=secure-password

# Mail
MAIL_FROM_ADDRESS=noreply@example.com

# Session security
SESSION_DRIVER=cookie
SESSION_SECURE_COOKIES=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=strict

# CSRF
CSRF_TRUSTED_HOSTS=localhost,127.0.0.1
```

### .gitignore

```
.env
.env.local
*.key
storage/logs/*
storage/framework/sessions/*
node_modules/
vendor/
```

---

## 🔟 TESTING SECURITY

### Test Rate Limiting

```php
// tests/Feature/RateLimitTest.php
test('ai chat has rate limiting', function () {
    $user = User::factory()->create();

    for ($i = 0; $i < 10; $i++) {
        $response = $this->actingAs($user)
            ->post('/ai-chat/send', ['message' => 'Test']);

        if ($i < 5) {
            $response->assertStatus(200);
        } else {
            $response->assertStatus(429); // Too many requests
        }
    }
});
```

### Test Authorization

```php
test('only professor can grade course', function () {
    $professor = User::factory()->professor()->create();
    $student = User::factory()->student()->create();
    $course = CourseOffering::factory()->create(['lecturer_user_id' => $professor->id]);

    // Student cannot grade
    $response = $this->actingAs($student)->post('/professor/grades', [
        'course_id' => $course->id,
    ]);
    $response->assertForbidden();

    // Professor can grade
    $response = $this->actingAs($professor)->post('/professor/grades', [
        'course_id' => $course->id,
    ]);
    $response->assertSuccessful();
});
```

### Test Input Validation

```php
test('ai chat validates input', function () {
    $user = User::factory()->create();

    // Too long message
    $response = $this->actingAs($user)->post('/ai-chat/send', [
        'message' => str_repeat('a', 1001),
    ]);
    $response->assertInvalid('message');

    // Empty message
    $response = $this->actingAs($user)->post('/ai-chat/send', [
        'message' => '',
    ]);
    $response->assertInvalid('message');
});
```

---

## 📋 DEPLOYMENT CHECKLIST

- [ ] All environment variables set in `.env.production`
- [ ] `APP_DEBUG=false`
- [ ] `APP_ENV=production`
- [ ] HTTPS enabled on server
- [ ] Migrations run: `php artisan migrate --force`
- [ ] Storage permissions correct: `chmod 775 storage bootstrap/cache`
- [ ] Cron job configured for scheduler
- [ ] Log rotation configured
- [ ] Backups configured
- [ ] Monitoring set up for errors
- [ ] Rate limiting verified
- [ ] Security headers verified: `curl -I https://yoursite.com`

---

## ⚠️ COMMON MISTAKES TO AVOID

❌ **DON'T:**

- Commit `.env` file to git
- Log passwords or API keys
- Trust user input
- Disable CSRF for convenience
- Use `dd()` in production
- Skip validation
- Store API keys in code
- Use `SELECT *` in queries

✅ **DO:**

- Use `.env.example` as template
- Log only safe data
- Validate all input
- Keep CSRF protection
- Use proper error handling
- Always validate before processing
- Use environment variables
- Select only needed columns

---

**Last Updated:** January 20, 2025  
**Version:** 1.0  
**Status:** Production Ready ✅
