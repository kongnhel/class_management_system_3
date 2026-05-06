<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

/**
 * Activity Logger Service
 * 
 * Logs all critical actions for audit trail
 * Enables tracking of data changes, user actions, and security events
 */
class ActivityLogger
{
    /**
     * Log user action
     * 
     * @param string $action Action name (e.g., 'grade_updated', 'user_deleted')
     * @param string $description Human-readable description
     * @param array $data Additional data to log
     * @param string $severity 'info', 'warning', 'critical'
     */
    public static function log(
        string $action,
        string $description,
        array $data = [],
        string $severity = 'info'
    ): void {
        $userId = Auth::id() ?? 'unauthenticated';
        $userEmail = Auth::user()?->email ?? 'unknown';

        $logData = [
            'action' => $action,
            'user_id' => $userId,
            'user_email' => $userEmail,
            'description' => $description,
            'timestamp' => now()->toIso8601String(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ];

        if (!empty($data)) {
            $logData['data'] = $data;
        }

        // Log based on severity level
        match ($severity) {
            'critical' => Log::critical("AUDIT: {$action}", $logData),
            'warning' => Log::warning("AUDIT: {$action}", $logData),
            default => Log::info("AUDIT: {$action}", $logData),
        };
    }

    /**
     * Log grade update
     */
    public static function logGradeUpdate(
        int $studentId,
        int $courseOfferingId,
        float $oldScore,
        float $newScore
    ): void {
        self::log(
            'grade_updated',
            "Student #{$studentId} grade updated in course offering #{$courseOfferingId}",
            [
                'student_id' => $studentId,
                'course_offering_id' => $courseOfferingId,
                'old_score' => $oldScore,
                'new_score' => $newScore,
            ],
            'critical'
        );
    }

    /**
     * Log attendance record
     */
    public static function logAttendanceRecord(
        int $studentId,
        int $courseOfferingId,
        string $status
    ): void {
        self::log(
            'attendance_recorded',
            "Attendance recorded for student #{$studentId} in course #{$courseOfferingId}: {$status}",
            [
                'student_id' => $studentId,
                'course_offering_id' => $courseOfferingId,
                'status' => $status,
            ]
        );
    }

    /**
     * Log user creation
     */
    public static function logUserCreated(
        int $userId,
        string $email,
        string $role
    ): void {
        self::log(
            'user_created',
            "New user created: {$email} (Role: {$role})",
            [
                'new_user_id' => $userId,
                'email' => $email,
                'role' => $role,
            ],
            'warning'
        );
    }

    /**
     * Log user deletion
     */
    public static function logUserDeleted(
        int $userId,
        string $email
    ): void {
        self::log(
            'user_deleted',
            "User deleted: {$email} (ID: {$userId})",
            [
                'deleted_user_id' => $userId,
                'email' => $email,
            ],
            'critical'
        );
    }

    /**
     * Log unauthorized access attempt
     */
    public static function logUnauthorizedAccess(string $reason): void {
        self::log(
            'unauthorized_access',
            "Unauthorized access attempt: {$reason}",
            [],
            'critical'
        );
    }

    /**
     * Log API error
     */
    public static function logAPIError(
        string $service,
        int $statusCode,
        string $errorMessage
    ): void {
        self::log(
            'api_error',
            "API Error from {$service} (Status: {$statusCode})",
            [
                'service' => $service,
                'status_code' => $statusCode,
                'error_message' => $errorMessage,
            ],
            'warning'
        );
    }

    /**
     * Log rate limit exceeded
     */
    public static function logRateLimitExceeded(string $endpoint): void {
        self::log(
            'rate_limit_exceeded',
            "Rate limit exceeded on endpoint: {$endpoint}",
            ['endpoint' => $endpoint],
            'warning'
        );
    }
}
