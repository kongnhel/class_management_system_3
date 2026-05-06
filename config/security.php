<?php

/**
 * Security Configuration
 * This file defines security policies and rules for the application
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Rate Limiting Configuration
    |--------------------------------------------------------------------------
    | Configure rate limits for sensitive operations
    */
    'rate_limits' => [
        'login' => '5,1',           // 5 attempts per minute
        'password_reset' => '3,60', // 3 attempts per hour
        'api_calls' => '60,1',      // 60 calls per minute
        'ai_chat' => '5,1',         // 5 messages per minute
        'file_upload' => '10,1',    // 10 uploads per minute
    ],

    /*
    |--------------------------------------------------------------------------
    | Encryption Configuration
    |--------------------------------------------------------------------------
    | Control which data fields should be encrypted
    */
    'encrypted_fields' => [
        'chat_messages' => ['message'],
        'notifications' => ['data'],
        'submissions' => ['file_path'], // Optional - for sensitive files
    ],

    /*
    |--------------------------------------------------------------------------
    | Audit Logging Configuration
    |--------------------------------------------------------------------------
    | Events to log for audit trail
    */
    'audit_events' => [
        'grade_updated' => true,
        'attendance_recorded' => true,
        'user_created' => true,
        'user_deleted' => true,
        'course_offering_updated' => true,
        'assessment_graded' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Headers
    |--------------------------------------------------------------------------
    | Security headers to send with all responses
    */
    'security_headers' => [
        'X-Content-Type-Options' => 'nosniff',
        'X-Frame-Options' => 'DENY',
        'X-XSS-Protection' => '1; mode=block',
        'Strict-Transport-Security' => 'max-age=31536000; includeSubDomains',
        'Content-Security-Policy' => "default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'",
    ],

    /*
    |--------------------------------------------------------------------------
    | Sensitive Routes
    |--------------------------------------------------------------------------
    | Routes that require extra security checks
    */
    'sensitive_routes' => [
        'admin.*',
        'professor.grades.*',
        'professor.attendance.*',
    ],

    /*
    |--------------------------------------------------------------------------
    | API Key Rotation
    |--------------------------------------------------------------------------
    | Security settings for API keys and tokens
    */
    'api_security' => [
        'token_expiry_hours' => 24,
        'rotate_keys_monthly' => true,
        'max_active_tokens' => 5,
    ],
];
