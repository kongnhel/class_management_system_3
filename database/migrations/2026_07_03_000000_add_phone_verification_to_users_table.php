<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'phone')) {
                $table->string('phone', 20)->nullable()->after('student_id_code');
            }
            if (! Schema::hasColumn('users', 'otp_code')) {
                $table->string('otp_code', 6)->nullable();
            }
            if (! Schema::hasColumn('users', 'otp_expires_at')) {
                $table->timestamp('otp_expires_at')->nullable();
            }
            if (! Schema::hasColumn('users', 'is_verified')) {
                $table->boolean('is_verified')->default(false);
            }
            if (! Schema::hasColumn('users', 'verification_method')) {
                $table->string('verification_method', 20)->nullable()->default('email');
            }
            if (! Schema::hasColumn('users', 'phone_verified_at')) {
                $table->timestamp('phone_verified_at')->nullable();
            }
            if (! Schema::hasColumn('users', 'otp_purpose')) {
                $table->string('otp_purpose', 50)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'otp_code',
                'otp_expires_at',
                'is_verified',
                'verification_method',
                'phone_verified_at',
                'otp_purpose',
            ]);
        });
    }
};
