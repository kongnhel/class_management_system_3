# 🚀 SECURITY QUICK REFERENCE CARD

**Print this and keep it on your desk!**

---

## ⚡ MOST COMMON TASKS

### ✅ Log an Action

```php
use App\Services\ActivityLogger;

ActivityLogger::logGradeUpdate(123, 456, 75, 82);
ActivityLogger::logAttendanceRecord(123, 456, 'present');
ActivityLogger::logUserCreated(789, 'user@email.com', 'student');
ActivityLogger::logUnauthorizedAccess('Tried to access admin');
```

### ✅ Validate Input

```php
// Create Form Request: app/Http/Requests/CustomRequest.php
class StoreGradeRequest extends FormRequest {
    public function rules(): array {
        return [
            'score' => 'required|numeric|min:0|max:100',
            'comment' => 'nullable|string|max:500',
        ];
    }
}

// Use in controller
public function store(StoreGradeRequest $request) {
    $data = $request->validated(); // Safe data!
}
```

### ✅ Rate Limiting

```php
// In routes
Route::post('/endpoint', [Controller::class, 'method'])
    ->middleware('throttle:60,1'); // 60 per minute

// In controller
if (RateLimiter::tooManyAttempts($key, 5)) {
    return response()->json(['error' => 'Too many requests'], 429);
}
RateLimiter::hit($key, 60); // seconds
```

### ✅ Error Handling

```php
try {
    $result = SomeAPI::call();
} catch (\Exception $e) {
    Log::error("Error details", ['error' => $e->getMessage()]);
    return response()->json(['error' => 'Service error'], 500);
}
```

### ✅ Soft Deletes

```php
// Soft delete
$user->delete();

// View all (including deleted)
$users = User::withTrashed()->get();

// Restore
$user->restore();

// View only deleted
$deleted = User::onlyTrashed()->get();
```

### ✅ Encryption

```php
// Automatic in ChatMessage
$msg = ChatMessage::create(['message' => 'Secret']);
echo $msg->message; // Shows decrypted

// Manual encryption
$encrypted = encrypt('sensitive data');
$decrypted = decrypt($encrypted);
```

---

## ⚠️ SECURITY SINS - NEVER DO THIS

```
❌ DON'T: Log passwords, API keys, or tokens
❌ DON'T: Trust user input without validation
❌ DON'T: Disable CSRF for convenience
❌ DON'T: Use SELECT * in queries
❌ DON'T: Commit .env file to git
❌ DON'T: Store API keys in code
❌ DON'T: Show full error messages to users
❌ DON'T: Hardcode sensitive values
❌ DON'T: Use eval() or dynamic queries
❌ DON'T: Skip authorization checks
```

---

## ✅ SECURITY SAINTS - ALWAYS DO THIS

```
✅ DO: Use Form Requests for validation
✅ DO: Log only safe business data
✅ DO: Check authorization on sensitive routes
✅ DO: Use environment variables for secrets
✅ DO: Encrypt sensitive data
✅ DO: Add activity logging to critical actions
✅ DO: Use prepared statements (Eloquent does this)
✅ DO: Return generic error messages to users
✅ DO: Test with invalid/malicious input
✅ DO: Review security logs regularly
```

---

## 🔐 SECURITY CHECKLIST BEFORE COMMITTING

- [ ] No credentials in code?
- [ ] Input validated?
- [ ] Authorization checked?
- [ ] Errors logged (not shown)?
- [ ] Encryption used for sensitive data?
- [ ] Activity logged?
- [ ] Rate limiting applied?
- [ ] Tests pass?
- [ ] No `dd()` in code?
- [ ] No SQL injection possible?

---

## 📋 CRITICAL FILES REFERENCE

| What                | Where                                               |
| ------------------- | --------------------------------------------------- |
| Activity Logging    | `app/Services/ActivityLogger.php`                   |
| Input Validation    | `app/Http/Requests/`                                |
| Security Middleware | `app/Http/Middleware/AddSecurityHeaders.php`        |
| Error Logging       | `storage/logs/laravel.log`                          |
| Security Config     | `config/security.php`                               |
| Rate Limits         | `routes/web.php` or controller                      |
| Environment         | `.env` (NEVER commit!)                              |
| Docs                | `SECURITY.md` or `SECURITY_IMPLEMENTATION_GUIDE.md` |

---

## 🔍 DEBUGGING SECURITY ISSUES

### API Key Leaked?

```bash
# 1. Revoke immediately
# 2. Generate new key
# 3. Update .env
# 4. Check logs for unauthorized usage
grep "Gemini API Error" storage/logs/laravel.log
```

### Database Compromised?

```bash
# 1. Chat messages are encrypted ✅
# 2. Passwords are hashed ✅
# 3. Check audit logs for unauthorized access
grep "AUDIT:" storage/logs/laravel.log
```

### User Hacked?

```bash
# 1. Force password reset
# 2. Check activity logs
grep "user_id:123" storage/logs/laravel.log
# 3. Review their actions
# 4. Restore data if needed
```

---

## 🚨 EMERGENCY PROCEDURES

### 🔴 SQL Injection Suspected

```bash
# 1. Check for unusual queries in logs
# 2. Use Eloquent (prevents SQL injection)
# 3. Never pass raw user input to DB
# 4. Report to security team
```

### 🔴 XSS Attack Detected

```bash
# 1. Blade automatically escapes output ✅
# 2. Use {!! !!} only for trusted HTML
# 3. Sanitize user input
# 4. Review security headers
curl -I yoursite.com | grep CSP
```

### 🔴 Rate Limit Bypass

```bash
# 1. Check logs for suspicious IPs
grep "rate_limit_exceeded" storage/logs/laravel.log
# 2. Ban IP if needed
# 3. Adjust limits
# 4. Implement CAPTCHA if needed
```

---

## 📞 QUICK CONTACTS

**For Help:**

1. Read `SECURITY.md` first
2. Check `SECURITY_IMPLEMENTATION_GUIDE.md`
3. Review code comments
4. Ask in team chat

---

## 🎯 METRICS TO MONITOR

```bash
# API Performance
grep "AUDIT:" storage/logs/laravel.log | wc -l
# Goal: < 100 errors per day

# Rate Limit Hits
grep "rate_limit_exceeded" storage/logs/laravel.log
# Goal: < 10 per day (unless under attack)

# Decryption Failures
grep "Failed to decrypt" storage/logs/laravel.log
# Goal: 0

# Unauthorized Access Attempts
grep "unauthorized_access" storage/logs/laravel.log
# Goal: < 5 per day
```

---

**Last Updated:** January 20, 2025  
**Keep This Handy!** 📌
