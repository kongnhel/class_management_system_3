<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $columns = DB::select("SHOW COLUMNS FROM users");
        $existing = array_column($columns, 'Field');

        $queries = [
            'phone' => "ALTER TABLE users ADD COLUMN phone VARCHAR(20) NULL AFTER student_id_code",
            'otp_code' => "ALTER TABLE users ADD COLUMN otp_code VARCHAR(6) NULL",
            'otp_expires_at' => "ALTER TABLE users ADD COLUMN otp_expires_at TIMESTAMP NULL",
            'is_verified' => "ALTER TABLE users ADD COLUMN is_verified TINYINT(1) DEFAULT 0",
            'verification_method' => "ALTER TABLE users ADD COLUMN verification_method VARCHAR(20) NULL DEFAULT 'email'",
            'phone_verified_at' => "ALTER TABLE users ADD COLUMN phone_verified_at TIMESTAMP NULL",
            'otp_purpose' => "ALTER TABLE users ADD COLUMN otp_purpose VARCHAR(50) NULL",
        ];

        foreach ($queries as $column => $sql) {
            if (! in_array($column, $existing)) {
                DB::statement($sql);
            }
        }
    }

    public function down(): void
    {
        $columns = DB::select("SHOW COLUMNS FROM users");
        $existing = array_column($columns, 'Field');

        $droppable = ['phone', 'otp_code', 'otp_expires_at', 'is_verified', 'verification_method', 'phone_verified_at', 'otp_purpose'];

        foreach ($droppable as $column) {
            if (in_array($column, $existing)) {
                DB::statement("ALTER TABLE users DROP COLUMN {$column}");
            }
        }
    }
};
